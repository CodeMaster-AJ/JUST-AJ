<?php
/**
 * Product Download Handler
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/razorpay.php';

if (isset($_GET['order'])) {
    // Download via order verification (for paid products)
    $orderId = sanitize($_GET['order']);
    $order = getOrderByRazorpayId($orderId);
    
    if (!$order) {
        setFlash('error', 'Invalid order');
        redirect(BASE_URL . '/products/');
    }
    
    // Verify payment is completed
    if ($order['status'] !== 'paid') {
        setFlash('error', 'Payment not completed for this order');
        redirect(BASE_URL . '/products/checkout.php?slug=' . getProductSlug($order['product_id']));
    }
    
    // Check if free or paid product
    if ($order['is_free'] ?? false) {
        incrementDownloadCount($order['product_id']);
    }
    
    // Redirect to file or show download page
    if (!empty($order['file_path'])) {
        incrementDownloadCount($order['product_id']);
        header('Location: ' . $order['file_path']);
        exit;
    } else {
        setFlash('info', 'Download link not available. Please check your email.');
        redirect(BASE_URL . '/products/');
    }
    
} elseif (isset($_GET['slug'])) {
    // Direct download for free products (backward compatibility)
    $slug = sanitize($_GET['slug']);
    $product = getProduct($slug);
    
    if (!$product) {
        setFlash('error', 'Product not found');
        redirect(BASE_URL . '/products/');
    }
    
    if ($product['is_free'] !== 'yes') {
        setFlash('info', 'This is a paid product. Please purchase first.');
        redirect(BASE_URL . '/products/checkout.php?slug=' . $slug);
    }
    
    if (!empty($product['file_path'])) {
        incrementDownloadCount($product['id']);
        header('Location: ' . $product['file_path']);
        exit;
    } else {
        setFlash('error', 'Download not available yet. Contact support.');
        redirect(BASE_URL . '/products/');
    }
    
} else {
    redirect(BASE_URL . '/products/');
}

/**
 * Get product slug by ID
 */
function getProductSlug($productId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT slug FROM products WHERE id = ?');
    $stmt->execute([$productId]);
    $result = $stmt->fetch();
    return $result['slug'] ?? '';
}