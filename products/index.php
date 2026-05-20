<?php
/**
 * Products Page - Digital Products Store
 * Ready-to-use templates, guides, and resources to help you learn faster and build better.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'Digital Products | ' . getSetting('site_name', 'JUST AJ');
$searchQuery = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$activeCategory = isset($_GET['category']) ? sanitize($_GET['category']) : null;
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : null;

$categories = getProductCategories();
$featuredProducts = getFeaturedProducts(6);
$siteName = getSetting('site_name', 'JUST AJ');

function getProductCategory($slug) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM product_categories WHERE slug = ? AND status = "active"');
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

function getProductsByCategory($categoryId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT p.*, pc.name as category_name, pc.slug as category_slug
                           FROM products p
                           LEFT JOIN product_categories pc ON p.category_id = pc.id
                           WHERE p.category_id = ? AND p.status = "active"
                           ORDER BY p.sort_order');
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
    <meta name="description" content="Ready-to-use templates, guides, and resources to help you learn faster and build better.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php include INCLUDES_PATH . '/header.php'; ?>

    <main class="products-page">
        <section class="products-hero">
            <div class="container">
                <h1>Digital Products</h1>
                <p class="products-hero-sub">Ready-to-use templates, guides, and resources to help you learn faster and build better.</p>
                
                <form class="search-box" action="<?php echo BASE_URL; ?>/products/" method="GET">
                    <input type="text" name="q" placeholder="Search products..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <button type="submit"><i class="fa-solid fa-search"></i></button>
                </form>
                
                <div class="filter-tabs">
                    <a href="<?php echo BASE_URL; ?>/products/" class="tab <?php echo !$activeCategory && !$filter ? 'active' : ''; ?>">All</a>
                    <a href="<?php echo BASE_URL; ?>/products/?filter=free" class="tab <?php echo $filter === 'free' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-gift"></i> Free
                    </a>
                    <a href="<?php echo BASE_URL; ?>/products/?filter=paid" class="tab <?php echo $filter === 'paid' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-indian-rupee-sign"></i> Paid
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?php echo BASE_URL; ?>/products/?category=<?php echo $cat['slug']; ?>" 
                           class="tab <?php echo $activeCategory === $cat['slug'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="products-section">
            <div class="container">
                <?php if ($searchQuery): ?>
                    <div class="search-header">
                        <h2>Search results for "<?php echo htmlspecialchars($searchQuery); ?>"</h2>
                        <a href="<?php echo BASE_URL; ?>/products/" class="clear-search">Clear search</a>
                    </div>
                <?php endif; ?>

                <?php if (!$searchQuery && !$activeCategory && !$filter && !empty($featuredProducts)): ?>
                    <div class="featured-products">
                        <div class="section-header-inline">
                            <div>
                                <h2>Featured Products</h2>
                                <p>Handpicked resources for you</p>
                            </div>
                        </div>
                        <div class="products-grid">
                            <?php foreach ($featuredProducts as $product): ?>
                                <div class="product-card">
                                    <div class="product-image">
                                        <?php if ($product['is_free'] === 'yes'): ?>
                                            <span class="product-badge free">FREE</span>
                                        <?php elseif ($product['featured'] === 'yes'): ?>
                                            <span class="product-badge featured">FEATURED</span>
                                        <?php endif; ?>
                                        <?php if (!empty($product['preview_image'])): ?>
                                        <img src="<?php echo htmlspecialchars($product['preview_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php else: ?>
                                        <div class="product-placeholder">
                                            <i class="fa-solid fa-box"></i>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-content">
                                        <span class="product-category-label"><?php echo htmlspecialchars($product['category_name']); ?></span>
                                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                        <p><?php echo htmlspecialchars(truncate($product['short_description'] ?? '', 80)); ?></p>
                                        <div class="product-footer">
                                            <span class="product-price">
                                                <?php if ($product['is_free'] === 'yes'): ?>
                                                    <span class="price-free">FREE</span>
                                                <?php else: ?>
                                                    ₹<?php echo number_format($product['price'], 0); ?>
                                                <?php endif; ?>
                                            </span>
                                            <a href="<?php echo BASE_URL; ?>/products/checkout.php?slug=<?php echo $product['slug']; ?>" 
                                               class="product-link">
                                                <?php echo $product['is_free'] === 'yes' ? 'Download' : 'Get Product'; ?>
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
                            $catData = getProductCategory($activeCategory);
                            echo $catData ? $catData['name'] : 'All Products';
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
                                            <span class="product-badge-student">Best for Students</span>
                                        <?php elseif ($product['featured'] === 'yes'): ?>
                                            <span class="product-badge featured">FEATURED</span>
                                        <?php endif; ?>
                                        <?php if (!empty($product['preview_image'])): ?>
                                        <img src="<?php echo htmlspecialchars($product['preview_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php else: ?>
                                        <div class="product-placeholder">
                                            <i class="fa-solid fa-box"></i>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-content">
                                        <span class="product-category-label"><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></span>
                                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                        <p><?php echo htmlspecialchars(truncate($product['short_description'] ?? '', 80)); ?></p>
                                        <div class="product-footer">
                                            <span class="product-price">
                                                <?php if ($product['is_free'] === 'yes'): ?>
                                                    <span class="price-free">FREE</span>
                                                <?php else: ?>
                                                    ₹<?php echo number_format($product['price'], 0); ?>
                                                <?php endif; ?>
                                            </span>
                                            <a href="<?php echo BASE_URL; ?>/products/checkout.php?slug=<?php echo $product['slug']; ?>" 
                                               class="product-link">
                                                <?php echo $product['is_free'] === 'yes' ? 'Download' : 'Get Product'; ?>
                                                <i class="fa-solid fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <span class="empty-icon"><i class="fa-solid fa-box-open"></i></span>
                            <h3>No Products Found</h3>
                            <p>Check back soon for new products.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- FAQ Section -->
                <div class="faq-section">
                    <h2>Frequently Asked Questions</h2>
                    <div class="faq-grid">
                        <div class="faq-item">
                            <h3><i class="fa-solid fa-download"></i> How do I download the products?</h3>
                            <p>After purchase or download request, you'll receive a download link via email. You can also access it from your order confirmation page.</p>
                        </div>
                        <div class="faq-item">
                            <h3><i class="fa-solid fa-credit-card"></i> What payment methods are accepted?</h3>
                            <p>We accept all major credit/debit cards, UPI, and net banking through our secure payment partner, Razorpay.</p>
                        </div>
                        <div class="faq-item">
                            <h3><i class="fa-solid fa-shield-halved"></i> Are these products for beginners?</h3>
                            <p>Yes! Our products are designed to be beginner-friendly with step-by-step instructions and practical examples.</p>
                        </div>
                        <div class="faq-item">
                            <h3><i class="fa-solid fa-arrows-rotate"></i> Can I get a refund?</h3>
                            <p>If you're not satisfied with a product, contact us within 7 days and we'll work to resolve your issue.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="products-cta">
            <div class="container">
                <div class="cta-box">
                    <h2>Need a Custom Digital Product?</h2>
                    <p>I can create custom templates, guides, and digital resources tailored to your needs.</p>
                    <a href="<?php echo BASE_URL; ?>/contact.php" class="btn btn-primary">Work With AJ</a>
                </div>
            </div>
        </section>
    </main>

    <?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>

<style>
.products-hero {
    padding: var(--spacing-16) 0 var(--spacing-12);
    text-align: center;
    border-bottom: 1px solid var(--color-gray-800);
}

.products-hero h1 {
    font-size: var(--font-size-4xl);
    margin-bottom: var(--spacing-3);
}

.products-hero-sub {
    font-size: var(--font-size-lg);
    color: var(--color-gray-400);
    margin-bottom: var(--spacing-8);
}

.products-hero .search-box {
    display: flex;
    max-width: 500px;
    margin: 0 auto var(--spacing-8);
    background-color: var(--color-gray-800);
    border-radius: var(--border-radius);
    overflow: hidden;
}

.products-hero .search-box input {
    flex: 1;
    padding: var(--spacing-4);
    background: transparent;
    border: none;
    color: var(--color-white);
    font-size: var(--font-size-base);
}

.products-hero .search-box input:focus { outline: none; }

.products-hero .search-box button {
    padding: var(--spacing-4);
    background: transparent;
    border: none;
    color: var(--color-gray-400);
    cursor: pointer;
}

.products-hero .search-box button:hover { color: var(--color-white); }

.filter-tabs {
    display: flex;
    gap: var(--spacing-2);
    justify-content: center;
    flex-wrap: wrap;
}

.filter-tabs .tab {
    padding: var(--spacing-2) var(--spacing-4);
    font-size: var(--font-size-sm);
    color: var(--color-gray-400);
    border: 1px solid var(--color-gray-700);
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
}

.filter-tabs .tab:hover {
    border-color: var(--color-gray-500);
    color: var(--color-white);
}

.filter-tabs .tab.active {
    background-color: var(--color-white);
    color: var(--color-black);
    border-color: var(--color-white);
}

.filter-tabs .tab i {
    margin-right: var(--spacing-1);
}

.products-section {
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

.search-header h2, .all-products h2 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-6);
}

.clear-search { font-size: var(--font-size-sm); color: var(--color-gray-400); }
.clear-search:hover { color: var(--color-white); }

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: var(--spacing-6);
}

.product-card {
    background-color: var(--color-gray-800);
    border: 1px solid var(--color-gray-700);
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    transition: all 0.3s ease;
}

.product-card:hover {
    border-color: var(--color-gray-600);
    transform: translateY(-4px);
}

.product-image {
    position: relative;
    height: 180px;
    background-color: var(--color-gray-700);
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: var(--color-gray-600);
}

.product-badge {
    position: absolute;
    top: var(--spacing-3);
    left: var(--spacing-3);
    padding: var(--spacing-1) var(--spacing-3);
    font-size: var(--font-size-xs);
    font-weight: 600;
    border-radius: var(--border-radius);
    z-index: 2;
}

.product-badge.free {
    background-color: rgba(34, 197, 94, 0.9);
    color: var(--color-white);
}

.product-badge.featured {
    background-color: rgba(251, 191, 36, 0.9);
    color: var(--color-black);
}

.product-badge-student {
    position: absolute;
    top: var(--spacing-3);
    right: var(--spacing-3);
    background-color: rgba(59, 130, 246, 0.9);
    color: var(--color-white);
    padding: var(--spacing-1) var(--spacing-3);
    font-size: 10px;
    font-weight: 600;
    border-radius: var(--border-radius);
}

.product-content {
    padding: var(--spacing-5);
}

.product-category-label {
    display: inline-block;
    font-size: var(--font-size-xs);
    color: var(--color-gray-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: var(--spacing-2);
}

.product-card h3 {
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin-bottom: var(--spacing-2);
}

.product-card p {
    font-size: var(--font-size-sm);
    color: var(--color-gray-400);
    margin-bottom: var(--spacing-4);
    line-height: 1.6;
}

.product-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: var(--spacing-4);
    border-top: 1px solid var(--color-gray-700);
}

.product-price {
    font-size: var(--font-size-xl);
    font-weight: 700;
}

.price-free {
    color: #22c55e;
}

.product-link {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-2);
    font-size: var(--font-size-sm);
    font-weight: 500;
    color: var(--color-white);
    padding: var(--spacing-2) var(--spacing-4);
    background-color: var(--color-black);
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
}

.product-link:hover {
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

/* FAQ Section */
.faq-section {
    margin-top: var(--spacing-16);
    padding-top: var(--spacing-12);
    border-top: 1px solid var(--color-gray-800);
}

.faq-section h2 {
    font-size: var(--font-size-2xl);
    text-align: center;
    margin-bottom: var(--spacing-10);
}

.faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--spacing-6);
}

.faq-item {
    background-color: var(--color-gray-900);
    border: 1px solid var(--color-gray-800);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-6);
}

.faq-item h3 {
    font-size: var(--font-size-base);
    font-weight: 600;
    margin-bottom: var(--spacing-3);
    display: flex;
    align-items: center;
    gap: var(--spacing-2);
}

.faq-item h3 i {
    color: var(--color-gray-400);
}

.faq-item p {
    font-size: var(--font-size-sm);
    color: var(--color-gray-400);
    line-height: 1.6;
}

.products-cta {
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

.featured-products, .all-products {
    margin-bottom: var(--spacing-12);
}

@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: 1fr;
    }
    
    .faq-grid {
        grid-template-columns: 1fr;
    }
}
</style>