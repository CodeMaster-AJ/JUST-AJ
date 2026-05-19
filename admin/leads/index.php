<?php
/**
 * Leads List (Admin)
 */
define('AJOS_INIT', true);
$currentPage = 'leads';
$pageTitle = 'Leads';

require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireLogin();

// Get all leads
$stmt = $pdo->query('SELECT * FROM leads ORDER BY created_at DESC');
$leads = $stmt->fetchAll();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = filter_input(INPUT_POST, 'lead_id', FILTER_VALIDATE_INT);
    $status = sanitize($_POST['status']);
    
    if ($id && in_array($status, ['new', 'read', 'replied', 'archived'])) {
        $stmt = $pdo->prepare('UPDATE leads SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
        setFlash('success', 'Lead status updated.');
        redirect(SITE_URL . '/admin/leads/index.php');
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare('DELETE FROM leads WHERE id = ?');
        $stmt->execute([$id]);
        setFlash('success', 'Lead deleted.');
        redirect(SITE_URL . '/admin/leads/index.php');
    }
}
?>
<?php include '../../includes/admin-header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Leads</h1>
</div>

<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
<div class="alert alert-<?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
    <?php echo htmlspecialchars($flash['message']); ?>
</div>
<?php endif; ?>

<?php if (!empty($leads)): ?>
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($leads as $lead): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($lead['name']); ?></strong></td>
                <td><a href="mailto:<?php echo htmlspecialchars($lead['email']); ?>"><?php echo htmlspecialchars($lead['email']); ?></a></td>
                <td><?php echo htmlspecialchars($lead['subject'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars(truncate($lead['message'], 50)); ?></td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="lead_id" value="<?php echo $lead['id']; ?>">
                        <select name="status" class="form-select" style="width: 100px; padding: 4px;" onchange="this.form.submit()">
                            <option value="new" <?php echo $lead['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                            <option value="read" <?php echo $lead['status'] === 'read' ? 'selected' : ''; ?>>Read</option>
                            <option value="replied" <?php echo $lead['status'] === 'replied' ? 'selected' : ''; ?>>Replied</option>
                            <option value="archived" <?php echo $lead['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
                        </select>
                        <input type="hidden" name="update_status" value="1">
                    </form>
                </td>
                <td><?php echo formatDate($lead['created_at']); ?></td>
                <td>
                    <a href="?view=<?php echo $lead['id']; ?>" class="btn btn-secondary btn-sm">View</a>
                    <a href="?delete=<?php echo $lead['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Are you sure you want to delete this lead?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="empty-state">
    <span class="empty-state-icon">◇</span>
    <h3 class="empty-state-title">No Leads Yet</h3>
    <p class="empty-state-description">Contact form submissions will appear here.</p>
</div>
<?php endif; ?>

<?php include '../../includes/admin-footer.php'; ?>