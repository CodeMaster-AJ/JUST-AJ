<?php
/**
 * About Page
 */
define('AJOS_INIT', true);
$currentPage = 'about';
$pageTitle = 'About';

require_once 'includes/header.php';
?>

<section class="about-intro">
    <div class="container">
        <div class="about-content">
            <h1 class="hero-title" style="text-align: center; margin-bottom: var(--spacing-8);">About Me</h1>
            <p class="about-text">Hi, I'm AJ. I'm a builder, creator, and perpetual learner. I created JUST AJ as my personal digital space where I document my journey, share what I learn, and showcase the projects I build.</p>
            <p class="about-text">My mission is to build tools, content, and systems that help creators, students, and founders navigate the digital landscape more effectively.</p>
        </div>
    </div>
</section>

<section class="section" style="background-color: var(--color-gray-900);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">What I Value</h2>
            <p class="section-subtitle">The principles that guide my work</p>
        </div>
        <div class="values-grid">
            <div class="value-item">
                <span class="value-icon">◇</span>
                <h3 class="value-title">Build</h3>
            </div>
            <div class="value-item">
                <span class="value-icon">◇</span>
                <h3 class="value-title">Learn</h3>
            </div>
            <div class="value-item">
                <span class="value-icon">◇</span>
                <h3 class="value-title">Create</h3>
            </div>
            <div class="value-item">
                <span class="value-icon">◇</span>
                <h3 class="value-title">Grow</h3>
            </div>
        </div>
    </div>
</section>

<section class="skills-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Skills & Technologies</h2>
            <p class="section-subtitle">Tools I use to bring ideas to life</p>
        </div>
        <div class="skills-grid">
            <div class="skill-item">PHP</div>
            <div class="skill-item">MySQL</div>
            <div class="skill-item">HTML/CSS</div>
            <div class="skill-item">JavaScript</div>
            <div class="skill-item">WordPress</div>
            <div class="skill-item">Web Development</div>
            <div class="skill-item">UI/UX Design</div>
            <div class="skill-item">Content Strategy</div>
            <div class="skill-item">Project Management</div>
        </div>
    </div>
</section>

<section class="section" style="background-color: var(--color-gray-900);">
    <div class="container">
        <div class="cta-section">
            <h2 class="cta-title">Want to Work Together?</h2>
            <p class="cta-description">I'm always interested in hearing about new projects and opportunities.</p>
            <div class="cta-actions">
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">Contact Me</a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>