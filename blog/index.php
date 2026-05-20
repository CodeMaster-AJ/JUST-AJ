<?php
/**
 * Blog Listing Page
 * Fresh guides, tutorials, and ideas on AI, web development, digital skills, and student growth.
 */
define('AJOS_INIT', true);
$currentPage = 'blog';
$pageTitle = 'Blog | JUST AJ';

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

// Get featured post
$featuredPost = getFeaturedBlogPosts(1);
if (!empty($featuredPost)) {
    $featuredPost = $featuredPost[0];
}
?>

<section class="blog-hero">
    <div class="container">
        <h1>Blog</h1>
        <p class="blog-hero-sub">Fresh guides, tutorials, and ideas on AI, web development, digital skills, and student growth.</p>
        
        <form class="search-box" action="<?php echo SITE_URL; ?>/blog/" method="GET">
            <input type="text" name="s" placeholder="Search blogs..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit"><i class="fa-solid fa-search"></i></button>
        </form>
        
        <div class="category-pills">
            <a href="<?php echo SITE_URL; ?>/blog/" class="pill <?php echo !$categorySlug ? 'active' : ''; ?>">All</a>
            <?php foreach ($categories as $cat): ?>
            <a href="<?php echo SITE_URL; ?>/blog/?category=<?php echo $cat['slug']; ?>" 
               class="pill <?php echo $categorySlug === $cat['slug'] ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($cat['name']); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="blog-layout">
            <div class="blog-main">
                <?php if ($search): ?>
                <div class="search-header">
                    <h2>Search results for "<?php echo htmlspecialchars($search); ?>"</h2>
                    <a href="<?php echo SITE_URL; ?>/blog/" class="clear-search">Clear search</a>
                </div>
                <?php elseif ($categorySlug): ?>
                <h2 class="filter-title"><?php echo htmlspecialchars($filterTitle); ?></h2>
                <?php endif; ?>
                
                <?php if (!empty($posts)): ?>
                <div class="posts-list">
                    <?php foreach ($posts as $post): ?>
                    <article class="post-item">
                        <?php if ($post['featured_image']): ?>
                        <div class="post-thumb">
                            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                        </div>
                        <?php endif; ?>
                        <div class="post-body">
                            <div class="post-meta">
                                <?php if ($post['category_name']): ?>
                                <span class="post-cat"><?php echo htmlspecialchars($post['category_name']); ?></span>
                                <?php endif; ?>
                                <span class="post-date"><?php echo formatDate($post['published_at'] ?? $post['created_at']); ?></span>
                            </div>
                            <h2 class="post-title">
                                <a href="<?php echo SITE_URL; ?>/blog/<?php echo $post['slug']; ?>"><?php echo htmlspecialchars($post['title']); ?></a>
                            </h2>
                            <p class="post-excerpt"><?php echo htmlspecialchars(truncate($post['excerpt'] ?? $post['content'] ?? '', 150)); ?></p>
                            <a href="<?php echo SITE_URL; ?>/blog/<?php echo $post['slug']; ?>" class="read-more">Read More →</a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo $categorySlug ? '&category=' . $categorySlug : ''; ?><?php echo $search ? '&s=' . urlencode($search) : ''; ?>" class="btn btn-outline">← Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo $categorySlug ? '&category=' . $categorySlug : ''; ?><?php echo $search ? '&s=' . urlencode($search) : ''; ?>" 
                       class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-outline'; ?>">
                        <?php echo $i; ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo $categorySlug ? '&category=' . $categorySlug : ''; ?><?php echo $search ? '&s=' . urlencode($search) : ''; ?>" class="btn btn-outline">Next →</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="empty-state">
                    <span class="empty-icon">📝</span>
                    <h3>No Posts Found</h3>
                    <p>Try a different search term or browse all posts.</p>
                    <a href="<?php echo SITE_URL; ?>/blog/" class="btn btn-outline">View All Posts</a>
                </div>
                <?php endif; ?>
            </div>
            
            <aside class="blog-sidebar">
                <?php if (!empty($featuredPost)): ?>
                <div class="sidebar-widget featured-post">
                    <span class="widget-label">Featured</span>
                    <?php if ($featuredPost['featured_image']): ?>
                    <div class="featured-img">
                        <img src="<?php echo htmlspecialchars($featuredPost['featured_image']); ?>" alt="<?php echo htmlspecialchars($featuredPost['title']); ?>">
                    </div>
                    <?php endif; ?>
                    <h3><a href="<?php echo SITE_URL; ?>/blog/<?php echo $featuredPost['slug']; ?>"><?php echo htmlspecialchars($featuredPost['title']); ?></a></h3>
                    <p><?php echo htmlspecialchars(truncate($featuredPost['excerpt'] ?? '', 100)); ?></p>
                    <a href="<?php echo SITE_URL; ?>/blog/<?php echo $featuredPost['slug']; ?>" class="read-more">Read Article →</a>
                </div>
                <?php endif; ?>
                
                <div class="sidebar-widget">
                    <h3 class="widget-title">Categories</h3>
                    <ul class="category-list">
                        <li><a href="<?php echo SITE_URL; ?>/blog/" class="<?php echo !$categorySlug ? 'active' : ''; ?>">All Posts</a></li>
                        <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="<?php echo SITE_URL; ?>/blog/?category=<?php echo $cat['slug']; ?>" 
                               class="<?php echo $categorySlug === $cat['slug'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="sidebar-widget">
                    <h3 class="widget-title">Popular Posts</h3>
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
                
                <div class="sidebar-widget newsletter-widget">
                    <h3 class="widget-title">Join the Tech List</h3>
                    <p>Get AI tools, web dev tips, and free resources in your inbox.</p>
                    <form class="sidebar-newsletter" action="#" method="POST">
                        <input type="email" name="email" placeholder="Your email" required>
                        <button type="submit" class="btn btn-primary btn-sm">Join Free</button>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</section>

<style>
.blog-hero {
    padding: var(--spacing-16) 0 var(--spacing-12);
    text-align: center;
    border-bottom: 1px solid var(--color-gray-800);
}

.blog-hero h1 {
    font-size: var(--font-size-4xl);
    margin-bottom: var(--spacing-3);
}

.blog-hero-sub {
    font-size: var(--font-size-lg);
    color: var(--color-gray-400);
    margin-bottom: var(--spacing-8);
}

.blog-hero .search-box {
    display: flex;
    max-width: 500px;
    margin: 0 auto var(--spacing-8);
    background-color: var(--color-gray-800);
    border-radius: var(--border-radius);
    overflow: hidden;
}

.blog-hero .search-box input {
    flex: 1;
    padding: var(--spacing-4);
    background: transparent;
    border: none;
    color: var(--color-white);
    font-size: var(--font-size-base);
}

.blog-hero .search-box input:focus { outline: none; }

.blog-hero .search-box button {
    padding: var(--spacing-4);
    background: transparent;
    border: none;
    color: var(--color-gray-400);
    cursor: pointer;
}

.blog-hero .search-box button:hover { color: var(--color-white); }

.category-pills {
    display: flex;
    gap: var(--spacing-2);
    justify-content: center;
    flex-wrap: wrap;
}

.pill {
    padding: var(--spacing-2) var(--spacing-4);
    font-size: var(--font-size-sm);
    color: var(--color-gray-400);
    border: 1px solid var(--color-gray-700);
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
}

.pill:hover {
    border-color: var(--color-gray-500);
    color: var(--color-white);
}

.pill.active {
    background-color: var(--color-white);
    color: var(--color-black);
    border-color: var(--color-white);
}

.blog-layout { display: grid; grid-template-columns: 1fr 300px; gap: 40px; }
@media (max-width: 900px) { .blog-layout { grid-template-columns: 1fr; } }

.blog-main { min-width: 0; }

.search-header, .filter-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--spacing-8);
    font-size: var(--font-size-xl);
}

.clear-search { font-size: var(--font-size-sm); color: var(--color-gray-400); }
.clear-search:hover { color: var(--color-white); }

.posts-list { display: flex; flex-direction: column; gap: var(--spacing-8); }

.post-item {
    display: flex;
    gap: var(--spacing-6);
    padding-bottom: var(--spacing-8);
    border-bottom: 1px solid var(--color-gray-800);
}

@media (max-width: 600px) { .post-item { flex-direction: column; } }

.post-thumb {
    width: 200px;
    height: 140px;
    flex-shrink: 0;
    border-radius: var(--border-radius-lg);
    overflow: hidden;
}

.post-thumb img { width: 100%; height: 100%; object-fit: cover; }

.post-body { flex: 1; }

.post-meta { display: flex; gap: var(--spacing-4); margin-bottom: var(--spacing-2); }
.post-cat { font-size: var(--font-size-xs); font-weight: 600; color: var(--color-gray-400); text-transform: uppercase; }
.post-date { font-size: var(--font-size-xs); color: var(--color-gray-500); }

.post-title { font-size: var(--font-size-xl); margin-bottom: var(--spacing-3); }
.post-title a { color: var(--color-white); }
.post-title a:hover { color: var(--color-gray-300); }

.post-excerpt { font-size: var(--font-size-sm); color: var(--color-gray-400); margin-bottom: var(--spacing-4); line-height: 1.6; }

.read-more { font-size: var(--font-size-sm); font-weight: 500; color: var(--color-white); }
.read-more:hover { color: var(--color-gray-300); }

.pagination { display: flex; gap: var(--spacing-2); justify-content: center; margin-top: var(--spacing-10); flex-wrap: wrap; }

.empty-state { text-align: center; padding: var(--spacing-16) 0; }
.empty-icon { font-size: 3rem; display: block; margin-bottom: var(--spacing-4); }
.empty-state h3 { font-size: var(--font-size-xl); margin-bottom: var(--spacing-2); }
.empty-state p { color: var(--color-gray-500); margin-bottom: var(--spacing-6); }

.sidebar-widget { background: var(--color-gray-900); border: 1px solid var(--color-gray-800); border-radius: var(--border-radius-lg); padding: var(--spacing-6); margin-bottom: var(--spacing-6); }
.widget-label { font-size: var(--font-size-xs); font-weight: 600; color: var(--color-gray-400); text-transform: uppercase; margin-bottom: var(--spacing-3); display: block; }
.widget-title { font-size: var(--font-size-sm); font-weight: 600; margin-bottom: var(--spacing-4); color: var(--color-gray-300); }

.featured-post .featured-img { height: 140px; border-radius: var(--border-radius); overflow: hidden; margin-bottom: var(--spacing-4); }
.featured-post .featured-img img { width: 100%; height: 100%; object-fit: cover; }
.featured-post h3 { font-size: var(--font-size-base); margin-bottom: var(--spacing-2); }
.featured-post h3 a { color: var(--color-white); }
.featured-post p { font-size: var(--font-size-sm); color: var(--color-gray-400); margin-bottom: var(--spacing-4); }

.category-list, .recent-posts-list { list-style: none; }
.category-list li, .recent-posts-list li { margin-bottom: var(--spacing-3); }
.category-list a, .recent-posts-list a { font-size: var(--font-size-sm); color: var(--color-gray-400); }
.category-list a:hover, .recent-posts-list a:hover { color: var(--color-white); }
.category-list a.active { color: var(--color-white); font-weight: 600; }

.newsletter-widget p { font-size: var(--font-size-sm); color: var(--color-gray-400); margin-bottom: var(--spacing-4); }
.sidebar-newsletter input { width: 100%; padding: var(--spacing-3); background: var(--color-gray-800); border: 1px solid var(--color-gray-700); border-radius: var(--border-radius); color: var(--color-white); margin-bottom: var(--spacing-3); font-size: var(--font-size-sm); }
.sidebar-newsletter input:focus { outline: none; border-color: var(--color-white); }
.btn-sm { padding: var(--spacing-2) var(--spacing-4); font-size: var(--font-size-sm); }
</style>

<?php require_once '../includes/footer.php'; ?>