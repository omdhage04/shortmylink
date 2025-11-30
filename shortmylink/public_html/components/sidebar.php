<!-- SIDEBAR COMPONENT -->
<aside id="sidebar" class="sidebar-glass fixed md:relative inset-y-0 left-0 z-30 w-64 h-full flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-300 shadow-2xl md:shadow-none bg-white/80 dark:bg-black/70 backdrop-blur-xl border-r border-neutral-200 dark:border-white/10">
    
    <!-- Logo Area -->
    <div class="h-20 flex items-center justify-between px-6 border-b border-neutral-200/50 dark:border-white/5">
        <div class="flex items-center gap-3 cursor-pointer group" onclick="window.location.href='dashboard.php'">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-800 to-brand-600 flex items-center justify-center text-white shadow-lg shadow-brand-900/20 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-link text-sm"></i>
            </div>
            <span class="font-bold text-xl tracking-tight text-neutral-900 dark:text-white">shortmylink</span>
        </div>
        <button id="close-sidebar" class="md:hidden text-neutral-500 hover:text-brand-500 transition"><i class="fa-solid fa-xmark text-xl"></i></button>
    </div>

    <!-- Menu Items -->
    <div class="flex-1 overflow-y-auto py-6 space-y-1 px-3">
        <div class="text-[10px] font-bold text-neutral-400 uppercase px-4 mb-2 tracking-wider font-sans">Main Interface</div>

        <?php 
        function navItem($target, $icon, $label, $currentPage) {
            $isActive = ($target === $currentPage);
            $activeClass = $isActive 
                ? 'active bg-brand-50 dark:bg-brand-900/20 text-brand-700 dark:text-brand-400 font-bold shadow-sm ring-1 ring-brand-200 dark:ring-transparent' 
                : 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-white/5 hover:text-neutral-900 dark:hover:text-white font-medium';
            
            // Handle links appropriately
            $link = strpos($target, '.php') !== false ? $target : $target . '.php';

            echo '<a href="'.$link.'" class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all duration-200 '.$activeClass.'">
                    <i class="fa-solid '.$icon.' w-5 text-center"></i><span>'.$label.'</span>
                  </a>';
        }
        
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
        
        <!-- LOGOUT BUTTON (Calls systemLogout) -->
        <button onclick="systemLogout()" class="w-full nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors mt-2 text-left">
            <i class="fa-solid fa-right-from-bracket w-5 text-center"></i><span>Log Out</span>
        </button>
    </div>

    <!-- Profile Footer -->
    <div class="p-4 border-t border-neutral-200/50 dark:border-white/5 bg-neutral-50/50 dark:bg-white/5 backdrop-blur-md">
        <div onclick="window.location.href='profile.php'" class="rounded-xl p-2 flex items-center gap-3 cursor-pointer hover:bg-white dark:hover:bg-white/10 transition-all group border border-transparent hover:border-neutral-200 dark:hover:border-white/10">
            <div id="profile-avatar" class="w-9 h-9 rounded-full bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white text-xs font-bold group-hover:scale-105 transition-transform shadow-md shadow-brand-500/20">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="overflow-hidden flex-1">
                <div id="profile-username" class="text-xs font-bold text-neutral-900 dark:text-white truncate group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">
                    <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Loading...'; ?>
                </div>
                <div id="profile-role" class="text-[10px] text-neutral-500 dark:text-neutral-400 font-mono">
                    <?php echo isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : 'Verifying...'; ?>
                </div>
            </div>
            <i class="fa-solid fa-chevron-right text-[10px] text-neutral-400 group-hover:text-brand-500 transition-colors"></i>
        </div>
    </div>
</aside>

<!-- DIRECT SCRIPT (No external file needed) -->
<script>
    function systemLogout() {
        // 1. Try to use the fancy UI modal if it exists (from ui.js)
        if (typeof window.confirmAction === 'function') {
            window.confirmAction({
                title: 'Sign Out?',
                text: 'You will be returned to the home page.',
                isDanger: true,
                btnText: 'Log Out'
            }, function() {
                performCleanup();
            });
        } 
        // 2. Fallback to normal browser confirm
        else if (confirm("Are you sure you want to log out?")) {
            performCleanup();
        }
    }

    function performCleanup() {
        // Call backend API (optional, fires and forgets)
        fetch('api/logout.php', { method: 'POST' }).catch(e => console.error(e));

        // Clear Storage
        localStorage.removeItem('current_username');
        localStorage.removeItem('user_role');
        localStorage.removeItem('api_token');

        // Clear Cookies
        document.cookie.split(";").forEach(function(c) { 
            document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
        });

        // Redirect
        window.location.href = 'index.html';
    }
</script>