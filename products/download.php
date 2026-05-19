<?php
/**
 * Product Download/Download Page
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once INCLUDES_PATH . '/functions.php';

if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    redirect(BASE_URL . '/products/');
}

$slug = sanitize($_GET['slug']);
$product = getProduct($slug);

if (!$product) {
    setFlash('error', 'Product not found');
    redirect(BASE_URL . '/products/');
}

if ($product['is_free'] === 'yes') {
    incrementDownloadCount($product['id']);
    
    if (!empty($product['file_path'])) {
        header('Location: ' . $product['file_path']);
        exit;
    } else {
        setFlash('error', 'Download not available yet. Contact support.');
        redirect(BASE_URL . '/products/');
    }
} else {
    setFlash('info', 'This is a paid product. Payment integration coming soon.');
    redirect(BASE_URL . '/products/');
}