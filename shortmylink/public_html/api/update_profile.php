<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../secure_config/db_connect.php';

// 1. Check Method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 2. Get Input
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? ''; // We need to pass username from frontend to identify WHO to update
    $bio = $input['bio'] ?? '';

    // Security Check
    if (!$username) {
        echo json_encode(['status' => 'error', 'message' => 'Auth Error']);
        exit;
    }

    try {
        // 3. Perform Update
        // Only updating BIO for now since Email is locked
        // If you added a 'bio' column, uncomment the real SQL line below.
        
        // REAL SQL (Run this after altering table):
        // $stmt = $pdo->prepare("UPDATE users SET bio = ? WHERE username = ?");
        // $stmt->execute([$bio, $username]);

        // SIMULATED SUCCESS (For Portfolio Demo)
        // We pretend to update so the button turns green.
        
        echo json_encode(['status' => 'success', 'message' => 'Profile Updated']);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Update Failed']);
    }
}
?>