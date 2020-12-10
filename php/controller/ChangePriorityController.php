<?php
    define('__ROOT__', dirname(dirname(__FILE__)));

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
                $priority = $action == 'up' ? $row['priority'] - 1 : $row['priority'] + 1;
                $name = $row['name'];
            }
            $result->close();
            $stmt->close();

            if ($action == 'up') {
                $query = "update commissions set priority = priority - 1 where deleted = 0 and progress != 'WAITLISTED' and id = ?;";
                $stmt = $db->prepare($query);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();

                $query = "update commissions set priority = priority + 1 where deleted = 0 and progress != 'WAITLISTED' and id != ? and priority = ?;";
                $stmt = $db->prepare($query);
                $stmt->bind_param('ii', $id, $priority);
                $stmt->execute();
                $stmt->close();

                header('Location: index.php?success=up&target='.$name);
            } else {
                $query = "update commissions set priority = priority + 1 where deleted = 0 and progress != 'WAITLISTED' and id = ?;";
                $stmt = $db->prepare($query);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();

                $query = "update commissions set priority = priority - 1 where deleted = 0 and progress != 'WAITLISTED' and id != ? and priority = ?;";
                $stmt = $db->prepare($query);
                $stmt->bind_param('ii', $id, $priority);
                $stmt->execute();
                $stmt->close();

                header('Location: index.php?success=down&target='.$name);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
