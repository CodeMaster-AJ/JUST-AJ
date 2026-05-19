<?php
/**
 * Edit Service (Admin)
 */
define('AJOS_INIT', true);
$currentPage = 'services';
$pageTitle = 'Edit Service';

require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireLogin();

// Get service ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    setFlash('error', 'Invalid service ID.');
    redirect(SITE_URL . '/admin/services/index.php');
}

// Get service
$stmt = $pdo->prepare('SELECT * FROM services WHERE id = ?');
$stmt->execute([$id]);
$service = $stmt->fetch();

if (!$service) {
    setFlash('error', 'Service not found.');
    redirect(SITE_URL . '/admin/services/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $icon = sanitize($_POST['icon'] ?? '');
    $status = sanitize($_POST['status'] ?? 'active');
    $featured = sanitize($_POST['featured'] ?? 'no');
    
    if (empty($title)) {
        $error = 'Title is required.';
    } else {
        try {
            $stmt = $pdo->prepare('UPDATE services SET title = ?, description = ?, icon = ?, status = ?, featured = ? WHERE id = ?');
            $stmt->execute([$title, $description, $icon, $status, $featured, $id]);
            setFlash('success', 'Service updated successfully.');
            redirect(SITE_URL . '/admin/services/index.php');
        } catch (PDOException $e) {
            $error = 'Something went wrong. Please try again.';
        }
    }
}
?>
<?php include '../../includes/admin-header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Edit Service</h1>
</div>

<div class="form-container">
    <div class="form-card">
        <?php if ($error): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="title" class="form-label">Title *</label>
                <input type="text" id="title" name="title" class="form-input" required value="<?php echo htmlspecialchars($service['title']); ?>">
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-textarea"><?php echo htmlspecialchars($service['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="icon" class="form-label">Icon</label>
                <input type="text" id="icon" name="icon" class="form-input" placeholder="code" value="<?php echo htmlspecialchars($service['icon']); ?>">
                <p class="form-hint">Icon name or symbol to display</p>
            </div>
            
            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="active" <?php echo $service['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $service['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" name="featured" value="yes" <?php echo $service['featured'] === 'yes' ? 'checked' : ''; ?>>
                    <span>Featured on homepage</span>
                </label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Service</button>
                <a href="<?php echo SITE_URL; ?>/admin/services/index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/admin-footer.php'; ?>