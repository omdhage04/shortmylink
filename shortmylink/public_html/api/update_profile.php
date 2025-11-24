<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../secure_config/db_connect.php';

// 1. Check Method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Get Input
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true);

    if (!is_array($input)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
        exit;
    }

    $username = isset($input['username']) ? trim($input['username']) : '';
    $bio = isset($input['bio']) ? trim($input['bio']) : '';

    // Security Check
    if (!$username) {
        echo json_encode(['status' => 'error', 'message' => 'Auth Error']);
        exit;
    }

    // Basic validation / limits
    if (strlen($bio) > 2000) {
        echo json_encode(['status' => 'error', 'message' => 'Bio too long']);
        exit;
    }

    // Sanitize values before using in DB (if/when enabled)
    $username = filter_var($username, FILTER_SANITIZE_STRING);
    $bio = htmlspecialchars($bio, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    try {
        // 3. Perform Update
        // Only updating BIO for now since Email is locked
        // If you added a 'bio' column, uncomment the real SQL line below.

        // REAL SQL (Run this after altering table):
        // $stmt = $pdo->prepare("UPDATE users SET bio = ? WHERE username = ?");
        // $stmt->execute([$bio, $username]);

        // SIMULATED SUCCESS (For Portfolio Demo)
        echo json_encode(['status' => 'success', 'message' => 'Profile Updated']);

    } catch (Exception $e) {
        // Log exception to server-side log for debugging
        error_log($e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Update Failed']);
    }
}
?>