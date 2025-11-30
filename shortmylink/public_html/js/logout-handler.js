/**
 * Shared Logout Handler
 * Usage: <script src="js/logout-handler.js"></script>
 */

// 1. Define the global logout function immediately
window.logout = function(redirectTo = 'index.html') {
    
    // The actual logout logic
    const executeLogout = () => {
        // 1. Call backend API to destroy session
        fetch('api/logout.php', { method: 'POST' }).catch(err => console.error(err));

        // 2. Clear LocalStorage
        localStorage.removeItem('current_username');
        localStorage.removeItem('user_role');
        localStorage.removeItem('api_token');

        // 3. Clear Auth Cookie (Past expiration date)
        document.cookie = "shortmylink_auth=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

        // 4. Redirect
        window.location.href = redirectTo;
    };

    // 2. Check for UI helpers (Custom Confirm Modal)
    if (typeof window.confirmAction === 'function') {
        // Use the fancy modal from ui.js if available
        window.confirmAction({
            title: 'Log Out?',
            text: 'You will be returned to the home page.',
            isDanger: true,
            btnText: 'Yes, Log Out'
        }, () => executeLogout());
    } else {
        // Fallback to standard browser alert if ui.js isn't loaded
        if (confirm('Are you sure you want to logout?')) {
            executeLogout();
        }
    }
};

// 3. Alias for compatibility (in case other files use performLogout)
window.performLogout = window.logout;

// 4. Attach to any buttons with class ".logout-btn" automatically
document.addEventListener('DOMContentLoaded', () => {
    const nodes = document.querySelectorAll('.logout-btn');
    nodes.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            window.logout();
        });
    });
});