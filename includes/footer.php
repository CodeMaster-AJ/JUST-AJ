</main>
    <footer class="site-footer">
        <div class="container">
            <div class="footer-inner">
                <div class="footer-brand">
                    <span class="logo-text"><?php echo htmlspecialchars(getSetting('site_name', 'JUST AJ')); ?></span>
                    <p class="footer-tagline"><?php echo htmlspecialchars(getSetting('site_tagline', '')); ?></p>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/about.php">About</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/projects.php">Projects</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/services.php">Services</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-social">
                    <h4>Connect</h4>
                    <div class="social-links">
                        <a href="<?php echo getSetting('linkedin_url', '#'); ?>" target="_blank" rel="noopener" class="social-link">LinkedIn</a>
                        <a href="<?php echo getSetting('github_url', '#'); ?>" target="_blank" rel="noopener" class="social-link">GitHub</a>
                        <a href="<?php echo getSetting('instagram_url', '#'); ?>" target="_blank" rel="noopener" class="social-link">Instagram</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(getSetting('site_name', 'JUST AJ')); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>