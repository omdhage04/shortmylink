<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../secure_config/db_connect.php';

// 1. Check Request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $username = $_GET['user'] ?? '';

    if (!$username) {
        echo json_encode(['status' => 'error', 'message' => 'No user specified']);
        exit;
    }

    try {
        // 2. Fetch Data
        // Note: We select columns needed for the profile form
        // We do NOT select password_hash for security
        $stmt = $pdo->prepare("
            SELECT username, email, created_at 
            FROM users 
            WHERE username = ? 
            LIMIT 1
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            // 3. Format Response
            // Since we don't have separate first_name/last_name columns in the DB yet,
            // we will mock them by splitting the username or just returning empty.
            echo json_encode([
                'status' => 'success',
                'user' => [
                    'first_name' => ucfirst($user['username']), // Capitalize
                    'last_name' => '', // Placeholder
                    'email' => $user['email'],
                    'bio' => "Joined " . date("F Y", strtotime($user['created_at'])), // Auto-generate bio
                    'avatar' => "https://ui-avatars.com/api/?name=" . $user['username'] . "&background=10b981&color=fff"
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
        }

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database Error']);
    }
}
?>