<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Program | ShortMyLink.in</title>
    
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    
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
        /* LENIS SMOOTH SCROLL */
        html.lenis, html.lenis body { height: auto; }
        .lenis.lenis-smooth { scroll-behavior: auto !important; }
        .lenis.lenis-smooth [data-lenis-prevent] { overscroll-behavior: contain; }
        .lenis.lenis-stopped { overflow: hidden; }

        /* GLOBAL */
        body { font-family: 'Plus Jakarta Sans', sans-serif; transition: background-color 0.3s ease, color 0.3s ease; }
        body { background-color: #f8fafc; color: #1e293b; }
        .dark body { background-color: #020408; color: #e5e5e5; }

        /* BACKGROUND */
        .tech-grid {
            position: absolute; inset: 0; pointer-events: none;
            background-size: 40px 40px;
            background-image: linear-gradient(to right, rgba(0,0,0,0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(0,0,0,0.05) 1px, transparent 1px);
        }
        .dark .tech-grid {
            background-image: linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px);
        }

        /* CARDS (High Contrast Fix) */
        .std-card {
            background: white;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05);
        }
        .dark .std-card {
            background: #0f1115; 
            border: 1px solid rgba(255,255,255,0.08); 
            box-shadow: 0 20px 40px -20px rgba(0,0,0,0.5);
        }

        /* BEST SELLER GLOW */
        .best-seller {
            border: 2px solid #10b981;
            position: relative;
            z-index: 10;
            box-shadow: 0 20px 50px -12px rgba(16, 185, 129, 0.25);
        }
        .dark .best-seller {
            background: #0f1115;
            box-shadow: 0 20px 50px -12px rgba(16, 185, 129, 0.15);
        }

        /* ELITE GLOW */
        .elite-card {
            background: linear-gradient(145deg, #ffffff, #f3e8ff);
            border: 1px solid rgba(147, 51, 234, 0.2);
            box-shadow: 0 20px 50px -12px rgba(147, 51, 234, 0.15);
        }
        .dark .elite-card {
            background: linear-gradient(145deg, #0f1115, #1a1025);
            border: 1px solid rgba(147, 51, 234, 0.3);
            box-shadow: 0 20px 50px -12px rgba(147, 51, 234, 0.2);
        }

        .check-list li { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; font-size: 0.875rem; }
        .check-list i { color: #10b981; } 
        .elite-card .check-list i { color: #a855f7; } 

        /* SCROLL CONTAINER */
        #smooth-content { will-change: transform; transform-origin: center top; }
    </style>
</head>
<body class="h-screen overflow-hidden flex text-neutral-800 dark:text-neutral-200">

    <!-- MOBILE OVERLAY -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden backdrop-blur-sm transition-opacity opacity-0"></div>

    <!-- 1. SIDEBAR INCLUDE -->
    <?php $page = 'partner'; include 'components/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative bg-neutral-50 dark:bg-[#020408]">
        <div class="absolute inset-0 tech-grid opacity-100 pointer-events-none"></div>

        <!-- 2. HEADER INCLUDE -->
        <?php $pageTitle = 'Partner Program'; include 'components/header.php'; ?>

        <!-- SCROLLABLE AREA -->
        <div id="smooth-content" class="flex-1 overflow-y-auto relative z-10 p-4 md:p-8 lg:p-12 pb-24">
            
            <!-- HERO -->
            <div class="text-center max-w-3xl mx-auto mb-16 pt-8 gsap-fade-up">
                <span class="px-3 py-1 rounded-full bg-brand-100 dark:bg-brand-900/30 text-brand-700 dark:text-brand-400 text-[10px] font-bold uppercase tracking-widest mb-4 inline-block border border-brand-200 dark:border-brand-500/20">
                    <span class="w-2 h-2 rounded-full bg-brand-500 inline-block mr-1 animate-pulse"></span> Creator Economy
                </span>
                <h2 class="text-4xl md:text-6xl font-extrabold text-neutral-900 dark:text-white mb-6 tracking-tight leading-tight">
                    Keep <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-brand-400">100%</span> of your earnings.
                </h2>
                <p class="text-neutral-500 dark:text-neutral-400 text-lg leading-relaxed">
                    Upgrade your publisher status to remove platform fees, unlock VIP CPM rates, and get paid instantly.
                </p>
            </div>

            <!-- PRICING GRID -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto items-center">
                
                <!-- 1. STARTER -->
                <div class="std-card rounded-2xl p-8 relative transform transition-all duration-300 hover:-translate-y-2 hover:shadow-xl gsap-fade-up" style="animation-delay: 0.1s">
                    <div class="text-center mb-6">
                        <h3 class="font-bold text-neutral-900 dark:text-white text-xl">Starter</h3>
                        <div class="my-4">
                            <span class="text-4xl font-bold font-mono text-neutral-900 dark:text-white">₹499</span>
                            <span class="text-neutral-500 dark:text-neutral-400 text-xs">/ 30 days</span>
                        </div>
                        <span class="px-3 py-1 bg-neutral-100 dark:bg-white/10 text-neutral-600 dark:text-neutral-300 rounded text-xs font-bold">90% Revenue Share</span>
                    </div>
                    <div class="h-px w-full bg-neutral-100 dark:bg-white/5 my-6"></div>
                    <ul class="check-list text-neutral-600 dark:text-neutral-400 mb-8">
                        <li><i class="fa-solid fa-check"></i> Standard CPM Rates</li>
                        <li><i class="fa-solid fa-check"></i> Daily Payouts</li>
                        <li><i class="fa-solid fa-check"></i> 24/7 Ticket Support</li>
                        <li><i class="fa-solid fa-check"></i> Basic Analytics</li>
                    </ul>
                    <a href="buysubscription.html?plan=starter" class="block w-full py-3.5 rounded-xl bg-neutral-100 dark:bg-white/5 border border-neutral-200 dark:border-white/10 text-neutral-900 dark:text-white font-bold text-center hover:bg-neutral-200 dark:hover:bg-white/10 transition text-sm">Choose Starter</a>
                </div>

                <!-- 2. PRO (Best Seller) -->
                <div class="std-card best-seller rounded-2xl p-8 relative transform scale-105 z-10 transition-all duration-300 hover:-translate-y-2 gsap-fade-up" style="animation-delay: 0.2s">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-brand-600 text-white px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest shadow-lg">Most Popular</div>
                    <div class="text-center mb-6">
                        <h3 class="font-bold text-neutral-900 dark:text-white text-2xl">Influencer Pro</h3>
                        <div class="my-4">
                            <span class="text-5xl font-bold font-mono text-brand-600 dark:text-brand-400">₹899</span>
                            <span class="text-neutral-500 dark:text-neutral-400 text-xs">/ 30 days</span>
                        </div>
                        <span class="px-3 py-1 bg-brand-50 dark:bg-brand-900/20 text-brand-700 dark:text-brand-400 rounded text-xs font-bold border border-brand-100 dark:border-brand-500/20">95% Revenue Share</span>
                    </div>
                    <ul class="check-list text-neutral-600 dark:text-neutral-300 mb-8 space-y-3">
                        <li><i class="fa-solid fa-check-double"></i> <b>High</b> CPM Rates</li>
                        <li><i class="fa-solid fa-check-double"></i> Priority Processing</li>
                        <li><i class="fa-solid fa-check-double"></i> Dedicated Manager</li>
                        <li><i class="fa-solid fa-check-double"></i> No Dashboard Ads</li>
                    </ul>
                    <a href="buysubscription.html?plan=pro" class="block w-full py-4 rounded-xl bg-brand-600 text-white font-bold text-center hover:bg-brand-700 shadow-lg shadow-brand-500/30 transition text-sm">Get Started</a>
                </div>

                <!-- 3. ELITE -->
                <div class="elite-card rounded-2xl p-8 relative transform transition-all duration-300 hover:-translate-y-2 gsap-fade-up" style="animation-delay: 0.3s">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/10 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none"></div>
                    <div class="text-center mb-6 relative z-10">
                        <h3 class="font-bold text-neutral-900 dark:text-white text-xl flex items-center justify-center gap-2"><i class="fa-solid fa-crown text-yellow-500"></i> Elite</h3>
                        <div class="my-4">
                            <span class="text-4xl font-bold font-mono text-neutral-900 dark:text-white">₹1399</span>
                            <span class="text-neutral-500 dark:text-neutral-400 text-xs">/ 30 days</span>
                        </div>
                        <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded text-xs font-bold border border-purple-100 dark:border-purple-500/20">100% Revenue Share</span>
                    </div>
                    <div class="h-px w-full bg-purple-100 dark:bg-purple-500/10 my-6 relative z-10"></div>
                    <ul class="check-list text-neutral-600 dark:text-neutral-300 mb-8 relative z-10">
                        <li><i class="fa-solid fa-star"></i> <b>Highest</b> Possible CPM</li>
                        <li><i class="fa-solid fa-bolt"></i> Instant Payouts</li>
                        <li><i class="fa-brands fa-whatsapp"></i> VIP WhatsApp Support</li>
                        <li><i class="fa-solid fa-globe"></i> Custom Short Domain</li>
                    </ul>
                    <a href="buysubscription.html?plan=elite" class="block w-full py-3.5 rounded-xl bg-purple-600 text-white font-bold text-center hover:bg-purple-700 shadow-lg shadow-purple-500/20 transition text-sm relative z-10">Join Elite</a>
                </div>

            </div>

            <!-- GUIDELINES -->
            <div class="max-w-4xl mx-auto mt-20 border-t border-neutral-200 dark:border-white/10 pt-10">
                <h3 class="text-center font-bold text-neutral-900 dark:text-white mb-8">Program Details</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="std-card p-6 rounded-xl">
                        <h4 class="font-bold text-neutral-900 dark:text-white mb-2 flex items-center gap-2"><i class="fa-solid fa-circle-info text-brand-500"></i> Zero Platform Fees</h4>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">Standard users share ~30% of ad revenue with us. Partner plans waive this fee entirely.</p>
                    </div>
                    <div class="std-card p-6 rounded-xl">
                        <h4 class="font-bold text-neutral-900 dark:text-white mb-2 flex items-center gap-2"><i class="fa-solid fa-credit-card text-brand-500"></i> Gateway Fees</h4>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">Note: Third-party payment gateways (PayPal/Bank) may still charge their standard 1-2% processing fee.</p>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- JS Includes -->
    <script src="js/ui.js"></script>
    <script src="https://unpkg.com/@studio-freight/lenis@1.0.29/dist/lenis.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // 1. Lenis Smooth Scroll
            const contentDiv = document.getElementById('smooth-content');
            const lenis = new Lenis({
                wrapper: contentDiv,
                content: contentDiv,
                duration: 1.2,
                easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                direction: 'vertical', 
                smooth: true,
                mouseMultiplier: 1
            });

            function raf(time) {
                lenis.raf(time * 1000);
                requestAnimationFrame(raf);
            }
            requestAnimationFrame(raf);

            // 2. GSAP Animations
            gsap.from(".gsap-fade-up", {
                y: 40, opacity: 0, duration: 0.8, stagger: 0.15, ease: "power3.out", delay: 0.1
            });
            
            // 3. Shared Logic
            const username = localStorage.getItem('current_username');
            if(!username) window.location.href = 'index.html';
            
            // Mobile Menu & Sidebar Logic handled by components/header.php, 
            // but we need to init the shared toggles if not already present in ui.js
            // (Assuming ui.js or inline script from dashboard.php handles sidebar toggling)
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            const closeBtn = document.getElementById('close-sidebar');

            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
                setTimeout(() => overlay.classList.toggle('opacity-0'), 10);
            }

            if(mobileBtn) mobileBtn.onclick = toggleSidebar;
            if(overlay) overlay.onclick = toggleSidebar;
            if(closeBtn) closeBtn.onclick = toggleSidebar;
            
            // Theme Toggle
            document.getElementById('theme-toggle').onclick = () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            };
            
            // Profile Info
            document.getElementById('profile-username').innerText = username;
            document.getElementById('profile-role').innerText = localStorage.getItem('user_role') === 'ROOT_ADMIN' ? 'Administrator' : 'Publisher';
        });
    </script>
</body>
</html>