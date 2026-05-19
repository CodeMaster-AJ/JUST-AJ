<?php
/**
 * Products List
 */
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/db.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$pageTitle = 'Products Management';

if (isset($_GET['delete'])) {
    $id = (int)($_GET['delete']);
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
    setFlash('success', 'Product deleted successfully');
    redirect(BASE_URL . '/admin/products/products/');
}

$products = $pdo->query('SELECT p.*, pc.name as category_name 
                      FROM products p 
                      LEFT JOIN product_categories pc ON p.category_id = pc.id 
                      ORDER BY pc.sort_order, p.sort_order')->fetchAll();

include INCLUDES_PATH . '/admin-header.php';
include INCLUDES_PATH . '/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="page-header">
        <div>
            <h1>Products Management</h1>
            <p class="page-subtitle">Manage your digital products</p>
        </div>
        <a href="create.php" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i>
            Add Product
        </a>
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
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Downloads</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <div class="product-cell">
                                <img src="<?php echo htmlspecialchars($product['preview_image'] ?? 'https://via.placeholder.com/50x50/171717/ffffff?text=P'); ?>" 
                                     alt="" class="product-thumb">
                                <div>
                                    <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                    <?php if ($product['is_free'] === 'yes'): ?>
                                        <span class="badge free-badge">FREE</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($product['category_name'] ?? '-'); ?></td>
                        <td>
                            <?php if ($product['is_free'] === 'yes'): ?>
                                <span class="text-success">FREE</span>
                            <?php else: ?>
                                $<?php echo number_format($product['price'], 2); ?>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge"><?php echo $product['download_count']; ?></span></td>
                        <td>
                            <span class="status-badge <?php echo $product['status']; ?>">
                                <?php echo ucfirst($product['status']); ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <a href="?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.product-cell {
    display: flex;
    align-items: center;
    gap: var(--spacing-3);
}

.product-thumb {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: var(--border-radius);
}

.free-badge {
    display: inline-block;
    background-color: rgba(34, 197, 94, 0.1);
    color: #22c55e;
    font-size: var(--font-size-xs);
    padding: 2px 6px;
    border-radius: var(--border-radius);
    margin-left: var(--spacing-2);
}

.text-success {
    color: #22c55e;
}
</style>

<?php include INCLUDES_PATH . '/admin-footer.php'; ?>