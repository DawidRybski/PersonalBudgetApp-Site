<?php
	session_start();

	const REMEMBER_ME_LIFETIME = 60 * 60 * 24 * 30;
	
	if((!isset($_POST['email'])) || (!isset($_POST['password'])))
	{
		header('Location: index.php');
		exit();
	}

    try {
		require_once __DIR__.'/config/database.php';

		$email = $_POST["email"];
		$password = $_POST["password"];

		$query = $db->prepare('SELECT id, username, email, password FROM users WHERE email = :email');
		$query->execute([':email' => $email]);
		$user = $query->fetch();

		if ($user === false)
		{
			$_SESSION['login_error'] = 'Incorrect email or password!';

			header('Location: logIn.php');
			exit;
		} else
		{
			if(password_verify($password, $user['password']))
			{
				session_regenerate_id(true);
				
				$_SESSION['user_id'] = $user['id'];
				$_SESSION['name'] = $user['username'];

				if (isset($_POST['rememberMe']))
    			{
					$token = bin2hex(random_bytes(32));
					$tokenHash = hash('sha256', $token);
					$tokenExpireTimestamp = time() + REMEMBER_ME_LIFETIME;
					$tokenExpire = date('Y-m-d H:i:s', $tokenExpireTimestamp);

					$query = $db->prepare('UPDATE users SET token_hash = :tokenHash, token_expires_at = :tokenExpire WHERE id = :userId');
					$query->execute([':tokenHash' => $tokenHash, ':userId' => $user['id'], ':tokenExpire' => $tokenExpire]);

					setcookie('remember_token', $token, 
					[
    					'expires' => $tokenExpireTimestamp,
    					'path' => '/',
    					'secure' => false, // ustawienie dla testów lokalnych
    					'httponly' => true,
    					'samesite' => 'Lax'
					]);

    			}


				header('Location: homePage.php');
    			exit;
			} else
			{
				$_SESSION['login_error'] = 'Incorrect email or password!';

				header('Location: logIn.php');
				exit;
			}
		}

    } catch (PDOException $error){
		error_log($error->getMessage());
		$_SESSION['toast_error'] = 'Server error. Please try again later.';
        header('Location: logIn.php');
        exit;
	}