<aside class="admin-sidebar">
                <div class="sidebar-header">
                    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="sidebar-logo">
                        <span class="logo-text"><?php echo htmlspecialchars(getSetting('site_name', 'AJ OS')); ?></span>
                        <span class="logo-badge">Admin</span>
                    </a>
                </div>
                <nav class="sidebar-nav">
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="nav-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                                <span class="nav-icon">◈</span>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo SITE_URL; ?>/admin/projects/index.php" class="nav-link <?php echo $currentPage === 'projects' ? 'active' : ''; ?>">
                                <span class="nav-icon">◇</span>
                                Projects
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo SITE_URL; ?>/admin/services/index.php" class="nav-link <?php echo $currentPage === 'services' ? 'active' : ''; ?>">
                                <span class="nav-icon">◇</span>
                                Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo SITE_URL; ?>/admin/leads/index.php" class="nav-link <?php echo $currentPage === 'leads' ? 'active' : ''; ?>">
                                <span class="nav-icon">◇</span>
                                Leads
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo SITE_URL; ?>/admin/settings.php" class="nav-link <?php echo $currentPage === 'settings' ? 'active' : ''; ?>">
                                <span class="nav-icon">⚙</span>
                                Settings
                            </a>
                        </li>
                    </ul>
                    <div class="nav-divider"></div>
                    <p class="nav-label">Blog</p>
                    <ul class="nav-menu">
                        <li class="nav-item nav-sub">
                            <a href="<?php echo SITE_URL; ?>/admin/blog/posts/" class="nav-link <?php echo $currentPage === 'blog-posts' ? 'active' : ''; ?>">
                                <span class="nav-icon">◇</span>
                                Posts
                            </a>
                        </li>
                        <li class="nav-item nav-sub">
                            <a href="<?php echo SITE_URL; ?>/admin/blog/categories/" class="nav-link <?php echo $currentPage === 'blog-categories' ? 'active' : ''; ?>">
                                <span class="nav-icon">◇</span>
                                Categories
                            </a>
                        </li>
                        <li class="nav-item nav-sub">
                            <a href="<?php echo SITE_URL; ?>/admin/blog/tags/" class="nav-link <?php echo $currentPage === 'blog-tags' ? 'active' : ''; ?>">
                                <span class="nav-icon">◇</span>
                                Tags
                            </a>
                        </li>
                    </ul>
                    <div class="nav-divider"></div>
                    <p class="nav-label">Tools</p>
                    <ul class="nav-menu">
                        <li class="nav-item nav-sub">
                            <a href="<?php echo SITE_URL; ?>/admin/tools/tools/" class="nav-link <?php echo $currentPage === 'tools-manage' ? 'active' : ''; ?>">
                                <span class="nav-icon">◇</span>
                                Manage Tools
                            </a>
                        </li>
                        <li class="nav-item nav-sub">
                            <a href="<?php echo SITE_URL; ?>/admin/tools/categories/" class="nav-link <?php echo $currentPage === 'tool-categories' ? 'active' : ''; ?>">
                                <span class="nav-icon">◇</span>
                                Categories
                            </a>
                        </li>
                    </ul>
                    <div class="nav-divider"></div>
                    <p class="nav-label">Coming Soon</p>
                    <ul class="nav-menu nav-disabled">
                        <li class="nav-item">
                            <span class="nav-link disabled">
                                <span class="nav-icon">◇</span>
                                AI Writer
                                <span class="badge-soon">Soon</span>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link disabled">
                                <span class="nav-icon">◇</span>
                                Products
                                <span class="badge-soon">Soon</span>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link disabled">
                                <span class="nav-icon">◇</span>
                                Analytics
                                <span class="badge-soon">Soon</span>
                            </span>
                        </li>
                    </ul>
                </nav>
            </aside>