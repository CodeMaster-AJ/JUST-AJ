<?php
/**
 * Admin Header
 */

if (!defined('AJOS_INIT')) {
    die('Direct access not allowed');
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
requireLogin();

$currentAdmin = getCurrentAdmin();
$siteName = getSetting('site_name', 'AJ OS');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' . $siteName : $siteName . ' Admin'; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <?php include INCLUDES_PATH . 'admin-sidebar.php'; ?>
        <div class="admin-main">
            <header class="admin-header">
                <div class="admin-header-left">
                    <button class="sidebar-toggle" aria-label="Toggle sidebar">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <h1><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h1>
                </div>
                <div class="admin-header-right">
                    <span class="admin-user"><?php echo htmlspecialchars($currentAdmin['name']); ?></span>
                    <a href="<?php echo SITE_URL; ?>/admin/logout.php" class="btn-logout">Logout</a>
                </div>
            </header>
            <div class="admin-content">