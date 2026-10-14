<?php

    session_start();

    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/config/mail.php';

    if (isset($_SESSION['expired_verification_user_id'])) {
        $userId = $_SESSION['expired_verification_user_id'];
    }
    elseif (isset($_SESSION['pending_verification_user_id'])) {
        $userId = $_SESSION['pending_verification_user_id'];
    }
    else {
        $_SESSION['verification_info_toast'] = 'There is no pending email verification.';

        header('Location: logIn.php');
        exit;
    }

    $query = $db->prepare('SELECT id, email, email_verified FROM users WHERE id = :user_id');

    $query->execute([':user_id' => $userId]);

    $user = $query->fetch();

    if (!$user) {
        unset(
            $_SESSION['pending_verification_user_id'],
            $_SESSION['expired_verification_user_id']
        );

        $_SESSION['registration_error_toast'] = 'User account could not be found.';

        header('Location: register.php');
        exit;
    }

    if ($user['email_verified']) {
        unset(
            $_SESSION['pending_verification_user_id'],
            $_SESSION['expired_verification_user_id']
        );

        $_SESSION['verification_info_toast'] = 'Your account has already been verified. You can log in.';

        header('Location: logIn.php');
        exit;
    }

    if (isset($_POST['resendEmail'])) {

        $verificationToken = bin2hex(random_bytes(32));
        $verificationTokenHash = hash('sha256', $verificationToken);

        $tokenExpiresAt = new DateTime('+24 hour');
        $tokenExpiresAt = $tokenExpiresAt->format('Y-m-d H:i:s');

        $query = $db->prepare(
            'UPDATE users
                SET verification_token = :verification_token,
                verification_token_expires_at = :verification_token_expires_at
            WHERE id = :user_id AND email_verified = 0'
        );

        $query->execute([
            ':verification_token' => $verificationTokenHash,
            ':verification_token_expires_at' => $tokenExpiresAt,
            ':user_id' => $userId
        ]);

        sendVerificationEmail($user['email'], $verificationToken);

        $_SESSION['pending_verification_user_id'] = $user['id'];

        unset($_SESSION['expired_verification_user_id']);
        $_SESSION['resend_email_toast'] = 'A new verification link has been sent to your email address.';


        header('Location: confirmRegister.php');
        exit;
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
        <?php if (isset($_SESSION['resend_email_toast'])): ?>
            <div class="toast-container position-absolute top-0 end-0 p-3">
                <div id="resendEmailToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <?= htmlspecialchars($_SESSION['resend_email_toast']) ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const toastElement =
                        document.getElementById('resendEmailToast');

                    const toast = new bootstrap.Toast(toastElement);
                    toast.show();
                });
            </script>
        <?php
            unset($_SESSION['resend_email_toast']);
            endif;
        ?>
        <?php if (isset($_SESSION['expired_token_toast'])): ?>
            <div class="toast-container position-absolute top-0 end-0 p-3">
                <div id="tokenToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <?= isset($_SESSION['expired_token_toast']) ? htmlspecialchars($_SESSION['expired_token_toast']) : '' ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const toastElement = document.getElementById('tokenToast');
                    const toast = new bootstrap.Toast(toastElement);
                    toast.show();
                });
            </script>
        <?php
            unset($_SESSION['expired_token_toast']);
            endif;
        ?>
        <section class="form-section" style="max-width: 480px;">
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
                                <span class="col-2 input-symbol">
                                    <img src="assets/images/forms/mail-icon.svg" alt="" aria-hidden="true">
                                </span>
                                <label for="email" class="visually-hidden">Email address</label>
                                <input id="email" class="col field" type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                            </div>
                            <p class="forwarding">Didn't receive the email or did it expire?
                                    Check your spam folder or click below to resend it. 
                            </p>
                            <button class="button-primary" name="resendEmail" type="submit">Resend email</button>
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