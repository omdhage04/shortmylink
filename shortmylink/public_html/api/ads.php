<?php
// -------------------------------------------------------------------
// ADS SETTINGS API
// -------------------------------------------------------------------

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
ini_set('display_errors', 0);

try {
    require_once __DIR__ . '/../../secure_config/db_connect.php';
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'System Config Error']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$username = $input['username'] ?? '';

if (!$username) {
    echo json_encode(['status' => 'error', 'message' => 'Auth missing']);
    exit;
}

try {
    // 1. Get User ID
    $uStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $uStmt->execute([$username, $username]);
    $user = $uStmt->fetch();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit;
    }
    $userId = $user['id'];

    // 2. Switch Actions
    switch ($action) {
        
        case 'fetch':
            // Check if user has settings row, if not, create it
            $sStmt = $pdo->prepare("SELECT allow_adult, allowed_categories FROM user_ad_settings WHERE user_id = ?");
            $sStmt->execute([$userId]);
            $settings = $sStmt->fetch();

            if (!$settings) {
                // Create default
                $ins = $pdo->prepare("INSERT INTO user_ad_settings (user_id, allow_adult) VALUES (?, 0)");
                $ins->execute([$userId]);
                $settings = ['allow_adult' => 0, 'allowed_categories' => null];
            }

            echo json_encode([
                'status' => 'success',
                'allow_adult' => $settings['allow_adult'],
                'categories' => json_decode($settings['allowed_categories'])
            ]);
            break;

        case 'update_adult':
            $allow = intval($input['allow_adult']);
            
            // Upsert Logic (Update if exists, Insert if not)
            $sql = "INSERT INTO user_ad_settings (user_id, allow_adult) VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE allow_adult = VALUES(allow_adult)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$userId, $allow])) {
                echo json_encode(['status' => 'success', 'message' => 'Updated']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'DB Error']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid Action']);
            break;
    }

} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server Error']);
}
?>