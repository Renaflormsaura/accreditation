<?php
require_once '../connection.php';

// Start session if not already started
startSecureSession();

// Get user info for logging
$userId = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? null;

// Clear session token from database if exists
if (isset($_SESSION['session_token']) && $userId) {
    try {
        $stmt = $db->prepare("DELETE FROM user_sessions WHERE user_id = ? AND session_token = ?");
        $stmt->execute([$userId, $_SESSION['session_token']]);
    } catch (Exception $e) {
        error_log('Logout database error: ' . $e->getMessage());
    }
}

// Clear remember me cookie and token
if (isset($_COOKIE['remember_token']) && $userId) {
    try {
        $stmt = $db->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
        $stmt->execute([$userId]);
        
        // Clear the cookie
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    } catch (Exception $e) {
        error_log('Logout remember token error: ' . $e->getMessage());
    }
}

// Log the logout
if ($username) {
    error_log("User logged out: {$username} (ID: {$userId})");
}

// Destroy the session
session_unset();
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to login page
header('Location: login.php?logged_out=1');
exit();
?>