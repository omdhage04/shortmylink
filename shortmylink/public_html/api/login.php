<?php
// 1. Setup Headers
header('Content-Type: application/json');
ini_set('display_errors', 0); // Hide system errors from output
error_reporting(E_ALL); // Log errors internally

// 2. Connect to Database
try {
    // Adjust path if necessary: Go up 2 levels to find secure_config
    require_once __DIR__ . '/../../secure_config/db_connect.php';
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Backend Config Error']);
    exit;
}

// 3. Process Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON
    $input = json_decode(file_get_contents('php://input'), true);
    $user = trim($input['username'] ?? '');
    $pass = $input['password'] ?? '';

    if (!$user || !$pass) {
        echo json_encode(['status' => 'error', 'message' => 'Missing Username or Password']);
        exit;
    }

    try {
        // 4. Look for the user
        // We check both 'username' AND 'email' columns
        $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$user, $user]);
        $account = $stmt->fetch();

        // 5. Verify Logic
        if (!$account) {
            // User does not exist in DB
            echo json_encode(['status' => 'error', 'message' => 'User not found. Please Sign Up first.']);
        } else {
            // User found, check password
            if (password_verify($pass, $account['password_hash'])) {
                // SUCCESS!
                echo json_encode([
                    'status' => 'success', 
                    'username' => $account['username'],
                    'role' => $account['role']
                ]);
            } else {
                // Wrong Password
                echo json_encode(['status' => 'error', 'message' => 'Incorrect Password']);
            }
        }

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database Query Failed']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request Method']);
}
?>