<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Links | ShortMyLink.in</title>
    
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] },
                    colors: { brand: { 50: '#ecfdf5', 500: '#10b981', 600: '#059669', 900: '#064e3b' } }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; transition: background-color 0.3s ease; }
        .dark body { background-color: #020408; color: #e5e5e5; }
        .tech-grid { position: absolute; inset: 0; pointer-events: none; background-image: linear-gradient(to right, rgba(0,0,0,0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(0,0,0,0.05) 1px, transparent 1px); background-size: 40px 40px; }
        .dark .tech-grid { background-image: linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px); }
    </style>
</head>
<body class="h-screen overflow-hidden flex text-neutral-800 dark:text-neutral-200">

    <!-- 1. SIDEBAR -->
    <?php $page = 'manage_links'; include 'components/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative bg-neutral-50 dark:bg-[#020408]">
        <div class="tech-grid"></div>

        <!-- 2. HEADER -->
        <?php $pageTitle = 'Link Management'; include 'components/header.php'; ?>

        <div class="flex-1 overflow-y-auto relative z-10 p-4 md:p-8 pb-20">
            
            <!-- Search & Controls -->
            <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
                <div class="relative w-full md:w-96">
                    <i class="fa-solid fa-search absolute left-4 top-3.5 text-neutral-400 text-xs"></i>
                    <input type="text" id="searchInput" onkeyup="filterLinks()" placeholder="Search links..." class="w-full bg-white dark:bg-white/5 border border-neutral-200 dark:border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm focus:outline-none focus:border-brand-500 transition-colors">
                </div>
                <button onclick="openModal('create')" class="md:hidden w-full py-3 bg-brand-600 text-white rounded-xl font-bold shadow-lg">
                    <i class="fa-solid fa-plus mr-2"></i> Create New Link
                </button>
            </div>

            <!-- Links Table -->
            <div class="bg-white dark:bg-[#0f1115] border border-neutral-200 dark:border-white/5 rounded-2xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-neutral-50 dark:bg-white/5 text-neutral-500 uppercase font-bold text-[10px] border-b border-neutral-200 dark:border-white/5">
                            <tr>
                                <th class="px-6 py-4">Destination</th>
                                <th class="px-6 py-4">Short Link</th>
                                <th class="px-6 py-4">Performance</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="links-table-body" class="divide-y divide-neutral-200 dark:divide-white/5">
                            <tr><td colspan="4" class="px-6 py-8 text-center text-neutral-500">Loading links...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- CREATE LINK MODAL -->
    <div id="linkModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center backdrop-blur-sm p-4 opacity-0 transition-opacity">
        <div class="bg-white dark:bg-[#15171c] p-6 rounded-2xl w-full max-w-md shadow-2xl border border-neutral-200 dark:border-white/10 transform scale-95 transition-transform">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold dark:text-white">Shorten a URL</h3>
                <button onclick="closeModal()" class="text-neutral-400 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            
            <div class="mb-6">
                <label class="block text-[10px] font-bold text-neutral-500 uppercase mb-2">Paste Long URL</label>
                <input type="url" id="createUrl" class="w-full bg-neutral-50 dark:bg-black/20 border border-neutral-200 dark:border-white/10 focus:border-brand-500 rounded-xl p-4 text-sm dark:text-white outline-none transition-colors font-mono" placeholder="https://youtube.com/watch?v=...">
            </div>

            <div class="mb-6">
                <label class="block text-[10px] font-bold text-neutral-500 uppercase mb-2">Title (Optional)</label>
                <input type="text" id="createTitle" class="w-full bg-neutral-50 dark:bg-black/20 border border-neutral-200 dark:border-white/10 focus:border-brand-500 rounded-xl p-3 text-sm dark:text-white outline-none transition-colors" placeholder="e.g. Funny Cat Video">
            </div>

            <button onclick="submitCreateLink()" id="createBtn" class="w-full py-3.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-lg shadow-brand-500/20 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Generate Short Link
            </button>
        </div>
    </div>

    <script src="js/ui.js"></script>
    <script>
        const API_URL = 'api/links.php';
        const DOMAIN = 'shortmylink.in'; 
        const username = localStorage.getItem('current_username');

        document.addEventListener('DOMContentLoaded', () => {
            if(!username) window.location.href = 'index.html';
            fetchLinks();
        });

        // 1. FETCH LINKS
        async function fetchLinks() {
            try {
                const res = await fetch(API_URL, {
                    method: 'POST',
                    body: JSON.stringify({ action: 'fetch', username: username })
                });
                const data = await res.json();
                renderTable(data.status === 'success' ? data.data : []);
            } catch(e) { console.error(e); }
        }

        // 2. RENDER TABLE
        function renderTable(links) {
            const tbody = document.getElementById('links-table-body');
            tbody.innerHTML = '';
            
            if(links.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-8 text-neutral-500">No links created yet.</td></tr>';
                return;
            }

            links.forEach(l => {
                const shortLink = `${window.location.origin}/${l.short_code}`; // Dynamic domain
                tbody.innerHTML += `
                <tr class="hover:bg-neutral-50 dark:hover:bg-white/5 transition">
                    <td class="px-6 py-4">
                        <div class="font-bold text-sm dark:text-white mb-1">${l.title || 'Untitled'}</div>
                        <div class="text-xs text-neutral-500 truncate max-w-[200px]">${l.original_url}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-brand-600 text-xs bg-brand-50 dark:bg-brand-900/20 px-2 py-1 rounded border border-brand-200 dark:border-brand-900/30">/${l.short_code}</span>
                            <button onclick="copyLink('${shortLink}')" class="text-neutral-400 hover:text-brand-500"><i class="fa-regular fa-copy"></i></button>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-4 text-xs font-medium">
                            <span class="flex items-center gap-1 text-neutral-600 dark:text-neutral-400"><i class="fa-solid fa-eye"></i> ${l.total_views}</span>
                            <span class="flex items-center gap-1 text-green-600"><i class="fa-solid fa-dollar-sign"></i> ${l.total_revenue}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="deleteLink(${l.id})" class="text-neutral-400 hover:text-red-500 transition"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>`;
            });
        }

        // 3. CREATE LINK
        async function submitCreateLink() {
            const url = document.getElementById('createUrl').value;
            const title = document.getElementById('createTitle').value;
            const btn = document.getElementById('createBtn');

            if(!url) return showToast('error', 'Please enter a URL');

            btn.innerHTML = 'Generating...';
            btn.disabled = true;

            try {
                const res = await fetch(API_URL, {
                    method: 'POST',
                    body: JSON.stringify({ 
                        action: 'create', 
                        username: username, 
                        url: url, 
                        title: title 
                    })
                });
                const data = await res.json();
                
                if(data.status === 'success') {
                    showToast('success', 'Link created successfully!');
                    closeModal();
                    fetchLinks();
                    document.getElementById('createUrl').value = '';
                } else {
                    showToast('error', data.message);
                }
            } catch(e) { showToast('error', 'Network error'); }
            
            btn.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> Generate Short Link';
            btn.disabled = false;
        }

        // UTILS
        function copyLink(text) {
            navigator.clipboard.writeText(text);
            showToast('success', 'Copied to clipboard');
        }
        
        function openModal(id) {
            const el = document.getElementById('linkModal');
            el.classList.remove('hidden');
            setTimeout(() => { el.classList.remove('opacity-0'); el.children[0].classList.remove('scale-95'); }, 10);
        }
        function closeModal() {
            const el = document.getElementById('linkModal');
            el.classList.add('opacity-0');
            el.children[0].classList.add('scale-95');
            setTimeout(() => el.classList.add('hidden'), 300);
        }

        function deleteLink(id) {
            confirmAction({
                title: 'Delete Link?',
                text: 'This action cannot be undone.',
                isDanger: true
            }, async () => {
                await fetch(API_URL, { method: 'POST', body: JSON.stringify({ action: 'delete', username, link_id: id }) });
                fetchLinks();
                closeModal(); // Close the global modal
            });
        }
    </script>
</body>
</html>