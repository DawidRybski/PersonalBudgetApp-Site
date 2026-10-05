<?php

    session_start();

    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/config/mail.php';

    if (!isset($_SESSION['pending_verification_user_id'])){
        header('Location: register.php');
        exit;
    }

    $userId = $_SESSION['pending_verification_user_id'];

    $query = $db->prepare('SELECT id, email, email_verified FROM users WHERE id = :user_id');

    $query->execute([':user_id' => $userId]);

    $user = $query->fetch();


    if (isset($_POST['resendEmail'])) {
        sendVerificationEmail($user['email']);
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Manager - confirmRegister</title>
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
    <main class="container main-centered position-relative">
        <section class="form-section">
            <h2 id="formTitle" class="form-header h4">Confirm register</h2> 
            <div class="form-card"> 
                <div class="description">
                    <p>We have sent you an email confirming your account creation. 
                        Once you confirm your registration, you will be redirected to the login page, 
                        where you will see a confirmation message.</p>
                </div>
                <form method="post" aria-labelledby="formTitle" novalidate>
                    <div class="fields">
                        <div class="container field-control">
                            <div class="row input-wrapper">
                                <p class="forwarding">Didn't receive the email?
                                    Check your spam or junk folder, or click below to resend it.? 
                                </p>
                                <button class="button-primary" name="resendEmail" type="submit">Resend email</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section> 
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
    <script src="assets/js/validation.js"></script>
    <script src="assets/js/modals.js"></script>
    <script src="assets/js/balance.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>