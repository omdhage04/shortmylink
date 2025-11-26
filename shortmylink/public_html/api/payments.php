<?php
// -------------------------------------------------------------------
// PAYMENTS API (Wallet, Settings, Withdrawals)
// -------------------------------------------------------------------

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
ini_set('display_errors', 0); // Hide errors from output
error_reporting(E_ALL);       // Log errors internally

// CONFIGURATION
$MIN_WITHDRAWAL = 5.00;

try {
    require_once __DIR__ . '/../../secure_config/db_connect.php';
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'System Config Error']);
    exit;
}

// 1. INPUT & AUTH
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$username = $input['username'] ?? '';

if (!$username) {
    echo json_encode(['status' => 'error', 'message' => 'Authentication missing']);
    exit;
}

try {
    // Get User ID & Current Balance
    // We select FOR UPDATE in withdrawal transactions, but simple select here is fine
    $uStmt = $pdo->prepare("SELECT id, wallet_balance, default_payment_method, default_payment_account FROM users WHERE username = ? OR email = ?");
    $uStmt->execute([$username, $username]);
    $user = $uStmt->fetch();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit;
    }
    $userId = $user['id'];

    // --- SWITCH ACTIONS ---
    switch ($action) {

        // ===============================================================
        // ACTION 1: FETCH DASHBOARD DATA
        // ===============================================================
        case 'fetch':
            // 1. Get History (Last 20)
            $histStmt = $pdo->prepare("
                SELECT id, amount, method, status, DATE_FORMAT(requested_at, '%Y-%m-%d') as date 
                FROM payouts 
                WHERE user_id = ? 
                ORDER BY requested_at DESC 
                LIMIT 20
            ");
            $histStmt->execute([$userId]);
            $history = $histStmt->fetchAll();

            // 2. Format Method for Frontend
            $methodData = null;
            if ($user['default_payment_method']) {
                $methodData = [
                    'type' => $user['default_payment_method'],
                    'detail' => $user['default_payment_account']
                ];
            }

            echo json_encode([
                'status' => 'success',
                'balance' => $user['wallet_balance'],
                'method' => $methodData,
                'history' => $history
            ]);
            break;

        // ===============================================================
        // ACTION 2: SAVE PAYMENT SETTINGS
        // ===============================================================
        case 'save_method':
            $method = strtoupper($input['method'] ?? ''); // UPI, PAYPAL, BANK
            $detail = trim($input['detail'] ?? '');

            // Validation
            $allowedMethods = ['UPI', 'PAYPAL', 'BANK', 'IMPS'];
            if (!in_array($method, $allowedMethods)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid payment method']);
                exit;
            }
            if (empty($detail)) {
                echo json_encode(['status' => 'error', 'message' => 'Payment details cannot be empty']);
                exit;
            }

            // Map 'BANK' to 'IMPS' if your DB Enum requires it
            if($method === 'BANK') $method = 'IMPS';

            // Update DB
            $upd = $pdo->prepare("UPDATE users SET default_payment_method = ?, default_payment_account = ? WHERE id = ?");
            if ($upd->execute([$method, $detail, $userId])) {
                echo json_encode(['status' => 'success', 'message' => 'Payment settings saved']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
            }
            break;

        // ===============================================================
        // ACTION 3: REQUEST WITHDRAWAL (CRITICAL)
        // ===============================================================
        case 'withdraw':
            $amount = floatval($input['amount'] ?? 0);

            // 1. Basic Validations
            if ($amount < $MIN_WITHDRAWAL) {
                echo json_encode(['status' => 'error', 'message' => "Minimum withdrawal is $$MIN_WITHDRAWAL"]);
                exit;
            }
            if (empty($user['default_payment_method']) || empty($user['default_payment_account'])) {
                echo json_encode(['status' => 'error', 'message' => 'Please set a payment method first']);
                exit;
            }

            // 2. START TRANSACTION (To prevent race conditions)
            $pdo->beginTransaction();

            try {
                // 3. Re-fetch Balance with Lock (FOR UPDATE)
                // This ensures no one else can modify the balance while we check it
                $checkStmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ? FOR UPDATE");
                $checkStmt->execute([$userId]);
                $currentBal = $checkStmt->fetchColumn();

                if ($currentBal < $amount) {
                    $pdo->rollBack();
                    echo json_encode(['status' => 'error', 'message' => 'Insufficient funds']);
                    exit;
                }

                // 4. Deduct Balance
                $deduct = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
                $deduct->execute([$amount, $userId]);

                // 5. Create Payout Record
                $ins = $pdo->prepare("
                    INSERT INTO payouts (user_id, amount, method, account_details, status) 
                    VALUES (?, ?, ?, ?, 'pending')
                ");
                $ins->execute([
                    $userId, 
                    $amount, 
                    $user['default_payment_method'], 
                    $user['default_payment_account']
                ]);

                // 6. Commit Transaction
                $pdo->commit();

                echo json_encode(['status' => 'success', 'message' => 'Withdrawal requested successfully']);

            } catch (Exception $ex) {
                $pdo->rollBack();
                error_log("Withdrawal Error: " . $ex->getMessage());
                echo json_encode(['status' => 'error', 'message' => 'Transaction failed. Please try again.']);
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