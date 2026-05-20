<?php
/**
 * Home Page - JUST AJ Brand Portal
 * AI, Web Development & Digital Skills for Students, Creators, and Founders
 */
define('AJOS_INIT', true);
$currentPage = 'home';
$pageTitle = 'JUST AJ - AI, Web Dev & Digital Skills for Students, Creators & Founders';

require_once 'includes/header.php';

// Get latest blog posts (3)
$latestPosts = getBlogPosts(1, 3);

// Get featured tools (6)
$featuredTools = getFeaturedTools(6);

// Get featured products (3)
$featuredProductsData = getFeaturedProducts(3);

// Get categories for popular categories section
$blogCategories = getBlogCategories();

// Flash message
$flash = getFlash();
?>

<!-- 1. HERO SECTION -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Learn AI, Web Development & Digital Skills with JUST AJ</h1>
            <p class="hero-subtitle">Practical blogs, free tools, templates, and digital products for students, creators, and founders who want to build smarter.</p>
            <div class="hero-actions">
                <a href="<?php echo SITE_URL; ?>/blog/" class="btn btn-primary">Explore Blogs</a>
                <a href="<?php echo SITE_URL; ?>/tools/" class="btn btn-secondary">Try Free Tools</a>
                <a href="<?php echo SITE_URL; ?>/products/" class="btn btn-outline">Shop Products</a>
            </div>
            <p class="hero-trust">Built by AJ — BTech student, web developer, and tech builder.</p>
        </div>
    </div>
</section>

<!-- 2. TRUST STRIP -->
<section class="trust-strip">
    <div class="container">
        <div class="trust-items">
            <div class="trust-item">
                <span class="trust-number">2.5K+</span>
                <span class="trust-label">LinkedIn followers</span>
            </div>
            <div class="trust-divider"></div>
            <div class="trust-item">
                <span class="trust-icon"><i class="fa-solid fa-brain"></i></span>
                <span class="trust-label">AI + Web Dev content</span>
            </div>
            <div class="trust-divider"></div>
            <div class="trust-item">
                <span class="trust-icon"><i class="fa-solid fa-toolbox"></i></span>
                <span class="trust-label">Free tools for students</span>
            </div>
            <div class="trust-divider"></div>
            <div class="trust-item">
                <span class="trust-icon"><i class="fa-solid fa-box-open"></i></span>
                <span class="trust-label">Digital products & templates</span>
            </div>
        </div>
    </div>
</section>

<!-- 3. LATEST BLOGS -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Latest Blogs</h2>
            <p class="section-subtitle">Fresh guides, tutorials, and ideas on AI, web development, digital skills, and student growth.</p>
        </div>
        
        <?php if (!empty($latestPosts)): ?>
        <div class="cards-grid">
            <?php foreach ($latestPosts as $post): ?>
            <article class="card blog-card">
                <?php if (!empty($post['featured_image'])): ?>
                <div class="card-image">
                    <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <?php if (!empty($post['category_name'])): ?>
                    <span class="card-category"><?php echo htmlspecialchars($post['category_name']); ?></span>
                    <?php endif; ?>
                    <h3 class="card-title">
                        <a href="<?php echo SITE_URL; ?>/blog/<?php echo $post['slug']; ?>"><?php echo htmlspecialchars($post['title']); ?></a>
                    </h3>
                    <p class="card-excerpt"><?php echo htmlspecialchars(truncate($post['excerpt'] ?? $post['content'] ?? '', 100)); ?></p>
                    <div class="card-footer">
                        <span class="card-date"><?php echo formatDate($post['published_at'] ?? $post['created_at']); ?></span>
                        <a href="<?php echo SITE_URL; ?>/blog/<?php echo $post['slug']; ?>" class="card-link">Read More →</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <p>No blog posts yet. Check back soon!</p>
        </div>
        <?php endif; ?>
        
        <div class="section-cta">
            <a href="<?php echo SITE_URL; ?>/blog/" class="btn btn-outline">View All Blogs</a>
        </div>
    </div>
</section>

<!-- 4. BEST AI TOOLS PICKED TODAY -->
<section class="section section-dark">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Best AI Tools Picked Today</h2>
            <p class="section-subtitle">Handpicked AI tools to help students, creators, and founders save time, build faster, and work smarter.</p>
        </div>
        
        <?php if (!empty($featuredTools)): ?>
        <div class="tools-grid-mini">
            <?php foreach (array_slice($featuredTools, 0, 6) as $tool): ?>
            <div class="tool-card-mini">
                <div class="tool-card-header">
                    <div class="tool-icon-sm">
                        <i class="fa-solid <?php echo htmlspecialchars($tool['icon'] ?? 'fa-wrench'); ?>"></i>
                    </div>
                    <span class="tool-badge-today">Picked Today</span>
                </div>
                <h4><?php echo htmlspecialchars($tool['name']); ?></h4>
                <p><?php echo htmlspecialchars(truncate($tool['description'] ?? '', 60)); ?></p>
                <span class="tool-cat"><?php echo htmlspecialchars($tool['category_name'] ?? ''); ?></span>
                <a href="<?php echo SITE_URL; ?>/tools/redirect.php?slug=<?php echo $tool['slug']; ?>" target="_blank" rel="noopener" class="tool-btn-mini">
                    Explore Tool <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <p>No tools available yet.</p>
        </div>
        <?php endif; ?>
        
        <div class="section-cta">
            <a href="<?php echo SITE_URL; ?>/tools/" class="btn btn-outline">View All Tools</a>
        </div>
    </div>
</section>

<!-- 5. FEATURED DIGITAL PRODUCTS -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Featured Digital Products</h2>
            <p class="section-subtitle">Ready-to-use templates, guides, and resources to help you learn faster and build better.</p>
        </div>
        
        <?php if (!empty($featuredProductsData)): ?>
        <div class="cards-grid">
            <?php foreach ($featuredProductsData as $product): ?>
            <div class="card product-card-home">
                <?php if ($product['is_free'] === 'yes'): ?>
                <span class="product-badge-sm free">FREE</span>
                <?php endif; ?>
                <?php if (!empty($product['preview_image'])): ?>
                <div class="card-image">
                    <img src="<?php echo htmlspecialchars($product['preview_image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <h3 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="card-excerpt"><?php echo htmlspecialchars(truncate($product['short_description'] ?? '', 80)); ?></p>
                    <div class="card-footer">
                        <span class="product-price-sm">
                            <?php if ($product['is_free'] === 'yes'): ?>
                                <span class="price-free">FREE</span>
                            <?php else: ?>
                                ₹<?php echo number_format($product['price'], 0); ?>
                            <?php endif; ?>
                        </span>
                        <a href="<?php echo SITE_URL; ?>/products/checkout.php?slug=<?php echo $product['slug']; ?>" class="card-link">
                            <?php echo $product['is_free'] === 'yes' ? 'Download' : 'Get Product'; ?> →
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <p>No products available yet.</p>
        </div>
        <?php endif; ?>
        
        <div class="section-cta">
            <a href="<?php echo SITE_URL; ?>/products/" class="btn btn-outline">View All Products</a>
        </div>
    </div>
</section>

<!-- 6. FREE RESOURCES -->
<section class="section section-dark">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Free Resources</h2>
            <p class="section-subtitle">Download useful checklists, templates, and guides made for students and builders.</p>
        </div>
        
        <div class="resources-grid">
            <div class="resource-card">
                <div class="resource-icon"><i class="fa-solid fa-robot"></i></div>
                <h4>AI Tools List for Students</h4>
                <p>A curated list of free AI tools every student should know about.</p>
                <a href="#" class="resource-btn">Download Free</a>
            </div>
            <div class="resource-card">
                <div class="resource-icon"><i class="fa-solid fa-briefcase"></i></div>
                <h4>Portfolio Website Checklist</h4>
                <p>Step-by-step checklist to build a standout portfolio website.</p>
                <a href="#" class="resource-btn">Download Free</a>
            </div>
            <div class="resource-card">
                <div class="resource-icon"><i class="fa-brands fa-linkedin"></i></div>
                <h4>LinkedIn Profile Checklist</h4>
                <p>Make your LinkedIn profile stand out and attract opportunities.</p>
                <a href="#" class="resource-btn">Download Free</a>
            </div>
            <div class="resource-card">
                <div class="resource-icon"><i class="fa-solid fa-code"></i></div>
                <h4>Web Development Roadmap</h4>
                <p>Complete roadmap from beginner to job-ready web developer.</p>
                <a href="#" class="resource-btn">Download Free</a>
            </div>
        </div>
    </div>
</section>

<!-- 7. POPULAR CATEGORIES -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Explore by Category</h2>
        </div>
        
        <div class="categories-grid">
            <a href="<?php echo SITE_URL; ?>/tools/?category=ai-tools" class="category-card">
                <i class="fa-solid fa-brain"></i>
                <span>AI Tools</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/blog/?category=web-development" class="category-card">
                <i class="fa-solid fa-code"></i>
                <span>Web Development</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/blog/?category=digital-skills" class="category-card">
                <i class="fa-solid fa-laptop"></i>
                <span>Digital Skills</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/blog/?category=student-productivity" class="category-card">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>Student Productivity</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/blog/?category=personal-branding" class="category-card">
                <i class="fa-solid fa-user"></i>
                <span>Personal Branding</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/blog/?category=freelancing" class="category-card">
                <i class="fa-solid fa-briefcase"></i>
                <span>Freelancing</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/blog/?category=startup-journey" class="category-card">
                <i class="fa-solid fa-rocket"></i>
                <span>Startup Journey</span>
            </a>
        </div>
    </div>
</section>

<!-- 8. WORK WITH ME -->
<section class="section section-dark">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Need a Website or Digital System?</h2>
            <p class="section-subtitle">I help students, creators, founders, and small businesses build clean websites, landing pages, and simple digital systems.</p>
        </div>
        
        <div class="services-grid">
            <div class="service-card-home">
                <div class="service-icon-sm"><i class="fa-solid fa-user"></i></div>
                <h3>Portfolio Website</h3>
                <p>Showcase your work with a clean, professional portfolio that gets you noticed.</p>
            </div>
            <div class="service-card-home">
                <div class="service-icon-sm"><i class="fa-solid fa-building"></i></div>
                <h3>Business Website</h3>
                <p>Get a professional online presence for your business or startup.</p>
            </div>
            <div class="service-card-home">
                <div class="service-icon-sm"><i class="fa-solid fa-file-signature"></i></div>
                <h3>Landing Page</h3>
                <p>High-converting landing pages for your product, service, or campaign.</p>
            </div>
            <div class="service-card-home">
                <div class="service-icon-sm"><i class="fa-solid fa-microchip"></i></div>
                <h3>AI Tool / Automation Setup</h3>
                <p>Set up AI tools and automations to save time and work smarter.</p>
            </div>
        </div>
        
        <div class="section-cta">
            <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">Work With AJ</a>
        </div>
    </div>
</section>

<!-- 9. TESTIMONIALS / EARLY FEEDBACK -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">What People Say</h2>
        </div>
        
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <p class="testimonial-text">"AJ explains tech in a simple and practical way. His content is useful for students who want to build real skills."</p>
                <span class="testimonial-label">Early Reader Feedback</span>
            </div>
            <div class="testimonial-card">
                <p class="testimonial-text">"The resources are clear, beginner-friendly, and focused on action instead of theory."</p>
                <span class="testimonial-label">Student Builder</span>
            </div>
            <div class="testimonial-card">
                <p class="testimonial-text">"JUST AJ feels like a practical tech hub for students, creators, and young founders."</p>
                <span class="testimonial-label">Creator Feedback</span>
            </div>
        </div>
    </div>
</section>

<!-- 10. NEWSLETTER CTA -->
<section class="section newsletter-section">
    <div class="container">
        <div class="newsletter-box">
            <div class="newsletter-content">
                <h2>Join the JUST AJ Tech List</h2>
                <p>Get AI tools, web development tips, free resources, and digital skill guides directly in your inbox.</p>
                <form class="newsletter-form" action="#" method="POST">
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <button type="submit" class="btn btn-primary">Join Free</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- 11. FINAL CTA -->
<section class="section section-dark">
    <div class="container">
        <div class="final-cta">
            <h2>Start Building Smarter Today</h2>
            <p>Explore blogs, try free tools, download resources, or work with AJ to build your next digital project.</p>
            <div class="final-cta-buttons">
                <a href="<?php echo SITE_URL; ?>/blog/" class="btn btn-primary">Read Blogs</a>
                <a href="<?php echo SITE_URL; ?>/tools/" class="btn btn-secondary">Explore Tools</a>
                <a href="<?php echo SITE_URL; ?>/products/" class="btn btn-outline">View Products</a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>