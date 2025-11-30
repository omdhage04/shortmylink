<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Command Center | ShortMyLink.in</title>
    
    <!-- 1. IMMEDIATE THEME CHECK -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] },
                    colors: { brand: { 50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 400: '#34d399', 500: '#10b981', 600: '#059669', 900: '#064e3b' } }
                }
            }
        }
    </script>
    <style>
        /* GLOBAL STYLES */
        body { font-family: 'Plus Jakarta Sans', sans-serif; transition: background-color 0.3s ease, color 0.3s ease; }
        body { background-color: #f8fafc; color: #1e293b; }
        .dark body { background-color: #020408; color: #e5e5e5; }

        /* TECH GRID */
        .tech-grid {
            position: absolute; inset: 0; pointer-events: none;
            background-size: 40px 40px;
            background-image: linear-gradient(to right, rgba(0,0,0,0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(0,0,0,0.05) 1px, transparent 1px);
        }
        .dark .tech-grid {
            background-image: linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px);
        }

        /* STAT CARDS */
        .stat-card {
            background: white; border: 1px solid rgba(0,0,0,0.05); border-radius: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .dark .stat-card {
            background: #0f1115; border: 1px solid rgba(255,255,255,0.05);
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1);
            border-color: rgba(16, 185, 129, 0.3);
        }

        /* SKELETON LOADER */
        .skeleton {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            background-color: rgba(0,0,0,0.1); color: transparent !important; border-radius: 4px;
        }
        .dark .skeleton { background-color: rgba(255,255,255,0.1); }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }

        /* CUSTOM SCROLLBAR */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .dark ::-webkit-scrollbar-thumb { background: #334155; }
    </style>
</head>
<body class="h-screen overflow-hidden flex text-neutral-800 dark:text-neutral-200">

    <!-- MOBILE OVERLAY -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden backdrop-blur-sm transition-opacity opacity-0"></div>

    <!-- 1. INCLUDE SIDEBAR (This handles navigation & Logout) -->
    <?php $page = 'dashboard'; include 'components/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative bg-neutral-50 dark:bg-[#020408]">
        <!-- Background -->
        <div class="absolute inset-0 tech-grid opacity-100 pointer-events-none"></div>

        <!-- 2. INCLUDE HEADER (This handles Theme Toggle & Mobile Menu) -->
        <?php $pageTitle = 'Command Center'; include 'components/header.php'; ?>

        <!-- CONTENT AREA -->
        <div id="dashboard-content" class="flex-1 overflow-y-auto relative z-10 p-4 md:p-6 lg:p-10 pb-20">
            
            <!-- Greeting & Date Range -->
            <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4 gsap-fade-up">
                <div>
                    <p class="text-xs font-mono text-brand-600 dark:text-brand-400 mb-1" id="last-login-text">LAST LOGIN: LOADING...</p>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-neutral-900 dark:text-white tracking-tight">Overview</h2>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-white/5 border border-neutral-200 dark:border-white/10 rounded-lg shadow-sm hover:bg-neutral-50 dark:hover:bg-white/10 transition cursor-pointer">
                    <i class="fa-regular fa-calendar text-neutral-400 text-xs"></i>
                    <span class="text-xs font-bold dark:text-white font-mono">Last 30 Days</span>
                    <i class="fa-solid fa-chevron-down text-xs text-neutral-400 ml-1"></i>
                </div>
            </div>

            <!-- STATS GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-10">
                
                <!-- 1. VIEWS -->
                <div class="stat-card p-6 gsap-fade-up">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600"><i class="fa-solid fa-eye"></i></div>
                        <span class="text-[10px] font-bold text-green-600 bg-green-100 dark:bg-green-900/30 px-2 py-1 rounded-full flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Live</span>
                    </div>
                    <div id="val-views" class="text-3xl font-bold text-neutral-900 dark:text-white font-mono mb-1 skeleton">0</div>
                    <div class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Total Views</div>
                </div>

                <!-- 2. EARNINGS -->
                <div class="stat-card p-6 gsap-fade-up" style="animation-delay: 0.1s">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-lg bg-brand-50 dark:bg-brand-900/20 flex items-center justify-center text-brand-600"><i class="fa-solid fa-sack-dollar"></i></div>
                    </div>
                    <div id="val-earnings" class="text-3xl font-bold text-neutral-900 dark:text-white font-mono mb-1 skeleton">$0.00</div>
                    <div class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Total Earnings</div>
                </div>

                <!-- 3. CPM -->
                <div class="stat-card p-6 gsap-fade-up" style="animation-delay: 0.2s">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600"><i class="fa-solid fa-chart-line"></i></div>
                    </div>
                    <div id="val-cpm" class="text-3xl font-bold text-neutral-900 dark:text-white font-mono mb-1 skeleton">$0.00</div>
                    <div class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Average CPM</div>
                </div>

                <!-- 4. BALANCE (Actionable) -->
                <div class="stat-card p-6 relative overflow-hidden group cursor-pointer border-brand-500/20 gsap-fade-up" style="animation-delay: 0.3s" onclick="window.location.href='payments.php'">
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-600 to-brand-800 opacity-90 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative z-10 text-white">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-white backdrop-blur-sm"><i class="fa-solid fa-wallet"></i></div>
                            <span class="text-[10px] font-bold bg-black/30 hover:bg-black/50 px-3 py-1 rounded-full transition-colors backdrop-blur-sm">WITHDRAW</span>
                        </div>
                        <div id="val-balance" class="text-3xl font-bold font-mono mb-1 skeleton">$0.00</div>
                        <div class="text-xs text-brand-100 font-medium">Available Balance</div>
                    </div>
                </div>
            </div>

            <!-- CHART SECTION -->
            <div class="stat-card p-6 md:p-8 mb-10 gsap-fade-up" style="animation-delay: 0.4s">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-neutral-900 dark:text-white">Revenue Analytics</h3>
                        <p class="text-xs text-neutral-500 mt-1">Income performance over the last 30 days.</p>
                    </div>
                    <div class="flex bg-neutral-100 dark:bg-white/5 p-1 rounded-lg border border-neutral-200 dark:border-white/5">
                        <button class="px-3 py-1 text-xs font-bold rounded-md bg-brand-500 text-white shadow-sm">Revenue</button>
                        <button class="px-3 py-1 text-xs font-bold rounded-md text-neutral-500 hover:text-neutral-900 dark:hover:text-white transition">CPM</button>
                    </div>
                </div>
                <div class="w-full h-[300px] md:h-[350px] relative"><canvas id="revenueChart"></canvas></div>
            </div>

            <!-- MICRO APP FOOTER (Clean & Minimal) -->
            <footer class="border-t border-neutral-200 dark:border-white/5 pt-6 mt-10 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-neutral-500 dark:text-neutral-500">
                <p>&copy; 2024 ShortMyLink.in <span class="mx-2">•</span> System v2.4</p>
                <div class="flex gap-4">
                    <a href="support.php" class="hover:text-brand-500 transition">Help Center</a>
                    <a href="LegalHub.html" class="hover:text-brand-500 transition">Privacy</a>
                    <a href="LegalHub.html" class="hover:text-brand-500 transition">Terms</a>
                </div>
            </footer>

        </div>
    </main>

    <!-- JS Includes (Order Matters) -->
    <script src="js/ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            
            // --- 1. MOBILE MENU & THEME LOGIC ---
            // (Now handled by components/header.php + shared JS below)
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const closeBtn = document.getElementById('close-sidebar');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');

            function toggleSidebar() {
                const isClosed = sidebar.classList.contains('-translate-x-full');
                if (isClosed) {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                    setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                } else {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('opacity-0');
                    setTimeout(() => overlay.classList.add('hidden'), 300);
                }
            }

            if(mobileBtn) mobileBtn.addEventListener('click', toggleSidebar);
            if(closeBtn) closeBtn.addEventListener('click', toggleSidebar);
            if(overlay) overlay.addEventListener('click', toggleSidebar);

            const themeToggleBtn = document.getElementById('theme-toggle');
            if(themeToggleBtn) themeToggleBtn.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                location.reload(); // Reload charts to fix colors
            });

            // --- 2. USER PROFILE & AUTH CHECK ---
            let username = localStorage.getItem('current_username');
            let userRole = localStorage.getItem('user_role');
            
            if (!username) {
                window.location.href = 'index.html'; // Force login
            } else {
                // These IDs are in components/sidebar.php
                const pName = document.getElementById('profile-username');
                const pRole = document.getElementById('profile-role');
                const pAvatar = document.getElementById('profile-avatar');

                if(pName) pName.innerText = username;
                if(pRole) pRole.innerText = (userRole === 'ROOT_ADMIN') ? "Administrator" : "Publisher";
                if(pAvatar && userRole === 'ROOT_ADMIN') {
                    pAvatar.classList.add('bg-red-500');
                    pAvatar.innerHTML = '<i class="fa-solid fa-shield-halved"></i>';
                }
            }

            // --- 3. FETCH DATA ---
            fetch('api/dashboard_data.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username: username })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    const ov = data.overview;
                    updateStat('val-views', ov.views);
                    updateStat('val-earnings', '$' + ov.earnings);
                    updateStat('val-cpm', '$' + ov.cpm);
                    updateStat('val-balance', '$' + ov.balance);
                    document.getElementById('last-login-text').innerText = "LAST LOGIN: " + ov.last_login.toUpperCase();
                    initChart(data.chart.labels, data.chart.data);
                } else {
                    initChart(['No Data'], [0]);
                }
            })
            .catch(err => {
                console.log("API Error or Offline");
                initChart(['No Data'], [0]);
            });

            function updateStat(id, value) {
                const el = document.getElementById(id);
                if(el) {
                    el.innerText = value;
                    el.classList.remove('skeleton');
                }
            }

            // --- 4. CHARTS ---
            function initChart(labels, dataPoints) {
                const ctx = document.getElementById('revenueChart').getContext('2d');
                const isDark = document.documentElement.classList.contains('dark');
                const brandColor = '#10b981';
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
                gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Earnings ($)', 
                            data: dataPoints, 
                            borderColor: brandColor, 
                            backgroundColor: gradient,
                            borderWidth: 3, 
                            tension: 0.4, 
                            fill: true, 
                            pointRadius: 0, 
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: isDark ? '#666' : '#aaa', font: {size: 10} } },
                            y: { grid: { color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' }, ticks: { color: isDark ? '#666' : '#aaa', font: {size: 10} } }
                        }
                    }
                });
            }

            // --- 5. ANIMATION ---
            if (typeof gsap !== 'undefined') {
                gsap.from(".gsap-fade-up", { y: 30, opacity: 0, duration: 0.8, stagger: 0.1, ease: "power3.out", delay: 0.2 });
            }
        });
    </script>
</body>
</html>