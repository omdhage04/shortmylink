<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../secure_config/db_connect.php';
require_once __DIR__ . '/../../core_logic/auth_functions.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['username']) || !isset($input['email']) || !isset($input['password'])) {
    echo json_encode(['status'=>'error','message'=>'Missing fields']);
    exit;
}

// If registerUser exists in auth_functions, call it, otherwise perform insert
if (function_exists('registerUser')) {
    $res = registerUser($input['username'], $input['email'], $input['password']);
    echo json_encode($res);
    exit;
}

// Fallback simple registration
$username = trim(htmlspecialchars($input['username']));
$email = trim(filter_var($input['email'], FILTER_SANITIZE_EMAIL));
$hashed = password_hash($input['password'], PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? OR username = ?');
    $stmt->execute([$email, $username]);
    if ($stmt->rowCount()>0) {
        echo json_encode(['status'=>'error','message'=>'User exists']);
        exit;
    }
    $api_token = bin2hex(random_bytes(32));
    $ins = $pdo->prepare('INSERT INTO users (username,email,password_hash,api_token,role) VALUES (?,?,?,?,"user")');
    if ($ins->execute([$username,$email,$hashed,$api_token])) {
        echo json_encode(['status'=>'success','message'=>'Account created']);
    } else {
        echo json_encode(['status'=>'error','message'=>'DB insert failed']);
    }
} catch (Exception $e) {
    error_log($e->getMessage(),3,__DIR__.'/../../logs/error.log');
    echo json_encode(['status'=>'error','message'=>'Server error']);
}
?>