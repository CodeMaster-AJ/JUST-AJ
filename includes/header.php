<?php
/**
 * Public Header
 */

if (!defined('AJOS_INIT')) {
    die('Direct access not allowed');
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$siteName = getSetting('site_name', 'JUST AJ');
$siteTagline = getSetting('site_tagline', 'Building tools, content, and systems for creators, students, and founders.');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($siteTagline); ?>">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' . $siteName : $siteName; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="<?php echo SITE_URL; ?>" class="logo">
                <span class="logo-text"><?php echo htmlspecialchars($siteName); ?></span>
            </a>
            <nav class="main-nav">
                <ul class="nav-list">
                    <li><a href="<?php echo SITE_URL; ?>" class="<?php echo $currentPage === 'home' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/about.php" class="<?php echo $currentPage === 'about' ? 'active' : ''; ?>">About</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/blog/" class="<?php echo $currentPage === 'blog' ? 'active' : ''; ?>">Blog</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/tools/" class="<?php echo $currentPage === 'tools' ? 'active' : ''; ?>">Tools</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/projects.php" class="<?php echo $currentPage === 'projects' ? 'active' : ''; ?>">Projects</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/services.php" class="<?php echo $currentPage === 'services' ? 'active' : ''; ?>">Services</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/contact.php" class="<?php echo $currentPage === 'contact' ? 'active' : ''; ?>">Contact</a></li>
                </ul>
            </nav>
            <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary cta-btn">Work With Me</a>
            <button class="mobile-menu-toggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>
    <main class="main-content">