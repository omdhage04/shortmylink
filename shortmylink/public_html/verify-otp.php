<?php
session_start();

$email = $_POST['email'];
$otp = $_POST['otp'];

if (isset($_SESSION['otp'][$email]) && $_SESSION['otp'][$email] == $otp) {
    unset($_SESSION['otp'][$email]);
    echo json_encode(["redirect" => "welcome.html"]);
} else {
    echo json_encode(["message" => "❌ Invalid OTP"]);
}
?>
