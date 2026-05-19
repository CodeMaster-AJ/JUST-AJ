<?php
/**
 * Blog Tags List (Admin)
 */
define('AJOS_INIT', true);
$currentPage = 'blog-tags';
$pageTitle = 'Tags';

require_once '../../../includes/config.php';
require_once '../../../includes/db.php';
require_once '../../../includes/functions.php';
require_once '../../../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_tag'])) {
    $name = sanitize($_POST['name'] ?? '');
    $slug = sanitize($_POST['slug'] ?? '');
    
    if (empty($slug)) {
        $slug = generateSlug($name);
    }
    
    try {
        $stmt = $pdo->prepare('INSERT INTO blog_tags (name, slug) VALUES (?, ?)');
        $stmt->execute([$name, $slug]);
        setFlash('success', 'Tag added successfully.');
        redirect(SITE_URL . '/admin/blog/tags/');
    } catch (PDOException $e) {
        setFlash('error', 'Failed to add tag.');
    }
}

$tags = $pdo->query('SELECT bt.*, COUNT(bpt.post_id) as post_count 
                     FROM blog_tags bt 
                     LEFT JOIN blog_post_tags bpt ON bt.id = bpt.tag_id 
                     GROUP BY bt.id 
                     ORDER BY bt.name')->fetchAll();
?>
<?php include '../../../includes/admin-header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Tags</h1>
</div>

<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
<div class="alert alert-<?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
    <?php echo htmlspecialchars($flash['message']); ?>
</div>
<?php endif; ?>

<div class="form-card" style="max-width: 500px; margin-bottom: 30px;">
    <h3 style="margin-bottom: 15px;">Add New Tag</h3>
    <form method="POST">
        <div class="form-group">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" id="slug" name="slug" class="form-input" placeholder="auto-generated">
        </div>
        <button type="submit" name="add_tag" class="btn btn-primary">Add Tag</button>
    </form>
</div>

<?php if (!empty($tags)): ?>
<div class="tags-grid">
    <?php foreach ($tags as $tag): ?>
    <div class="tag-item">
        <div class="tag-info">
            <strong><?php echo htmlspecialchars($tag['name']); ?></strong>
            <span class="tag-slug"><?php echo htmlspecialchars($tag['slug']); ?></span>
            <span class="tag-count"><?php echo $tag['post_count']; ?> posts</span>
        </div>
        <div class="tag-actions">
            <a href="edit.php?id=<?php echo $tag['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
            <a href="delete.php?id=<?php echo $tag['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Delete this tag?')">Delete</a>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="empty-state">
    <h3 class="empty-state-title">No Tags</h3>
    <p class="empty-state-description">Add tags to label your blog posts.</p>
</div>
<?php endif; ?>

<style>
.tags-grid { display: grid; gap: 10px; }
.tag-item { display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--color-gray-900); border: 1px solid var(--color-gray-800); border-radius: 8px; }
.tag-info { display: flex; align-items: center; gap: 15px; }
.tag-slug { color: var(--color-gray-500); font-size: 12px; }
.tag-count { color: var(--color-gray-400); font-size: 12px; }
.tag-actions { display: flex; gap: 5px; }
</style>

<?php include '../../../includes/admin-footer.php'; ?>