<?php
    define('__ROOT__', dirname(dirname(__FILE__)));
    require_once __ROOT__.'/resources/DatabaseProperties.php';
    require_once __ROOT__.'/model/Commission.php';

    try {
        @ $db = new mysqli(DB_HOST_IP.':'.DB_PORT, DB_USERNAME, DB_PASSWORD, DB_NAME);

        $dbError = mysqli_connect_errno();
        if ($dbError) {
            throw new Exception('Error: Could not connect to database. Please try again later.');
        }

        $query = 'select * from commissions where deleted = false order by priority;';
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
            array_push($comms, $comm);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
?>