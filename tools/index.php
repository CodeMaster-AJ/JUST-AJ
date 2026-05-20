<?php
/**
 * Tools Page - External Tools Directory
 * Handpicked AI tools to help students, creators, and founders save time, build faster, and work smarter.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'Free Tools | ' . getSetting('site_name', 'JUST AJ');
$searchQuery = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$activeCategory = isset($_GET['category']) ? sanitize($_GET['category']) : null;

$categories = getToolCategories();
$allTools = $searchQuery ? searchTools($searchQuery) : getAllTools();
$featuredTools = getFeaturedTools(6);
$siteName = getSetting('site_name', 'JUST AJ');
$siteTagline = getSetting('site_tagline', 'Free tools for everyone');

function getToolCategory($slug) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM tool_categories WHERE slug = ? AND status = "active"');
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

function getToolsByCategory($categoryId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT t.*, tc.name as category_name, tc.slug as category_slug
                           FROM tools t
                           LEFT JOIN tool_categories tc ON t.category_id = tc.id
                           WHERE t.category_id = ? AND t.status = "active"
                           ORDER BY t.sort_order');
    $stmt->execute([$categoryId]);
    return $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="Handpicked AI tools to help students, creators, and founders save time, build faster, and work smarter.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php include INCLUDES_PATH . '/header.php'; ?>

    <main class="tools-page">
        <section class="tools-hero">
            <div class="container">
                <h1>Free AI Tools</h1>
                <p class="tools-hero-sub">Handpicked AI tools to help students, creators, and founders save time, build faster, and work smarter.</p>
                
                <form class="search-box" action="<?php echo BASE_URL; ?>/tools/" method="GET">
                    <input type="text" name="q" placeholder="Search tools..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <button type="submit"><i class="fa-solid fa-search"></i></button>
                </form>
                
                <div class="category-tabs">
                    <a href="<?php echo BASE_URL; ?>/tools/" class="tab <?php echo !$activeCategory && !$searchQuery ? 'active' : ''; ?>">All Tools</a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?php echo BASE_URL; ?>/tools/?category=<?php echo $cat['slug']; ?>" 
                           class="tab <?php echo $activeCategory === $cat['slug'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="tools-section">
            <div class="container">
                <?php if ($searchQuery): ?>
                    <div class="search-header">
                        <h2>Search results for "<?php echo htmlspecialchars($searchQuery); ?>"</h2>
                        <a href="<?php echo BASE_URL; ?>/tools/" class="clear-search">Clear search</a>
                    </div>
                    <?php if (empty($allTools)): ?>
                        <div class="empty-state">
                            <span class="empty-icon"><i class="fa-solid fa-search"></i></span>
                            <h3>No Tools Found</h3>
                            <p>Try a different search term.</p>
                            <a href="<?php echo BASE_URL; ?>/tools/" class="btn btn-outline">View All Tools</a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (!$searchQuery && !$activeCategory && !empty($featuredTools)): ?>
                    <div class="featured-tools">
                        <div class="section-header-inline">
                            <div>
                                <h2>Featured Tools</h2>
                                <p>Handpicked by AJ for you</p>
                            </div>
                        </div>
                        <div class="tools-grid">
                            <?php foreach ($featuredTools as $tool): ?>
                                <div class="tool-card">
                                    <div class="tool-card-top">
                                        <div class="tool-icon">
                                            <i class="fa-solid <?php echo $tool['icon'] ?? 'fa-wrench'; ?>"></i>
                                        </div>
                                        <span class="tool-badge-today">Picked Today</span>
                                    </div>
                                    <h3><?php echo htmlspecialchars($tool['name']); ?></h3>
                                    <p class="tool-use-case"><?php echo htmlspecialchars(truncate($tool['description'] ?? '', 100)); ?></p>
                                    <span class="tool-category-label"><?php echo htmlspecialchars($tool['category_name'] ?? ''); ?></span>
                                    <div class="tool-footer">
                                        <a href="<?php echo BASE_URL; ?>/tools/redirect.php?slug=<?php echo $tool['slug']; ?>" 
                                           class="tool-link" target="_blank" rel="noopener">
                                            Explore Tool <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="all-tools">
                    <h2>
                        <?php 
                        if ($searchQuery) {
                            echo 'Search Results';
                        } elseif ($activeCategory) {
                            $catData = getToolCategory($activeCategory);
                            echo $catData ? $catData['name'] : 'All Tools';
                        } else {
                            echo 'All Tools';
                        }
                        ?>
                    </h2>
                    
                    <?php if ($activeCategory && !$searchQuery): ?>
                        <?php 
                        $catData = getToolCategory($activeCategory);
                        if ($catData && !empty($catData['description'])): ?>
                            <p class="category-desc"><?php echo htmlspecialchars($catData['description']); ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php 
                    if ($searchQuery) {
                        $displayTools = $allTools;
                    } elseif ($activeCategory) {
                        $catData = getToolCategory($activeCategory);
                        $displayTools = $catData ? getToolsByCategory($catData['id']) : [];
                    } else {
                        $displayTools = $allTools;
                    }
                    ?>
                    
                    <?php if (!empty($displayTools)): ?>
                        <div class="tools-grid">
                            <?php foreach ($displayTools as $tool): ?>
                                <div class="tool-card">
                                    <div class="tool-card-top">
                                        <div class="tool-icon">
                                            <i class="fa-solid <?php echo $tool['icon'] ?? 'fa-wrench'; ?>"></i>
                                        </div>
                                        <?php if ($tool['featured'] === 'yes'): ?>
                                        <span class="tool-badge-today">Featured</span>
                                        <?php endif; ?>
                                    </div>
                                    <h3><?php echo htmlspecialchars($tool['name']); ?></h3>
                                    <p class="tool-use-case"><?php echo htmlspecialchars(truncate($tool['description'] ?? '', 100)); ?></p>
                                    <span class="tool-category-label"><?php echo htmlspecialchars($tool['category_name'] ?? ''); ?></span>
                                    <div class="tool-footer">
                                        <a href="<?php echo BASE_URL; ?>/tools/redirect.php?slug=<?php echo $tool['slug']; ?>" 
                                           class="tool-link" target="_blank" rel="noopener">
                                            Explore Tool <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif (!$searchQuery): ?>
                        <div class="empty-state">
                            <span class="empty-icon"><i class="fa-solid fa-toolbox"></i></span>
                            <h3>No Tools Available</h3>
                            <p>Check back soon for new tools.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="tools-cta">
            <div class="container">
                <div class="cta-box">
                    <h2>Want a Custom Tool or Automation?</h2>
                    <p>I can help set up AI tools and automations tailored to your needs.</p>
                    <a href="<?php echo BASE_URL; ?>/contact.php" class="btn btn-primary">Work With AJ</a>
                </div>
            </div>
        </section>
    </main>

    <?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>

<style>
.tools-hero {
    padding: var(--spacing-16) 0 var(--spacing-12);
    text-align: center;
    border-bottom: 1px solid var(--color-gray-800);
}

.tools-hero h1 {
    font-size: var(--font-size-4xl);
    margin-bottom: var(--spacing-3);
}

.tools-hero-sub {
    font-size: var(--font-size-lg);
    color: var(--color-gray-400);
    margin-bottom: var(--spacing-8);
}

.tools-hero .search-box {
    display: flex;
    max-width: 500px;
    margin: 0 auto var(--spacing-8);
    background-color: var(--color-gray-800);
    border-radius: var(--border-radius);
    overflow: hidden;
}

.tools-hero .search-box input {
    flex: 1;
    padding: var(--spacing-4);
    background: transparent;
    border: none;
    color: var(--color-white);
    font-size: var(--font-size-base);
}

.tools-hero .search-box input:focus { outline: none; }

.tools-hero .search-box button {
    padding: var(--spacing-4);
    background: transparent;
    border: none;
    color: var(--color-gray-400);
    cursor: pointer;
}

.tools-hero .search-box button:hover { color: var(--color-white); }

.category-tabs {
    display: flex;
    gap: var(--spacing-2);
    justify-content: center;
    flex-wrap: wrap;
}

.category-tabs .tab {
    padding: var(--spacing-2) var(--spacing-4);
    font-size: var(--font-size-sm);
    color: var(--color-gray-400);
    border: 1px solid var(--color-gray-700);
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
}

.category-tabs .tab:hover {
    border-color: var(--color-gray-500);
    color: var(--color-white);
}

.category-tabs .tab.active {
    background-color: var(--color-white);
    color: var(--color-black);
    border-color: var(--color-white);
}

.tools-section {
    padding: var(--spacing-12) 0;
}

.section-header-inline {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--spacing-8);
}

.section-header-inline h2 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-1);
}

.section-header-inline p {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
}

.search-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--spacing-8);
}

.search-header h2, .all-tools h2 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-6);
}

.clear-search { font-size: var(--font-size-sm); color: var(--color-gray-400); }
.clear-search:hover { color: var(--color-white); }

.category-desc {
    color: var(--color-gray-500);
    margin-bottom: var(--spacing-8);
}

.tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--spacing-6);
}

.tool-card {
    background-color: var(--color-gray-800);
    border: 1px solid var(--color-gray-700);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-6);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.tool-card:hover {
    border-color: var(--color-gray-600);
    transform: translateY(-2px);
}

.tool-card-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--spacing-4);
}

.tool-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--color-gray-700);
    border-radius: var(--border-radius);
    font-size: 20px;
}

.tool-badge-today {
    font-size: 10px;
    font-weight: 600;
    color: var(--color-white);
    background-color: rgba(251, 191, 36, 0.15);
    border: 1px solid rgba(251, 191, 36, 0.4);
    padding: 4px 10px;
    border-radius: var(--border-radius);
}

.tool-card h3 {
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin-bottom: var(--spacing-3);
}

.tool-use-case {
    font-size: var(--font-size-sm);
    color: var(--color-gray-400);
    line-height: 1.6;
    margin-bottom: var(--spacing-3);
    flex: 1;
}

.tool-category-label {
    display: inline-block;
    font-size: var(--font-size-xs);
    color: var(--color-gray-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: var(--spacing-4);
}

.tool-footer {
    margin-top: auto;
}

.tool-link {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-2);
    font-size: var(--font-size-sm);
    font-weight: 500;
    color: var(--color-white);
    padding: var(--spacing-3) var(--spacing-4);
    background-color: var(--color-black);
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
}

.tool-link:hover {
    background-color: var(--color-gray-700);
    color: var(--color-white);
}

.empty-state {
    text-align: center;
    padding: var(--spacing-16) 0;
}

.empty-icon {
    font-size: 3rem;
    color: var(--color-gray-600);
    margin-bottom: var(--spacing-4);
    display: block;
}

.empty-state h3 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-2);
}

.empty-state p {
    color: var(--color-gray-500);
    margin-bottom: var(--spacing-6);
}

.tools-cta {
    padding: var(--spacing-16) 0;
    border-top: 1px solid var(--color-gray-800);
}

.cta-box {
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
}

.cta-box h2 {
    font-size: var(--font-size-2xl);
    margin-bottom: var(--spacing-4);
}

.cta-box p {
    font-size: var(--font-size-base);
    color: var(--color-gray-400);
    margin-bottom: var(--spacing-8);
}

.featured-tools, .all-tools {
    margin-bottom: var(--spacing-12);
}

@media (max-width: 768px) {
    .tools-grid {
        grid-template-columns: 1fr;
    }
}
</style>