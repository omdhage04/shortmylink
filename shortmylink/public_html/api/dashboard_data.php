<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); // Added for safety
header("Access-Control-Allow-Methods: POST");

// Hide system errors, log them internally
ini_set('display_errors', 0);

try {
    require_once __DIR__ . '/../../secure_config/db_connect.php';
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Config Error']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';

    if (!$username) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        exit;
    }

    try {
        // 1. Get User Info (UPDATED: Checks Username OR Email)
        $stmt = $pdo->prepare("SELECT id, wallet_balance, last_login_at FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }

        $userId = $user['id'];

        // 2. Get Totals (Using COALESCE to handle NULLs for new users)
        $stmt = $pdo->prepare("
            SELECT 
                COALESCE(SUM(total_views), 0) as total_views, 
                COALESCE(SUM(total_revenue), 0) as total_earnings 
            FROM links WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $stats = $stmt->fetch();

        $views = $stats['total_views'];
        $earnings = $user['wallet_balance']; // Better to show Wallet Balance as "Total Earnings" on dashboard
        
        // Avoid Division by Zero for CPM
        $avgCpm = ($views > 0) ? ($earnings / $views) * 1000 : 0;

        // 3. Get Chart Data (Last 30 Days)
        $stmt = $pdo->prepare("
            SELECT 
                DATE(clicks.created_at) as date, 
                SUM(clicks.revenue_generated) as daily_earnings
            FROM clicks 
            JOIN links ON clicks.link_id = links.id
            WHERE links.user_id = ? 
            AND clicks.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(clicks.created_at)
            ORDER BY date ASC
        ");
        $stmt->execute([$userId]);
        $chartData = $stmt->fetchAll();

        // Format Chart
        $labels = [];
        $dataPoints = [];
        
        if (empty($chartData)) {
            // Fallback: Show last 7 days with 0 data so chart isn't empty
            for ($i = 6; $i >= 0; $i--) {
                $labels[] = date('M d', strtotime("-$i days"));
                $dataPoints[] = 0;
            }
        } else {
            foreach ($chartData as $row) {
                $labels[] = date('M d', strtotime($row['date']));
                $dataPoints[] = (float)$row['daily_earnings'];
            }
        }

        echo json_encode([
            'status' => 'success',
            'overview' => [
                'views' => number_format($views),
                'earnings' => number_format($earnings, 4),
                'cpm' => number_format($avgCpm, 2),
                'balance' => number_format($user['wallet_balance'], 2),
                'last_login' => $user['last_login_at'] ? date("F j, g:i a", strtotime($user['last_login_at'])) : 'First Login'
            ],
            'chart' => [
                'labels' => $labels,
                'data' => $dataPoints
            ]
        ]);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'System Error']); // Don't show raw SQL error
    }
}
?>