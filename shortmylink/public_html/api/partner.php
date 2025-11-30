<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

require_once __DIR__ . '/../../secure_config/db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';

if (!$username) {
    echo json_encode(['status' => 'error', 'message' => 'Auth failed']);
    exit;
}

// In a real app, you would check the DB to see if they are ALREADY subscribed
// For now, we return the plan configs
echo json_encode([
    'status' => 'success',
    'plans' => [
        'starter' => ['id' => 1, 'price' => 499, 'revenue_share' => 90],
        'pro' => ['id' => 2, 'price' => 899, 'revenue_share' => 95],
        'elite' => ['id' => 3, 'price' => 1399, 'revenue_share' => 100]
    ]
]);
?>