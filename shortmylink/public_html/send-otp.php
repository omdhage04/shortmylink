<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

// Get email from form
$email = $_POST['email'];
$otp = rand(100000, 999999);

// Store OTP in session
$_SESSION['otp'][$email] = $otp;

// Setup mail
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp-relay.brevo.com';
    $mail->SMTPAuth = true;
    $mail->Username = '905f1d001@smtp-brevo.com';
    $mail->Password = 'U6DydkNMVg4HI0aF';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('omdhage04@gmail.com', 'ShortMyLink.in');
    $mail->addAddress($email);
    $mail->Subject = 'Your OTP for ShortMyLink.in';
    $mail->Body = "Your OTP is: $otp";

    $mail->send();
    echo json_encode(["message" => "✅ OTP sent to your email."]);
} catch (Exception $e) {
    echo json_encode(["error" => "Mailer Error: " . $mail->ErrorInfo]);
}
?>
