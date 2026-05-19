<?php
/**
 * Authentication Helper
 */

if (!defined('AJOS_INIT')) {
    die('Direct access not allowed');
}

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Require login - redirect if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('error', 'Please login to access the admin area.');
        redirect(SITE_URL . '/admin/login.php');
    }
}

/**
 * Login admin user
 */
function loginAdmin($email, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    error_log("Login attempt - Email: $email, User found: " . ($user ? 'yes' : 'no'));
    error_log("Password hash in DB: " . ($user ? $user['password'] : 'none'));
    error_log("password_verify result: " . ($user && password_verify($password, $user['password']) ? 'true' : 'false'));
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_name'] = $user['name'];
        $_SESSION['admin_email'] = $user['email'];
        return true;
    }
    return false;
}

/**
 * Logout admin user
 */
function logoutAdmin() {
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_email']);
    session_destroy();
}

/**
 * Get current admin info
 */
function getCurrentAdmin() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['admin_id'],
            'name' => $_SESSION['admin_name'],
            'email' => $_SESSION['admin_email']
        ];
    }
    return null;
}

/**
 * Verify CSRF token
 */
function verifyCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF input field
 */
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}