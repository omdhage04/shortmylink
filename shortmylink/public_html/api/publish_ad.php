<?php
// -------------------------------------------------------------------
// PUBLISH AD API
// Handles Form Data + File Uploads
// -------------------------------------------------------------------

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
// Note: We do NOT set Content-Type: application/json here because 
// the response is JSON, but the Request is Multipart/Form-Data.

ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    require_once __DIR__ . '/../../secure_config/db_connect.php';
} catch (Exception $e) {
    jsonResponse('error', 'System Config Error');
}

// Helper to send JSON
function jsonResponse($status, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $data));
    exit;
}

// 1. AUTHENTICATION
// Since we are using FormData, we retrieve username from POST, not JSON body
$username = $_POST['username'] ?? '';

if (empty($username)) jsonResponse('error', 'Authentication missing');

try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if (!$user) jsonResponse('error', 'User not found');
    $userId = $user['id'];

    // 2. VALIDATION
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $budget = floatval($_POST['budget'] ?? 0);
    $adType = $_POST['type'] ?? 'BANNER';
    $countries = $_POST['countries'] ?? 'ALL';
    $isAdult = isset($_POST['is_adult']) && $_POST['is_adult'] === 'true' ? 1 : 0;

    if (empty($name) || empty($email) || empty($url)) jsonResponse('error', 'All fields are required');
    if ($budget < 5) jsonResponse('error', 'Minimum budget is $5.00');
    if (!filter_var($url, FILTER_VALIDATE_URL)) jsonResponse('error', 'Invalid Destination URL');

    // 3. FILE UPLOAD HANDLING
    $mediaPath = null;
    
    if (isset($_FILES['creative']) && $_FILES['creative']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['creative']['tmp_name'];
        $fileName = $_FILES['creative']['name'];
        $fileSize = $_FILES['creative']['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Config
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm'];
        $maxSize = 30 * 1024 * 1024; // 30MB
        $uploadDir = __DIR__ . '/../uploads/ads/'; // Folder: public_html/uploads/ads/

        // Checks
        if (!in_array($fileExt, $allowedExts)) jsonResponse('error', 'Invalid file type. Use JPG, PNG or MP4.');
        if ($fileSize > $maxSize) jsonResponse('error', 'File too large (Max 30MB).');

        // Create Dir if not exists
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        // Generate Unique Name
        $newFileName = 'ad_' . $userId . '_' . uniqid() . '.' . $fileExt;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmp, $destPath)) {
            $mediaPath = 'uploads/ads/' . $newFileName; // Path stored in DB
        } else {
            jsonResponse('error', 'Failed to save file.');
        }
    } else {
        jsonResponse('error', 'Ad creative file is required.');
    }

    // 4. DATABASE INSERTION
    $sql = "INSERT INTO ad_campaigns 
            (user_id, advertiser_name, contact_email, ad_type, destination_url, budget, targeting_countries, is_adult, media_path, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending_review')";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $userId, $name, $email, $adType, $url, $budget, $countries, $isAdult, $mediaPath
    ]);

    if ($result) {
        // Optional: Send email to Admin here
        jsonResponse('success', 'Campaign submitted for review!', ['campaign_id' => $pdo->lastInsertId()]);
    } else {
        jsonResponse('error', 'Database error');
    }

} catch (Exception $e) {
    error_log($e->getMessage());
    jsonResponse('error', 'Server Error');
}
?>