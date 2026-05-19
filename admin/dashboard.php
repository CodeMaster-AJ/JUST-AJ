<?php
/**
 * Admin Dashboard
 */
define('AJOS_INIT', true);
$currentPage = 'dashboard';
$pageTitle = 'Dashboard';

require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireLogin();

// Get stats
$totalProjects = countRows('projects');
$totalServices = countRows('services');
$totalLeads = countRows('leads');

// Get recent leads
$recentLeads = getRecentLeads(5);
?>
<?php include '../includes/admin-header.php'; ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total Projects</span>
            <span class="stat-icon">◇</span>
        </div>
        <div class="stat-value"><?php echo $totalProjects; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total Services</span>
            <span class="stat-icon">◇</span>
        </div>
        <div class="stat-value"><?php echo $totalServices; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total Leads</span>
            <span class="stat-icon">◇</span>
        </div>
        <div class="stat-value"><?php echo $totalLeads; ?></div>
    </div>
</div>

<div class="dashboard-section">
    <div class="section-header">
        <h2 class="section-title">Recent Leads</h2>
        <a href="<?php echo SITE_URL; ?>/admin/leads/index.php" class="btn btn-secondary btn-sm">View All</a>
    </div>
    
    <?php if (!empty($recentLeads)): ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentLeads as $lead): ?>
                <tr>
                    <td><?php echo htmlspecialchars($lead['name']); ?></td>
                    <td><?php echo htmlspecialchars($lead['email']); ?></td>
                    <td><?php echo htmlspecialchars($lead['subject'] ?? '-'); ?></td>
                    <td><span class="status-badge <?php echo $lead['status']; ?>"><?php echo ucfirst($lead['status']); ?></span></td>
                    <td><?php echo formatDate($lead['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <span class="empty-state-icon">◇</span>
        <h3 class="empty-state-title">No Leads Yet</h3>
        <p class="empty-state-description">Leads will appear here when someone contacts you.</p>
    </div>
    <?php endif; ?>
</div>

<div class="dashboard-section">
    <div class="section-header">
        <h2 class="section-title">Quick Actions</h2>
    </div>
    <div class="quick-actions">
        <a href="<?php echo SITE_URL; ?>/admin/projects/create.php" class="quick-action-card">
            <span class="quick-action-icon">+</span>
            <div class="quick-action-content">
                <h3 class="quick-action-title">Add Project</h3>
                <p class="quick-action-description">Create a new project</p>
            </div>
        </a>
        <a href="<?php echo SITE_URL; ?>/admin/services/create.php" class="quick-action-card">
            <span class="quick-action-icon">+</span>
            <div class="quick-action-content">
                <h3 class="quick-action-title">Add Service</h3>
                <p class="quick-action-description">Add a new service</p>
            </div>
        </a>
        <a href="<?php echo SITE_URL; ?>/admin/leads/index.php" class="quick-action-card">
            <span class="quick-action-icon">◇</span>
            <div class="quick-action-content">
                <h3 class="quick-action-title">View Leads</h3>
                <p class="quick-action-description">Check contact submissions</p>
            </div>
        </a>
    </div>
</div>

<?php include '../includes/admin-footer.php'; ?>