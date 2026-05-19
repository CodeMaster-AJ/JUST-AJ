<?php
/**
 * Orders List
 */
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/db.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/razorpay.php';

requireLogin();

$pageTitle = 'Orders';

if (isset($_GET['view'])) {
    $id = (int)($_GET['view']);
    $stmt = $pdo->prepare('SELECT o.*, p.name as product_name, p.preview_image 
                           FROM orders o 
                           LEFT JOIN products p ON o.product_id = p.id 
                           WHERE o.id = ?');
    $stmt->execute([$id]);
    $order = $stmt->fetch();
    
    if ($order) {
        $pageTitle = 'Order #' . $order['order_id'];
    }
}

if (isset($_GET['refund'])) {
    $id = (int)($_GET['refund']);
    $stmt = $pdo->prepare('UPDATE orders SET status = "refunded" WHERE id = ? AND status = "paid"');
    $stmt->execute([$id]);
    setFlash('success', 'Order marked as refunded');
    redirect(BASE_URL . '/admin/products/orders/');
}

if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)($_GET['id']);
    $status = sanitize($_GET['status']);
    if (in_array($status, ['pending', 'paid', 'failed', 'refunded'])) {
        $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
        setFlash('success', 'Order status updated');
        redirect(BASE_URL . '/admin/products/orders/');
    }
}

if (isset($_GET['view'])) {
    if (!$order) {
        setFlash('error', 'Order not found');
        redirect(BASE_URL . '/admin/products/orders/');
    }
    
    include INCLUDES_PATH . '/admin-header.php';
    include INCLUDES_PATH . '/admin-sidebar.php';
    ?>
    
    <div class="admin-content">
        <div class="page-header">
            <div>
                <h1>Order #<?php echo htmlspecialchars($order['order_id']); ?></h1>
                <p class="page-subtitle"><?php echo formatDate($order['created_at']); ?></p>
            </div>
            <a href="index.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Orders
            </a>
        </div>

        <div class="form-container">
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fa-solid fa-box"></i>
                    <h3>Order Details</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Order ID</label>
                        <p class="static-value"><?php echo htmlspecialchars($order['order_id']); ?></p>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <span class="status-badge <?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                    <div class="form-group">
                        <label>Amount</label>
                        <p class="static-value">₹<?php echo number_format($order['amount'], 2); ?></p>
                    </div>
                    <div class="form-group">
                        <label>Currency</label>
                        <p class="static-value"><?php echo htmlspecialchars($order['currency']); ?></p>
                    </div>
                    <div class="form-group">
                        <label>Payment ID</label>
                        <p class="static-value"><?php echo htmlspecialchars($order['payment_id'] ?? '-'); ?></p>
                    </div>
                    <div class="form-group">
                        <label>Razorpay Order ID</label>
                        <p class="static-value"><?php echo htmlspecialchars($order['razorpay_order_id']); ?></p>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-header">
                    <i class="fa-solid fa-user"></i>
                    <h3>Customer Details</h3>
                </div>
                <div class="form-grid form-grid-2">
                    <div class="form-group">
                        <label>Name</label>
                        <p class="static-value"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <p class="static-value"><?php echo htmlspecialchars($order['customer_email']); ?></p>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-header">
                    <i class="fa-solid fa-box"></i>
                    <h3>Product</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Product Name</label>
                        <p class="static-value"><?php echo htmlspecialchars($order['product_name']); ?></p>
                    </div>
                </div>
            </div>

            <?php if ($order['status'] === 'paid' && $order['status'] !== 'refunded'): ?>
                <div class="form-actions">
                    <a href="?refund=<?php echo $order['id']; ?>" class="btn btn-danger" onclick="return confirm('Mark this order as refunded?')">
                        <i class="fa-solid fa-undo"></i>
                        Mark as Refunded
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include INCLUDES_PATH . '/admin-footer.php';
    
} else {
    $orders = getOrders(100);
    
    include INCLUDES_PATH . '/admin-header.php';
    include INCLUDES_PATH . '/admin-sidebar.php';
    ?>
    
    <div class="admin-content">
        <div class="page-header">
            <div>
                <h1>Orders</h1>
                <p class="page-subtitle">Manage product purchases</p>
            </div>
        </div>

        <?php if ($flash = getFlash()): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <i class="fa-solid fa-<?php echo $flash['type'] === 'success' ? 'check' : 'xmark'; ?>"></i>
                <p><?php echo $flash['message']; ?></p>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><code style="font-size: 11px;"><?php echo htmlspecialchars(substr($o['order_id'], 0, 20)); ?>...</code></td>
                            <td><?php echo htmlspecialchars($o['product_name']); ?></td>
                            <td>
                                <div><?php echo htmlspecialchars($o['customer_name']); ?></div>
                                <small style="color: var(--color-gray-500);"><?php echo htmlspecialchars($o['customer_email']); ?></small>
                            </td>
                            <td>₹<?php echo number_format($o['amount'], 2); ?></td>
                            <td>
                                <span class="status-badge <?php echo $o['status']; ?>">
                                    <?php echo ucfirst($o['status']); ?>
                                </span>
                            </td>
                            <td><?php echo formatDate($o['created_at']); ?></td>
                            <td class="actions">
                                <a href="?view=<?php echo $o['id']; ?>" class="btn btn-sm">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-receipt"></i>
                <p class="empty-state-title">No orders yet</p>
                <p class="empty-state-description">Orders will appear here once customers start purchasing products.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include INCLUDES_PATH . '/admin-footer.php';
}