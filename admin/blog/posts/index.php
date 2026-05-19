<?php
/**
 * Blog Posts List (Admin)
 */
define('AJOS_INIT', true);
$currentPage = 'blog-posts';
$pageTitle = 'Blog Posts';

require_once '../../../includes/config.php';
require_once '../../../includes/db.php';
require_once '../../../includes/functions.php';
require_once '../../../includes/auth.php';

requireLogin();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Search
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$where = '';
$params = [];

if ($search) {
    $where = "WHERE title LIKE ? OR content LIKE ?";
    $params = ["%$search%", "%$search%"];
}

// Get total count
$countSql = "SELECT COUNT(*) as total FROM blog_posts $where";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalPosts = $stmt->fetch()['total'];
$totalPages = ceil($totalPosts / $perPage);

// Get posts
$sql = "SELECT bp.*, bc.name as category_name, au.name as author_name 
        FROM blog_posts bp 
        LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
        LEFT JOIN admin_users au ON bp.author_id = au.id 
        $where 
        ORDER BY bp.created_at DESC 
        LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

// Get categories for filter
$categories = $pdo->query('SELECT * FROM blog_categories ORDER BY name')->fetchAll();
?>
<?php include '../../../includes/admin-header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Blog Posts</h1>
    <a href="<?php echo SITE_URL; ?>/admin/blog/posts/create.php" class="btn btn-primary">Add New Post</a>
</div>

<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
<div class="alert alert-<?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
    <?php echo htmlspecialchars($flash['message']); ?>
</div>
<?php endif; ?>

<div class="filter-bar">
    <form method="GET" class="filter-form">
        <input type="text" name="search" placeholder="Search posts..." value="<?php echo htmlspecialchars($search); ?>" class="form-input">
        <button type="submit" class="btn btn-secondary">Search</button>
        <?php if ($search): ?>
        <a href="<?php echo SITE_URL; ?>/admin/blog/posts/" class="btn btn-outline">Clear</a>
        <?php endif; ?>
    </form>
</div>

<?php if (!empty($posts)): ?>
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Author</th>
                <th>Status</th>
                <th>Views</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
            <tr>
                <td>
                    <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                    <?php if ($post['featured'] === 'yes'): ?>
                    <span class="status-badge active" style="margin-left: 5px;">Featured</span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($post['category_name'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($post['author_name'] ?? '-'); ?></td>
                <td><span class="status-badge <?php echo $post['status']; ?>"><?php echo ucfirst($post['status']); ?></span></td>
                <td><?php echo number_format($post['view_count']); ?></td>
                <td><?php echo formatDate($post['created_at']); ?></td>
                <td>
                    <a href="<?php echo SITE_URL; ?>/blog/<?php echo $post['slug']; ?>" target="_blank" class="btn btn-outline btn-sm">View</a>
                    <a href="<?php echo SITE_URL; ?>/admin/blog/posts/edit.php?id=<?php echo $post['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="<?php echo SITE_URL; ?>/admin/blog/posts/delete.php?id=<?php echo $post['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Delete this post?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
       class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-outline'; ?> btn-sm">
        <?php echo $i; ?>
    </a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<?php else: ?>
<div class="empty-state">
    <span class="empty-state-icon">📝</span>
    <h3 class="empty-state-title">No Posts Yet</h3>
    <p class="empty-state-description">Create your first blog post to get started.</p>
    <a href="<?php echo SITE_URL; ?>/admin/blog/posts/create.php" class="btn btn-primary">Create Post</a>
</div>
<?php endif; ?>

<style>
.filter-bar { margin-bottom: 20px; }
.filter-form { display: flex; gap: 10px; }
.filter-form .form-input { flex: 1; max-width: 300px; }
.pagination { display: flex; gap: 5px; margin-top: 20px; justify-content: center; }
</style>

<?php include '../../../includes/admin-footer.php'; ?>