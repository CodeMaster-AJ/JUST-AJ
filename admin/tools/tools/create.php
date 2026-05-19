<?php
/**
 * Create Tool
 */
require_once __DIR__ . '/../../../includes/config.php';
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
        <h1>Add Tool</h1>
        <a href="index.php" class="btn">Back</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="admin-form">
        <div class="form-group">
            <label for="name">Name *</label>
            <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>">
            <span class="form-hint">Leave empty to auto-generate from name</span>
        </div>

        <div class="form-group">
            <label for="category_id">Category *</label>
            <select id="category_id" name="category_id" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo ($_POST['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="tool_url">Tool URL *</label>
            <input type="url" id="tool_url" name="tool_url" required value="<?php echo htmlspecialchars($_POST['tool_url'] ?? ''); ?>">
            <span class="form-hint">The external URL to redirect to</span>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="icon">Icon</label>
            <input type="text" id="icon" name="icon" value="<?php echo htmlspecialchars($_POST['icon'] ?? ''); ?>">
        </div>

        <div class="form-row">
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

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Tool</button>
        </div>
    </form>
</div>

<?php include INCLUDES_PATH . '/admin-footer.php'; ?>