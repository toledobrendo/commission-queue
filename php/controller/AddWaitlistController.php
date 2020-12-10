<?php
    define('__ROOT__', dirname(dirname(__FILE__)));
    require_once __ROOT__.'/model/Commission.php';

    $name = $_POST['commName'];
    $id = $_POST['id'];
    $priority = $_POST['priority'];
    $currentDate = date("Y-m-d H:i:s ",time());

    if (!$name) {
        header('Location: index.php?error=input-waitlist');
    } else {
        require_once __ROOT__.'/util/ConnectDatabase.php';

        if (!$id) {
            $deleted = 0;
            $progress = 'WAITLISTED';
            $query = 'insert into commissions (name, progress, created_date, modified_date, deleted) values (?, ?, ?, ?, ?);';
            $stmt = $db->prepare($query);
            $stmt->bind_param('ssssi', $name, $progress, $currentDate, $currentDate, $deleted);

            $stmt->execute();
            $stmt->close();
            $db->close();

            header('Location: index.php?success=create-waitlist');
        } else {
            $query = 'update commissions set name = ?, modified_date = ? where id = ?;';
            $stmt = $db->prepare($query);
            $stmt->bind_param('ssi', $name, $currentDate, $id);

            $stmt->execute();
            $stmt->close();
            $db->close();

            header('Location: index.php?success=update-waitlist');
        }
    }
?>