<!-- HEADER COMPONENT -->
<header class="h-20 px-6 lg:px-10 flex items-center justify-between border-b border-neutral-200/50 dark:border-white/5 bg-white/80 dark:bg-black/70 backdrop-blur-xl z-20 relative sticky top-0">
    <div class="flex items-center gap-4">
        <!-- Mobile Toggle -->
        <button id="mobile-menu-btn" class="md:hidden text-neutral-500 dark:text-white hover:text-brand-500 transition-colors p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-white/10">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
        
        <!-- Page Title -->
        <h1 class="text-lg font-bold text-neutral-900 dark:text-white flex items-center gap-3 font-sans">
            <?php echo isset($pageTitle) ? $pageTitle : 'ShortMyLink'; ?>
        </h1>
    </div>
    
    <div class="flex items-center gap-3 sm:gap-4">
        <!-- Dynamic CTA Button -->
        <?php if(isset($page) && $page !== 'manage_links'): ?>
        <button onclick="window.location.href='manage_links.php'" class="hidden sm:flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-full text-xs font-bold shadow-lg shadow-brand-500/20 transition-all hover:-translate-y-0.5 magnetic-btn border border-brand-500/50">
            <i class="fa-solid fa-plus"></i> <span>New Link</span>
        </button>
        <?php endif; ?>
        
        <?php if(isset($page) && $page === 'manage_links'): ?>
        <button onclick="openModal('create')" class="hidden sm:flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-full text-xs font-bold shadow-lg shadow-brand-500/20 transition-all hover:-translate-y-0.5 magnetic-btn border border-brand-500/50">
            <i class="fa-solid fa-plus"></i> <span>Create</span>
        </button>
        <?php endif; ?>
        
        <div class="h-6 w-px bg-neutral-200 dark:bg-white/10 hidden sm:block"></div>

        <!-- Theme Toggle -->
        <button id="theme-toggle" class="w-9 h-9 rounded-full bg-white dark:bg-white/5 border border-neutral-200 dark:border-white/10 flex items-center justify-center text-neutral-500 dark:text-neutral-400 hover:text-brand-600 dark:hover:text-brand-400 hover:border-brand-200 dark:hover:border-brand-900 transition-all shadow-sm">
            <i class="fa-solid fa-moon dark:hidden"></i>
            <i class="fa-solid fa-sun hidden dark:block"></i>
        </button>
    </div>
</header>