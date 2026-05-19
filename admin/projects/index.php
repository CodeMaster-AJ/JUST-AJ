<?php
/**
 * Projects List (Admin)
 */
define('AJOS_INIT', true);
$currentPage = 'projects';
$pageTitle = 'Projects';

require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireLogin();

// Get all projects
$stmt = $pdo->query('SELECT * FROM projects ORDER BY created_at DESC');
$projects = $stmt->fetchAll();
?>
<?php include '../../includes/admin-header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Projects</h1>
    <a href="<?php echo SITE_URL; ?>/admin/projects/create.php" class="btn btn-primary">Add Project</a>
</div>

<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
<div class="alert alert-<?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
    <?php echo htmlspecialchars($flash['message']); ?>
</div>
<?php endif; ?>

<?php if (!empty($projects)): ?>
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Tech Stack</th>
                <th>Status</th>
                <th>Featured</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($project['title']); ?></strong></td>
                <td><?php echo htmlspecialchars($project['tech_stack'] ?? '-'); ?></td>
                <td><span class="status-badge <?php echo $project['status']; ?>"><?php echo ucfirst($project['status']); ?></span></td>
                <td><?php echo $project['featured'] === 'yes' ? 'Yes' : 'No'; ?></td>
                <td>
                    <a href="<?php echo SITE_URL; ?>/admin/projects/edit.php?id=<?php echo $project['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="<?php echo SITE_URL; ?>/admin/projects/delete.php?id=<?php echo $project['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Are you sure you want to delete this project?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="empty-state">
    <span class="empty-state-icon">◇</span>
    <h3 class="empty-state-title">No Projects Yet</h3>
    <p class="empty-state-description">Add your first project to get started.</p>
    <a href="<?php echo SITE_URL; ?>/admin/projects/create.php" class="btn btn-primary">Add Project</a>
</div>
<?php endif; ?>

<?php include '../../includes/admin-footer.php'; ?>