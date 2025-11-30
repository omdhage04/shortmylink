<?php
// api/links.php

// 1. Headers & Config
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
ini_set('display_errors', 0); // Hide PHP warnings so they don't break JSON

try {
    require_once __DIR__ . '/../../secure_config/db_connect.php';
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Config Error']);
    exit;
}

// 2. Helper: Generate Random Short Code
function generateShortCode($length = 6) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

// 3. Process Request
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$username = $input['username'] ?? '';

if (!$username) {
    echo json_encode(['status' => 'error', 'message' => 'Authentication missing']);
    exit;
}

try {
    // Resolve User ID
    $uStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $uStmt->execute([$username, $username]);
    $user = $uStmt->fetch();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit;
    }
    $userId = $user['id'];

    // --- SWITCH ACTIONS ---

    switch ($action) {
        
        // A. FETCH ALL LINKS (With Money Formatting)
        case 'fetch':
            $q = "SELECT id, title, original_url, short_code, total_views, total_revenue, created_at 
                  FROM links 
                  WHERE user_id = ? 
                  ORDER BY created_at DESC";
            $stmt = $pdo->prepare($q);
            $stmt->execute([$userId]);
            $links = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as associative array
            
            // Format the Money for the Dashboard
            foreach ($links as &$link) {
                // Example: Turns 0.001500 into $0.0015
                $link['total_revenue'] = '$' . number_format((float)$link['total_revenue'], 4); 
            }
            
            echo json_encode(['status' => 'success', 'data' => $links]);
            break;

        // B. CREATE SHORT LINK (Dashboard)
        case 'create':
            $url = trim($input['url'] ?? '');
            $title = trim($input['title'] ?? 'Untitled Link');

            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid URL format']);
                exit;
            }

            // Generate Unique Code (Loop to ensure uniqueness)
            $shortCode = '';
            $isUnique = false;
            $attempts = 0;

            while (!$isUnique && $attempts < 5) {
                $shortCode = generateShortCode(6);
                $check = $pdo->prepare("SELECT id FROM links WHERE short_code = ?");
                $check->execute([$shortCode]);
                if ($check->rowCount() === 0) {
                    $isUnique = true;
                }
                $attempts++;
            }

            if (!$isUnique) {
                echo json_encode(['status' => 'error', 'message' => 'Server busy, try again']);
                exit;
            }

            // Insert
            $ins = $pdo->prepare("INSERT INTO links (user_id, original_url, short_code, title, is_active) VALUES (?, ?, ?, ?, 1)");
            if ($ins->execute([$userId, $url, $shortCode, $title])) {
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Link Created',
                    'short_code' => $shortCode
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database Insert Failed']);
            }
            break;

        // C. EDIT LINK (Destination Only)
        case 'edit':
            $linkId = $input['link_id'];
            $newUrl = trim($input['new_url']);

            if (!filter_var($newUrl, FILTER_VALIDATE_URL)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid URL']);
                exit;
            }

            // Secure Update: Check user_id to prevent hacking
            $upd = $pdo->prepare("UPDATE links SET original_url = ? WHERE id = ? AND user_id = ?");
            if ($upd->execute([$newUrl, $linkId, $userId])) {
                echo json_encode(['status' => 'success', 'message' => 'Link Updated']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Update Failed']);
            }
            break;

        // D. DELETE LINK
        case 'delete':
            $linkId = $input['link_id'];
            
            // Secure Delete
            $del = $pdo->prepare("DELETE FROM links WHERE id = ? AND user_id = ?");
            if ($del->execute([$linkId, $userId])) {
                echo json_encode(['status' => 'success', 'message' => 'Link Deleted']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Delete Failed']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid Action']);
            break;
    }

} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'System Error']);
}
?>