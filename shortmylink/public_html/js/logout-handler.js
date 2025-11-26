/**
 * Shared Logout Handler
 * Include this script in all dashboard-related pages to add logout functionality
 * Usage: <script src="js/logout-handler.js"></script>
 */

function initLogout() {
    // Expose a global performLogout() so other UI modules can call it (e.g. ui.js)
    window.performLogout = function(redirectTo = 'login.html') {
        // If the UI module provides a unified logout helper, prefer it
        if (typeof window.uiLogout === 'function') {
            // uiLogout will show confirmation and handle redirect/cleanup
            try {
                window.uiLogout();
            } catch (e) {
                // fallback to internal flow if uiLogout fails
            }
            return;
        }

        const doLogout = () => {
            fetch('api/logout.php', { method: 'POST' }).catch(() => {});
            localStorage.removeItem('current_username');
            localStorage.removeItem('user_role');
            localStorage.removeItem('api_token');
            // clear auth cookie as well
            document.cookie = "shortmylink_auth=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            window.location.href = redirectTo;
        };

        // Prefer the global confirm modal if available
        if (window.confirmAction) {
            window.confirmAction({
                title: 'Log Out?',
                text: 'You will be redirected to the login page.',
                isDanger: true,
                btnText: 'Log Out'
            }, () => doLogout());
        } else if (confirm('Are you sure you want to logout?')) {
            doLogout();
        }
    };

    // Attach to any element with .logout-btn class
    const nodes = document.querySelectorAll('.logout-btn');
    nodes.forEach(n => n.addEventListener('click', (e) => {
        e.preventDefault();
        // If uiLogout exists prefer it (it already shows confirmation)
        if (typeof window.uiLogout === 'function') {
            try { window.uiLogout(); } catch (err) { window.performLogout(); }
            return;
        }
        window.performLogout();
    }));
}

// Initialize on DOM ready
if(document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLogout);
} else {
    initLogout();
}
