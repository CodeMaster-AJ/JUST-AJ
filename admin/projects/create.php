<?php
/**
 * Create Project (Admin)
 */
define('AJOS_INIT', true);
$currentPage = 'projects';
$pageTitle = 'Add Project';

require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireLogin();

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $tech_stack = sanitize($_POST['tech_stack'] ?? '');
    $live_link = sanitize($_POST['live_link'] ?? '');
    $github_link = sanitize($_POST['github_link'] ?? '');
    $status = sanitize($_POST['status'] ?? 'active');
    $featured = sanitize($_POST['featured'] ?? 'no');
    
    if (empty($title)) {
        $error = 'Title is required.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO projects (title, description, tech_stack, live_link, github_link, status, featured) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$title, $description, $tech_stack, $live_link, $github_link, $status, $featured]);
            $success = true;
            setFlash('success', 'Project created successfully.');
            redirect(SITE_URL . '/admin/projects/index.php');
        } catch (PDOException $e) {
            $error = 'Something went wrong. Please try again.';
        }
    }
}
?>
<?php include '../../includes/admin-header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Add Project</h1>
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
                <input type="text" id="title" name="title" class="form-input" required value="<?php echo $_POST['title'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-textarea"><?php echo $_POST['description'] ?? ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="tech_stack" class="form-label">Tech Stack</label>
                <input type="text" id="tech_stack" name="tech_stack" class="form-input" placeholder="PHP, MySQL, JavaScript" value="<?php echo $_POST['tech_stack'] ?? ''; ?>">
                <p class="form-hint">Comma-separated list of technologies used</p>
            </div>
            
            <div class="form-group">
                <label for="live_link" class="form-label">Live Link</label>
                <input type="url" id="live_link" name="live_link" class="form-input" placeholder="https://example.com" value="<?php echo $_POST['live_link'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="github_link" class="form-label">GitHub Link</label>
                <input type="url" id="github_link" name="github_link" class="form-input" placeholder="https://github.com/user/repo" value="<?php echo $_POST['github_link'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="active" <?php echo ($_POST['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" name="featured" value="yes" <?php echo ($_POST['featured'] ?? '') === 'yes' ? 'checked' : ''; ?>>
                    <span>Featured on homepage</span>
                </label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Project</button>
                <a href="<?php echo SITE_URL; ?>/admin/projects/index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/admin-footer.php'; ?>