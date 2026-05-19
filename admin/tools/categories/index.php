<?php
/**
 * Tool Categories List
 */
require_once __DIR__ . '/../../../includes/config.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$pageTitle = 'Tool Categories';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM tool_categories WHERE id = ?');
    $stmt->execute([$id]);
    setFlash('success', 'Category deleted successfully');
    redirect(BASE_URL . '/admin/tools/categories/');
}

$categories = $pdo->query('SELECT tc.*, COUNT(t.id) as tool_count 
                           FROM tool_categories tc 
                           LEFT JOIN tools t ON tc.id = t.category_id 
                           GROUP BY tc.id 
                           ORDER BY tc.sort_order')->fetchAll();

include INCLUDES_PATH . '/admin-header.php';
include INCLUDES_PATH . '/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="page-header">
        <h1>Tool Categories</h1>
        <a href="create.php" class="btn btn-primary">Add Category</a>
    </div>

    <?php if ($flash = getFlash()): ?>
        <div class="alert alert-<?php echo $flash['type']; ?>"><?php echo $flash['message']; ?></div>
    <?php endif; ?>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Icon</th>
                    <th>Tools</th>
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
                        <td><?php echo htmlspecialchars($cat['icon'] ?: '-'); ?></td>
                        <td><span class="badge"><?php echo $cat['tool_count']; ?></span></td>
                        <td>
                            <span class="status-badge <?php echo $cat['status']; ?>">
                                <?php echo ucfirst($cat['status']); ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="edit.php?id=<?php echo $cat['id']; ?>" class="btn btn-sm">Edit</a>
                            <a href="?delete=<?php echo $cat['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include INCLUDES_PATH . '/admin-footer.php'; ?>