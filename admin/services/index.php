<?php
/**
 * Services List (Admin)
 */
define('AJOS_INIT', true);
$currentPage = 'services';
$pageTitle = 'Services';

require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireLogin();

// Get all services
$stmt = $pdo->query('SELECT * FROM services ORDER BY created_at DESC');
$services = $stmt->fetchAll();
?>
<?php include '../../includes/admin-header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Services</h1>
    <a href="<?php echo SITE_URL; ?>/admin/services/create.php" class="btn btn-primary">Add Service</a>
</div>

<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
<div class="alert alert-<?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
    <?php echo htmlspecialchars($flash['message']); ?>
</div>
<?php endif; ?>

<?php if (!empty($services)): ?>
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Icon</th>
                <th>Status</th>
                <th>Featured</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $service): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($service['title']); ?></strong></td>
                <td><?php echo htmlspecialchars($service['icon'] ?? '-'); ?></td>
                <td><span class="status-badge <?php echo $service['status']; ?>"><?php echo ucfirst($service['status']); ?></span></td>
                <td><?php echo $service['featured'] === 'yes' ? 'Yes' : 'No'; ?></td>
                <td>
                    <a href="<?php echo SITE_URL; ?>/admin/services/edit.php?id=<?php echo $service['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="<?php echo SITE_URL; ?>/admin/services/delete.php?id=<?php echo $service['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Are you sure you want to delete this service?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="empty-state">
    <span class="empty-state-icon">◇</span>
    <h3 class="empty-state-title">No Services Yet</h3>
    <p class="empty-state-description">Add your first service to get started.</p>
    <a href="<?php echo SITE_URL; ?>/admin/services/create.php" class="btn btn-primary">Add Service</a>
</div>
<?php endif; ?>

<?php include '../../includes/admin-footer.php'; ?>