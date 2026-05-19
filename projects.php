<?php
/**
 * Projects Page
 */
define('AJOS_INIT', true);
$currentPage = 'projects';
$pageTitle = 'Projects';

require_once 'includes/header.php';

// Get all projects
$projects = getAllProjects();
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">My Projects</h2>
            <p class="section-subtitle">A collection of things I've built over time</p>
        </div>
        
        <?php if (!empty($projects)): ?>
        <div class="cards-grid">
            <?php foreach ($projects as $project): ?>
            <div class="card">
                <h3 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                <p class="card-description"><?php echo htmlspecialchars($project['description']); ?></p>
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
        <?php else: ?>
        <div class="empty-state">
            <span class="empty-state-icon">◇</span>
            <h3 class="empty-state-title">No Projects Yet</h3>
            <p class="empty-state-description">Check back soon for new projects.</p>
            <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">Request a Project</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="section" style="background-color: var(--color-gray-900);">
    <div class="container">
        <div class="cta-section">
            <h2 class="cta-title">Have a Project in Mind?</h2>
            <p class="cta-description">I'm open to new opportunities and collaborations.</p>
            <div class="cta-actions">
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">Let's Talk</a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>