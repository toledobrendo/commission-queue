<?php
    define('__ROOT__', dirname(dirname(__FILE__)));
    require_once __ROOT__.'/model/Commission.php';
    require_once __ROOT__.'/repository/CommissionRepository.php';

    $name = $_POST['commName'];
    $progress = $_POST['progress'];
    $paid = $_POST['paid'] == 'true' ? 1 : 0;
    $id = $_POST['id'];
    $priority = $_POST['priority'];
    $currentDate = date("Y-m-d H:i:s ",time());
    $expectedDays = $_POST['expectedDays'];
    $description = $_POST['description'];
    $startDate = $_POST['startDate'];
    $dueDate = null;
    $oldStartDate = null;

    if (!$name || is_null($paid) || !$progress) {
        header('Location: index.php?error=input');
    } else {
        require_once __ROOT__.'/util/ConnectDatabase.php';

        if (!$id) {
            $defaultComm = getNextCommissionValues($db);
            $priority = $defaultComm->getPriority();
            $deleted = 0;
            $startDate = $defaultComm->getStartDate()->format('Y-m-d');
            $expectedDays = $defaultComm->getExpectedDays();
            $dueDate = $defaultComm->getDueDate()->format('Y-m-d');

            $query = 'insert into commissions (name, progress, paid, priority, expected_days, created_date, modified_date, deleted, description, start_date, due_date) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);';
            $stmt = $db->prepare($query);
            $stmt->bind_param('sssiississs', $name, $progress, $paid, $priority, $expectedDays, $currentDate, $currentDate, $deleted, $description, $startDate, $dueDate);
            $stmt->execute();
            $stmt->close();
            $db->close();

            header('Location: index.php?success=create');
        } else {
            if ($progress == 'WAITLISTED') {
                $defaultComm = getNextCommissionValues($db);
                $priority = $defaultComm->getPriority();
                $startDate = $defaultComm->getStartDate()->format('Y-m-d');
                $expectedDays = $defaultComm->getExpectedDays();
                $dueDate = $defaultComm->getDueDate()->format('Y-m-d');
                $progress = 'Queued';
            } else {
                $dueDate = new DateTime($startDate);
                $dueDate = $dueDate->add(new DateInterval('P'.($expectedDays-1).'D'));
                $dueDate = $dueDate->format('Y-m-d');
            }

            $query = 'update commissions set name = ?, progress = ?, paid = ?, priority = ?, modified_date = ?, expected_days = ?, description = ?, start_date = ?, due_date = ? where id = ?;';
            $stmt = $db->prepare($query);
            $stmt->bind_param('sssisisssi', $name, $progress, $paid, $priority, $currentDate, $expectedDays, $description, $startDate, $dueDate, $id);
            $stmt->execute();
            $stmt->close();

            optimizePriorities($db, new DateTime($startDate), new DateTime($dueDate));

            $db->close();

            header('Location: index.php?success=update');
        }
    }

    function getNextCommissionValues(&$db) {
        $nextComm = new Commission();
        $nextComm->setExpectedDays(14);

        $query = 'select max(priority) as priority from commissions where deleted = 0';
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $nextComm->setPriority($row['priority'] + 1);
        } else {
            $nextComm->setPriority(1);
        }
        $result->close();
        $stmt->close();

        $query = 'select max(due_date) as due_date from commissions where deleted = 0';
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $nextStartDate = null;

        if ($row = $result->fetch_assoc()) {
            if ($row['due_date']) {
                $nextStartDate = new DateTime($row['due_date']);
                $nextStartDate = $nextStartDate->add(new DateInterval('P1D'));
            }
        }
        if (!$nextStartDate) {
            $nextStartDate = new DateTime();
        }
        $nextComm->setStartDate($nextStartDate);
        $nextComm->setDueDate(new DateTime($nextStartDate->format('Y-m-d')));

        $nextComm->setDueDate($nextComm->getDueDate()->add(new DateInterval('P'.($nextComm->getExpectedDays()-1).'D')));
        $result->close();
        $stmt->close();


        return $nextComm;
    }
?>