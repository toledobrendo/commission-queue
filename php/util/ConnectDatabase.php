<?php
    require_once __ROOT__.'/resources/DatabaseProperties.php';

    @ $db = new mysqli(DB_HOST_IP.':'.DB_PORT, DB_USERNAME, DB_PASSWORD, DB_NAME);

    $dbError = mysqli_connect_errno();
    if ($dbError) {
        throw new Exception('Error: Could not connect to database. Please try again later.');
    }
?>