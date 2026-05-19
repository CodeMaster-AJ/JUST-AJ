<?php
/**
 * Products Page - Digital Products Store
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'Products | ' . getSetting('site_name', 'JUST AJ');
$searchQuery = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$activeCategory = isset($_GET['category']) ? sanitize($_GET['category']) : null;
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : null;

$categories = getProductCategories();
$featuredProducts = getFeaturedProducts(6);
$siteName = getSetting('site_name', 'JUST AJ');
$siteTagline = getSetting('site_tagline', 'Digital products for creators');
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php include INCLUDES_PATH . '/header.php'; ?>

    <main class="products-page">
        <section class="hero">
            <div class="container">
                <h1>Digital Products</h1>
                <p class="tagline">Templates, e-books, presets, and source files for creators and developers</p>
                
                <form class="search-box" action="<?php echo BASE_URL; ?>/products/" method="GET">
                    <input type="text" name="q" placeholder="Search products..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <button type="submit">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </form>
            </div>
        </section>

        <section class="products-section">
            <div class="container">
                <?php if ($searchQuery): ?>
                    <div class="search-results-header">
                        <h2>Search results for "<?php echo htmlspecialchars($searchQuery); ?>"</h2>
                        <a href="<?php echo BASE_URL; ?>/products/" class="clear-search">Clear search</a>
                    </div>
                <?php else: ?>
                    <div class="category-tabs">
                        <a href="<?php echo BASE_URL; ?>/products/" class="tab <?php echo !$activeCategory && !$filter ? 'active' : ''; ?>">All</a>
                        <a href="<?php echo BASE_URL; ?>/products/?filter=free" class="tab <?php echo $filter === 'free' ? 'active' : ''; ?>">
                            <i class="fa-solid fa-gift"></i> Free
                        </a>
                        <a href="<?php echo BASE_URL; ?>/products/?filter=paid" class="tab <?php echo $filter === 'paid' ? 'active' : ''; ?>">
                            <i class="fa-solid fa-dollar-sign"></i> Paid
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="<?php echo BASE_URL; ?>/products/?category=<?php echo $cat['slug']; ?>" 
                               class="tab <?php echo $activeCategory === $cat['slug'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!$searchQuery && !$activeCategory && !$filter && !empty($featuredProducts)): ?>
                    <div class="featured-products">
                        <h2>Featured Products</h2>
                        <div class="products-grid">
                            <?php foreach ($featuredProducts as $product): ?>
                                <div class="product-card">
                                    <div class="product-image">
                                        <?php if ($product['is_free'] === 'yes'): ?>
                                            <span class="product-badge free">FREE</span>
                                        <?php elseif ($product['featured'] === 'yes'): ?>
                                            <span class="product-badge featured">FEATURED</span>
                                        <?php endif; ?>
                                        <img src="<?php echo htmlspecialchars($product['preview_image'] ?? 'https://via.placeholder.com/400x300/171717/ffffff?text=Product'); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    </div>
                                    <div class="product-content">
                                        <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                        <p><?php echo htmlspecialchars(truncate($product['short_description'] ?? '', 80)); ?></p>
                                        <div class="product-footer">
                                            <span class="product-price">
                                                <?php if ($product['is_free'] === 'yes'): ?>
                                                    <span class="price-free">FREE</span>
                                                <?php else: ?>
                                                    $<?php echo number_format($product['price'], 2); ?>
                                                <?php endif; ?>
                                            </span>
                                            <a href="<?php echo BASE_URL; ?>/products/download.php?slug=<?php echo $product['slug']; ?>" 
                                               class="product-link">
                                                <?php echo $product['is_free'] === 'yes' ? 'Download' : 'Get Now'; ?>
                                                <i class="fa-solid fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="all-products">
                    <h2>
                        <?php 
                        if ($searchQuery) {
                            echo 'Search Results';
                        } elseif ($filter === 'free') {
                            echo 'Free Products';
                        } elseif ($filter === 'paid') {
                            echo 'Paid Products';
                        } elseif ($activeCategory) {
                            echo ucfirst(str_replace('-', ' ', $activeCategory));
                        } else {
                            echo 'All Products';
                        }
                        ?>
                    </h2>
                    
                    <?php 
                    if ($searchQuery) {
                        $displayProducts = searchProducts($searchQuery);
                    } elseif ($filter === 'free') {
                        $displayProducts = getFreeProducts();
                    } elseif ($filter === 'paid') {
                        $displayProducts = getPaidProducts();
                    } elseif ($activeCategory) {
                        $catData = getProductCategory($activeCategory);
                        $displayProducts = $catData ? getProductsByCategory($catData['id']) : [];
                    } else {
                        $displayProducts = getAllProducts();
                    }
                    ?>
                    
                    <?php if (!empty($displayProducts)): ?>
                        <div class="products-grid">
                            <?php foreach ($displayProducts as $product): ?>
                                <div class="product-card">
                                    <div class="product-image">
                                        <?php if ($product['is_free'] === 'yes'): ?>
                                            <span class="product-badge free">FREE</span>
                                        <?php elseif ($product['featured'] === 'yes'): ?>
                                            <span class="product-badge featured">FEATURED</span>
                                        <?php endif; ?>
                                        <img src="<?php echo htmlspecialchars($product['preview_image'] ?? 'https://via.placeholder.com/400x300/171717/ffffff?text=Product'); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    </div>
                                    <div class="product-content">
                                        <span class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></span>
                                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                        <p><?php echo htmlspecialchars(truncate($product['short_description'] ?? '', 80)); ?></p>
                                        <div class="product-footer">
                                            <span class="product-price">
                                                <?php if ($product['is_free'] === 'yes'): ?>
                                                    <span class="price-free">FREE</span>
                                                <?php else: ?>
                                                    $<?php echo number_format($product['price'], 2); ?>
                                                <?php endif; ?>
                                            </span>
                                            <a href="<?php echo BASE_URL; ?>/products/download.php?slug=<?php echo $product['slug']; ?>" 
                                               class="product-link">
                                                <?php echo $product['is_free'] === 'yes' ? 'Download' : 'Get Now'; ?>
                                                <i class="fa-solid fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-results">
                            <i class="fa-solid fa-box-open"></i>
                            <p>No products found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>