<?php
/**
 * Create Product Category
 */
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/db.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$pageTitle = 'Add Product Category';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $slug = generateSlug($_POST['slug'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $status = sanitize($_POST['status'] ?? 'active');
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($slug)) {
        $slug = generateSlug($name);
    }
    
    $stmt = $pdo->prepare('SELECT id FROM product_categories WHERE slug = ?');
    $stmt->execute([$slug]);
    if ($stmt->fetch()) {
        $errors[] = 'Slug already exists';
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO product_categories (name, slug, description, sort_order, status) 
                               VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $slug, $description, $sortOrder, $status]);
        setFlash('success', 'Category created successfully');
        redirect(BASE_URL . '/admin/products/categories/');
    }
}

include INCLUDES_PATH . '/admin-header.php';
include INCLUDES_PATH . '/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="page-header">
        <div>
            <h1>Add Product Category</h1>
            <p class="page-subtitle">Create a new category for your products</p>
        </div>
        <a href="index.php" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Back
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <i class="fa-solid fa-xmark"></i>
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
                    <i class="fa-solid fa-folder"></i>
                    <h3>Category Details</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label for="name">Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" placeholder="e.g., Templates">
                    </div>
                    
                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <div class="input-with-prefix">
                            <span class="input-prefix">/products/</span>
                            <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>" placeholder="auto-generated">
                        </div>
                        <span class="form-hint">Leave empty to auto-generate from name</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="sort_order">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order" value="<?php echo htmlspecialchars($_POST['sort_order'] ?? 0); ?>">
                    </div>
                    
                    <div class="form-group form-group-full">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
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
                    <i class="fa-solid fa-plus"></i>
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>

<?php include INCLUDES_PATH . '/admin-footer.php'; ?>