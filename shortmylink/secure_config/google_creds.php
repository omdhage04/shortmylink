<?php
// -----------------------------
// Google OAuth Credentials
// -----------------------------

// Your Public Client ID
define('GOOGLE_CLIENT_ID', '1046973989585-kg5fd0so5e595b5qrvdamrgghj1jl387.apps.googleusercontent.com');

// Your Secret Key (Never share this publicly)
define('GOOGLE_CLIENT_SECRET', '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');

// 🚨 IMPORTANT: CHANGE 'yourdomain.com' TO YOUR ACTUAL WEBSITE DOMAIN 🚨
// This LINK must match EXACTLY what you put in the Google Cloud Console "Authorized Redirect URIs"
define('GOOGLE_REDIRECT_URL', 'https://yourdomain.com/api/google_auth.php'); 
?>