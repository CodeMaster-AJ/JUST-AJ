<?php
/**
 * Services Page
 */
define('AJOS_INIT', true);
$currentPage = 'services';
$pageTitle = 'Services';

require_once 'includes/header.php';

// Get all services
$services = getAllServices();
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Services</h2>
            <p class="section-subtitle">How I can help bring your vision to life</p>
        </div>
        
        <?php if (!empty($services)): ?>
        <div class="cards-grid">
            <?php foreach ($services as $service): ?>
            <div class="card service-card">
                <div class="service-icon">◈</div>
                <h3 class="card-title"><?php echo htmlspecialchars($service['title']); ?></h3>
                <p class="card-description"><?php echo htmlspecialchars($service['description']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <span class="empty-state-icon">◇</span>
            <h3 class="empty-state-title">Services Coming Soon</h3>
            <p class="empty-state-description">Check back soon for available services.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="process-section" style="background-color: var(--color-gray-900);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">My Process</h2>
            <p class="section-subtitle">How I approach every project</p>
        </div>
        <div class="process-grid">
            <div class="process-item">
                <span class="process-number">1</span>
                <h3 class="process-title">Discover</h3>
                <p style="color: var(--color-gray-500); font-size: var(--font-size-sm);">Understanding your needs and goals</p>
            </div>
            <div class="process-item">
                <span class="process-number">2</span>
                <h3 class="process-title">Build</h3>
                <p style="color: var(--color-gray-500); font-size: var(--font-size-sm);">Creating the solution</p>
            </div>
            <div class="process-item">
                <span class="process-number">3</span>
                <h3 class="process-title">Launch</h3>
                <p style="color: var(--color-gray-500); font-size: var(--font-size-sm);">Going live together</p>
            </div>
            <div class="process-item">
                <span class="process-number">4</span>
                <h3 class="process-title">Improve</h3>
                <p style="color: var(--color-gray-500); font-size: var(--font-size-sm);">Iterating based on feedback</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="cta-section">
            <h2 class="cta-title">Ready to Get Started?</h2>
            <p class="cta-description">Let's discuss your project and see how I can help.</p>
            <div class="cta-actions">
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">Contact Me</a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>