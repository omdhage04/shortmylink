<?php
// File: public_html/redirect.php

// 1. Enable Error Reporting (Temporary, to debug 500 errors)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Correct Database Path
// We use __DIR__ . '/../' to go up one folder from public_html to root
$dbPath = __DIR__ . '/../secure_config/db_connect.php';

if (!file_exists($dbPath)) {
    die("<h1>System Error</h1><p>Database configuration file not found at: " . htmlspecialchars($dbPath) . "</p>");
}
require_once $dbPath;

$shortCode = isset($_GET['code']) ? trim($_GET['code']) : '';

if (empty($shortCode)) {
    header("Location: index.html");
    exit;
}

try {
    // 3. Get Link Data
    $stmt = $pdo->prepare("SELECT id, user_id, original_url, total_views FROM links WHERE short_code = ? AND is_active = 1");
    $stmt->execute([$shortCode]);
    $link = $stmt->fetch();

    if (!$link) {
        die("<div style='color:white; background:black; height:100vh; display:flex; align-items:center; justify-content:center; font-family:sans-serif;'><h1>404 - Link Not Found</h1></div>");
    }

    // 4. REVENUE CALCULATION (Fixed for Precision)
    $revenue = '0.0015'; 

    // 5. Update LINK Stats (+1 View, +$0.0015)
    $updateLink = $pdo->prepare("UPDATE links SET total_views = total_views + 1, total_revenue = total_revenue + :rev WHERE id = :id");
    $updateLink->execute([':rev' => $revenue, ':id' => $link['id']]);

    // 6. Update USER Wallet (+$0.0015)
    $updateUser = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + :rev WHERE id = :uid");
    $updateUser->execute([':rev' => $revenue, ':uid' => $link['user_id']]);

    // 7. Log the Click
    $ip = $_SERVER['REMOTE_ADDR'];
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $device = (stripos($ua, 'Mobile') !== false || stripos($ua, 'Android') !== false) ? 'mobile' : 'desktop';
    
    $logClick = $pdo->prepare("INSERT INTO clicks (link_id, visitor_ip, device_type, revenue_generated) VALUES (:lid, :ip, :dev, :rev)");
    $logClick->execute([':lid' => $link['id'], ':ip' => $ip, ':dev' => $device, ':rev' => $revenue]);

    // 8. Set Destination for JS
    $destination = $link['original_url'];

} catch (Exception $e) {
    die("<h1>Database Error</h1><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Please Wait... | ShortMyLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: { brand: { 500: '#10b981', 600: '#059669' } }
                }
            }
        }
    </script>
    <style>
        body { background-color: #050505; color: #e5e5e5; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .tech-grid {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background-image: linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px), 
                              linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .ad-space {
            background: rgba(255,255,255,0.05); border: 1px dashed rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            color: #555; font-size: 0.8rem; border-radius: 8px; margin: 20px 0;
        }

        .timer-circle {
            width: 120px; height: 120px; border-radius: 50%;
            background: conic-gradient(#10b981 var(--progress), rgba(255,255,255,0.1) 0deg);
            display: flex; align-items: center; justify-content: center;
            position: relative; margin: 0 auto;
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.2);
        }
        .timer-inner {
            width: 110px; height: 110px; background: #0f1115; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; z-index: 2;
        }
        .timer-count { font-size: 2rem; font-weight: 800; color: white; }
        .timer-label { font-size: 0.7rem; color: #10b981; text-transform: uppercase; letter-spacing: 1px; }

        .btn-glow {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
            animation: pulse-glow 2s infinite;
        }
        @keyframes pulse-glow { 0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); } 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); } }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <div class="tech-grid"></div>

    <header class="h-16 border-b border-white/10 flex items-center justify-center relative z-10 bg-black/50 backdrop-blur-md">
        <span class="font-bold text-xl tracking-tight text-white">shortmylink<span class="text-brand-500">.in</span></span>
    </header>

    <main class="flex-1 flex flex-col items-center justify-start pt-8 px-4 relative z-10">
        
        <!-- TOP AD -->
        <div class="ad-space w-full max-w-3xl h-24"><span>Responsive Ad Space (Top)</span></div>

        <!-- TIMER CARD -->
        <div class="bg-[#0f1115] border border-white/10 rounded-2xl p-8 w-full max-w-md text-center shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-brand-600 to-blue-600"></div>
            
            <h2 class="text-xl font-bold text-white mb-6">Generating Destination...</h2>

            <div class="timer-circle mb-6" id="timer-ring" style="--progress: 0%;">
                <div class="timer-inner">
                    <span class="timer-count" id="countdown">25</span>
                    <span class="timer-label">Seconds</span>
                </div>
            </div>

            <p class="text-sm text-neutral-400 mb-6">Please wait while we secure your link.</p>

            <!-- MIDDLE AD -->
            <div class="ad-space w-full h-64 mx-auto"><span>Square Ad Space (Middle)</span></div>

            <button id="get-link-btn" onclick="goToDestination()" class="hidden w-full py-4 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl transition-all btn-glow text-lg uppercase tracking-wide">
                <i class="fa-solid fa-link mr-2"></i> Get Link
            </button>
            
            <button id="wait-btn" class="w-full py-4 bg-neutral-800 text-neutral-500 font-bold rounded-xl cursor-not-allowed uppercase tracking-wide">
                Please Wait...
            </button>
        </div>

        <!-- BOTTOM AD -->
        <div class="ad-space w-full max-w-3xl h-24 mt-8"><span>Responsive Ad Space (Bottom)</span></div>

    </main>

    <footer class="py-6 text-center text-xs text-neutral-600 relative z-10">
        &copy; 2024 ShortMyLink.in
    </footer>

    <script>
        let timeLeft = 25; 
        const destination = "<?php echo htmlspecialchars_decode($destination); ?>";
        
        const countdownEl = document.getElementById('countdown');
        const ringEl = document.getElementById('timer-ring');
        const getLinkBtn = document.getElementById('get-link-btn');
        const waitBtn = document.getElementById('wait-btn');

        const totalTime = timeLeft;
        
        const timer = setInterval(() => {
            timeLeft--;
            countdownEl.innerText = timeLeft;
            const percentage = ((totalTime - timeLeft) / totalTime) * 100;
            ringEl.style.setProperty('--progress', percentage + '%');

            if (timeLeft <= 0) {
                clearInterval(timer);
                ringEl.style.display = 'none';
                waitBtn.classList.add('hidden');
                getLinkBtn.classList.remove('hidden');
                document.querySelector('h2').innerText = "Link Ready!";
            }
        }, 1000);

        function goToDestination() {
            window.location.href = destination;
        }
    </script>
</body>
</html>