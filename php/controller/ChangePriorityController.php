<?php
    define('__ROOT__', dirname(dirname(__FILE__)));
    require_once __ROOT__.'/model/Commission.php';
    require_once __ROOT__ . '/repository/CommissionRepository.php';
    session_start();
    if (!isset($_SESSION['username'])) {
        header('Location: index.php?error=access-denied');
    }

    $id = $_GET['id'];
    $action = $_GET['action'];

    if (!$id) {
        header('Location: index.php?error=no-id');
    } else if (!$action || ($action != 'up' && $action != 'down')) {
        header('Location: index.php?error=invalid-action');
    } else {
        try {
            require_once __ROOT__ . '/util/ConnectDatabase.php';

            $query = 'select priority, name from commissions where id = ?';
            $stmt = $db->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $priority = $row['priority'];
                $name = $row['name'];
            }
            $result->close();
            $stmt->close();

            if ($action == 'up') {
                $otherPriority = $priority - 1;
                echo 'up: '.$priority.' and '.$otherPriority.'<br/>';
                $comms = getAdjacentCommissions($db, $priority, $otherPriority);

                if (sizeof($comms) != 2) {
                    throw new Exception("Invalid Up Operation");
                }
            } else {
                $otherPriority = $priority + 1;
                echo 'down: '.$priority.' and '.$otherPriority.'<br/>';
                $comms = getAdjacentCommissions($db, $priority, $otherPriority);

                if (sizeof($comms) != 2) {
                    throw new Exception("Invalid Down Operation");
                }
            }

            echo 'prio = '.$comms[0]->getPriority().'<br/>';

            echo 'old start date 1 = '.$comms[1]->getStartDate().'<br/>';
            $comms[1]->setStartDate(new DateTime($comms[0]->getStartDate()));
            $dateTimeCont = new DateTime($comms[1]->getStartDate()->format('Y-m-d'));
            echo 'new start date 1 = '.$comms[1]->getStartDate()->format('Y-m-d').'<br/>';

            echo 'old due date 1 = '.$comms[1]->getDueDate().'<br/>';
            $comms[1]->setDueDate($dateTimeCont->add(new DateInterval('P'.($comms[1]->getExpectedDays()-1).'D')));
            $dateTimeCont = new DateTime($comms[1]->getDueDate()->format('Y-m-d'));
            echo 'new due date 1 = '.$comms[1]->getDueDate()->format('Y-m-d').'<br/>';

            echo 'old start date 0 = '.$comms[0]->getStartDate().'<br/>';
            $comms[0]->setStartDate($dateTimeCont->add(new DateInterval('P1D')));
            $dateTimeCont = new DateTime($comms[0]->getStartDate()->format('Y-m-d'));
            echo 'new start date 0 = '.$comms[0]->getStartDate()->format('Y-m-d').'<br/>';

            echo 'old due date 0 = '.$comms[0]->getDueDate().'<br/>';
            $comms[0]->setDueDate($dateTimeCont->add(new DateInterval('P'.($comms[0]->getExpectedDays()-1).'D')));
            echo 'new due date 0 = '.$comms[0]->getDueDate()->format('Y-m-d').'<br/>';

            foreach ($comms as &$comm) {
                $query = 'update commissions set start_date = ?, due_date = ? where id = ?;';
                $stmt = $db->prepare($query);
                @ $stmt->bind_param('ssi', $comm->getStartDate()->format('Y-m-d'), $comm->getDueDate()->format('Y-m-d'), $comm->getId());
                $stmt->execute();
                $stmt->close();
            }

            optimizePriorities($db, new DateTime($comms[0]->getStartDate()->format('Y-m-d')), new DateTime($comms[0]->getDueDate()->format('Y-m-d')));

            header('Location: index.php?success='.$action.'&target='.$name);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
