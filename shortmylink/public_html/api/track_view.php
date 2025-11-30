<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once __DIR__ . '/../../secure_config/db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if(empty($data->link_id) || empty($data->user_id)) { exit; }

$linkId = $data->link_id;
$userId = $data->user_id;
$ip = $_SERVER['REMOTE_ADDR'];

// 1. CHECK FOR UNIQUE VIEW (24 Hour Rule)
// Prevents spamming refresh to earn money
$check = $pdo->prepare("SELECT id FROM clicks WHERE link_id = ? AND visitor_ip = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
$check->execute([$linkId, $ip]);

if($check->rowCount() > 0) {
    echo json_encode(['status' => 'ignored', 'message' => 'Not unique']);
    exit;
}

// 2. DETERMINE CPM RATE (Simplified)
// In a real app, use a GeoIP library to get country code.
// For now, we default to 'Global' rate ($3.00 CPM => $0.003 per view)
$revenue = 0.0030; 

// 3. RECORD CLICK
$ins = $pdo->prepare("INSERT INTO clicks (link_id, visitor_ip, revenue_generated) VALUES (?, ?, ?)");
$ins->execute([$linkId, $ip, $revenue]);

// 4. UPDATE LINK STATS
$updLink = $pdo->prepare("UPDATE links SET total_views = total_views + 1, total_revenue = total_revenue + ? WHERE id = ?");
$updLink->execute([$revenue, $linkId]);

// 5. ADD MONEY TO USER WALLET
$updUser = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
$updUser->execute([$revenue, $userId]);

echo json_encode(['status' => 'success', 'revenue' => $revenue]);
?>