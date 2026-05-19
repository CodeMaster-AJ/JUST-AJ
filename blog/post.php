<?php
/**
 * Single Blog Post Page with SEO
 */
define('AJOS_INIT', true);
$currentPage = 'blog-post';

// Load includes
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';

if (empty($slug)) {
    redirect(SITE_URL . '/blog/');
}

$post = getBlogPost($slug);

if (!$post) {
    http_response_code(404);
    $pageTitle = 'Post Not Found';
    require_once '../includes/header.php';
    ?>
    <section class="section">
        <div class="container">
            <div class="empty-state" style="padding: 100px 0;">
                <h1 class="empty-state-title">404 - Post Not Found</h1>
                <p class="empty-state-description">The post you're looking for doesn't exist or has been removed.</p>
                <a href="<?php echo SITE_URL; ?>/blog/" class="btn btn-primary">Back to Blog</a>
            </div>
        </div>
    </section>
    <?php require_once '../includes/footer.php';
    exit;
}

// Increment views
incrementPostViews($post['id']);

// Get SEO data
$seo = getBlogSEO($post['id']);

// Get tags
$tags = getPostTags($post['id']);

// Get related posts (same category)
$relatedPosts = getBlogPosts(1, 3, $post['category_id']);

// Set page title
$pageTitle = htmlspecialchars($post['title']);

// SEO meta output
$seoTitle = !empty($seo['seo_title']) ? $seo['seo_title'] : $post['title'];
$seoDescription = !empty($seo['seo_description']) ? $seo['seo_description'] : truncate(strip_tags($post['excerpt']), 160);
$ogTitle = !empty($seo['og_title']) ? $seo['og_title'] : $post['title'];
$ogDescription = !empty($seo['og_description']) ? $seo['og_description'] : truncate(strip_tags($post['excerpt']), 200);
$ogImage = !empty($seo['og_image']) ? $seo['og_image'] : ($post['featured_image'] ?? '');
$canonicalUrl = !empty($seo['canonical_url']) ? $seo['canonical_url'] : SITE_URL . '/blog/' . $post['slug'];
$indexFollow = $seo['index_follow'] ?? 'index,follow';
$parts = explode(',', $indexFollow);
$robotsMeta = implode(', ', $parts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title><?php echo $seoTitle; ?> | <?php echo getSetting('site_name', 'JUST AJ'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($seoDescription); ?>">
    <?php if (!empty($seo['seo_keywords'])): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars($seo['seo_keywords']); ?>">
    <?php endif; ?>
    <meta name="robots" content="<?php echo $robotsMeta; ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($ogTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($ogDescription); ?>">
    <?php if ($ogImage): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars($ogImage); ?>">
    <?php endif; ?>
    <meta property="og:site_name" content="<?php echo getSetting('site_name', 'JUST AJ'); ?>">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($ogTitle); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($ogDescription); ?>">
    <?php if ($ogImage): ?>
    <meta property="twitter:image" content="<?php echo htmlspecialchars($ogImage); ?>">
    <?php endif; ?>
    
    <!-- Article Meta -->
    <meta property="article:published_time" content="<?php echo $post['published_at'] ?? $post['created_at']; ?>">
    <?php if (!empty($post['author_name'])): ?>
    <meta property="article:author" content="<?php echo htmlspecialchars($post['author_name']); ?>">
    <?php endif; ?>
    <?php if (!empty($post['category_name'])): ?>
    <meta property="article:section" content="<?php echo htmlspecialchars($post['category_name']); ?>">
    <?php endif; ?>
    
    <!-- Existing CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        .blog-post { max-width: 800px; margin: 0 auto; }
        .post-header { margin-bottom: 40px; }
        .post-category-link { color: var(--color-gray-400); font-size: 14px; margin-bottom: 10px; display: block; }
        .post-category-link:hover { color: var(--color-white); }
        .post-title { font-size: 36px; line-height: 1.2; margin-bottom: 20px; }
        .post-meta { display: flex; gap: 20px; color: var(--color-gray-500); font-size: 14px; margin-bottom: 20px; }
        .post-featured-image { margin-bottom: 40px; border-radius: 8px; overflow: hidden; }
        .post-featured-image img { width: 100%; height: auto; }
        .post-content { font-size: 17px; line-height: 1.8; }
        .post-content h2 { font-size: 24px; margin: 40px 0 20px; }
        .post-content h3 { font-size: 20px; margin: 30px 0 15px; }
        .post-content p { margin-bottom: 20px; }
        .post-content ul, .post-content ol { margin: 20px 0; padding-left: 30px; }
        .post-content li { margin-bottom: 10px; }
        .post-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 20px 0; }
        .post-content blockquote { border-left: 4px solid var(--color-gray-600); padding-left: 20px; margin: 30px 0; color: var(--color-gray-400); font-style: italic; }
        .post-content pre { background: var(--color-gray-900); padding: 20px; border-radius: 8px; overflow-x: auto; margin: 20px 0; }
        .post-content code { background: var(--color-gray-800); padding: 2px 6px; border-radius: 4px; font-family: monospace; }
        .post-tags { margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--color-gray-800); }
        .post-tags-title { font-size: 14px; color: var(--color-gray-500); margin-bottom: 10px; }
        .post-tag-list { display: flex; gap: 10px; flex-wrap: wrap; }
        .post-tag { padding: 5px 12px; background: var(--color-gray-900); border: 1px solid var(--color-gray-700); border-radius: 4px; font-size: 13px; color: var(--color-gray-400); }
        .post-tag:hover { border-color: var(--color-white); color: var(--color-white); }
        .share-section { margin-top: 30px; }
        .share-buttons { display: flex; gap: 10px; }
        .share-btn { padding: 8px 16px; border: 1px solid var(--color-gray-700); border-radius: 4px; font-size: 13px; }
        .share-btn:hover { border-color: var(--color-white); }
        .related-posts { margin-top: 60px; padding-top: 40px; border-top: 1px solid var(--color-gray-800); }
        .related-title { font-size: 20px; margin-bottom: 30px; }
        .related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        @media (max-width: 768px) {
            .related-grid { grid-template-columns: 1fr; }
            .post-title { font-size: 28px; }
        }
        .related-card { background: var(--color-gray-900); border: 1px solid var(--color-gray-800); border-radius: 8px; padding: 15px; }
        .related-card h4 { font-size: 14px; margin-bottom: 5px; }
        .related-card a:hover { color: var(--color-gray-300); }
        .related-card span { font-size: 12px; color: var(--color-gray-500); }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <main class="main-content">
        <section class="section">
            <div class="container">
                <article class="blog-post">
                    <header class="post-header">
                        <?php if ($post['category_name']): ?>
                        <a href="<?php echo SITE_URL; ?>/blog/?category=<?php echo $post['category_slug']; ?>" class="post-category-link">
                            <?php echo htmlspecialchars($post['category_name']); ?>
                        </a>
                        <?php endif; ?>
                        
                        <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                        
                        <div class="post-meta">
                            <span>By <?php echo htmlspecialchars($post['author_name'] ?? 'AJ'); ?></span>
                            <span><?php echo formatDate($post['published_at'] ?? $post['created_at']); ?></span>
                            <span><?php echo number_format($post['view_count']); ?> views</span>
                        </div>
                    </header>
                    
                    <?php if ($post['featured_image']): ?>
                    <div class="post-featured-image">
                        <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    </div>
                    <?php endif; ?>
                    
                    <div class="post-content">
                        <?php echo $post['content']; ?>
                    </div>
                    
                    <?php if (!empty($tags)): ?>
                    <div class="post-tags">
                        <p class="post-tags-title">Tags:</p>
                        <div class="post-tag-list">
                            <?php foreach ($tags as $tag): ?>
                            <a href="<?php echo SITE_URL; ?>/blog/?tag=<?php echo $tag['slug']; ?>" class="post-tag">
                                <?php echo htmlspecialchars($tag['name']); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="share-section">
                        <p class="post-tags-title">Share:</p>
                        <div class="share-buttons">
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($canonicalUrl); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" class="share-btn">Twitter</a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($canonicalUrl); ?>&title=<?php echo urlencode($post['title']); ?>" target="_blank" class="share-btn">LinkedIn</a>
                        </div>
                    </div>
                </article>
                
                <?php if (!empty($relatedPosts) && count($relatedPosts) > 1): ?>
                <div class="related-posts">
                    <h2 class="related-title">Related Posts</h2>
                    <div class="related-grid">
                        <?php foreach (array_filter($relatedPosts, fn($p) => $p['id'] !== $post['id']) as $related): ?>
                        <div class="related-card">
                            <h4><a href="<?php echo SITE_URL; ?>/blog/<?php echo $related['slug']; ?>"><?php echo htmlspecialchars($related['title']); ?></a></h4>
                            <span><?php echo formatDate($related['published_at'] ?? $related['created_at']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>