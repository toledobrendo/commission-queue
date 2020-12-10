<?php
    define('__ROOT__', dirname(dirname(__FILE__)));
    session_start();
    if (!isset($_SESSION['username'])) {
        header('Location: index.php?error=access-denied');
    }

    $id = $_GET['id'];
    if (!$id) {
        header('Location: index.php?error=no-id');
    } else {
        try {
            require_once __ROOT__ . '/util/ConnectDatabase.php';

            $query = 'select priority, progress from commissions where id = ?';
            $stmt = $db->prepare($query);
            $stmt->bind_param('i', $id);

            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $priority = $row['priority'];
                $progress = $row['progress'];
            } else {
                throw new Exception("Could not be deleted");
            }
            $result->close();
            $stmt->close();

            $query = 'update commissions set deleted = 1 where id = ?;';
            $stmt = $db->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();

            if ($progress != 'WAITLISTED') {
                $query = 'update commissions set priority = priority - 1 where deleted = 0 and priority > ?;';
                $stmt = $db->prepare($query);
                $stmt->bind_param('i', $priority);
                $stmt->execute();
                $stmt->close();

                if ($priority == 1) {
                    $currentDate = date("Y-m-d",time());
                    $query = 'update commissions set start_date = ? where deleted = 0 and priority = 1;';
                    $stmt = $db->prepare($query);
                    $stmt->bind_param('s', $currentDate);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            $db->close();

            header('Location: index.php?success=delete');
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

?>