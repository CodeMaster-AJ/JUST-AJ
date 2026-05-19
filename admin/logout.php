<?php
/**
 * Admin Logout
 */
define('AJOS_INIT', true);

require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

logoutAdmin();
setFlash('success', 'You have been logged out.');
redirect(SITE_URL . '/admin/login.php');