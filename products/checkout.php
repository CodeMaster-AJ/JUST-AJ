<?php
/**
 * Product Checkout Page
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/razorpay.php';

if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    redirect(BASE_URL . '/products/');
}

$slug = sanitize($_GET['slug']);
$product = getProduct($slug);

if (!$product) {
    setFlash('error', 'Product not found');
    redirect(BASE_URL . '/products/');
}

// Check if already purchased (for logged in users, or check by email)
$purchased = false;
if (isset($_SESSION['user_email'])) {
    $purchased = isProductPurchased($product['id'], $_SESSION['user_email']);
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $product['is_free'] === 'no') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    
    if (empty($errors)) {
        try {
            $orderData = createProductOrder($product, $name, $email);
            
            // Store customer info in session for verification
            $_SESSION['checkout_order_id'] = $orderData['razorpay_order_id'];
            $_SESSION['checkout_product_id'] = $product['id'];
            $_SESSION['checkout_email'] = $email;
            $_SESSION['checkout_name'] = $name;
            
            // Return JSON for AJAX
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'razorpay_order_id' => $orderData['razorpay_order_id'],
                'amount' => $orderData['amount'],
                'currency' => $orderData['currency']
            ]);
            exit;
            
        } catch (Exception $e) {
            $errors[] = 'Payment error: ' . $e->getMessage();
        }
    }
}

// Handle AJAX request for free products
if ($product['is_free'] === 'yes' && isset($_POST['action']) && $_POST['action'] === 'free_download') {
    $email = sanitize($_POST['email'] ?? '');
    $name = sanitize($_POST['name'] ?? 'Valued Customer');
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Valid email is required']);
        exit;
    }
    
    // Create order for free product (already marked as paid)
    try {
        $orderData = createProductOrder($product, $name, $email);
        
        // Send download email
        $downloadUrl = BASE_URL . '/products/download.php?order=' . $orderData['razorpay_order_id'];
        
        if (!empty($product['file_path'])) {
            sendDownloadEmail($email, $name, $product['name'], $downloadUrl, false);
        }
        
        // Return download URL
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'download_url' => $downloadUrl,
            'email_sent' => !empty($product['file_path'])
        ]);
        exit;
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

$pageTitle = 'Checkout | ' . $product['name'];
$siteName = getSetting('site_name', 'JUST AJ');
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
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        .checkout-container {
            max-width: 600px;
            margin: 0 auto;
            padding: var(--spacing-8);
        }
        
        .product-summary {
            background: var(--color-gray-800);
            border-radius: var(--border-radius-lg);
            padding: var(--spacing-6);
            margin-bottom: var(--spacing-6);
            border: 1px solid var(--color-gray-700);
        }
        
        .product-summary h2 {
            font-size: var(--font-size-xl);
            margin-bottom: var(--spacing-4);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-4);
        }
        
        .product-price {
            font-size: var(--font-size-2xl);
            font-weight: 700;
            color: #22c55e;
        }
        
        .checkout-form {
            background: var(--color-gray-800);
            border-radius: var(--border-radius-lg);
            padding: var(--spacing-6);
            border: 1px solid var(--color-gray-700);
        }
        
        .checkout-form h3 {
            font-size: var(--font-size-lg);
            margin-bottom: var(--spacing-6);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-4);
        }
        
        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
        
        .btn-buy {
            width: 100%;
            padding: var(--spacing-4);
            font-size: var(--font-size-lg);
            margin-top: var(--spacing-4);
        }
        
        .secure-badge {
            display: flex;
            align-items: center;
            gap: var(--spacing-2);
            color: var(--color-gray-500);
            font-size: var(--font-size-sm);
            margin-top: var(--spacing-4);
            justify-content: center;
        }
        
        .already-purchased {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #22c55e;
            padding: var(--spacing-4);
            border-radius: var(--border-radius);
            text-align: center;
        }
        
        .already-purchased a {
            color: var(--color-white);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include INCLUDES_PATH . '/header.php'; ?>

    <main class="main-content">
        <div class="checkout-container">
            <a href="<?php echo BASE_URL; ?>/products/" class="back-link" style="color: var(--color-gray-400); margin-bottom: var(--spacing-4); display: inline-block;">
                <i class="fa-solid fa-arrow-left"></i> Back to Products
            </a>
            
            <div class="product-summary">
                <img src="<?php echo htmlspecialchars($product['preview_image'] ?? 'https://via.placeholder.com/600x300/171717/ffffff?text=Product'); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p style="color: var(--color-gray-400); margin-bottom: var(--spacing-4);">
                    <?php echo htmlspecialchars($product['description'] ?? ''); ?>
                </p>
                <div class="product-price">
                    <?php if ($product['is_free'] === 'yes'): ?>
                        FREE
                    <?php else: ?>
                        ₹<?php echo number_format($product['price'], 2); ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($product['is_free'] === 'yes'): ?>
                <div class="checkout-form">
                    <h3>Get Free Product</h3>
                    <p style="color: var(--color-gray-400); margin-bottom: var(--spacing-4);">
                        Enter your details to receive the download link.
                    </p>
                    <form id="free-form" method="POST">
                        <input type="hidden" name="action" value="free_download">
                        <div class="form-group">
                            <label for="name">Your Name</label>
                            <input type="text" id="name" name="name" required placeholder="John Doe">
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required placeholder="you@example.com">
                        </div>
                        <button type="submit" class="btn btn-primary btn-buy">
                            <i class="fa-solid fa-download"></i>
                            Download Now
                        </button>
                    </form>
                    <div class="secure-badge">
                        <i class="fa-solid fa-shield-halved"></i>
                        We'll email you the download link
                    </div>
                </div>
            <?php else: ?>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="checkout-form">
                    <h3>Complete Your Purchase</h3>
                    <form id="payment-form" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" required placeholder="John Doe">
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" required placeholder="john@example.com">
                            </div>
                        </div>
                        <button type="submit" id="rzp-button" class="btn btn-primary btn-buy">
                            <i class="fa-brands fa-razorpay"></i>
                            Pay ₹<?php echo number_format($product['price'], 2); ?>
                        </button>
                    </form>
                    <div class="secure-badge">
                        <i class="fa-solid fa-shield-halved"></i>
                        Secure payment powered by Razorpay
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include INCLUDES_PATH . '/footer.php'; ?>

    <script>
    // Razorpay Checkout
    document.getElementById('payment-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        
        // Disable button
        const btn = document.getElementById('rzp-button');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
        
        // Create order
        fetch(window.location.pathname + window.location.search, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'name=' + encodeURIComponent(name) + '&email=' + encodeURIComponent(email)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Open Razorpay
                const options = {
                    "key": "<?php echo RAZORPAY_KEY_ID; ?>",
                    "amount": data.amount,
                    "currency": data.currency,
                    "name": "<?php echo $siteName; ?>",
                    "description": "<?php echo addslashes($product['name']); ?>",
                    "order_id": data.razorpay_order_id,
                    "handler": function(response) {
                        // Verify payment
                        fetch('<?php echo BASE_URL; ?>/products/verify.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_signature: response.razorpay_signature
                            })
                        })
                        .then(r => r.json())
                        .then(result => {
                            if (result.success) {
                                window.location.href = '<?php echo BASE_URL; ?>/products/success.php?order=' + response.razorpay_order_id;
                            } else {
                                alert('Payment verification failed: ' + result.message);
                                btn.disabled = false;
                                btn.innerHTML = '<i class="fa-brands fa-razorpay"></i> Pay ₹<?php echo number_format($product['price'], 2); ?>';
                            }
                        });
                    },
                    "prefill": {
                        "name": name,
                        "email": email
                    },
                    "theme": {
                        "color": "#000000"
                    }
                };
                
                const rzp = new Razorpay(options);
                rzp.on('payment.failed', function(response) {
                    alert('Payment failed: ' + response.error.description);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-brands fa-razorpay"></i> Pay ₹<?php echo number_format($product['price'], 2); ?>';
                });
                rzp.open();
            } else {
                alert('Error: ' + (data.errors ? data.errors.join(', ') : 'Unknown error'));
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-brands fa-razorpay"></i> Pay ₹<?php echo number_format($product['price'], 2); ?>';
            }
        })
        .catch(err => {
            alert('Error creating order');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-brands fa-razorpay"></i> Pay ₹<?php echo number_format($product['price'], 2); ?>';
        });
    });
    
    // Free download
    document.getElementById('free-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const btn = this.querySelector('button');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
        
        fetch(window.location.pathname + window.location.search, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=free_download&email=' + encodeURIComponent(email)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.download_url;
            } else {
                alert('Error: ' + data.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-download"></i> Download Now';
            }
        });
    });
    </script>
</body>
</html>