<?php
	session_start();
	
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
			echo "test";

			header('Location: logIn.php');
			exit;
		} else
		{
			if(password_verify($password, $user['password']))
			{
				session_regenerate_id(true);
				
				$_SESSION['user_id'] = $user['id'];
				$_SESSION['name'] = $user['username'];

				header('Location: homePage.php');
    			exit;
			} else
			{
				$_SESSION['login_error'] = 'Incorrect email or password!';

				header('Location: logIn.php');
				exit;
			}
		}

    } catch (PDOException $error)
    {
		error_log($error->getMessage());

        $_SESSION['toast_error'] = 'Server error. Please try again later.';
        header('Location: logIn.php');
        exit;
	}