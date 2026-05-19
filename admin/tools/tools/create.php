<?php
/**
 * Create Tool
 */
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/db.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$pageTitle = 'Add Tool';
$errors = [];

$categories = $pdo->query('SELECT * FROM tool_categories WHERE status = "active" ORDER BY sort_order')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $slug = generateSlug($_POST['slug'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $toolUrl = sanitize($_POST['tool_url'] ?? '');
    $icon = sanitize($_POST['icon'] ?? '');
    $featured = sanitize($_POST['featured'] ?? 'no');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $status = sanitize($_POST['status'] ?? 'active');
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($toolUrl)) {
        $errors[] = 'Tool URL is required';
    }
    
    if (empty($categoryId)) {
        $errors[] = 'Category is required';
    }
    
    if (empty($slug)) {
        $slug = generateSlug($name);
    }
    
    $stmt = $pdo->prepare('SELECT id FROM tools WHERE slug = ?');
    $stmt->execute([$slug]);
    if ($stmt->fetch()) {
        $errors[] = 'Slug already exists';
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO tools (name, slug, category_id, description, tool_url, icon, featured, sort_order, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $slug, $categoryId, $description, $toolUrl, $icon, $featured, $sortOrder, $status]);
        setFlash('success', 'Tool created successfully');
        redirect(BASE_URL . '/admin/tools/tools/');
    }
}

include INCLUDES_PATH . '/admin-header.php';
include INCLUDES_PATH . '/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="page-header">
        <div>
            <h1>Add New Tool</h1>
            <p class="page-subtitle">Add a new external tool to your directory</p>
        </div>
        <a href="index.php" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Tools
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/>
            </svg>
            <div>
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" class="admin-form">
            <div class="form-section">
                <div class="form-section-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                    </svg>
                    <h3>Basic Information</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label for="name">Tool Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required placeholder="e.g., JPG to PDF Converter" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <div class="input-with-prefix">
                            <span class="input-prefix">/tools/</span>
                            <input type="text" id="slug" name="slug" placeholder="auto-generated" value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>">
                        </div>
                        <span class="form-hint">Leave empty to auto-generate from name</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Category <span class="required">*</span></label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($_POST['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                    </svg>
                    <h3>Tool Link</h3>
                </div>
                <div class="form-group form-group-full">
                    <label for="tool_url">Tool URL <span class="required">*</span></label>
                    <input type="url" id="tool_url" name="tool_url" required placeholder="https://example.com/tool" value="<?php echo htmlspecialchars($_POST['tool_url'] ?? ''); ?>">
                    <span class="form-hint">The external URL users will be redirected to when clicking this tool</span>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                    <h3>Details</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Brief description of what this tool does..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="icon">Icon</label>
                        <input type="text" id="icon" name="icon" placeholder="e.g., image, file, search" value="<?php echo htmlspecialchars($_POST['icon'] ?? ''); ?>">
                        <span class="form-hint">Icon name for display purposes</span>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 20V10M18 20V4M6 20v-4"/>
                    </svg>
                    <h3>Settings</h3>
                </div>
                <div class="form-grid form-grid-3">
                    <div class="form-group">
                        <label for="sort_order">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order" value="<?php echo htmlspecialchars($_POST['sort_order'] ?? 0); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="featured">Featured</label>
                        <select id="featured" name="featured">
                            <option value="no" <?php echo ($_POST['featured'] ?? 'no') === 'no' ? 'selected' : ''; ?>>No</option>
                            <option value="yes" <?php echo ($_POST['featured'] ?? '') === 'yes' ? 'selected' : ''; ?>>Yes</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="active" <?php echo ($_POST['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Create Tool
                </button>
            </div>
        </form>
    </div>
</div>

<?php include INCLUDES_PATH . '/admin-footer.php'; ?>