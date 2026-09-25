<?php
    session_start();

    if(isset($_COOKIE['remember_token'])){

        if (isset($_SESSION['user_id'])){
            try {
                require_once __DIR__ . '/config/database.php';

                $query = $db->prepare('UPDATE users SET token_hash = NULL, token_expires_at = NULL WHERE id = :userId');
                $query->execute([':userId' => $_SESSION['user_id']]);

            } catch (PDOException $error){
                error_log($error->getMessage());
            }
        }

        setcookie('remember_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    session_unset();
    session_destroy();

    header('Location: index.php');
    exit;