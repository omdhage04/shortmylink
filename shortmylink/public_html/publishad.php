<?php
// 1. Security Check: Start session and verify user is logged in
session_start();

// Check for session OR "Remember Me" cookie
if (!isset($_SESSION['user_id']) && !isset($_COOKIE['shortmylink_auth'])) {
    // If neither exists, redirect to login
    header("Location: login.html");
    exit();
}

// 2. Page Configuration for Sidebar
$page = 'publishad'; // This makes the "Publish Ad" tab active in the sidebar
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publish Your Ad | ShortMyLink.in</title>
    
    <!-- IMMEDIATE THEME CHECK (Prevents flash of wrong color) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Configuration for Tailwind -->
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

    <!-- Icons & Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        /* Global Transitions */
        body { font-family: 'Plus Jakarta Sans', sans-serif; transition: background-color 0.3s ease, color 0.3s ease; }
        body { background-color: #f8fafc; color: #1e293b; }
        .dark body { background-color: #020408; color: #e5e5e5; }

        /* Tech Grid Background */
        .tech-grid {
            position: absolute; inset: 0; pointer-events: none;
            background-size: 40px 40px;
            background-image: linear-gradient(to right, rgba(0,0,0,0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(0,0,0,0.05) 1px, transparent 1px);
        }
        .dark .tech-grid {
            background-image: linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px);
        }

        /* Form Inputs */
        .input-field {
            background: #f8fafc; border: 1px solid #e2e8f0;
        }
        .dark .input-field {
            background: #0f1115; border: 1px solid rgba(255,255,255,0.1); color: white;
        }
        .input-field:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); }

        /* Glass Panels */
        .glass-panel {
            background: white; border: 1px solid #e2e8f0;
        }
        .dark .glass-panel {
            background: #0f1115; border: 1px solid rgba(255,255,255,0.05);
        }

        /* File Upload Area */
        .upload-area {
            border: 2px dashed #cbd5e1; background: #f8fafc; transition: all 0.2s;
        }
        .dark .upload-area {
            border-color: rgba(255,255,255,0.1); background: rgba(255,255,255,0.02);
        }
        .upload-area:hover { border-color: #10b981; background: rgba(16, 185, 129, 0.05); }

        /* Horizontal Scroll Hide */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="h-screen overflow-hidden flex text-neutral-800 dark:text-neutral-200">

    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden backdrop-blur-sm transition-opacity opacity-0"></div>

    <!-- 1. PHP INCLUDE: SIDEBAR -->
    <?php include 'components/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative bg-neutral-50 dark:bg-[#020408]">
        <div class="absolute inset-0 tech-grid opacity-100 pointer-events-none"></div>

        <!-- 2. PHP INCLUDE: HEADER -->
        <?php $pageTitle = 'Publish Your Ad'; include 'components/header.php'; ?>

        <div class="flex-1 overflow-y-auto relative z-10 p-4 md:p-8 lg:p-10 pb-20">
            
            <!-- Hero Banner -->
            <div class="relative w-full h-48 rounded-2xl overflow-hidden mb-10 border border-neutral-200 dark:border-white/10">
                <!-- Placeholder Image from Unsplash -->
                <img src="https://images.unsplash.com/photo-1557683316-973673baf926?q=80&w=2000&auto=format&fit=crop" class="w-full h-full object-cover opacity-30">
                <div class="absolute inset-0 bg-gradient-to-r from-brand-900/90 to-black/50 flex flex-col justify-center px-8">
                    <h2 class="text-3xl font-extrabold text-white mb-2">Promote on ShortMyLink</h2>
                    <p class="text-brand-100 text-sm max-w-xl">Reach thousands of users instantly before they redirect. High conversion, low cost inventory available now.</p>
                </div>
            </div>

            <!-- Pricing Cards (Horizontal Scroll) -->
            <div class="flex overflow-x-auto gap-4 mb-10 pb-4 no-scrollbar">
                <div onclick="selectRate('VIDEO', 3.00)" class="rate-card min-w-[160px] glass-panel p-4 rounded-xl cursor-pointer hover:border-brand-500 transition-colors group">
                    <div class="text-xs font-bold text-neutral-500 uppercase mb-2">Video (15s)</div>
                    <div class="text-2xl font-bold text-neutral-900 dark:text-white font-mono group-hover:text-brand-500">$3.00</div>
                    <div class="text-[10px] text-neutral-400">CPM (1k Views)</div>
                </div>
                <div onclick="selectRate('INTERSTITIAL', 2.50)" class="rate-card min-w-[160px] glass-panel p-4 rounded-xl cursor-pointer hover:border-brand-500 transition-colors group">
                    <div class="text-xs font-bold text-neutral-500 uppercase mb-2">Interstitial</div>
                    <div class="text-2xl font-bold text-neutral-900 dark:text-white font-mono group-hover:text-brand-500">$2.50</div>
                    <div class="text-[10px] text-neutral-400">CPM (1k Views)</div>
                </div>
                <div onclick="selectRate('BANNER', 1.50)" class="rate-card min-w-[160px] glass-panel p-4 rounded-xl cursor-pointer hover:border-brand-500 transition-colors group">
                    <div class="text-xs font-bold text-neutral-500 uppercase mb-2">Banner</div>
                    <div class="text-2xl font-bold text-neutral-900 dark:text-white font-mono group-hover:text-brand-500">$1.50</div>
                    <div class="text-[10px] text-neutral-400">CPM (1k Views)</div>
                </div>
                <div onclick="selectRate('NATIVE', 1.00)" class="rate-card min-w-[160px] glass-panel p-4 rounded-xl cursor-pointer hover:border-brand-500 transition-colors group">
                    <div class="text-xs font-bold text-neutral-500 uppercase mb-2">Native</div>
                    <div class="text-2xl font-bold text-neutral-900 dark:text-white font-mono group-hover:text-brand-500">$1.00</div>
                    <div class="text-[10px] text-neutral-400">CPM (1k Views)</div>
                </div>
                <div onclick="selectRate('REDIRECT', 0.05, true)" class="rate-card min-w-[160px] glass-panel p-4 rounded-xl cursor-pointer hover:border-brand-500 transition-colors group border-brand-500/30 bg-brand-50/5">
                    <div class="text-xs font-bold text-brand-500 uppercase mb-2">Redirect</div>
                    <div class="text-2xl font-bold text-neutral-900 dark:text-white font-mono group-hover:text-brand-500">$0.05</div>
                    <div class="text-[10px] text-neutral-400">CPC (Per Click)</div>
                </div>
            </div>

            <!-- Form Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- LEFT: Inputs -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Section 1: Details -->
                    <div class="glass-panel p-6 rounded-2xl">
                        <h3 class="font-bold text-lg text-neutral-900 dark:text-white mb-6">Campaign Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 uppercase mb-2">Advertiser Name</label>
                                <input type="text" id="adName" class="w-full input-field rounded-xl p-3 text-sm" placeholder="Company or Brand Name">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 uppercase mb-2">Contact Email</label>
                                <input type="email" id="adContact" class="w-full input-field rounded-xl p-3 text-sm" placeholder="marketing@brand.com">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 uppercase mb-2">Destination URL</label>
                                <input type="url" id="adUrl" class="w-full input-field rounded-xl p-3 text-sm" placeholder="https://your-landing-page.com">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 uppercase mb-2">Ad Format</label>
                                <select id="adType" onchange="updateCalculator()" class="w-full input-field rounded-xl p-3 text-sm">
                                    <option value="VIDEO" data-rate="3.00">Video Ad ($3.00 CPM)</option>
                                    <option value="INTERSTITIAL" data-rate="2.50">Interstitial ($2.50 CPM)</option>
                                    <option value="BANNER" data-rate="1.50" selected>Banner Ad ($1.50 CPM)</option>
                                    <option value="NATIVE" data-rate="1.00">Native Ad ($1.00 CPM)</option>
                                    <option value="REDIRECT" data-rate="0.05" data-cpc="true">Sponsored Redirect ($0.05 CPC)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Budget -->
                    <div class="glass-panel p-6 rounded-2xl">
                        <h3 class="font-bold text-lg text-neutral-900 dark:text-white mb-6">Targeting & Budget</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 uppercase mb-2">Countries</label>
                                <select multiple class="w-full input-field rounded-xl p-2 text-sm h-24 custom-scroll">
                                    <option value="ALL" selected>All Countries (Global)</option>
                                    <option value="US">United States</option>
                                    <option value="IN">India</option>
                                    <option value="GB">United Kingdom</option>
                                    <option value="CA">Canada</option>
                                </select>
                                <p class="text-[10px] text-neutral-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 uppercase mb-2">Campaign Budget ($)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3 text-neutral-500">$</span>
                                    <input type="number" id="adBudget" oninput="updateCalculator()" class="w-full input-field rounded-xl p-3 pl-8 text-sm font-bold" placeholder="15.00" min="5">
                                </div>
                                <p class="text-[10px] text-brand-600 mt-1" id="min-spend-msg">Minimum spend: $5.00</p>
                            </div>
                        </div>

                        <!-- Adult Toggle -->
                        <div class="p-4 rounded-xl bg-neutral-50 dark:bg-white/5 border border-neutral-200 dark:border-white/10 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-sm text-neutral-900 dark:text-white">18+ Adult Content?</span>
                                <p class="text-xs text-neutral-500">Requires strict admin approval.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="adultContent" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Section 3: Upload -->
                    <div class="glass-panel p-6 rounded-2xl">
                        <h3 class="font-bold text-lg text-neutral-900 dark:text-white mb-4">Ad Creative</h3>
                        <label class="upload-area w-full h-32 rounded-xl flex flex-col items-center justify-center cursor-pointer">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl text-neutral-400 mb-3"></i>
                                <p class="text-sm text-neutral-500"><span class="font-bold">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-neutral-400">SVG, PNG, JPG or MP4 (MAX. 30MB)</p>
                            </div>
                            <input id="adFile" type="file" class="hidden" accept="image/*,video/*" onchange="previewFile()" />
                        </label>
                    </div>

                </div>

                <!-- RIGHT: Summary (Sticky) -->
                <div class="lg:col-span-1">
                    <div class="sticky top-6 space-y-6">
                        
                        <!-- Preview Box -->
                        <div class="glass-panel p-4 rounded-2xl">
                            <h4 class="text-xs font-bold text-neutral-500 uppercase mb-3">Creative Preview</h4>
                            <div class="rounded-lg overflow-hidden bg-black aspect-video border border-neutral-200 dark:border-white/10 relative group">
                                <img id="preview-img" src="https://images.unsplash.com/photo-1557683316-973673baf926?q=80&w=2000&auto=format&fit=crop" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                                <video id="preview-video" class="hidden w-full h-full object-cover" controls></video>
                                <div class="absolute top-2 right-2 bg-black/60 px-2 py-1 rounded text-[10px] text-white font-bold">Ad</div>
                            </div>
                        </div>

                        <!-- Cost Calculator -->
                        <div class="glass-panel p-6 rounded-2xl border-t-4 border-brand-500">
                            <h3 class="font-bold text-lg text-neutral-900 dark:text-white mb-4">Estimated Reach</h3>
                            
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-neutral-500">Ad Type</span>
                                <span id="calc-type" class="text-sm font-bold dark:text-white">Banner</span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-neutral-500">Rate</span>
                                <span id="calc-rate" class="text-sm font-mono dark:text-white">$1.50 / 1k</span>
                            </div>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-sm text-neutral-500">Budget</span>
                                <span id="calc-budget" class="text-sm font-mono font-bold text-brand-500">$0.00</span>
                            </div>

                            <div class="bg-neutral-50 dark:bg-white/5 p-4 rounded-xl text-center mb-6">
                                <div class="text-xs text-neutral-500 uppercase font-bold mb-1" id="reach-label">Est. Impressions</div>
                                <div id="calc-result" class="text-3xl font-extrabold text-neutral-900 dark:text-white font-mono">0</div>
                                <div id="calc-inr" class="text-[10px] text-neutral-400 mt-1">≈ ₹0</div>
                            </div>

                            <div class="flex items-start gap-2 mb-6">
                                <input type="checkbox" id="toc" class="mt-1 accent-brand-500">
                                <label for="toc" class="text-xs text-neutral-500">I confirm I own the ad content and accept <a href="#" class="text-brand-500 hover:underline">ShortMyLink Policies</a>.</label>
                            </div>

                            <button onclick="submitAd()" id="submitBtn" class="w-full py-3 rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-400 text-white font-bold shadow-lg shadow-brand-500/20 transition-all transform hover:-translate-y-0.5">
                                Submit for Review
                            </button>
                            <p class="text-[10px] text-center text-neutral-500 mt-3">Payment required upon approval.</p>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </main>

    <!-- Global Toast Container -->
    <div id="toast-container" class="fixed bottom-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none"></div>

    <!-- GSAP for Animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    
    <!-- Shared UI Scripts -->
    <script src="js/ui.js"></script>

    <script>
        // CONFIG
        const RATES = {
            'VIDEO': 3.00,
            'INTERSTITIAL': 2.50,
            'BANNER': 1.50,
            'NATIVE': 1.00,
            'REDIRECT': 0.05
        };
        const USD_TO_INR = 84; 

        document.addEventListener("DOMContentLoaded", () => {
            const username = localStorage.getItem('current_username');
            
            // Note: PHP header handles redirect if session missing, but this JS check adds double safety
            if(!username) window.location.href = 'login.html';
            
            // GSAP Entry Animation
            gsap.from(".glass-panel", { y: 20, opacity: 0, duration: 0.6, stagger: 0.1, ease: "power2.out", delay: 0.1 });
        });

        // --- 1. LIVE CALCULATOR ---
        function updateCalculator() {
            const typeSelect = document.getElementById('adType');
            const budgetInput = document.getElementById('adBudget');
            const selectedOption = typeSelect.options[typeSelect.selectedIndex];
            
            const typeName = selectedOption.text.split(' ')[0];
            const rate = parseFloat(selectedOption.getAttribute('data-rate'));
            const isCpc = selectedOption.getAttribute('data-cpc') === 'true';
            const budget = parseFloat(budgetInput.value) || 0;

            // Update Text
            document.getElementById('calc-type').innerText = typeName;
            document.getElementById('calc-rate').innerText = isCpc ? `$${rate} / Click` : `$${rate} / 1k Views`;
            document.getElementById('calc-budget').innerText = `$${budget.toFixed(2)}`;
            document.getElementById('reach-label').innerText = isCpc ? "Est. Clicks" : "Est. Impressions";

            // Calc Logic
            let reach = 0;
            if(budget > 0) {
                if(isCpc) {
                    reach = Math.floor(budget / rate);
                } else {
                    reach = Math.floor((budget / rate) * 1000);
                }
            }

            // Render Numbers
            document.getElementById('calc-result').innerText = reach.toLocaleString();
            document.getElementById('calc-inr').innerText = `≈ ₹${(budget * USD_TO_INR).toFixed(0)}`;

            // Validation Styling
            const minMsg = document.getElementById('min-spend-msg');
            if(budget > 0 && budget < 5) {
                minMsg.classList.remove('text-brand-600');
                minMsg.classList.add('text-red-500', 'font-bold');
            } else {
                minMsg.classList.add('text-brand-600');
                minMsg.classList.remove('text-red-500', 'font-bold');
            }
        }

        // Helper to click cards to select dropdown
        function selectRate(value, rate) {
            const select = document.getElementById('adType');
            select.value = value;
            updateCalculator();
            if(window.innerWidth < 1024) {
                document.getElementById('adType').scrollIntoView({behavior: 'smooth'});
            }
        }

        // --- 2. FILE PREVIEW ---
        function previewFile() {
            const file = document.getElementById('adFile').files[0];
            const imgPreview = document.getElementById('preview-img');
            const vidPreview = document.getElementById('preview-video');

            if (file) {
                const url = URL.createObjectURL(file);
                if (file.type.startsWith('image/')) {
                    imgPreview.src = url;
                    imgPreview.classList.remove('hidden');
                    vidPreview.classList.add('hidden');
                } else if (file.type.startsWith('video/')) {
                    vidPreview.src = url;
                    vidPreview.classList.remove('hidden');
                    imgPreview.classList.add('hidden');
                }
            }
        }

        // --- 3. SUBMIT LOGIC ---
        async function submitAd() {
            const budget = parseFloat(document.getElementById('adBudget').value);
            const toc = document.getElementById('toc').checked;
            const name = document.getElementById('adName').value;
            const contact = document.getElementById('adContact').value;
            const url = document.getElementById('adUrl').value;
            const type = document.getElementById('adType').value;
            const fileInput = document.getElementById('adFile');
            const username = localStorage.getItem('current_username');

            // 1. Validation
            if (!username) return window.location.href = 'login.html';
            if (!name || !contact || !url) return showToast('error', 'Please fill all fields');
            if (budget < 5) return showToast('error', 'Minimum budget is $5.00');
            if (!toc) return showToast('error', 'Please accept terms & policies');
            if (fileInput.files.length === 0) return showToast('error', 'Please upload an ad creative');

            // 2. Prepare Data
            const formData = new FormData();
            formData.append('username', username);
            formData.append('name', name);
            formData.append('email', contact);
            formData.append('url', url);
            formData.append('type', type);
            formData.append('budget', budget);
            formData.append('is_adult', document.getElementById('adultContent').checked ? 1 : 0);
            formData.append('creative', fileInput.files[0]); 
            
            // Gather Countries
            const countries = Array.from(document.querySelector('select[multiple]').selectedOptions).map(o => o.value).join(',');
            formData.append('countries', countries);

            // 3. UI Loading State
            const btn = document.getElementById('submitBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Uploading...';
            btn.disabled = true;

            // 4. Send Request
            try {
                const res = await fetch('api/publish_ad.php', {
                    method: 'POST',
                    body: formData // Content-Type auto-set for FormData
                });
                const data = await res.json();

                if (data.status === 'success') {
                    // Alert & Redirect using ui.js confirmAction if available, else standard alert
                    if (typeof confirmAction === 'function') {
                        confirmAction({
                            title: 'Campaign Submitted',
                            text: 'Your ad (ID #' + data.campaign_id + ') is now pending review. You will be notified via email once approved for payment.',
                            isDanger: false,
                            btnText: 'View Dashboard'
                        }, () => {
                            window.location.href = 'dashboard.php';
                        });
                    } else {
                        alert('Campaign Submitted! Redirecting...');
                        window.location.href = 'dashboard.php';
                    }
                } else {
                    showToast('error', data.message || 'Submission failed');
                }
            } catch (e) {
                console.error(e);
                showToast('error', 'Network or Server Error');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>