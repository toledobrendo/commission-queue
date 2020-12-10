<?php
    define('__ROOT__', dirname(dirname(__FILE__)));
    require_once __ROOT__.'/model/Commission.php';

    $name = $_POST['commName'];
    $progress = $_POST['progress'];
    $paid = $_POST['paid'] == 'true' ? 1 : 0;
    $id = $_POST['id'];
    $priority = $_POST['priority'];
    $currentDate = date("Y-m-d H:i:s ",time());
    $expectedDays = $_POST['expectedDays'];

    if (!$name || is_null($paid) || !$progress) {
        header('Location: index.php?error=input');
    } else {
        require_once __ROOT__.'/util/ConnectDatabase.php';

        if (!$id) {
            $priority = getNextPriority($db);
            $deleted = 0;
            $query = 'insert into commissions (name, progress, paid, priority, expected_days, created_date, modified_date, deleted) values (?, ?, ?, ?, ?, ?, ?, ?);';
            $stmt = $db->prepare($query);
            $stmt->bind_param('sssiissi', $name, $progress, $paid, $priority, $expectedDays, $currentDate, $currentDate, $deleted);

            $stmt->execute();
            $stmt->close();
            $db->close();

            header('Location: index.php?success=create');
        } else {
            if ($progress == 'WAITLISTED') {
                $priority = getNextPriority($db);
                $progress = 'Queued';
            }

            $query = 'update commissions set name = ?, progress = ?, paid = ?, priority = ?, modified_date = ?, expected_days = ?  where id = ?;';
            $stmt = $db->prepare($query);
            $stmt->bind_param('sssisii', $name, $progress, $paid, $priority, $currentDate, $expectedDays, $id);

            $stmt->execute();
            $stmt->close();
            $db->close();

            header('Location: index.php?success=update');
        }
    }

    function getNextPriority(&$db) {
        $query = 'select max(priority) as priority from commissions where deleted = 0';
        $stmt = $db->prepare($query);

        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $priority = $row['priority'] + 1;
        } else {
            $priority = 1;
        }
        $result->close();
        $stmt->close();

        return $priority;
    }
?>