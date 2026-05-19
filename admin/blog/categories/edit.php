<?php
/**
 * Edit Blog Category (Admin)
 */
define('AJOS_INIT', true);
$currentPage = 'blog-categories';
$pageTitle = 'Edit Category';

require_once '../../../includes/config.php';
require_once '../../../includes/db.php';
require_once '../../../includes/functions.php';
require_once '../../../includes/auth.php';

requireLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    setFlash('error', 'Invalid category ID.');
    redirect(SITE_URL . '/admin/blog/categories/');
}

$stmt = $pdo->prepare('SELECT * FROM blog_categories WHERE id = ?');
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    setFlash('error', 'Category not found.');
    redirect(SITE_URL . '/admin/blog/categories/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $slug = sanitize($_POST['slug'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    
    if (empty($slug)) {
        $slug = generateSlug($name);
    }
    
    try {
        $stmt = $pdo->prepare('UPDATE blog_categories SET name = ?, slug = ?, description = ? WHERE id = ?');
        $stmt->execute([$name, $slug, $description, $id]);
        setFlash('success', 'Category updated successfully.');
        redirect(SITE_URL . '/admin/blog/categories/');
    } catch (PDOException $e) {
        setFlash('error', 'Failed to update category.');
    }
}
?>
<?php include '../../../includes/admin-header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Edit Category</h1>
    <a href="<?php echo SITE_URL; ?>/admin/blog/categories/" class="btn btn-secondary">Back</a>
</div>

<div class="form-card" style="max-width: 500px;">
    <form method="POST">
        <div class="form-group">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" class="form-input" required value="<?php echo htmlspecialchars($category['name']); ?>">
        </div>
        <div class="form-group">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" id="slug" name="slug" class="form-input" value="<?php echo htmlspecialchars($category['slug']); ?>">
        </div>
        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-textarea" style="min-height: 80px;"><?php echo htmlspecialchars($category['description']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Category</button>
    </form>
</div>

<?php include '../../../includes/admin-footer.php'; ?>