<?php
    function getCommissions(&$db, $sortBy = 'priority') {
        $query = "select * from commissions where deleted = false and progress != 'WAITLISTED' order by ".$sortBy.";";
        $stmt = $db->prepare($query);

        $stmt->execute();
        $result = $stmt->get_result();

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
            $comm->setDescription($row['description']);
            $comm->setDueDate($row['due_date']);
            array_push($comms, $comm);
        }

        $result->close();
        $stmt->close();

        return $comms;
    }

    function getAdjacentCommissions(&$db, $prio1, $prio2) {
        $query = "select * from commissions where deleted = false and progress != 'WAITLISTED' and (priority = ? or priority = ?) order by priority;";
        $stmt = $db->prepare($query);
        $stmt->bind_param('ii', $prio1, $prio2);

        $stmt->execute();
        $result = $stmt->get_result();

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
            $comm->setDescription($row['description']);
            $comm->setDueDate($row['due_date']);
            array_push($comms, $comm);
        }

        $result->close();
        $stmt->close();

        return $comms;
    }

    function optimizePriorities(&$db, $startDate, $dueDate) {
        $comms = getCommissions($db, "start_date");

        $priority = 1;
        $prevDueDate = $dueDate;
        foreach ($comms as &$comm) {
            echo $comm->getName().'<br/>';
            $comm->setPriority($priority);
            $priority++;

            $commStartDate = new DateTime($comm->getStartDate());
            $commDueDate = new DateTime($comm->getDueDate());
            echo $startDate->format('Y-m-d').'<'.$comm->getStartDate().'<br/>';
            if ($startDate < $commStartDate) {
                echo 'will adjust<br/>';
                $diff = intval($prevDueDate->diff($commStartDate)->format('%r%a'));

                echo $prevDueDate->format('Y-m-d').' - '.$commStartDate->format('Y-m-d').' = '.$diff.'<br/>';
                if ($diff > 1) {
                    $commStartDate = $commStartDate->sub(new DateInterval('P'.($diff-1).'D'));
                    $comm->setStartDate($commStartDate->format('Y-m-d'));
                    $commDueDate = $commStartDate->add(new DateInterval('P'.($comm->getExpectedDays()-1).'D'));
                    $comm->setDueDate($commDueDate->format('Y-m-d'));
                } else if ($diff <= -1) {
                    $commStartDate = $commStartDate->add(new DateInterval('P'.(($diff-1)*-1).'D'));
                    $comm->setStartDate($commStartDate->format('Y-m-d'));
                    $commDueDate = $commStartDate->add(new DateInterval('P'.($comm->getExpectedDays()-1).'D'));
                    $comm->setDueDate($commDueDate->format('Y-m-d'));
                }

                echo 'new start = '.$comm->getStartDate().'<br/>';
            }

            $query = 'update commissions set priority = ?, start_date = ?, due_date = ? where id = ?;';
            $stmt = $db->prepare($query);
            @ $stmt->bind_param('issi', $comm->getPriority(), $comm->getStartDate(), $comm->getDueDate(), $comm->getId());
            $stmt->execute();
            $stmt->close();

            $prevDueDate = $commDueDate;
        }
    }
?>