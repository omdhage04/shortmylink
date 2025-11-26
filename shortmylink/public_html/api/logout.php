<?php
header('Content-Type: application/json');

// --- LOGOUT ENDPOINT ---
// Clears session and logs out the user

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // If using server sessions, destroy them here
        session_start();
        session_destroy();
        
        // Log the logout event (optional)
        error_log("User logged out at " . date('Y-m-d H:i:s'));
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode([
            'status' => 'success',
            'message' => 'Logged out'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>
