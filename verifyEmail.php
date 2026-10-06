<?php
    session_start();

    require_once __DIR__ . '/config/database.php';

    if (empty($_GET['token'])) {
        $_SESSION['registration_error_toast'] = 'Verification token not found';
        header('Location: register.php');
        exit;
    }

    $verificationToken = $_GET['token'];

    $verificationTokenHash = hash('sha256', $verificationToken);

    $query = $db->prepare(
        'SELECT id, email_verified, verification_token_expires_at
            FROM users
        WHERE verification_token = :verification_token'
    );

    $query->execute([
        ':verification_token' => $verificationTokenHash
    ]);

    $user = $query->fetch();

    if (!$user) {
        $_SESSION['registration_error_toast'] = 'Invalid verification token';
        header('Location: register.php');
        exit;
    }

    if (strtotime($user['verification_token_expires_at']) < time()) {
        $_SESSION['expired_token_toast'] = 'Verification token has expired';
        header('Location: confirmRegister.php');
        exit;
    }

    $query = $db->prepare(
        'UPDATE users
        SET email_verified = 1,
            verification_token = NULL,
            verification_token_expires_at = NULL
        WHERE id = :user_id'
    );

    $query->execute([
        ':user_id' => $user['id']
    ]);

    $_SESSION['registration_toast'] = 'Your email has been successfully verified!';

    unset($_SESSION['pending_verification_user_id']);

    header('Location: logIn.php');
    exit;