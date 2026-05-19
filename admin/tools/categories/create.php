<?php
/**
 * Create Tool Category
 */
require_once __DIR__ . '/../../../includes/config.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$pageTitle = 'Add Tool Category';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $slug = generateSlug($_POST['slug'] ?? '');
    $icon = sanitize($_POST['icon'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $status = sanitize($_POST['status'] ?? 'active');
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($slug)) {
        $slug = generateSlug($name);
    }
    
    $stmt = $pdo->prepare('SELECT id FROM tool_categories WHERE slug = ?');
    $stmt->execute([$slug]);
    if ($stmt->fetch()) {
        $errors[] = 'Slug already exists';
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO tool_categories (name, slug, icon, description, sort_order, status) 
                               VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $slug, $icon, $description, $sortOrder, $status]);
        setFlash('success', 'Category created successfully');
        redirect(BASE_URL . '/admin/tools/categories/');
    }
}

include INCLUDES_PATH . '/admin-header.php';
include INCLUDES_PATH . '/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="page-header">
        <h1>Add Tool Category</h1>
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
            <label for="icon">Icon (Font Awesome class)</label>
            <input type="text" id="icon" name="icon" value="<?php echo htmlspecialchars($_POST['icon'] ?? ''); ?>">
            <span class="form-hint">e.g., file-pdf, image, search, qrcode</span>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="sort_order">Sort Order</label>
            <input type="number" id="sort_order" name="sort_order" value="<?php echo htmlspecialchars($_POST['sort_order'] ?? 0); ?>">
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="active" <?php echo ($_POST['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Category</button>
        </div>
    </form>
</div>

<?php include INCLUDES_PATH . '/admin-footer.php'; ?>