<?php
    define('__ROOT__', dirname(dirname(__FILE__)));
    session_start();

    $password = $_POST['password'];
    $username = 'sol';
    if (!$password) {
        header('Location: index.php?error=inc-cred');
    } else {
        try {
            $encryptedPass = password_hash($password, PASSWORD_DEFAULT);

            require_once __ROOT__ . '/util/ConnectDatabase.php';

            $query = 'select username, password from user_info where username = ? and deleted = 0';
            $stmt = $db->prepare($query);
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if (password_verify($password, $row['password'])) {
                    $_SESSION['username'] = $username;
                } else {
                    header('Location: index.php?error=inc-cred');
                }
                header('Location: index.php');
            } else {
                header('Location: index.php?error=inc-cred');
            }

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
?>