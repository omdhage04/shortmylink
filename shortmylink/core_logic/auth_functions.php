<?php
require_once __DIR__ . '/../secure_config/db_connect.php';

function registerUser($username, $email, $password) {
    global $pdo;

    // 1. Sanitize inputs
    $username = trim(htmlspecialchars($username));
    $email = trim(filter_var($email, FILTER_SANITIZE_EMAIL));

    // 2. Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->rowCount() > 0) {
        return ['status' => 'error', 'message' => 'User already exists'];
    }

    // 3. Hash the password (Security Standard)
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // 4. Generate a random API Token
    $api_token = bin2hex(random_bytes(32));

    // 5. Insert into Database
    $sql = "INSERT INTO users (username, email, password_hash, api_token, role) VALUES (?, ?, ?, ?, 'user')";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$username, $email, $hashed_password, $api_token])) {
        return ['status' => 'success', 'message' => 'Account created successfully'];
    } else {
        return ['status' => 'error', 'message' => 'Database insert failed'];
    }
}
?>