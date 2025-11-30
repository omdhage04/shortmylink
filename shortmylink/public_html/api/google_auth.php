<?php
session_start();

// 1. Load Secure Configs
require_once __DIR__ . '/../../secure_config/db_connect.php';
require_once __DIR__ . '/../../secure_config/google_creds.php';

// ------------------------------------------------------------------
// PART A: Redirect User to Google (If no code is present)
// ------------------------------------------------------------------
if (!isset($_GET['code'])) {
    $params = [
        'response_type' => 'code',
        'client_id'     => GOOGLE_CLIENT_ID,
        'redirect_uri'  => GOOGLE_REDIRECT_URL,
        'scope'         => 'email profile',
        'access_type'   => 'online'
    ];
    header('Location: https://accounts.google.com/o/oauth2/auth?' . http_build_query($params));
    exit();
}

// ------------------------------------------------------------------
// PART B: Handle Google's Response (If code IS present)
// ------------------------------------------------------------------
if (isset($_GET['code'])) {
    
    // 1. Exchange Code for Access Token
    $token_params = [
        'code'          => $_GET['code'],
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => GOOGLE_REDIRECT_URL,
        'grant_type'    => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $token_data = json_decode($response, true);

    if (!isset($token_data['access_token'])) {
        die("Error: Could not verify with Google. Please try again.");
    }

    // 2. Get User Profile Info
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $token_data['access_token']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $google_user = json_decode(curl_exec($ch), true);
    curl_close($ch);

    $email = $google_user['email'];
    $google_id = $google_user['id'];
    // Generate a username from email (e.g., "omdhage" from "omdhage@gmail.com")
    $base_username = explode('@', $email)[0];

    try {
        // 3. Check if User Exists in Database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // --- EXISTING USER (LOGIN) ---
            
            // If they signed up with password before, link their Google ID now
            if (empty($user['google_id'])) {
                $upd = $pdo->prepare("UPDATE users SET google_id = ? WHERE id = ?");
                $upd->execute([$google_id, $user['id']]);
            }
            
            $final_username = $user['username'];
            $role = $user['role'];

        } else {
            // --- NEW USER (SIGNUP) ---
            
            // Ensure username is unique (add random number if needed)
            $final_username = $base_username;
            $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $check->execute([$final_username]);
            if ($check->rowCount() > 0) {
                $final_username = $base_username . rand(100, 999);
            }

            // Generate API Token
            $api_token = bin2hex(random_bytes(32));

            // Insert into Users Table
            $ins = $pdo->prepare("INSERT INTO users (username, email, google_id, api_token, role, created_at) VALUES (?, ?, ?, ?, 'user', NOW())");
            $ins->execute([$final_username, $email, $google_id, $api_token]);
            $new_user_id = $pdo->lastInsertId();

            // Create User Ad Settings (Important based on your schema)
            $pdo->prepare("INSERT INTO user_ad_settings (user_id) VALUES (?)")->execute([$new_user_id]);

            $role = 'user';
        }

        // 4. Set Secure Auth Cookie (PHP Side)
        $expire = time() + (24 * 60 * 60); // 1 Day
        setcookie("shortmylink_auth", "true", $expire, "/");

        // 5. JavaScript Redirect to set LocalStorage
        // This is a small HTML page that runs JS to save the name, then moves to dashboard
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Redirecting...</title>
            <style>
                body{background:#000;color:#10b981;font-family:monospace;display:flex;align-items:center;justify-content:center;height:100vh;}
            </style>
        </head>
        <body>
            <div id="status">Syncing Dashboard...</div>
            <script>
                // Save details for the Sidebar/UI
                localStorage.setItem('current_username', '<?php echo htmlspecialchars($final_username); ?>');
                localStorage.setItem('user_role', '<?php echo strtoupper($role); ?>');
                
                // Redirect to Dashboard
                setTimeout(() => {
                    window.location.href = '../dashboard.php';
                }, 1000);
            </script>
        </body>
        </html>
        <?php
        exit();

    } catch (PDOException $e) {
        error_log($e->getMessage(), 3, __DIR__.'/../../logs/error.log');
        die("System Error: Database connection failed during Google Auth.");
    }
}
?>