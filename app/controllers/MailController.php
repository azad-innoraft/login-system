<?php

namespace App\controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class MailController {
    /**
     * Check email is valid or not
     * @param string $email Email address
     * @return bool True if email is valid, otherwise false. 
     */
    public function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function send(string $email, string $token) {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = $_ENV['MAIL_HOST'];                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = $_ENV['MAIL_USER'];                     //SMTP username
            $mail->Password   = $_ENV['MAIL_PASS'];                     //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to
            $mail->SMTPDebug = 0;                                       // Disable debug output

            //Recipients
            $mail->setFrom($_ENV['MAIL_USER'], 'Auth App');
            $mail->addAddress($email);     //Add a recipient

            //Content
            $mail->isHTML(true);
            $mail->Subject = "Reset Your Password";

            $host = $_SERVER['HTTP_HOST'] ?? 'auth.com';
            $resetLink = "http://" . $host . "/reset?token=" . urlencode($token);

            $mail->Body = "<div style='font-family: Arial, sans-serif; background:#f4f4f4; padding:20px;'>
                            <div style='max-width:600px; margin:auto; background:#ffffff; padding:20px; border-radius:8px;'>

                                <h2 style='text-align:center; color:#333;'>Password Reset Request</h2>

                                <p>Hi,</p>

                                <p>You recently requested to reset your password. Click the button below to proceed:</p>

                                <div style='text-align:center; margin:20px 0;'>
                                    <a href='$resetLink' 
                                    style='background:#667eea; color:#fff; padding:12px 20px; text-decoration:none; border-radius:5px; display:inline-block;'>
                                    Reset Password
                                    </a>
                                </div>

                                <p>If you did not request this, please ignore this email.</p>

                                <p><strong>This link will expire in 5 minutes.</strong></p>

                                <hr>

                                <p style='font-size:12px; color:#888; text-align:center;'>
                                    If the button doesn't work, copy and paste this link:<br>
                                    $resetLink
                                </p>

                            </div>
                        </div>
                        ";

            return $mail->send();
        } catch (\Exception $e) {
            return false;
        }
    }
}
