<?php
/**
 * Home Page
 */
define('AJOS_INIT', true);
$currentPage = 'home';
$pageTitle = 'Home';

require_once 'includes/header.php';

// Get featured projects
$featuredProjects = getFeaturedProjects(6);

// Get featured services
$featuredServices = getFeaturedServices(6);

// Flash message
$flash = getFlash();
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title"><?php echo htmlspecialchars(getSetting('site_tagline', 'Building tools, content, and systems for creators, students, and founders.')); ?></h1>
            <p class="hero-subtitle"><?php echo htmlspecialchars(getSetting('site_tagline', '')); ?></p>
            <div class="hero-actions">
                <a href="<?php echo SITE_URL; ?>/projects.php" class="btn btn-primary">View Projects</a>
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-secondary">Work With Me</a>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($featuredProjects)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Featured Projects</h2>
            <p class="section-subtitle">A collection of projects I've built and shipped</p>
        </div>
        <div class="cards-grid">
            <?php foreach ($featuredProjects as $project): ?>
            <div class="card">
                <h3 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                <p class="card-description"><?php echo htmlspecialchars(truncate($project['description'], 100)); ?></p>
                <?php if ($project['tech_stack']): ?>
                <p class="card-tech"><?php echo htmlspecialchars($project['tech_stack']); ?></p>
                <?php endif; ?>
                <div class="card-links">
                    <?php if ($project['live_link'] && $project['live_link'] !== '#'): ?>
                    <a href="<?php echo htmlspecialchars($project['live_link']); ?>" target="_blank" rel="noopener" class="card-link">Live Demo →</a>
                    <?php endif; ?>
                    <?php if ($project['github_link'] && $project['github_link'] !== '#'): ?>
                    <a href="<?php echo htmlspecialchars($project['github_link']); ?>" target="_blank" rel="noopener" class="card-link">GitHub →</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center;">
            <a href="<?php echo SITE_URL; ?>/projects.php" class="view-all-link">View All Projects →</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($featuredServices)): ?>
<section class="section" style="background-color: var(--color-gray-900);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">What I Do</h2>
            <p class="section-subtitle">Services I offer to help bring your vision to life</p>
        </div>
        <div class="cards-grid">
            <?php foreach ($featuredServices as $service): ?>
            <div class="card service-card">
                <div class="service-icon">◈</div>
                <h3 class="card-title"><?php echo htmlspecialchars($service['title']); ?></h3>
                <p class="card-description"><?php echo htmlspecialchars(truncate($service['description'], 120)); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center;">
            <a href="<?php echo SITE_URL; ?>/services.php" class="view-all-link">View All Services →</a>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="container">
        <div class="cta-section">
            <h2 class="cta-title">Let's Build Something Together</h2>
            <p class="cta-description">Have a project in mind? I'm always open to discussing new opportunities and interesting ideas.</p>
            <div class="cta-actions">
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">Get In Touch</a>
                <a href="<?php echo SITE_URL; ?>/about.php" class="btn btn-outline">Learn About Me</a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>