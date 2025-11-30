<?php
// 1. Start the session to access it
session_start();

// 2. Unset all session variables
$_SESSION = array();

// 3. Destroy the session cookie on the server
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destroy the PHP session
session_destroy();

// 5. Kill the "Remember Me" cookie (if you used one)
if (isset($_COOKIE['shortmylink_auth'])) {
    setcookie('shortmylink_auth', '', time() - 3600, '/'); 
}

// 6. Return success JSON
header('Content-Type: application/json');
echo json_encode(['status' => 'success']);
?>