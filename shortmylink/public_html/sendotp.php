<?php
session_start(); // Add this line at the very beginning
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Allow only POST requests and set response type to JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request"]);
    exit;
}

// Get the email from POST data
$email = $_POST['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => "Invalid email address."]);
    exit;
}

// Generate a 6-digit OTP
$otp = rand(100000, 999999);

// --- ADD THIS LINE TO STORE OTP IN SESSION ---
$_SESSION['otp'][$email] = $otp;
// ---------------------------------------------

// Create PHPMailer instance
$mail = new PHPMailer(true);

try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com'; // Brevo SMTP host
    $mail->SMTPAuth   = true;
    $mail->Username   = '905f1d003@smtp-brevo.com'; // change this
    $mail->Password   = 'UxHRpSDsWqO24V5Q'; // change this
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Email content
    $mail->setFrom('omdhage04@gmail.com', 'ShortMyLink.in'); // change this
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code';
    $mail->Body    = "<p>Your OTP code is: <strong>$otp</strong></p>";

    // Corrected PHPMailer method call
    $mail->send(); // This should be send(), not sendOTP()

    echo json_encode(["message" => "OTP sent successfully to $email."]);
} catch (Exception $e) {
    echo json_encode(["error" => "Mailer Error: " . $mail->ErrorInfo]);
}
?>