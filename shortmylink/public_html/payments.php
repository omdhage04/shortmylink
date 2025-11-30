<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments | ShortMyLink.in</title>
    
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; transition: background-color 0.3s ease, color 0.3s ease; }
        .dark body { background-color: #020408; color: #e5e5e5; }
        .tech-grid { position: absolute; inset: 0; pointer-events: none; background-image: linear-gradient(to right, rgba(0,0,0,0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(0,0,0,0.05) 1px, transparent 1px); background-size: 40px 40px; }
        .dark .tech-grid { background-image: linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px); }
        
        .skeleton { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; background-color: rgba(0,0,0,0.1); color: transparent !important; border-radius: 4px; }
        .dark .skeleton { background-color: rgba(255,255,255,0.1); }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
    </style>
</head>
<body class="h-screen overflow-hidden flex text-neutral-800 dark:text-neutral-200">

    <!-- MOBILE OVERLAY -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden backdrop-blur-sm transition-opacity opacity-0"></div>

    <!-- 1. SIDEBAR INCLUDE -->
    <?php $page = 'payments'; include 'components/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative bg-neutral-50 dark:bg-[#020408]">
        <div class="tech-grid"></div>

        <!-- 2. HEADER INCLUDE -->
        <?php $pageTitle = 'Wallet & Payments'; include 'components/header.php'; ?>

        <div class="flex-1 overflow-y-auto relative z-10 p-4 md:p-6 lg:p-10 pb-20">
            
            <!-- TOP ROW: Balance & Method -->
            <div class="grid md:grid-cols-3 gap-6 mb-10">
                
                <!-- 1. AVAILABLE BALANCE CARD -->
                <div class="md:col-span-2 bg-gradient-to-br from-brand-900 to-brand-700 rounded-2xl p-6 text-white relative overflow-hidden shadow-xl shadow-brand-900/20 gsap-fade-up">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
                    
                    <div class="relative z-10 flex flex-col h-full justify-between">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-brand-100 text-xs font-bold uppercase tracking-wider mb-1">Available Funds</p>
                                <h2 id="display-balance" class="text-4xl md:text-5xl font-extrabold font-mono tracking-tight skeleton">$0.00</h2>
                            </div>
                            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-md">
                                <i class="fa-solid fa-wallet text-2xl"></i>
                            </div>
                        </div>

                        <div class="mt-8">
                            <div class="flex justify-between text-xs font-medium mb-2">
                                <span id="progress-text">Progress to Minimum Payout ($5.00)</span>
                                <span id="progress-percent">0%</span>
                            </div>
                            <div class="w-full h-2 bg-black/20 rounded-full overflow-hidden">
                                <div id="payout-progress" class="h-full bg-white/90 rounded-full transition-all duration-1000" style="width: 0%"></div>
                            </div>
                            <div class="mt-4 flex gap-3">
                                <button onclick="openWithdrawModal()" id="withdrawBtn" class="bg-white text-brand-900 px-6 py-2.5 rounded-lg text-sm font-bold shadow-lg hover:bg-neutral-100 transition-all flex items-center gap-2 opacity-50 cursor-not-allowed" disabled>
                                    <i class="fa-solid fa-paper-plane"></i> Request Withdrawal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. PAYMENT METHOD CARD -->
                <div class="bg-white dark:bg-[#0f1115] border border-neutral-200 dark:border-white/5 rounded-2xl p-6 shadow-sm flex flex-col justify-between gsap-fade-up" style="animation-delay: 0.1s">
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-neutral-900 dark:text-white">Payout Method</h3>
                            <button onclick="openSettingsModal()" class="text-xs font-bold text-brand-600 hover:text-brand-500">Edit</button>
                        </div>
                        
                        <div id="method-display" class="p-4 bg-neutral-50 dark:bg-white/5 rounded-xl border border-neutral-100 dark:border-white/5 text-center py-8">
                            <div class="w-12 h-12 bg-neutral-200 dark:bg-white/10 rounded-full flex items-center justify-center mx-auto mb-3 text-neutral-400">
                                <i class="fa-solid fa-plus"></i>
                            </div>
                            <p class="text-sm font-medium text-neutral-500">No method added</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-[10px] text-neutral-400 leading-relaxed">
                            <i class="fa-solid fa-circle-info mr-1"></i> Payments are processed daily. Ensure details are correct to avoid rejection.
                        </p>
                    </div>
                </div>
            </div>

            <!-- TRANSACTION HISTORY -->
            <div class="bg-white dark:bg-[#0f1115] border border-neutral-200 dark:border-white/5 rounded-2xl overflow-hidden shadow-sm gsap-fade-up" style="animation-delay: 0.2s">
                <div class="px-6 py-4 border-b border-neutral-200 dark:border-white/5 flex justify-between items-center">
                    <h3 class="font-bold text-neutral-900 dark:text-white">Transaction History</h3>
                    <button onclick="fetchPaymentData()" class="text-neutral-400 hover:text-brand-500"><i class="fa-solid fa-rotate-right"></i></button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-neutral-50 dark:bg-white/5 text-neutral-500 uppercase font-bold text-[10px]">
                            <tr>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Transaction ID</th>
                                <th class="px-6 py-4">Method</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody id="payout-table-body" class="divide-y divide-neutral-200 dark:divide-white/5">
                            <tr><td colspan="5" class="px-6 py-8 text-center text-neutral-500">Loading history...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <!-- MODAL 1: WITHDRAW REQUEST -->
    <div id="withdrawModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center backdrop-blur-sm p-4 opacity-0 transition-opacity">
        <div class="bg-white dark:bg-[#15171c] p-6 rounded-2xl w-full max-w-sm shadow-2xl border border-neutral-200 dark:border-white/10 transform scale-95 transition-transform">
            <h3 class="text-lg font-bold dark:text-white mb-1">Request Withdrawal</h3>
            <p class="text-xs text-neutral-500 mb-4">Transfer funds to your active account.</p>
            
            <div class="mb-4">
                <label class="block text-[10px] font-bold text-neutral-500 uppercase mb-1">Amount ($)</label>
                <input type="number" id="withdrawAmount" class="w-full bg-neutral-100 dark:bg-black/20 border border-neutral-200 dark:border-white/10 rounded-xl p-3 font-mono text-lg font-bold dark:text-white outline-none focus:border-brand-500" placeholder="0.00">
                <p class="text-[10px] text-right mt-1 text-neutral-400">Available: <span id="modal-max-bal" class="text-brand-500 font-bold">$0.00</span></p>
            </div>

            <div class="flex gap-3">
                <button onclick="closeModals()" class="flex-1 py-2.5 rounded-lg text-sm font-bold text-neutral-500 hover:bg-neutral-100 dark:hover:bg-white/5 transition">Cancel</button>
                <button onclick="submitWithdraw()" id="btn-confirm-withdraw" class="flex-1 py-2.5 rounded-lg text-sm font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lg shadow-brand-500/20 transition">Confirm</button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: PAYMENT SETTINGS -->
    <div id="settingsModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center backdrop-blur-sm p-4 opacity-0 transition-opacity">
        <div class="bg-white dark:bg-[#15171c] p-6 rounded-2xl w-full max-w-md shadow-2xl border border-neutral-200 dark:border-white/10 transform scale-95 transition-transform">
            <h3 class="text-lg font-bold dark:text-white mb-4">Payment Settings</h3>
            
            <div class="mb-4">
                <label class="block text-[10px] font-bold text-neutral-500 uppercase mb-1">Payment Method</label>
                <select id="methodSelect" onchange="toggleMethodFields()" class="w-full bg-neutral-100 dark:bg-black/20 border border-neutral-200 dark:border-white/10 rounded-xl p-3 text-sm dark:text-white outline-none focus:border-brand-500">
                    <option value="UPI">UPI (India)</option>
                    <option value="PAYPAL">PayPal (International)</option>
                    <option value="BANK">Bank Transfer</option>
                </select>
            </div>

            <div id="field-upi" class="mb-6">
                <label class="block text-[10px] font-bold text-neutral-500 uppercase mb-1">UPI ID</label>
                <input type="text" id="upiInput" class="w-full bg-neutral-100 dark:bg-black/20 border border-neutral-200 dark:border-white/10 rounded-xl p-3 text-sm dark:text-white outline-none" placeholder="username@upi">
            </div>

            <div id="field-paypal" class="mb-6 hidden">
                <label class="block text-[10px] font-bold text-neutral-500 uppercase mb-1">PayPal Email</label>
                <input type="email" id="paypalInput" class="w-full bg-neutral-100 dark:bg-black/20 border border-neutral-200 dark:border-white/10 rounded-xl p-3 text-sm dark:text-white outline-none" placeholder="email@example.com">
            </div>

            <div id="field-bank" class="mb-6 hidden space-y-3">
                <input type="text" id="bankName" class="w-full bg-neutral-100 dark:bg-black/20 border border-neutral-200 dark:border-white/10 rounded-xl p-3 text-sm dark:text-white" placeholder="Bank Name">
                <input type="text" id="accNumber" class="w-full bg-neutral-100 dark:bg-black/20 border border-neutral-200 dark:border-white/10 rounded-xl p-3 text-sm dark:text-white" placeholder="Account Number">
                <input type="text" id="ifscCode" class="w-full bg-neutral-100 dark:bg-black/20 border border-neutral-200 dark:border-white/10 rounded-xl p-3 text-sm dark:text-white" placeholder="IFSC / SWIFT Code">
            </div>

            <div class="flex gap-3">
                <button onclick="closeModals()" class="flex-1 py-2.5 rounded-lg text-sm font-bold text-neutral-500 hover:bg-neutral-100 dark:hover:bg-white/5 transition">Cancel</button>
                <button onclick="savePaymentMethod()" id="btn-save-method" class="flex-1 py-2.5 rounded-lg text-sm font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lg shadow-brand-500/20 transition">Save Details</button>
            </div>
        </div>
    </div>

    <!-- UI & ANIMATIONS -->
    <script src="js/ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <script>
        // SHARED LOGIC (Theme, Sidebar, Profile)
        document.addEventListener("DOMContentLoaded", () => {
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

            // Profile
            const username = localStorage.getItem('current_username');
            if(!username) window.location.href = 'index.html';
            document.getElementById('profile-username').innerText = username;
            document.getElementById('profile-role').innerText = localStorage.getItem('user_role') === 'ROOT_ADMIN' ? 'Administrator' : 'Publisher';

            // Animation
            gsap.from(".gsap-fade-up", { y: 20, opacity: 0, duration: 0.6, stagger: 0.1, delay: 0.1 });

            // Fetch Data
            fetchPaymentData();
        });

        // --- PAGE SPECIFIC LOGIC ---
        const MIN_PAYOUT = 5.00; 
        const API_URL = 'api/payments.php';
        const username = localStorage.getItem('current_username');
        let currentBalance = 0.00;

        // 1. FETCH DATA
        async function fetchPaymentData() {
            try {
                const res = await fetch(API_URL, { 
                    method: 'POST', 
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ action: 'fetch', username: username }) 
                });
                const data = await res.json();
                
                if (data.status === 'success') {
                    // Balance
                    currentBalance = parseFloat(data.balance);
                    document.getElementById('display-balance').innerText = `$${currentBalance.toFixed(2)}`;
                    document.getElementById('display-balance').classList.remove('skeleton');
                    document.getElementById('modal-max-bal').innerText = `$${currentBalance.toFixed(2)}`;

                    // Progress Bar
                    const percent = Math.min((currentBalance / MIN_PAYOUT) * 100, 100);
                    document.getElementById('payout-progress').style.width = `${percent}%`;
                    document.getElementById('progress-percent').innerText = `${Math.round(percent)}%`;
                    
                    // Button State
                    const btn = document.getElementById('withdrawBtn');
                    if(currentBalance >= MIN_PAYOUT) {
                        btn.disabled = false;
                        btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }

                    renderMethod(data.method);
                    renderHistory(data.history);
                }
            } catch (e) { console.error(e); }
        }

        function renderMethod(method) {
            const container = document.getElementById('method-display');
            if(method && method.type) {
                let iconClass = 'fa-building-columns';
                if(method.type === 'UPI') iconClass = 'fa-mobile-screen';
                if(method.type === 'PAYPAL') iconClass = 'fa-paypal';

                container.innerHTML = `
                    <div class="flex items-center gap-4 text-left">
                        <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid ${iconClass}"></i></div>
                        <div>
                            <div class="font-bold text-neutral-900 dark:text-white">${method.type}</div>
                            <div class="text-xs text-neutral-500 font-mono">${method.detail}</div>
                        </div>
                    </div>
                `;
            }
        }

        function renderHistory(txns) {
            const tbody = document.getElementById('payout-table-body');
            tbody.innerHTML = '';
            
            if(txns.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-neutral-500">No transactions found.</td></tr>';
                return;
            }

            txns.forEach(txn => {
                let badgeClass = '';
                if(txn.status === 'completed') badgeClass = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
                else if(txn.status === 'processing') badgeClass = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                else if(txn.status === 'pending') badgeClass = 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400';
                else badgeClass = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';

                const row = `
                <tr class="hover:bg-neutral-50 dark:hover:bg-white/5 transition border-b border-neutral-100 dark:border-white/5 last:border-0">
                    <td class="px-6 py-4 font-mono text-xs text-neutral-500">${txn.date}</td>
                    <td class="px-6 py-4 font-mono text-xs">${txn.id || '-'}</td>
                    <td class="px-6 py-4 text-xs font-bold">${txn.method}</td>
                    <td class="px-6 py-4 font-mono font-bold text-neutral-900 dark:text-white">$${txn.amount}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider ${badgeClass}">${txn.status}</span>
                    </td>
                </tr>`;
                tbody.innerHTML += row;
            });
        }

        // --- MODALS & ACTIONS ---
        function openWithdrawModal() {
            showModal('withdrawModal');
            document.getElementById('withdrawAmount').value = '';
        }
        function openSettingsModal() { showModal('settingsModal'); }

        function showModal(id) {
            const el = document.getElementById(id);
            el.classList.remove('hidden');
            setTimeout(() => { el.classList.remove('opacity-0'); el.children[0].classList.remove('scale-95'); }, 10);
        }

        function closeModals() {
            ['withdrawModal', 'settingsModal'].forEach(id => {
                const el = document.getElementById(id);
                el.classList.add('opacity-0'); 
                el.children[0].classList.add('scale-95');
                setTimeout(() => el.classList.add('hidden'), 300);
            });
        }

        function toggleMethodFields() {
            const val = document.getElementById('methodSelect').value;
            ['upi', 'paypal', 'bank'].forEach(t => document.getElementById(`field-${t}`).classList.add('hidden'));
            document.getElementById(`field-${val.toLowerCase()}`).classList.remove('hidden');
        }

        async function submitWithdraw() {
            const amount = parseFloat(document.getElementById('withdrawAmount').value);
            const btn = document.getElementById('btn-confirm-withdraw');
            
            if(!amount || amount < MIN_PAYOUT) return showToast('error', `Minimum withdrawal is $${MIN_PAYOUT}`);
            if(amount > currentBalance) return showToast('error', 'Insufficient funds');

            btn.innerText = "Processing...";
            btn.disabled = true;

            try {
                const res = await fetch(API_URL, {
                    method: 'POST',
                    body: JSON.stringify({ action: 'withdraw', username: username, amount: amount })
                });
                const data = await res.json();

                if (data.status === 'success') {
                    showToast('success', 'Withdrawal requested successfully!');
                    closeModals();
                    fetchPaymentData();
                } else {
                    showToast('error', data.message);
                }
            } catch(e) { showToast('error', 'Network Error'); }
            
            btn.innerText = "Confirm";
            btn.disabled = false;
        }

        async function savePaymentMethod() {
            const method = document.getElementById('methodSelect').value;
            const btn = document.getElementById('btn-save-method');
            let detail = '';

            if(method === 'UPI') detail = document.getElementById('upiInput').value;
            else if(method === 'PAYPAL') detail = document.getElementById('paypalInput').value;
            else {
                const bank = document.getElementById('bankName').value;
                const acc = document.getElementById('accNumber').value;
                const ifsc = document.getElementById('ifscCode').value;
                detail = `Bank: ${bank}, Acc: ${acc}, IFSC: ${ifsc}`;
            }

            if(!detail) return showToast('error', 'Please fill all fields');

            btn.innerText = "Saving...";
            btn.disabled = true;

            try {
                const res = await fetch(API_URL, {
                    method: 'POST',
                    body: JSON.stringify({ 
                        action: 'save_method', 
                        username: username, 
                        method: method, 
                        detail: detail 
                    })
                });
                const data = await res.json();

                if(data.status === 'success') {
                    showToast('success', 'Payment details updated!');
                    closeModals();
                    fetchPaymentData();
                } else {
                    showToast('error', data.message);
                }
            } catch(e) { showToast('error', 'Network Error'); }
            
            btn.innerText = "Save Details";
            btn.disabled = false;
        }
    </script>
</body>
</html>