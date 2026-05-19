<?php
/**
 * Product Categories List
 */
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/db.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$pageTitle = 'Product Categories';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM product_categories WHERE id = ?');
    $stmt->execute([$id]);
    setFlash('success', 'Category deleted successfully');
    redirect(BASE_URL . '/admin/products/categories/');
}

$categories = $pdo->query('SELECT pc.*, COUNT(p.id) as product_count 
                           FROM product_categories pc 
                           LEFT JOIN products p ON pc.id = p.category_id 
                           GROUP BY pc.id 
                           ORDER BY pc.sort_order')->fetchAll();

include INCLUDES_PATH . '/admin-header.php';
include INCLUDES_PATH . '/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="page-header">
        <div>
            <h1>Product Categories</h1>
            <p class="page-subtitle">Organize your digital products</p>
        </div>
        <a href="create.php" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i>
            Add Category
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
                    <th>Order</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?php echo $cat['sort_order']; ?></td>
                        <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                        <td><code><?php echo htmlspecialchars($cat['slug']); ?></code></td>
                        <td><span class="badge"><?php echo $cat['product_count']; ?></span></td>
                        <td>
                            <span class="status-badge <?php echo $cat['status']; ?>">
                                <?php echo ucfirst($cat['status']); ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="edit.php?id=<?php echo $cat['id']; ?>" class="btn btn-sm">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <a href="?delete=<?php echo $cat['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include INCLUDES_PATH . '/admin-footer.php'; ?>