<?php
    define('__ROOT__', dirname(dirname(__FILE__)));
    require_once __ROOT__.'/model/Commission.php';

    session_start();

    try {
        require_once __ROOT__.'/util/ConnectDatabase.php';

        $comms = getQueue($db);

        $prevDate = null;
        $prevExpDays = null;
        $leastPrio = 0;
        foreach ($comms as &$comm) {
            if (!$prevDate) {
                $prevDate = new DateTime($comm->getStartDate());
                $comm->setStartDate($prevDate->format('M j, Y'));
            } else {
                $prevDate->add(new DateInterval('P'.$prevExpDays.'D'));
                $comm->setStartDate($prevDate->format('M j, Y').' (est)');
            }
            $prevExpDays = $comm->getExpectedDays();
            $leastPrio = $comm->getPriority();
        }

        $waitlist = getWaitlist($db);

        $db->close();
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    function getQueue(&$db) {
        $query = "select * from commissions where deleted = false and progress != 'WAITLISTED' order by priority;";
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