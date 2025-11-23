<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../../secure_config/db_connect.php';
require_once __DIR__ . '/../../core_logic/link_algo.php';

// simple placeholder
echo json_encode(['short_url'=>'https://shortmylink.in/abcd123']);
?>