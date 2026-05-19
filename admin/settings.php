<?php
/**
 * Settings Page (Admin)
 */
define('AJOS_INIT', true);
$currentPage = 'settings';
$pageTitle = 'Settings';

require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireLogin();

$error = '';
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = sanitize($_POST['site_name'] ?? '');
    $site_tagline = sanitize($_POST['site_tagline'] ?? '');
    $contact_email = sanitize($_POST['contact_email'] ?? '');
    $linkedin_url = sanitize($_POST['linkedin_url'] ?? '');
    $github_url = sanitize($_POST['github_url'] ?? '');
    $instagram_url = sanitize($_POST['instagram_url'] ?? '');
    
    try {
        // Update or insert settings
        $settings = [
            'site_name' => $site_name,
            'site_tagline' => $site_tagline,
            'contact_email' => $contact_email,
            'linkedin_url' => $linkedin_url,
            'github_url' => $github_url,
            'instagram_url' => $instagram_url
        ];
        
        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?');
            $stmt->execute([$key, $value, $value]);
        }
        
        setFlash('success', 'Settings saved successfully.');
        redirect(SITE_URL . '/admin/settings.php');
    } catch (PDOException $e) {
        $error = 'Failed to save settings.';
    }
}

// Get current settings
$settings = [
    'site_name' => getSetting('site_name', 'JUST AJ'),
    'site_tagline' => getSetting('site_tagline', ''),
    'contact_email' => getSetting('contact_email', ''),
    'linkedin_url' => getSetting('linkedin_url', '#'),
    'github_url' => getSetting('github_url', '#'),
    'instagram_url' => getSetting('instagram_url', '#')
];
?>
<?php include '../includes/admin-header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Settings</h1>
</div>

<div class="form-container">
    <div class="form-card">
        <?php $flash = getFlash(); ?>
        <?php if ($flash): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="site_name" class="form-label">Site Name</label>
                <input type="text" id="site_name" name="site_name" class="form-input" required value="<?php echo htmlspecialchars($settings['site_name']); ?>">
            </div>
            
            <div class="form-group">
                <label for="site_tagline" class="form-label">Site Tagline</label>
                <input type="text" id="site_tagline" name="site_tagline" class="form-input" value="<?php echo htmlspecialchars($settings['site_tagline']); ?>">
            </div>
            
            <div class="form-group">
                <label for="contact_email" class="form-label">Contact Email</label>
                <input type="email" id="contact_email" name="contact_email" class="form-input" value="<?php echo htmlspecialchars($settings['contact_email']); ?>">
            </div>
            
            <div class="form-group">
                <label for="linkedin_url" class="form-label">LinkedIn URL</label>
                <input type="url" id="linkedin_url" name="linkedin_url" class="form-input" value="<?php echo htmlspecialchars($settings['linkedin_url']); ?>">
            </div>
            
            <div class="form-group">
                <label for="github_url" class="form-label">GitHub URL</label>
                <input type="url" id="github_url" name="github_url" class="form-input" value="<?php echo htmlspecialchars($settings['github_url']); ?>">
            </div>
            
            <div class="form-group">
                <label for="instagram_url" class="form-label">Instagram URL</label>
                <input type="url" id="instagram_url" name="instagram_url" class="form-input" value="<?php echo htmlspecialchars($settings['instagram_url']); ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/admin-footer.php'; ?>