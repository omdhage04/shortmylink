<?php
// -------------------------------------------------------------------
// ADVANCED ANALYTICS API (For analytics.html)
// Aggregates Charts, Devices, Locations, and Referrers
// -------------------------------------------------------------------

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    require_once __DIR__ . '/../../secure_config/db_connect.php';
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'System Config Error']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';

// Default Date Range: Last 7 Days
$days = isset($input['days']) ? intval($input['days']) : 7; 

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

    // --- DATA AGGREGATION QUERIES ---

    // A. MAIN CHART (Daily Views)
    // We fetch daily counts for the requested range
    $chartQuery = "
        SELECT DATE(c.created_at) as date, COUNT(c.id) as views
        FROM clicks c
        JOIN links l ON c.link_id = l.id
        WHERE l.user_id = ? 
        AND c.created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        GROUP BY DATE(c.created_at)
        ORDER BY date ASC
    ";
    $stmt = $pdo->prepare($chartQuery);
    $stmt->execute([$userId, $days]);
    $dailyData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // Returns ['2023-11-01' => 12, ...]

    // Fill missing dates with 0
    $chartLabels = [];
    $chartValues = [];
    $totalViews = 0;

    for ($i = $days - 1; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        $val = isset($dailyData[$d]) ? intval($dailyData[$d]) : 0;
        
        $chartLabels[] = date('M d', strtotime($d)); // Nov 17
        $chartValues[] = $val;
        $totalViews += $val;
    }

    // B. DEVICE BREAKDOWN
    $deviceQuery = "
        SELECT device_type, COUNT(c.id) as count
        FROM clicks c
        JOIN links l ON c.link_id = l.id
        WHERE l.user_id = ? AND c.created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        GROUP BY device_type
    ";
    $stmt = $pdo->prepare($deviceQuery);
    $stmt->execute([$userId, $days]);
    $devicesRaw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Map to standard keys (Desktop, Mobile, Tablet, Other)
    $devices = [
        'Desktop' => intval($devicesRaw['desktop'] ?? 0),
        'Mobile'  => intval($devicesRaw['mobile'] ?? 0),
        'Tablet'  => intval($devicesRaw['tablet'] ?? 0)
    ];

    // C. TOP LOCATIONS (Countries)
    $geoQuery = "
        SELECT country_code, COUNT(c.id) as count
        FROM clicks c
        JOIN links l ON c.link_id = l.id
        WHERE l.user_id = ? AND c.created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        GROUP BY country_code
        ORDER BY count DESC
        LIMIT 5
    ";
    $stmt = $pdo->prepare($geoQuery);
    $stmt->execute([$userId, $days]);
    $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add full names (Simple map, usually you'd have a DB table for this)
    $countryNames = [
        'US' => 'United States', 'IN' => 'India', 'GB' => 'United Kingdom',
        'CA' => 'Canada', 'DE' => 'Germany', 'FR' => 'France', 'XX' => 'Unknown'
    ];
    
    foreach($countries as &$c) {
        $code = strtoupper($c['country_code']);
        $c['name'] = $countryNames[$code] ?? $code;
        $c['percent'] = ($totalViews > 0) ? round(($c['count'] / $totalViews) * 100, 1) : 0;
    }

    // D. TOP REFERRERS
    $refQuery = "
        SELECT referer_url, COUNT(c.id) as count
        FROM clicks c
        JOIN links l ON c.link_id = l.id
        WHERE l.user_id = ? AND c.created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        GROUP BY referer_url
        ORDER BY count DESC
        LIMIT 5
    ";
    $stmt = $pdo->prepare($refQuery);
    $stmt->execute([$userId, $days]);
    $referrers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Clean up Referrer Names (e.g., 'm.facebook.com' -> 'Facebook')
    foreach($referrers as &$ref) {
        $url = $ref['referer_url'];
        $name = 'Direct / Email';
        $icon = 'fa-envelope';
        $color = 'text-neutral-600';
        $bg = 'bg-neutral-100 dark:bg-white/10';

        if(empty($url)) {
            // Keep default
        } elseif(strpos($url, 'facebook') !== false) {
            $name = 'Facebook'; $icon = 'fa-facebook-f'; $color = 'text-blue-600'; $bg = 'bg-blue-50 dark:bg-blue-900/20';
        } elseif(strpos($url, 'instagram') !== false) {
            $name = 'Instagram'; $icon = 'fa-instagram'; $color = 'text-pink-600'; $bg = 'bg-pink-50 dark:bg-pink-900/20';
        } elseif(strpos($url, 'youtube') !== false) {
            $name = 'YouTube'; $icon = 'fa-youtube'; $color = 'text-red-600'; $bg = 'bg-red-50 dark:bg-red-900/20';
        } elseif(strpos($url, 'twitter') || strpos($url, 't.co') !== false) {
            $name = 'Twitter (X)'; $icon = 'fa-x-twitter'; $color = 'text-black dark:text-white'; $bg = 'bg-gray-100 dark:bg-white/10';
        } elseif(strpos($url, 'google') !== false) {
            $name = 'Google Search'; $icon = 'fa-google'; $color = 'text-blue-500'; $bg = 'bg-blue-50 dark:bg-blue-900/20';
        } else {
            $name = parse_url($url, PHP_URL_HOST) ?: 'Web';
            $icon = 'fa-globe';
        }

        $ref['name'] = $name;
        $ref['icon'] = $icon;
        $ref['style'] = ['color' => $color, 'bg' => $bg];
    }

    // --- RESPONSE ---
    echo json_encode([
        'status' => 'success',
        'total_views' => $totalViews,
        'chart' => [
            'labels' => $chartLabels,
            'data' => $chartValues
        ],
        'devices' => $devices,
        'locations' => $countries,
        'referrers' => $referrers
    ]);

} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Data fetch error']);
}
?>