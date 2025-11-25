<?php
// -----------------------------
// Hostinger Database Connection (FINAL UPDATED with site credentials)
// -----------------------------
define('DB_HOST', 'localhost');   // Hostinger MySQL host from your screenshot
define('DB_NAME', 'u545014911_UserInfo');
define('DB_USER', 'u545014911_user_info');
define('DB_PASS', '#Dhage@3005'); // your DB password

// Correct error log path (secure_config/db_connect.php -> go up two levels)
$LOG_PATH = __DIR__ . '/../../logs/error.log';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    error_log($e->getMessage(), 3, $LOG_PATH);
    // Do not reveal sensitive info to the user
    die("System Error: Database connection failedddd.");
}
?>