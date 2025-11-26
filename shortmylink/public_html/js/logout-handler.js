/**
 * Shared Logout Handler
 * Include this script in all dashboard-related pages to add logout functionality
 * Usage: <script src="js/logout-handler.js"></script>
 */

function initLogout() {
    const logoutBtn = document.getElementById('logout-btn');
    if(logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            if(confirm('Are you sure you want to logout?')) {
                fetch('api/logout.php', { method: 'POST' })
                    .then(() => {
                        localStorage.removeItem('current_username');
                        localStorage.removeItem('user_role');
                        localStorage.removeItem('api_token');
                        window.location.href = 'login.html';
                    })
                    .catch(err => {
                        console.error('Logout error:', err);
                        // Clear local storage anyway and redirect
                        localStorage.removeItem('current_username');
                        localStorage.removeItem('user_role');
                        localStorage.removeItem('api_token');
                        window.location.href = 'login.html';
                    });
            }
        });
    }
}

// Initialize on DOM ready
if(document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLogout);
} else {
    initLogout();
}
