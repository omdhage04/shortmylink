<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ad Manager | ShortMyLink.in</title>
    
    <!-- THEME CHECK -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
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
        /* LENIS SCROLL */
        html.lenis, html.lenis body { height: auto; }
        .lenis.lenis-smooth { scroll-behavior: auto !important; }
        .lenis.lenis-smooth [data-lenis-prevent] { overscroll-behavior: contain; }
        .lenis.lenis-stopped { overflow: hidden; }

        /* GLOBAL */
        body { font-family: 'Plus Jakarta Sans', sans-serif; transition: background-color 0.3s ease, color 0.3s ease; }
        
        /* Light Mode: Crisp Slate */
        body { background-color: #f8fafc; color: #334155; }
        /* Dark Mode: Deep Black */
        .dark body { background-color: #020408; color: #e5e5e5; }

        /* GRID BACKGROUND */
        .tech-grid {
            position: absolute; inset: 0; pointer-events: none;
            background-size: 40px 40px;
            background-image: linear-gradient(to right, rgba(0,0,0,0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(0,0,0,0.05) 1px, transparent 1px);
        }
        .dark .tech-grid {
            background-image: linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px);
        }

        /* SETTINGS CARDS (High Contrast for Light Mode) */
        .settings-card {
            background: #ffffff;
            border: 1px solid #e2e8f0; /* Stronger border for light mode */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }
        .dark .settings-card {
            background: #0f1115;
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: none;
        }
        .settings-card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); }

        /* ADULT WARNING CARD */
        .adult-card {
            background: #fff1f2; /* Light Red tint */
            border: 1px solid #fecaca;
        }
        .dark .adult-card {
            background: rgba(239, 68, 68, 0.05);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* TOGGLE SWITCH */
        .toggle-checkbox:checked { right: 0; border-color: #ef4444; }
        .toggle-checkbox:checked + .toggle-label { background-color: #ef4444; }
        
        /* STAT BARS */
        .stat-bar-bg { background: #e2e8f0; }
        .dark .stat-bar-bg { background: rgba(255,255,255,0.1); }

        #smooth-content { will-change: transform; transform-origin: center top; }
    </style>
</head>
<body class="h-screen overflow-hidden flex text-slate-800 dark:text-neutral-200">

    <!-- MOBILE OVERLAY -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden backdrop-blur-sm transition-opacity opacity-0"></div>

    <!-- 1. SIDEBAR -->
    <?php $page = 'advertise'; include 'components/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative bg-slate-50 dark:bg-[#020408]">
        <div class="absolute inset-0 tech-grid opacity-100 pointer-events-none"></div>

        <!-- 2. HEADER -->
        <?php $pageTitle = 'Ad Configuration'; include 'components/header.php'; ?>

        <div id="smooth-content" class="flex-1 overflow-y-auto relative z-10 p-4 md:p-8 lg:p-12 pb-24">
            
            <!-- 1. ADULT CONTENT TOGGLE -->
            <div class="adult-card rounded-2xl p-6 mb-8 relative overflow-hidden gsap-fade-up shadow-sm">
                <div class="absolute top-0 right-0 p-4 opacity-10 text-red-500 pointer-events-none"><i class="fa-solid fa-triangle-exclamation text-8xl md:text-9xl"></i></div>
                
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative z-10">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center text-red-600 dark:text-red-400 text-lg shadow-sm">
                                <i class="fa-solid fa-ban"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white leading-tight">Adult Content Ads</h3>
                                <span class="text-[10px] font-bold text-red-600 dark:text-red-400 uppercase tracking-wider">High Yield (18+)</span>
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 dark:text-neutral-400 max-w-xl leading-relaxed">
                            Enable ads suitable for mature audiences. Activating this typically increases your <span class="text-green-600 dark:text-green-400 font-bold bg-green-50 dark:bg-green-900/20 px-1 rounded">Revenue by ~30%</span> due to higher advertiser demand.
                        </p>
                        <div class="flex items-center gap-2 mt-3">
                            <i class="fa-solid fa-shield-halved text-xs text-slate-400"></i>
                            <p class="text-xs text-slate-500 dark:text-neutral-500">Strictly regulated "Safe for Work" mature ads only.</p>
                        </div>
                    </div>

                    <!-- The Toggle -->
                    <div class="flex items-center">
                        <div class="relative inline-block w-14 h-8 align-middle select-none transition duration-200 ease-in">
                            <input type="checkbox" name="toggle" id="adult-toggle" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 border-slate-300 appearance-none cursor-pointer transition-all duration-300 top-1 left-1 shadow-sm"/>
                            <label for="adult-toggle" class="toggle-label block overflow-hidden h-8 rounded-full bg-slate-300 dark:bg-neutral-700 cursor-pointer transition-colors duration-300 shadow-inner"></label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. AD PERFORMANCE GRID -->
            <div class="flex items-center justify-between mb-4 px-1">
                <h3 class="text-sm font-bold text-slate-500 dark:text-neutral-400 uppercase tracking-wider">Performance by Ad Type</h3>
                <span class="text-xs text-slate-400 bg-white dark:bg-white/5 px-2 py-1 rounded border border-slate-200 dark:border-white/10">Live CPM Rates</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                
                <!-- Interstitial -->
                <div class="settings-card rounded-2xl p-6 gsap-fade-up" style="animation-delay: 0.1s">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 text-xl"><i class="fa-solid fa-pager"></i></div>
                        <span class="text-xs font-mono font-bold text-slate-500 dark:text-neutral-400 bg-slate-100 dark:bg-white/5 px-2 py-1 rounded">CPM: $4.50</span>
                    </div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-lg">Interstitials</h4>
                    <p class="text-xs text-slate-500 mb-4 mt-1">Full screen ads between page loads.</p>
                    
                    <div class="mt-auto">
                        <div class="flex justify-between text-xs mb-1.5 font-medium">
                            <span class="text-slate-500">Revenue Share</span>
                            <span class="text-slate-900 dark:text-white">45%</span>
                        </div>
                        <div class="h-2 w-full stat-bar-bg rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 w-[45%] rounded-full shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                        </div>
                    </div>
                </div>

                <!-- Banners -->
                <div class="settings-card rounded-2xl p-6 gsap-fade-up" style="animation-delay: 0.2s">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600 text-xl"><i class="fa-solid fa-rectangle-ad"></i></div>
                        <span class="text-xs font-mono font-bold text-slate-500 dark:text-neutral-400 bg-slate-100 dark:bg-white/5 px-2 py-1 rounded">CPM: $1.20</span>
                    </div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-lg">Banner Ads</h4>
                    <p class="text-xs text-slate-500 mb-4 mt-1">Standard top/bottom display ads.</p>
                    
                    <div class="mt-auto">
                        <div class="flex justify-between text-xs mb-1.5 font-medium">
                            <span class="text-slate-500">Revenue Share</span>
                            <span class="text-slate-900 dark:text-white">20%</span>
                        </div>
                        <div class="h-2 w-full stat-bar-bg rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 w-[20%] rounded-full shadow-[0_0_10px_rgba(34,197,94,0.5)]"></div>
                        </div>
                    </div>
                </div>

                <!-- Popunders -->
                <div class="settings-card rounded-2xl p-6 gsap-fade-up" style="animation-delay: 0.3s">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 text-xl"><i class="fa-solid fa-window-restore"></i></div>
                        <span class="text-xs font-mono font-bold text-slate-500 dark:text-neutral-400 bg-slate-100 dark:bg-white/5 px-2 py-1 rounded">CPM: $6.80</span>
                    </div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-lg">Popunders</h4>
                    <p class="text-xs text-slate-500 mb-4 mt-1">New tab ads (Highest paying).</p>
                    
                    <div class="mt-auto">
                        <div class="flex justify-between text-xs mb-1.5 font-medium">
                            <span class="text-slate-500">Revenue Share</span>
                            <span class="text-slate-900 dark:text-white">35%</span>
                        </div>
                        <div class="h-2 w-full stat-bar-bg rounded-full overflow-hidden">
                            <div class="h-full bg-purple-500 w-[35%] rounded-full shadow-[0_0_10px_rgba(168,85,247,0.5)]"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. PREFERENCES & COMPLIANCE -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Category Preferences -->
                <div class="settings-card rounded-2xl p-6 gsap-fade-up" style="animation-delay: 0.4s">
                    <div class="flex justify-between items-center mb-6 border-b border-slate-100 dark:border-white/5 pb-4">
                        <h3 class="font-bold text-slate-900 dark:text-white text-lg">Allowed Categories</h3>
                        <button onclick="savePreferences()" class="text-xs font-bold bg-brand-50 text-brand-600 px-3 py-1.5 rounded-lg hover:bg-brand-100 transition-colors">Save Changes</button>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="flex items-center justify-between p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-transparent hover:border-brand-500/30 hover:bg-white dark:hover:bg-white/10 transition cursor-pointer group">
                            <span class="text-sm font-bold text-slate-700 dark:text-neutral-200 flex items-center gap-3">
                                <i class="fa-solid fa-gamepad text-slate-400 group-hover:text-brand-500 transition-colors"></i> Gaming & Software
                            </span>
                            <input type="checkbox" class="accent-brand-500 w-5 h-5 rounded border-slate-300" checked>
                        </label>

                        <label class="flex items-center justify-between p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-transparent hover:border-brand-500/30 hover:bg-white dark:hover:bg-white/10 transition cursor-pointer group">
                            <span class="text-sm font-bold text-slate-700 dark:text-neutral-200 flex items-center gap-3">
                                <i class="fa-solid fa-coins text-slate-400 group-hover:text-brand-500 transition-colors"></i> Crypto & Finance
                            </span>
                            <input type="checkbox" class="accent-brand-500 w-5 h-5 rounded border-slate-300" checked>
                        </label>

                        <label class="flex items-center justify-between p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-transparent hover:border-brand-500/30 hover:bg-white dark:hover:bg-white/10 transition cursor-pointer group">
                            <span class="text-sm font-bold text-slate-700 dark:text-neutral-200 flex items-center gap-3">
                                <i class="fa-solid fa-shirt text-slate-400 group-hover:text-brand-500 transition-colors"></i> Fashion & Lifestyle
                            </span>
                            <input type="checkbox" class="accent-brand-500 w-5 h-5 rounded border-slate-300" checked>
                        </label>
                    </div>
                </div>

                <!-- Compliance Info -->
                <div class="settings-card rounded-2xl p-6 gsap-fade-up" style="animation-delay: 0.5s">
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg mb-6 border-b border-slate-100 dark:border-white/5 pb-4">Compliance & Rules</h3>
                    <ul class="space-y-6">
                        <li class="flex gap-4 text-sm text-slate-600 dark:text-neutral-400">
                            <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 shrink-0"><i class="fa-solid fa-check"></i></div>
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block mb-1">No Misleading Links</span>
                                Links must not deceive users about the destination. "Clickbait" that leads to malware is strictly banned.
                            </div>
                        </li>
                        <li class="flex gap-4 text-sm text-slate-600 dark:text-neutral-400">
                            <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 shrink-0"><i class="fa-solid fa-shield-halved"></i></div>
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block mb-1">Virus/Malware Free</span>
                                Destination pages are scanned. Ensure content is safe for mobile and desktop users.
                            </div>
                        </li>
                        <li class="flex gap-4 text-sm text-slate-600 dark:text-neutral-400">
                            <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 shrink-0"><i class="fa-solid fa-triangle-exclamation"></i></div>
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block mb-1">Adult Labeling</span>
                                If "Adult Ads" is enabled, you must ensure your traffic audience is 18+.
                            </div>
                        </li>
                    </ul>
                </div>

            </div>

        </div>
    </main>

    <!-- JS Includes -->
    <script src="js/ui.js"></script>
    <script src="https://unpkg.com/@studio-freight/lenis@1.0.29/dist/lenis.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <script>
        const API_URL = 'api/ads.php';
        const username = localStorage.getItem('current_username');

        document.addEventListener("DOMContentLoaded", () => {
            if(!username) window.location.href = 'index.html';
            
            // --- LENIS SMOOTH SCROLL ---
            const contentDiv = document.getElementById('smooth-content');
            const lenis = new Lenis({
                wrapper: contentDiv,
                content: contentDiv,
                duration: 1.2,
                easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                direction: 'vertical', smooth: true, mouseMultiplier: 1
            });
            function raf(time) { lenis.raf(time * 1000); requestAnimationFrame(raf); }
            requestAnimationFrame(raf);

            // --- SHARED UI ---
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            const closeBtn = document.getElementById('close-sidebar');

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
            if(overlay) overlay.addEventListener('click', toggleSidebar);
            if(closeBtn) closeBtn.addEventListener('click', toggleSidebar);

            document.getElementById('theme-toggle').onclick = () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            };

            document.getElementById('profile-username').innerText = username;
            document.getElementById('profile-role').innerText = localStorage.getItem('user_role') === 'ROOT_ADMIN' ? 'Administrator' : 'Publisher';

            // --- FETCH SETTINGS ---
            fetchAdSettings();

            // --- ADULT TOGGLE LISTENER ---
            const toggle = document.getElementById('adult-toggle');
            toggle.addEventListener('change', (e) => {
                if(e.target.checked) {
                    // Warn user before enabling
                    e.target.checked = false; // Revert visually first
                    confirmAction({
                        title: 'Enable Adult Ads?',
                        text: 'You confirm that your content is suitable for 18+ audiences. Violation of policy may lead to bans.',
                        isDanger: true,
                        btnText: 'Enable & I Understand'
                    }, () => {
                        e.target.checked = true; // Check it on confirm
                        updateSettings(true);
                        closeModal();
                    });
                } else {
                    updateSettings(false);
                }
            });

            // Animations
            gsap.from(".gsap-fade-up", { y: 30, opacity: 0, duration: 0.8, stagger: 0.1, ease: "power3.out" });
        });

        async function fetchAdSettings() {
            try {
                const res = await fetch(API_URL, {
                    method: 'POST',
                    body: JSON.stringify({ action: 'fetch', username })
                });
                const data = await res.json();
                if(data.status === 'success') {
                    document.getElementById('adult-toggle').checked = data.allow_adult == 1;
                }
            } catch(e) { console.error(e); }
        }

        async function updateSettings(isAdult) {
            try {
                const res = await fetch(API_URL, {
                    method: 'POST',
                    body: JSON.stringify({ 
                        action: 'update_adult', 
                        username: username, 
                        allow_adult: isAdult ? 1 : 0 
                    })
                });
                const data = await res.json();
                if(data.status === 'success') showToast('success', 'Ad settings updated');
                else showToast('error', 'Failed to update');
            } catch(e) { showToast('error', 'Network Error'); }
        }

        function savePreferences() {
            // In a real app, gather checkbox values here
            showToast('success', 'Category preferences saved');
        }
    </script>
</body>
</html>