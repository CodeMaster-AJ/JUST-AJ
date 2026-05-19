<?php
/**
 * Tools List
 */
require_once __DIR__ . '/../../../includes/config.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$pageTitle = 'Tools Management';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM tools WHERE id = ?');
    $stmt->execute([$id]);
    setFlash('success', 'Tool deleted successfully');
    redirect(BASE_URL . '/admin/tools/tools/');
}

$tools = $pdo->query('SELECT t.*, tc.name as category_name 
                      FROM tools t 
                      LEFT JOIN tool_categories tc ON t.category_id = tc.id 
                      ORDER BY tc.sort_order, t.sort_order')->fetchAll();

include INCLUDES_PATH . '/admin-header.php';
include INCLUDES_PATH . '/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="page-header">
        <h1>Tools Management</h1>
        <a href="create.php" class="btn btn-primary">Add Tool</a>
    </div>

    <?php if ($flash = getFlash()): ?>
        <div class="alert alert-<?php echo $flash['type']; ?>"><?php echo $flash['message']; ?></div>
    <?php endif; ?>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>URL</th>
                    <th>Clicks</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tools as $tool): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($tool['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($tool['category_name']); ?></td>
                        <td><a href="<?php echo htmlspecialchars($tool['tool_url']); ?>" target="_blank">Open</a></td>
                        <td><span class="badge"><?php echo $tool['click_count']; ?></span></td>
                        <td>
                            <span class="status-badge <?php echo $tool['featured']; ?>">
                                <?php echo $tool['featured'] === 'yes' ? 'Featured' : '-'; ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $tool['status']; ?>">
                                <?php echo ucfirst($tool['status']); ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="edit.php?id=<?php echo $tool['id']; ?>" class="btn btn-sm">Edit</a>
                            <a href="?delete=<?php echo $tool['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this tool?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include INCLUDES_PATH . '/admin-footer.php'; ?>