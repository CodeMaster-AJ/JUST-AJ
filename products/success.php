<?php
/**
 * Payment Success Page
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/razorpay.php';

$orderId = sanitize($_GET['order'] ?? '');

if (empty($orderId)) {
    redirect(BASE_URL . '/products/');
}

$order = getOrderByRazorpayId($orderId);

if (!$order || $order['status'] !== 'paid') {
    setFlash('error', 'Invalid order or payment not completed');
    redirect(BASE_URL . '/products/');
}

$pageTitle = 'Payment Successful';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .success-container {
            max-width: 600px;
            margin: 0 auto;
            padding: var(--spacing-8);
            text-align: center;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(34, 197, 94, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--spacing-6);
            color: #22c55e;
            font-size: 36px;
        }
        
        .success-card {
            background: var(--color-gray-800);
            border-radius: var(--border-radius-lg);
            padding: var(--spacing-8);
            border: 1px solid var(--color-gray-700);
        }
        
        .success-card h1 {
            font-size: var(--font-size-2xl);
            margin-bottom: var(--spacing-2);
        }
        
        .success-card p {
            color: var(--color-gray-400);
            margin-bottom: var(--spacing-6);
        }
        
        .order-details {
            background: var(--color-gray-700);
            border-radius: var(--border-radius);
            padding: var(--spacing-4);
            margin-bottom: var(--spacing-6);
            text-align: left;
        }
        
        .order-details h3 {
            font-size: var(--font-size-sm);
            color: var(--color-gray-500);
            margin-bottom: var(--spacing-3);
        }
        
        .order-details p {
            margin-bottom: var(--spacing-2);
            color: var(--color-gray-300);
        }
        
        .btn-download {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-2);
            padding: var(--spacing-4) var(--spacing-8);
            font-size: var(--font-size-lg);
        }
    </style>
</head>
<body>
    <?php include INCLUDES_PATH . '/header.php'; ?>

    <main class="main-content">
        <div class="success-container">
            <div class="success-card">
                <div class="success-icon">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h1>Payment Successful!</h1>
                <p>Thank you for your purchase. Your download is ready.</p>
                
                <div class="order-details">
                    <h3>Order Details</h3>
                    <p><strong>Product:</strong> <?php echo htmlspecialchars($order['product_name']); ?></p>
                    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['razorpay_order_id']); ?></p>
                    <p><strong>Amount:</strong> ₹<?php echo number_format($order['amount'], 2); ?></p>
                    <p><strong>Payment ID:</strong> <?php echo htmlspecialchars($order['payment_id']); ?></p>
                </div>
                
                <?php if (!empty($order['file_path'])): ?>
                    <a href="<?php echo BASE_URL; ?>/products/download.php?order=<?php echo htmlspecialchars($order['razorpay_order_id']); ?>" 
                       class="btn btn-primary btn-download">
                        <i class="fa-solid fa-download"></i>
                        Download Now
                    </a>
                <?php else: ?>
                    <div style="padding: var(--spacing-4); background: rgba(251, 191, 36, 0.1); border-radius: var(--border-radius); margin-bottom: var(--spacing-4);">
                        <p style="color: #fbbf24; margin: 0;">
                            <i class="fa-solid fa-info-circle"></i>
                            Download not available yet. We'll email you the download link.
                        </p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/products/" class="btn btn-secondary">
                        Browse More Products
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>