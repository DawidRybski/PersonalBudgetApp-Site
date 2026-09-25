<?php
    if (!isset($_SESSION['user_id'])) {

        if(isset($_COOKIE['remember_token'])){

            $token = $_COOKIE['remember_token'];
            $tokenHash = hash('sha256', $token);

            try {
                require_once __DIR__ . '/database.php';

                $query = $db->prepare('SELECT u.id, u.username, u.token_expires_at FROM users u WHERE u.token_hash = :token_hash');
                $query->execute([':token_hash' => $tokenHash]);

                $user = $query->fetch();

                if($user !== false){

                    $actualDate = date('Y-m-d H:i:s');

                    if($user['token_expires_at'] > $actualDate){
                        session_regenerate_id(true);

                        $_SESSION['user_id'] = $user['id'];
				        $_SESSION['name'] = $user['username'];

                    }
                }

            } catch (PDOException $error) {
                error_log($error->getMessage());
                $_SESSION['toast_error'] = 'Server error. Please try again later.';
                header('Location: logIn.php');
                exit;
            }
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: logIn.php');
            exit;
        }
    }