<?php
/**
 * AJ OS Configuration
 * Database and site settings
 */

// Prevent direct access
define('AJOS_INIT', true);
if (!defined('AJOS_INIT')) {
    die('Direct access not allowed');
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'just_aj');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site URL
define('SITE_URL', 'http://localhost/just_aj');
define('BASE_URL', 'http://localhost/just_aj');

// Site Paths
define('ROOT_PATH', dirname(__DIR__) . '/');
define('INCLUDES_PATH', __DIR__ . '/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// Timezone
date_default_timezone_set('UTC');

// Error Reporting (enable for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . 'error.log');

// CSRF Token
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}