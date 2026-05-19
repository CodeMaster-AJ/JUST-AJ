<?php
/**
 * Blog Listing Page
 */
define('AJOS_INIT', true);
$currentPage = 'blog';
$pageTitle = 'Blog';

require_once '../includes/header.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = (int)getSetting('blog_posts_per_page', 10);

// Category filter
$categorySlug = isset($_GET['category']) ? sanitize($_GET['category']) : null;
$tagSlug = isset($_GET['tag']) ? sanitize($_GET['tag']) : null;

// Search
$search = isset($_GET['s']) ? sanitize($_GET['s']) : '';

// Get category/tag if filtering
$categoryId = null;
$tagId = null;
$filterTitle = 'All Posts';

if ($categorySlug) {
    $category = getBlogCategory($categorySlug);
    if ($category) {
        $categoryId = $category['id'];
        $filterTitle = $category['name'];
    }
}

if ($tagSlug) {
    $tag = getBlogTag($tagSlug);
    if ($tag) {
        $tagId = $tag['id'];
        $filterTitle = 'Tag: ' . $tag['name'];
    }
}

// Get posts
$posts = getBlogPosts($page, $perPage, $categoryId, $tagId);
$totalPosts = getBlogPostsCount($categoryId, $tagId);
$totalPages = ceil($totalPosts / $perPage);

// Get categories for sidebar
$categories = getBlogCategories();

// Get recent posts for sidebar
$recentPosts = getBlogPosts(1, 5);
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h1 class="section-title">Blog</h1>
            <p class="section-subtitle"><?php echo htmlspecialchars($filterTitle); ?></p>
        </div>
        
        <div class="blog-layout">
            <div class="blog-main">
                <?php if (!empty($posts)): ?>
                <div class="posts-grid">
                    <?php foreach ($posts as $post): ?>
                    <article class="post-card">
                        <?php if ($post['featured_image']): ?>
                        <div class="post-image">
                            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                        </div>
                        <?php endif; ?>
                        <div class="post-content">
                            <div class="post-meta">
                                <?php if ($post['category_name']): ?>
                                <a href="<?php echo SITE_URL; ?>/blog/?category=<?php echo $post['category_slug']; ?>" class="post-category">
                                    <?php echo htmlspecialchars($post['category_name']); ?>
                                </a>
                                <?php endif; ?>
                                <span class="post-date"><?php echo formatDate($post['published_at'] ?? $post['created_at']); ?></span>
                            </div>
                            <h2 class="post-title">
                                <a href="<?php echo SITE_URL; ?>/blog/<?php echo $post['slug']; ?>"><?php echo htmlspecialchars($post['title']); ?></a>
                            </h2>
                            <p class="post-excerpt"><?php echo htmlspecialchars(truncate($post['excerpt'], 150)); ?></p>
                            <div class="post-footer">
                                <span class="post-author">By <?php echo htmlspecialchars($post['author_name'] ?? 'AJ'); ?></span>
                                <a href="<?php echo SITE_URL; ?>/blog/<?php echo $post['slug']; ?>" class="read-more">Read More →</a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo $categorySlug ? '&category=' . $categorySlug : ''; ?>" class="btn btn-outline">← Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo $categorySlug ? '&category=' . $categorySlug : ''; ?>" 
                       class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-outline'; ?>">
                        <?php echo $i; ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo $categorySlug ? '&category=' . $categorySlug : ''; ?>" class="btn btn-outline">Next →</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="empty-state">
                    <span class="empty-state-icon">📝</span>
                    <h3 class="empty-state-title">No Posts Found</h3>
                    <p class="empty-state-description">Check back soon for new content.</p>
                </div>
                <?php endif; ?>
            </div>
            
            <aside class="blog-sidebar">
                <div class="sidebar-widget">
                    <h3 class="widget-title">Categories</h3>
                    <ul class="category-list">
                        <li><a href="<?php echo SITE_URL; ?>/blog/">All Posts</a></li>
                        <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="<?php echo SITE_URL; ?>/blog/?category=<?php echo $cat['slug']; ?>" 
                               class="<?php echo $categorySlug === $cat['slug'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                                <span>(<?php echo $cat['post_count']; ?>)</span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="sidebar-widget">
                    <h3 class="widget-title">Recent Posts</h3>
                    <ul class="recent-posts-list">
                        <?php foreach (array_slice($recentPosts, 0, 5) as $recent): ?>
                        <li>
                            <a href="<?php echo SITE_URL; ?>/blog/<?php echo $recent['slug']; ?>">
                                <?php echo htmlspecialchars($recent['title']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>

<style>
.blog-layout { display: grid; grid-template-columns: 1fr 300px; gap: 40px; }
@media (max-width: 768px) { .blog-layout { grid-template-columns: 1fr; } }

.posts-grid { display: grid; gap: 30px; }
.post-card { background: var(--color-gray-900); border: 1px solid var(--color-gray-800); border-radius: 8px; overflow: hidden; }
.post-image { height: 200px; overflow: hidden; }
.post-image img { width: 100%; height: 100%; object-fit: cover; }
.post-content { padding: 20px; }
.post-meta { display: flex; gap: 15px; margin-bottom: 10px; font-size: 12px; }
.post-category { color: var(--color-white); font-weight: 600; }
.post-date { color: var(--color-gray-500); }
.post-title { font-size: 20px; margin-bottom: 10px; }
.post-title a:hover { color: var(--color-gray-300); }
.post-excerpt { color: var(--color-gray-400); font-size: 14px; margin-bottom: 15px; line-height: 1.6; }
.post-footer { display: flex; justify-content: space-between; align-items: center; }
.post-author { font-size: 12px; color: var(--color-gray-500); }
.read-more { font-size: 14px; font-weight: 500; }

.sidebar-widget { background: var(--color-gray-900); border: 1px solid var(--color-gray-800); border-radius: 8px; padding: 20px; margin-bottom: 20px; }
.widget-title { font-size: 14px; font-weight: 600; margin-bottom: 15px; color: var(--color-gray-300); }
.category-list, .recent-posts-list { list-style: none; }
.category-list li, .recent-posts-list li { margin-bottom: 8px; }
.category-list a, .recent-posts-list a { display: flex; justify-content: space-between; font-size: 14px; color: var(--color-gray-400); }
.category-list a:hover, .recent-posts-list a:hover { color: var(--color-white); }
.category-list a.active { color: var(--color-white); font-weight: 600; }

.pagination { display: flex; gap: 10px; justify-content: center; margin-top: 40px; flex-wrap: wrap; }
</style>

<?php require_once '../includes/footer.php'; ?>