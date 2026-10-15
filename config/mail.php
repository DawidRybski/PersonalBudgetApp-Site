<?php
    require_once __DIR__ . '/../PHPMailer/src/Exception.php';
    require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    function sendVerificationEmail($email, $verificationToken){
        $verificationLink = 'https://personalbudgetapp-site.local/verifyEmail.php?token='.urlencode($verificationToken);

        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
		    $mail->SMTPDebug = SMTP::DEBUG_OFF;
			
		    $mail->Host = 'smtp.gmail.com';
		    $mail->Port = 465;
		    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		    $mail->SMTPAuth = true;
		    $mail->Username = 'dawid.rybski.programowanie@gmail.com';
		    $mail->Password = 'aeha izgi jgkq fvre ';
			
		    $mail->CharSet = 'UTF-8';
		    $mail->setFrom('dawid.rybski.programowanie@gmail.com','Budget Manager');
		    $mail->addAddress($email);
			
		    $mail->isHTML(true);
		    $mail->Subject = 'Register account in Budget Manager';

            $mail->Body = '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                </head>

                <body style="
                    margin: 0;
                    padding: 32px 16px;
                    background-color: #f5f5f5;
                    font-family: Arial, sans-serif;
                ">

                    <div style="
                        max-width: 600px;
                        margin: 0 auto;
                    ">

                        <div style="
                            background-color: rgb(76, 88, 255);
                            padding: 16px;
                            border-radius: 4px 4px 0 0;
                            text-align: center;
                            color: white;
                        ">
                            <h2 style="margin: 0;">
                                Verify your email
                            </h2>
                        </div>

                        <div style="
                            background-color: white;
                            padding: 32px;
                            border-radius: 0 0 4px 4px;
                        ">

                            <p>
                                Thank you for registering in Budget Manager.
                            </p>

                            <p>
                                Click the button below to verify your email address
                                and complete your registration.
                            </p>

                            <div style="
                                text-align: center;
                                margin-top: 24px;
                                margin-bottom: 24px;
                            ">

                                <a href="' . htmlspecialchars($verificationLink, ENT_QUOTES, 'UTF-8') . '"
                                style="
                                    display: inline-block;
                                    background-color: rgb(76, 88, 255);
                                    border: 1px solid rgb(154, 143, 255);
                                    border-radius: 4px;
                                    padding: 12px 24px;
                                    color: white;
                                    font-weight: 600;
                                    text-decoration: none;
                                ">
                                    Verify email
                                </a>

                            </div>

                            <p style="
                                margin-bottom: 0;
                                font-size: 14px;
                                color: #757575;
                            ">
                                If you did not create an account in Budget Manager,
                                you can ignore this email.
                            </p>

                        </div>

                    </div>

                </body>
                </html>
            ';

            $mail->AltBody = "Confirm your email address by opening this link:\n". $verificationLink;

            $mail->send();

            return true;
        
        } catch(Exception $error) {
			error_log($error->getMessage());
            return false;
		}
        
    }