<?php
// File: public_html/api/index.php
session_start();

// Adjust path to reach secure_config from the 'api' folder
require_once __DIR__ . '/../../secure_config/db_connect.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$long_url = trim($_POST['url'] ?? '');

// 1. Validation
if (empty($long_url) || !filter_var($long_url, FILTER_VALIDATE_URL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid URL (e.g., https://google.com)']);
    exit;
}

try {
    // 2. Determine User ID (Logged In vs Guest)
    $userId = null;

    if (isset($_SESSION['user_id'])) {
        // CASE A: User is Logged In -> Use their ID
        $userId = $_SESSION['user_id'];
    } else {
        // CASE B: User is NOT Logged In -> Use "System Guest" ID
        // Check if the guest user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'guest_system' LIMIT 1");
        $stmt->execute();
        $guest = $stmt->fetch();

        if ($guest) {
            $userId = $guest['id'];
        } else {
            // Auto-create the guest account if it doesn't exist
            // This ensures revenue from anonymous links goes to this account
            $dummyPass = password_hash('GuestSystem123!', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, status) VALUES ('guest_system', 'guest@shortmylink.in', ?, 'user', 'active')");
            $stmt->execute([$dummyPass]);
            $userId = $pdo->lastInsertId();
        }
    }

    // 3. Generate Short Code (Random 6 characters)
    // Using a loop to ensure uniqueness (rare collision check)
    $maxTries = 5;
    $shortCode = '';
    
    do {
        $shortCode = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
        $check = $pdo->prepare("SELECT id FROM links WHERE short_code = ?");
        $check->execute([$shortCode]);
        $exists = $check->rowCount() > 0;
        $maxTries--;
    } while ($exists && $maxTries > 0);

    if ($exists) {
        throw new Exception("Server busy, please try again.");
    }

    // 4. Insert Link into Database
    $stmt = $pdo->prepare("INSERT INTO links (user_id, original_url, short_code, title, is_active) VALUES (?, ?, ?, 'Public Link', 1)");
    $stmt->execute([$userId, $long_url, $shortCode]);

    // 5. Construct Result URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $domain = $_SERVER['HTTP_HOST'];
    // Note: If your site is in a subfolder, adjust accordingly. 
    // Usually standard hosting is root, so: domain.com/shortcode
    $fullShortLink = "$protocol://$domain/$shortCode";

    echo json_encode([
        'status' => 'success', 
        'short_url' => $fullShortLink,
        'user_type' => (isset($_SESSION['user_id']) ? 'registered' : 'guest')
    ]);
    exit;

} catch (Exception $e) {
    // Log error internally if needed: error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'System error. Please try again.']);
    exit;
}
?>