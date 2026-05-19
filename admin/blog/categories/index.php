<?php
/**
 * Blog Categories List (Admin)
 */
define('AJOS_INIT', true);
$currentPage = 'blog-categories';
$pageTitle = 'Categories';

require_once '../../../includes/config.php';
require_once '../../../includes/db.php';
require_once '../../../includes/functions.php';
require_once '../../../includes/auth.php';

requireLogin();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = sanitize($_POST['name'] ?? '');
    $slug = sanitize($_POST['slug'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    
    if (empty($slug)) {
        $slug = generateSlug($name);
    }
    
    try {
        $stmt = $pdo->prepare('INSERT INTO blog_categories (name, slug, description) VALUES (?, ?, ?)');
        $stmt->execute([$name, $slug, $description]);
        setFlash('success', 'Category added successfully.');
        redirect(SITE_URL . '/admin/blog/categories/');
    } catch (PDOException $e) {
        setFlash('error', 'Failed to add category.');
    }
}

$categories = $pdo->query('SELECT bc.*, COUNT(bp.id) as post_count 
                          FROM blog_categories bc 
                          LEFT JOIN blog_posts bp ON bc.id = bp.category_id 
                          GROUP BY bc.id 
                          ORDER BY bc.name')->fetchAll();
?>
<?php include '../../../includes/admin-header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Categories</h1>
</div>

<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
<div class="alert alert-<?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
    <?php echo htmlspecialchars($flash['message']); ?>
</div>
<?php endif; ?>

<div class="form-card" style="max-width: 500px; margin-bottom: 30px;">
    <h3 style="margin-bottom: 15px;">Add New Category</h3>
    <form method="POST">
        <div class="form-group">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" id="slug" name="slug" class="form-input" placeholder="auto-generated">
        </div>
        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-textarea" style="min-height: 80px;"></textarea>
        </div>
        <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
    </form>
</div>

<?php if (!empty($categories)): ?>
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Posts</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                <td><code><?php echo htmlspecialchars($cat['slug']); ?></code></td>
                <td><?php echo $cat['post_count']; ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $cat['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="delete.php?id=<?php echo $cat['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Delete this category?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="empty-state">
    <h3 class="empty-state-title">No Categories</h3>
    <p class="empty-state-description">Add categories to organize your blog posts.</p>
</div>
<?php endif; ?>

<?php include '../../../includes/admin-footer.php'; ?>