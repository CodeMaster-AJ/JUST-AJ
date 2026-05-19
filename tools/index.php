<?php
/**
 * Tools Page - External Tools Directory
 */
require_once __DIR__ . '/../includes/config.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'Free Tools | ' . getSetting('site_name', 'JUST AJ');
$searchQuery = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$activeCategory = isset($_GET['category']) ? sanitize($_GET['category']) : null;

$categories = getToolCategories();
$allTools = $searchQuery ? searchTools($searchQuery) : getAllTools();
$featuredTools = getFeaturedTools(6);
$siteName = getSetting('site_name', 'JUST AJ');
$siteTagline = getSetting('site_tagline', 'Free tools for everyone');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="<?php echo $siteTagline; ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <?php include INCLUDES_PATH . '/header.php'; ?>

    <main class="tools-page">
        <section class="hero">
            <div class="container">
                <h1>Free Tools</h1>
                <p class="tagline">Handpicked tools to boost your productivity. Click and go.</p>
                
                <form class="search-box" action="<?php echo BASE_URL; ?>/tools/" method="GET">
                    <input type="text" name="q" placeholder="Search tools..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <button type="submit">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="M21 21l-4.35-4.35"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </section>

        <section class="tools-section">
            <div class="container">
                <?php if ($searchQuery): ?>
                    <div class="search-results-header">
                        <h2>Search results for "<?php echo htmlspecialchars($searchQuery); ?>"</h2>
                        <a href="<?php echo BASE_URL; ?>/tools/" class="clear-search">Clear search</a>
                    </div>
                    <?php if (empty($allTools)): ?>
                        <div class="no-results">
                            <p>No tools found. Try a different search term.</p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="category-tabs">
                        <a href="<?php echo BASE_URL; ?>/tools/" class="tab <?php echo !$activeCategory ? 'active' : ''; ?>">All</a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="<?php echo BASE_URL; ?>/tools/?category=<?php echo $cat['slug']; ?>" 
                               class="tab <?php echo $activeCategory === $cat['slug'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!$searchQuery && !$activeCategory && !empty($featuredTools)): ?>
                    <div class="featured-tools">
                        <h2>Featured Tools</h2>
                        <div class="tools-grid">
                            <?php foreach ($featuredTools as $tool): ?>
                                <div class="tool-card">
                                    <div class="tool-icon">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                                        </svg>
                                    </div>
                                    <h3><?php echo htmlspecialchars($tool['name']); ?></h3>
                                    <p><?php echo htmlspecialchars(truncate($tool['description'], 80)); ?></p>
                                    <span class="tool-category"><?php echo htmlspecialchars($tool['category_name']); ?></span>
                                    <a href="<?php echo BASE_URL; ?>/tools/redirect.php?slug=<?php echo $tool['slug']; ?>" 
                                       class="tool-link" target="_blank" rel="noopener">
                                        Open Tool
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                            <polyline points="15 3 21 3 21 9"></polyline>
                                            <line x1="10" y1="14" x2="21" y2="3"></line>
                                        </svg>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="all-tools">
                    <h2><?php echo $searchQuery ? 'Search Results' : ($activeCategory ? ucfirst(str_replace('-', ' ', $activeCategory)) : 'All Tools'); ?></h2>
                    <?php if ($activeCategory && !$searchQuery): ?>
                        <?php 
                        $catData = getToolCategory($activeCategory);
                        if ($catData): ?>
                            <p class="category-desc"><?php echo htmlspecialchars($catData['description']); ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php 
                    $displayTools = $searchQuery ? $allTools : (
                        $activeCategory ? (
                            $catData = getToolCategory($activeCategory),
                            $catData ? getToolsByCategory($catData['id']) : []
                        ) : $allTools
                    );
                    ?>
                    
                    <?php if (!empty($displayTools)): ?>
                        <div class="tools-grid">
                            <?php foreach ($displayTools as $tool): ?>
                                <div class="tool-card">
                                    <div class="tool-icon">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                                        </svg>
                                    </div>
                                    <h3><?php echo htmlspecialchars($tool['name']); ?></h3>
                                    <p><?php echo htmlspecialchars(truncate($tool['description'], 80)); ?></p>
                                    <span class="tool-category"><?php echo htmlspecialchars($tool['category_name']); ?></span>
                                    <a href="<?php echo BASE_URL; ?>/tools/redirect.php?slug=<?php echo $tool['slug']; ?>" 
                                       class="tool-link" target="_blank" rel="noopener">
                                        Open Tool
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                            <polyline points="15 3 21 3 21 9"></polyline>
                                            <line x1="10" y1="14" x2="21" y2="3"></line>
                                        </svg>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-results">
                            <p>No tools available in this category.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>