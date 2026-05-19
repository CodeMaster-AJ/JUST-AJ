<?php
/**
 * Edit Blog Tag (Admin)
 */
define('AJOS_INIT', true);
$currentPage = 'blog-tags';
$pageTitle = 'Edit Tag';

require_once '../../../includes/config.php';
require_once '../../../includes/db.php';
require_once '../../../includes/functions.php';
require_once '../../../includes/auth.php';

requireLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    redirect(SITE_URL . '/admin/blog/tags/');
}

$stmt = $pdo->prepare('SELECT * FROM blog_tags WHERE id = ?');
$stmt->execute([$id]);
$tag = $stmt->fetch();

if (!$tag) {
    redirect(SITE_URL . '/admin/blog/tags/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $slug = sanitize($_POST['slug'] ?? '');
    
    if (empty($slug)) {
        $slug = generateSlug($name);
    }
    
    try {
        $stmt = $pdo->prepare('UPDATE blog_tags SET name = ?, slug = ? WHERE id = ?');
        $stmt->execute([$name, $slug, $id]);
        setFlash('success', 'Tag updated successfully.');
        redirect(SITE_URL . '/admin/blog/tags/');
    } catch (PDOException $e) {
        setFlash('error', 'Failed to update tag.');
    }
}
?>
<?php include '../../../includes/admin-header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Edit Tag</h1>
    <a href="<?php echo SITE_URL; ?>/admin/blog/tags/" class="btn btn-secondary">Back</a>
</div>

<div class="form-card" style="max-width: 500px;">
    <form method="POST">
        <div class="form-group">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" class="form-input" required value="<?php echo htmlspecialchars($tag['name']); ?>">
        </div>
        <div class="form-group">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" id="slug" name="slug" class="form-input" value="<?php echo htmlspecialchars($tag['slug']); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update Tag</button>
    </form>
</div>

<?php include '../../../includes/admin-footer.php'; ?>