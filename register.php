<?php
    session_start();

    if (isset($_POST['email']))
    {
        $validation_OK = true;

        $name = $_POST['name'];
     
        if ((strlen($name)<3) || (strlen($name)>50))
        {
            $validation_OK = false;
            $_SESSION['e_name']="Name must be between 3 and 50 characters long";
        }

        if (!preg_match('/^[\p{L}\p{N}_-]+$/u', $name))
        {
            $validation_OK = false;
			$_SESSION['e_name']="The name cannot contain special characters other than _ and -";
        }

        $email=$_POST['email'];
		$emailB=filter_var($email, FILTER_SANITIZE_EMAIL);

        if ((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB!=$email))
		{
			$validation_OK=false;
			$_SESSION['e_email']="Looks like this is not an email!";
		}

        $password = $_POST['password'];
		$confirmPassword = $_POST['confirmPassword'];
		
		if((strlen($password)<6) || (strlen($password)>64))
		{
			$validation_OK=false;
			$_SESSION['password']="Password must be between 6 and 64 characters long";
		}
		
		if($password!=$confirmPassword)
		{
			$validation_OK=false;
			$_SESSION['password']="Passwords do not match";
		}

        if (empty($_POST['email']) || empty($_POST['name']) || empty($_POST['password']) || empty($_POST['confirmPassword']))
        {
            $validation_OK = false;
        }

        $password_hash=password_hash($password,PASSWORD_DEFAULT);

        $_SESSION['fr_email'] = $email;
		$_SESSION['fr_name'] = $name;
		$_SESSION['fr_password'] = $password;
		$_SESSION['fr_confirmPassword'] = $confirmPassword;

        require_once 'config/database.php';

        try {
            // Does email exist?
            $query = $db->prepare('SELECT id FROM users WHERE email = :email');
            $query->execute([':email' => $email]);

            if ($query->fetch())
            {
                $validation_OK = false;
                $_SESSION['e_email'] = 'There is already an account with this email address!';
            }

             if ($validation_OK == true) 
            {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $query = $db->prepare('INSERT INTO users (username, email, password) VALUES (:name, :email, :password)');

                $query->execute([
                    ':name' => $name,
                    ':email' => $email,
                    ':password' => $password_hash
                ]);

                $_SESSION['successful_registration'] = true;
                header('Location: logIn.php');
                exit;
            }

        } catch (PDOException $error) {
            echo '<span style="color:red;">Błąd serwera! Spróbuj ponownie później.</span>';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Manager - Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header class="p-3 mb-3"> 
        <div class="container d-flex align-items-center justify-content-between"> 
            <a href="index.php" class="logo">
                <img src="assets/images/piggy-bank-icon.svg" height="25" alt="">
                <span class="h6">Budget <br>Manager</span>   
            </a>
            <div class="d-flex gap-2">
                <a class="button-primary px-3" href="logIn.php">Log in</a> 
            </div>
        </div> 
    </header>
    <main class="container main-centered">
        <section class="form-section">
            <h2 id="formTitle" class="form-header h4">Register Form</h2> 
            <div class="form-card"> 
                <div class="description">
                    <p>Enter your details to create an account</p>
                </div>
                <form method="post" aria-labelledby="formTitle" novalidate>
                    <div class="fields">
                        <div class="container field-control">
                            <div class="row input-wrapper">
                                <span class="col-2 input-symbol">
                                    <img src="assets/images/forms/mail-icon.svg" alt="" aria-hidden="true">
                                </span>
                                <label for="email" class="visually-hidden">Email Address</label>
                                <input id="email" name="email" class="col field" type="email" placeholder="Email Address" required>
                                <img class="error-icon" src="assets/images/forms/validate-icon.svg" alt="">
                            </div>
                        </div>
                        <div class="container field-control">
                            <div class="row input-wrapper">
                                <span class="col-2 input-symbol">
                                    <img src="assets/images/forms/person-icon.svg" alt="" aria-hidden="true">
                                </span>
                                <label for="name" class="visually-hidden">Name</label>
                                <input id="name" name="name" class="col field" type="text" placeholder="Name" required>
                                <img class="error-icon" src="assets/images/forms/validate-icon.svg" alt="">
                            </div>
                        </div>
                        <div class="container field-control">
                            <div class="row input-wrapper">
                                <span class="col-2 input-symbol">
                                    <img src="assets/images/forms/password-icon.svg" alt="" aria-hidden="true">
                                </span>
                                <label for="password" class="visually-hidden">Password</label>
                                <input id="password" name="password" class="col field" type="password" placeholder="Password" required>
                                <img class="error-icon" src="assets/images/forms/validate-icon.svg" alt="">
                            </div>
                        </div>
                        <div class="container field-control">
                            <div class="row input-wrapper">
                                <span class="col-2 input-symbol">
                                    <img src="assets/images/forms/password-icon.svg" alt="" aria-hidden="true">
                                </span>
                                <label for="confirmPassword" class="visually-hidden">Confirm password</label>
                                <input id="confirmPassword" name="confirmPassword" class="col field" type="password" placeholder="Confirm Password" required>
                                <img class="error-icon" src="assets/images/forms/validate-icon.svg" alt="">
                            </div>
                        </div>
                        <button class="button-primary submit" type="submit">Sign up</button>
                        <p class="forwarding">Already have an account? 
                            <a href="logIn.php">Log in</a>
                        </p>
                    </div>
                </form>
            </div>
        </section> 
    </main>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
<script src="assets/js/validation.js"></script>
<script src="assets/js/modals.js"></script>
<script src="assets/js/balance.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>