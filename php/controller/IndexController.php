<?php
    define('__ROOT__', dirname(dirname(__FILE__)));
    require_once __ROOT__.'/model/Commission.php';

    session_start();

    try {
        require_once __ROOT__.'/util/ConnectDatabase.php';

        $query = 'select * from commissions where deleted = false order by priority;';
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

        $prevDate = null;
        foreach ($comms as &$comm) {
            if (!$prevDate) {
                $prevDate = new DateTime($comm->getStartDate());
            } else {
                $prevDate->add(new DateInterval('P14D'));
                $comm->setStartDate($prevDate->format('Y-m-d'));
            }
            $leastPrio = $comm->getPriority();
        }

        if (!$leastPrio) {
            $leastPrio = 0;
        }
        
        $result->close();
        $stmt->close();
        $db->close();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
?>