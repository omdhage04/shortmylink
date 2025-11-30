<?php
session_start();
$page = 'support'; 
$username = isset($_SESSION['username']) ? $_SESSION['username'] : (isset($_COOKIE['current_username']) ? $_COOKIE['current_username'] : 'Guest');
$role = isset($_SESSION['role']) ? $_SESSION['role'] : (isset($_COOKIE['user_role']) ? $_COOKIE['user_role'] : 'Publisher');

// Sidebar Helper Function
function navItem($target, $icon, $label, $currentPage) {
    $isActive = ($target === $currentPage);
    // REMOVED border logic, kept simple background highlighting
    $activeClass = $isActive 
        ? 'active bg-brand-50 dark:bg-brand-900/20 text-brand-700 dark:text-brand-400 font-bold' 
        : 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-white/5 hover:text-neutral-900 dark:hover:text-white font-medium';
    
    $href = strpos($target, '.php') !== false ? $target : $target . '.php';
    echo '<a href="'.$href.'" class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all duration-200 '.$activeClass.'"><i class="fa-solid '.$icon.' w-5 text-center"></i><span>'.$label.'</span></a>';
}
?>

<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support | ShortMyLink.in</title>
    
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else { document.documentElement.classList.remove('dark'); }
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; transition: background-color 0.3s ease; }
        .dark body { background-color: #020408; color: #e5e5e5; }
        .tech-grid { position: absolute; inset: 0; pointer-events: none; background-image: linear-gradient(to right, rgba(0,0,0,0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(0,0,0,0.05) 1px, transparent 1px); background-size: 40px 40px; }
        .dark .tech-grid { background-image: linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px); }
        .sidebar-glass { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px); }
        .dark .sidebar-glass { background: rgba(10, 10, 10, 0.85); }
        
        /* REMOVED THE ::before GREEN LINE CSS */
        .nav-item:hover, .nav-item.active { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        
        .search-glow:focus-within { box-shadow: 0 0 20px -5px rgba(16, 185, 129, 0.3); border-color: #10b981; }
        .details-box[open] summary ~ * { animation: sweep .3s ease-in-out; }
        @keyframes sweep { 0% {opacity: 0; margin-top: -10px} 100% {opacity: 1; margin-top: 0px} }
        ::-webkit-scrollbar { width: 4px; } ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; } .dark ::-webkit-scrollbar-thumb { background: #334155; }
    </style>
</head>
<body class="h-screen overflow-hidden flex text-neutral-800 dark:text-neutral-200">

    <!-- MOBILE OVERLAY -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden backdrop-blur-sm transition-opacity opacity-0"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="sidebar-glass fixed md:relative inset-y-0 left-0 z-30 w-64 h-full flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-300 shadow-2xl md:shadow-none">
        <div class="h-20 flex items-center justify-between px-6 border-b border-neutral-200/50 dark:border-white/5">
            <div class="flex items-center gap-3 cursor-pointer group" onclick="window.location.href='dashboard.php'">
                <div class="w-8 h-8 rounded-lg bg-brand-500 flex items-center justify-center text-white shadow-lg"><i class="fa-solid fa-link text-sm"></i></div>
                <span class="font-bold text-xl tracking-tight text-neutral-900 dark:text-white">shortmylink</span>
            </div>
            <button id="close-sidebar" class="md:hidden text-neutral-500 hover:text-brand-500"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>

        <div class="flex-1 overflow-y-auto py-6 space-y-1 px-3">
            <div class="text-[10px] font-bold text-neutral-400 uppercase px-4 mb-2 tracking-wider font-sans">Main Interface</div>
            <?php 
            navItem('dashboard', 'fa-grid-2', 'Dashboard', $page);
            navItem('manage_links', 'fa-link', 'Manage Links', $page);
            navItem('payments', 'fa-wallet', 'Payments', $page);
            navItem('analytics', 'fa-chart-pie', 'Analytics', $page);
            ?>
            <div class="my-4 border-t border-neutral-200/50 dark:border-white/5 mx-4"></div>
            <div class="text-[10px] font-bold text-neutral-400 uppercase px-4 mb-2 tracking-wider font-sans">Monetization</div>
            <?php
            navItem('partner', 'fa-handshake', 'Be Partner', $page);
            navItem('advertise', 'fa-bullhorn', 'Ad Manager', $page);
            navItem('publishad', 'fa-rectangle-ad', 'Publish Ad', $page);
            ?>
            <div class="text-[10px] font-bold text-neutral-400 uppercase px-4 mb-2 mt-6 tracking-wider font-sans">System</div>
            <?php navItem('support', 'fa-headset', 'Support', $page); ?>
        </div>

        <div class="p-4 border-t border-neutral-200/50 dark:border-white/5">
            <div onclick="window.location.href='profile.php'" class="bg-neutral-100 dark:bg-white/5 rounded-xl p-3 flex items-center gap-3 cursor-pointer hover:bg-neutral-200 dark:hover:bg-white/10 transition">
                <div class="w-8 h-8 rounded-full bg-brand-500 flex items-center justify-center text-white text-xs font-bold"><i class="fa-solid fa-user"></i></div>
                <div class="overflow-hidden flex-1">
                    <div class="text-xs font-bold text-neutral-900 dark:text-white truncate"><?php echo htmlspecialchars($username); ?></div>
                    <div class="text-[10px] text-brand-600 dark:text-brand-400 font-mono"><?php echo htmlspecialchars($role); ?></div>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative bg-neutral-50 dark:bg-[#020408]">
        <div class="absolute inset-0 tech-grid opacity-100 pointer-events-none"></div>

        <header class="h-20 px-6 lg:px-10 flex items-center justify-between border-b border-neutral-200/50 dark:border-white/5 bg-white/50 dark:bg-black/20 backdrop-blur-md z-20 relative">
            <div class="flex items-center gap-4">
                <button id="mobile-menu-btn" class="md:hidden text-neutral-500 dark:text-white hover:text-brand-500"><i class="fa-solid fa-bars text-xl"></i></button>
                <h1 class="text-lg font-bold text-neutral-800 dark:text-white">Help Center</h1>
            </div>
            <div class="flex items-center gap-4">
                <button id="theme-toggle" class="w-9 h-9 rounded-full bg-neutral-100 dark:bg-white/10 flex items-center justify-center text-neutral-500 dark:text-neutral-400 hover:text-brand-600 transition-colors">
                    <i class="fa-solid fa-moon dark:hidden"></i><i class="fa-solid fa-sun hidden dark:block"></i>
                </button>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto relative z-10 p-4 md:p-8 lg:p-12 pb-24">
            
            <!-- SEARCH HERO -->
            <div class="text-center max-w-3xl mx-auto mb-12 pt-8">
                <h2 class="text-3xl md:text-4xl font-extrabold text-neutral-900 dark:text-white mb-6">How can we help you?</h2>
                <div class="relative group search-glow rounded-2xl transition-all duration-300">
                    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none"><i class="fa-solid fa-magnifying-glass text-neutral-400 text-lg"></i></div>
                    <input type="text" id="faq-search" oninput="filterFaqs()" class="block w-full pl-14 pr-4 py-5 rounded-2xl bg-white dark:bg-[#0f1115] border border-neutral-200 dark:border-white/10 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none text-lg shadow-lg" placeholder="Search (e.g. 'Withdrawal', 'Ads', 'Rates')...">
                </div>
            </div>

            <!-- DYNAMIC FAQ CONTAINER -->
            <div id="faq-container" class="max-w-4xl mx-auto space-y-8"></div>

            <!-- CONTACT FORM -->
            <div id="contact-section" class="max-w-3xl mx-auto mt-16 border-t border-neutral-200 dark:border-white/10 pt-10">
                <div class="text-center mb-10">
                    <h3 class="text-xl font-bold text-neutral-900 dark:text-white">Still need help?</h3>
                    <p class="text-neutral-500 dark:text-neutral-400 text-sm mt-1">Contact support. We typically reply within 24 hours.</p>
                    <p class="text-xs font-mono text-brand-600 dark:text-brand-400 mt-2">support@shortmylink.in</p>
                </div>

                <div class="bg-white dark:bg-[#0f1115] border border-neutral-200 dark:border-white/10 rounded-2xl p-8 shadow-lg">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 uppercase mb-2">Subject</label>
                            <select id="ticket-subject" class="w-full bg-neutral-50 dark:bg-black/20 border border-neutral-200 dark:border-white/10 rounded-xl p-3 text-sm dark:text-white focus:border-brand-500 outline-none">
                                <option>Payment Issue</option><option>Bug Report</option><option>Ad Approval</option><option>Account Ban</option><option>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 uppercase mb-2">Message</label>
                            <textarea id="ticket-msg" rows="5" class="w-full bg-neutral-50 dark:bg-black/20 border border-neutral-200 dark:border-white/10 rounded-xl p-3 text-sm dark:text-white focus:border-brand-500 outline-none" placeholder="Describe your issue..."></textarea>
                        </div>
                        <button onclick="submitTicket()" id="submitBtn" class="w-full py-3.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-lg shadow-brand-500/20 transition-all">Submit Ticket</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script>
        const username = "<?php echo htmlspecialchars($username); ?>";

        // --- FULL DATABASE OF 30+ QUESTIONS ---
        const FAQ_DATA = [
            {
                category: "About ShortMyLink.in",
                items: [
                    { q: "What is ShortMyLink.in?", a: "ShortMyLink.in is a link-shortening website that shows ads on shortened links. We keep 15% commission and give 85% earnings to the user who shared the link." },
                    { q: "How does ShortMyLink.in work?", a: "By shortening your long URLs and sharing them. When someone clicks, ads play and you earn." },
                    { q: "Is ShortMyLink.in free to use?", a: "Yes, it is completely free. We earn from ads, not from users." },
                    { q: "What makes ShortMyLink different?", a: "We take a small commission and offer huge earning opportunities, 48-hour withdrawals, and a dedicated support team." }
                ]
            },
            {
                category: "Account & Security",
                items: [
                    { q: "How do I create an account?", a: "Go to the Signup page and register." },
                    { q: "Do I need to verify my email/OTP?", a: "Verification happens during signup, during withdrawals, and through Google or Email login." },
                    { q: "I forgot my password—what should I do?", a: "Just click “Forgot Password” on the login page." },
                    { q: "My account is restricted—how do I fix it?", a: "Email us at <b>support@shortmylink.in</b>." }
                ]
            },
            {
                category: "Link Shortening",
                items: [
                    { q: "How do I shorten a link?", a: "Sign in → Open Manage Links → Click Create Link." },
                    { q: "Where can I see my link analytics?", a: "On the Analytics page and Dashboard page." },
                    { q: "Can I edit or delete a short link?", a: "Yes, on the Manage Links page." },
                    { q: "Is there a limit to how many links I can create?", a: "No, there is no limit." }
                ]
            },
            {
                category: "Earnings & Wallet",
                items: [
                    { q: "How do I earn money?", a: "You earn through advertisements (AdSense) shown on your link." },
                    { q: "How much can I earn per 1000 views?", a: "Between <b>$1 to $6 per 1000 views</b>, depending on the ad type and region." },
                    { q: "What is the minimum withdrawal?", a: "Minimum withdrawal is <b>$5.00</b>." },
                    { q: "How can I withdraw my earnings?", a: "Withdrawals are available through: PayPal, UPI, IMPS. (With a 1 user = 1 click limit to prevent abuse.)" }
                ]
            },
            {
                category: "Advertisements",
                items: [
                    { q: "How do advertisers upload ads?", a: "Go to the Advertise page and submit your ad details." },
                    { q: "What types of ads are supported?", a: "All major ad types are supported. Users can choose Adult or Normal category, and all ad settings are managed by the ShortMyLink.in team." },
                    { q: "How long does approval take?", a: "Up to 24 hours." },
                    { q: "What are the advertising rates?", a: "Banner ad: $1.20 per 1000 clicks (Rates vary by ad type)." }
                ]
            },
            {
                category: "Policies & Safety",
                items: [
                    { q: "What content is not allowed?", a: "Child pornography, Illegal content, Abusive or inappropriate content, Any banned material." },
                    { q: "Is my data encrypted?", a: "Yes, 100% fully encrypted." },
                    { q: "Do you share user data?", a: "No. Your information is highly secure and never shared." },
                    { q: "How do you handle copyright or illegal content?", a: "We comply with legal authorities and take strict action." }
                ]
            },
            {
                category: "Technical & Support",
                items: [
                    { q: "My link is not redirecting. What should I do?", a: "Wait a few seconds for ads to load, or contact support." },
                    { q: "How do I report bugs?", a: "Email us at <b>cookie@shortmylink.in</b>." },
                    { q: "How can I contact support?", a: "Visit the Help & Support page or email: 📩 <b>support@shortmylink.in</b>." }
                ]
            }
        ];

        document.addEventListener("DOMContentLoaded", () => {
            renderFAQs(''); // Initial Render
            
            // UI Logic
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            const closeBtn = document.getElementById('close-sidebar');

            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                if(!overlay) return;
                overlay.classList.toggle('hidden');
                setTimeout(() => overlay.classList.toggle('opacity-0'), 10);
            }

            if(mobileBtn) mobileBtn.onclick = toggleSidebar;
            if(overlay) overlay.onclick = toggleSidebar;
            if(closeBtn) closeBtn.onclick = toggleSidebar;

            document.getElementById('theme-toggle').onclick = () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            };

            gsap.from("#faq-container", { y: 20, opacity: 0, duration: 0.6, ease: "power2.out", delay: 0.2 });
        });

        // --- RENDER LOGIC ---
        function renderFAQs(filter) {
            const container = document.getElementById('faq-container');
            container.innerHTML = '';
            const term = filter.toLowerCase().trim();

            if (!term) {
                // --- DEFAULT VIEW: TOP 3 QUESTIONS ---
                const topHeader = document.createElement('h3');
                topHeader.className = "text-sm font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-4";
                topHeader.innerText = "Frequently Asked Questions";
                container.appendChild(topHeader);

                // Manually picking the 3 most important questions
                const topQs = [
                    { q: "What is ShortMyLink.in?", a: "ShortMyLink.in is a link-shortening website that shows ads. We keep 15% commission and give 85% earnings to you." },
                    { q: "How much can I earn per 1000 views?", a: "Between $1 to $6 per 1000 views, depending on the ad type and region." },
                    { q: "What is the minimum withdrawal?", a: "Minimum withdrawal is $5.00." }
                ];

                topQs.forEach(item => createItem(container, item));

            } else {
                // --- SEARCH MODE: FILTER ALL ---
                let hasResults = false;
                FAQ_DATA.forEach(section => {
                    const matches = section.items.filter(item => 
                        item.q.toLowerCase().includes(term) || item.a.toLowerCase().includes(term)
                    );

                    if (matches.length > 0) {
                        hasResults = true;
                        const header = document.createElement('h3');
                        header.className = "text-sm font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-4 mt-8 first:mt-0";
                        header.innerText = section.category;
                        container.appendChild(header);
                        matches.forEach(item => createItem(container, item));
                    }
                });

                if (!hasResults) {
                    container.innerHTML = `
                        <div class="text-center py-12 p-6 rounded-xl border border-dashed border-neutral-300 dark:border-neutral-700">
                            <p class="text-neutral-500 mb-2">No answers found for "${filter}"</p>
                            <button onclick="document.getElementById('contact-section').scrollIntoView({behavior:'smooth'})" class="text-brand-500 font-bold hover:underline">Contact Support</button>
                        </div>`;
                }
            }
        }

        function createItem(container, item) {
            const el = document.createElement('details');
            el.className = "details-box group bg-white dark:bg-[#0f1115] border border-neutral-200 dark:border-white/10 rounded-xl overflow-hidden mb-3";
            el.innerHTML = `
                <summary class="p-4 font-bold text-neutral-900 dark:text-white cursor-pointer flex justify-between items-center list-none select-none hover:bg-neutral-50 dark:hover:bg-white/5 transition">
                    ${item.q}
                    <i class="fa-solid fa-chevron-down text-xs text-neutral-400 group-open:rotate-180 transition-transform"></i>
                </summary>
                <div class="px-4 pb-4 text-sm text-neutral-600 dark:text-neutral-400 border-t border-neutral-100 dark:border-white/5 pt-3 leading-relaxed">
                    ${item.a}
                </div>
            `;
            container.appendChild(el);
        }

        function filterFaqs() {
            const input = document.getElementById('faq-search');
            renderFAQs(input.value);
        }

        async function submitTicket() {
            const subject = document.getElementById('ticket-subject').value;
            const message = document.getElementById('ticket-msg').value;
            const btn = document.getElementById('submitBtn');

            if(!message) return alert('Please describe your issue');

            btn.innerHTML = 'Sending...';
            btn.disabled = true;

            setTimeout(() => {
                alert('Support ticket submitted successfully! We will email you shortly.');
                document.getElementById('ticket-msg').value = '';
                btn.innerHTML = 'Submit Ticket';
                btn.disabled = false;
            }, 1000);
        }
    </script>
</body>
</html>