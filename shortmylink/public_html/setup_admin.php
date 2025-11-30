<?php
// 1. FORCE ERROR REPORTING (So we see why it crashes)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>ShortMyLink Admin Setup</h1>";

// 2. DATABASE CONFIGURATION (Enter your Hostinger details here)
$host = "localhost";
$db_name = "u545014911_UserInfo";  // Your DB Name
$username = "u545014911_user_info"; // Your DB User
$password = "#Dhage@3005"; // <--- PASTE YOUR DB PASSWORD HERE

// 3. ADMIN CREDENTIALS TO CREATE
$adminUser = "OMDHAGE";
$adminPass = "#Dhage@3005";
$adminEmail = "admin@shortmylink.in";

try {
    // Connect to Database directly
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>✔ Database Connected Successfully.</p>";

    // Create Password Hash
    $hashed_password = password_hash($adminPass, PASSWORD_BCRYPT);

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$adminUser]);

    if ($stmt->rowCount() > 0) {
        // UPDATE EXISTING
        $sql = "UPDATE users SET password_hash = ?, role = 'admin', wallet_balance = 999999.00 WHERE username = ?";
        $update = $pdo->prepare($sql);
        $update->execute([$hashed_password, $adminUser]);
        echo "<h2 style='color:blue'>SUCCESS: User '$adminUser' updated to Admin!</h2>";
    } else {
        // CREATE NEW
        $sql = "INSERT INTO users (username, email, password_hash, role, wallet_balance) VALUES (?, ?, ?, 'admin', 999999.00)";
        $insert = $pdo->prepare($sql);
        $insert->execute([$adminUser, $adminEmail, $hashed_password]);
        echo "<h2 style='color:green'>SUCCESS: New Admin '$adminUser' created!</h2>";
    }

    echo "<p>You can now delete this file and login.</p>";

} catch (PDOException $e) {
    // IF IT FAILS, SHOW THIS ERROR
    echo "<h2 style='color:red'>CRITICAL ERROR:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<p>Please check your database password in this file.</p>";
}
?>