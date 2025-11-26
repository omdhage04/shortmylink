/**
 * ui.js - Global UI Interactions for ShortMyLink
 * Handles Toasts and Confirmation Modals automatically.
 */

document.addEventListener("DOMContentLoaded", () => {
    injectUiElements();
});

function injectUiElements() {
    // 1. Create Toast Container if not exists
    if (!document.getElementById('toast-container')) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed bottom-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none';
        document.body.appendChild(container);
    }
    function logout() {
    // Use the Global Confirm Modal we built earlier
    confirmAction({
        title: 'Log Out?',
        text: 'You will be redirected to the login page.',
        isDanger: true, // Makes the button red
        btnText: 'Log Out'
    }, () => {
        // 1. Clear Local Storage
        localStorage.removeItem('current_username');
        localStorage.removeItem('user_role');
        
        // 2. Clear Auth Cookie
        document.cookie = "shortmylink_auth=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

        // 3. Redirect
        window.location.href = 'index.html'; // or login.html
    });
}
    // 2. Create Global Modal if not exists
    if (!document.getElementById('global-modal')) {
        const modalHtml = `
        <div id="global-modal" class="fixed inset-0 z-[100] hidden">
            <!-- Backdrop -->
            <div id="modal-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity opacity-0" onclick="closeModal()"></div>
            
            <!-- Card -->
            <div id="modal-card" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm p-6 bg-white dark:bg-[#15171c] rounded-2xl border border-neutral-200 dark:border-white/10 shadow-2xl transform scale-95 opacity-0 transition-all duration-300">
                <div id="modal-icon-bg" class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 transition-colors">
                    <i id="modal-icon" class="text-xl"></i>
                </div>
                
                <div class="text-center mb-6">
                    <h3 id="modal-title" class="text-lg font-bold text-neutral-900 dark:text-white mb-1"></h3>
                    <p id="modal-desc" class="text-sm text-neutral-500 dark:text-neutral-400"></p>
                </div>

                <div class="flex gap-3">
                    <button onclick="closeModal()" class="flex-1 py-2.5 text-sm font-bold text-neutral-600 dark:text-neutral-300 bg-neutral-100 dark:bg-white/5 hover:bg-neutral-200 dark:hover:bg-white/10 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button id="modal-confirm-btn" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl shadow-lg transition-all hover:-translate-y-0.5">
                        Confirm
                    </button>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
}

// --- GLOBAL FUNCTION: SHOW TOAST ---
window.showToast = function(type, message) {
    const container = document.getElementById('toast-container');
    
    // Config
    const isSuccess = type === 'success';
    const styles = isSuccess 
        ? 'border-green-500/50 bg-green-50/90 dark:bg-green-900/20 text-green-700 dark:text-green-400' 
        : 'border-red-500/50 bg-red-50/90 dark:bg-red-900/20 text-red-700 dark:text-red-400';
    const icon = isSuccess ? '<i class="fa-solid fa-check-circle"></i>' : '<i class="fa-solid fa-circle-exclamation"></i>';

    const toast = document.createElement('div');
    toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl border backdrop-blur-md shadow-lg transform translate-y-10 opacity-0 transition-all duration-500 ${styles}`;
    toast.innerHTML = `<div class="text-lg">${icon}</div><div class="text-sm font-bold">${message}</div>`;

    container.appendChild(toast);

    requestAnimationFrame(() => toast.classList.remove('translate-y-10', 'opacity-0'));
    setTimeout(() => {
        toast.classList.add('translate-y-10', 'opacity-0');
        setTimeout(() => toast.remove(), 500);
    }, 3000);
};

// --- GLOBAL FUNCTION: CONFIRM ACTION ---
window.confirmAction = function(options, onConfirm) {
    const modal = document.getElementById('global-modal');
    const backdrop = document.getElementById('modal-backdrop');
    const card = document.getElementById('modal-card');
    
    // 1. Setup Content
    document.getElementById('modal-title').innerText = options.title || 'Are you sure?';
    document.getElementById('modal-desc').innerText = options.text || 'This action cannot be undone.';
    
    // 2. Setup Style (Danger vs Info)
    const iconBg = document.getElementById('modal-icon-bg');
    const icon = document.getElementById('modal-icon');
    const btn = document.getElementById('modal-confirm-btn');

    if (options.isDanger) {
        iconBg.className = 'w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 bg-red-100 dark:bg-red-900/20 text-red-500';
        icon.className = 'fa-solid fa-triangle-exclamation text-xl';
        btn.className = 'flex-1 py-2.5 text-sm font-bold text-white rounded-xl shadow-lg transition-all hover:-translate-y-0.5 bg-red-500 hover:bg-red-600 shadow-red-500/30';
        btn.innerText = options.btnText || 'Yes, Delete';
    } else {
        iconBg.className = 'w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 bg-blue-100 dark:bg-blue-900/20 text-blue-500';
        icon.className = 'fa-solid fa-circle-info text-xl';
        btn.className = 'flex-1 py-2.5 text-sm font-bold text-white rounded-xl shadow-lg transition-all hover:-translate-y-0.5 bg-brand-600 hover:bg-brand-700 shadow-brand-500/30';
        btn.innerText = options.btnText || 'Confirm';
    }

    // 3. Setup Logic
    // Remove old listeners to prevent stacking
    const newBtn = btn.cloneNode(true);
    btn.parentNode.replaceChild(newBtn, btn);
    
    newBtn.onclick = function() {
        onConfirm(newBtn); // Pass button to callback for loading state
    };

    // 4. Show Modal
    modal.classList.remove('hidden');
    setTimeout(() => {
        backdrop.classList.remove('opacity-0');
        card.classList.remove('scale-95', 'opacity-0');
    }, 10);
};

// --- GLOBAL FUNCTION: CLOSE MODAL ---
window.closeModal = function() {
    const modal = document.getElementById('global-modal');
    if(!modal) return;
    
    const backdrop = document.getElementById('modal-backdrop');
    const card = document.getElementById('modal-card');
    
    backdrop.classList.add('opacity-0');
    card.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => modal.classList.add('hidden'), 300);
};