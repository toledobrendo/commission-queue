<?php
    define('__ROOT__', dirname(dirname(__FILE__)));
    require_once __ROOT__.'/model/Commission.php';
    require_once __ROOT__.'/repository/CommissionRepository.php';

    session_start();

    try {
        require_once __ROOT__.'/util/ConnectDatabase.php';

        $comms = getCommissions($db);

        $prevDate = null;
        $prevExpDays = null;
        $leastPrio = 0;
        $currentDate = new DateTime();
        $farthestDueDate = new DateTime();
        foreach ($comms as &$comm) {
            $comm->setStartDate(new DateTime($comm->getStartDate()));
//            $comm->setStartDate($comm->getStartDate()->format('Y-m-d'));
            $comm->setDueDate(new DateTime($comm->getDueDate()));
//            $comm->setDueDate($comm->getDueDate()->format('Y-m-d'));

            $prevExpDays = $comm->getExpectedDays();
            $leastPrio = $comm->getPriority();

            if ($comm->getDueDate() > $farthestDueDate) {
                $farthestDueDate = $comm->getDueDate();
            }
        }

        $waitlist = getWaitlist($db);

        $db->close();

        $dayPeriod = new DatePeriod(new DateTime(), new DateInterval('P1D'), $farthestDueDate->add(new DateInterval('P1D')));
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    function getWaitlist(&$db) {
        $query = "select * from commissions where deleted = false and progress = 'WAITLISTED' order by created_date;";
        $stmt = $db->prepare($query);

        $stmt->execute();
        $result = $stmt->get_result();

        $leastPrio = null;
        $comms = array();
        while ($row = $result->fetch_assoc()) {
            $comm = new Commission();
            $comm->setId($row['id']);
            $comm->setCreatedDate($row['created_date']);
            $comm->setModifiedDate($row['modified_date']);
            $comm->setName($row['name']);
            $comm->setStartDate($row['start_date']);
            $comm->setProgress($row['progress']);
            $comm->setPaid($row['paid']);
            $comm->setPriority($row['priority']);
            $comm->setExpectedDays($row['expected_days']);
            array_push($comms, $comm);
        }

        $result->close();
        $stmt->close();

        return $comms;
    }
?>