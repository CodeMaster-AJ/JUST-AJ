<?php
/**
 * Create Tool
 */
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/db.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$pageTitle = 'Add Tool';
$errors = [];

$categories = $pdo->query('SELECT * FROM tool_categories WHERE status = "active" ORDER BY sort_order')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $slug = generateSlug($_POST['slug'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $toolUrl = sanitize($_POST['tool_url'] ?? '');
    $icon = sanitize($_POST['icon'] ?? '');
    $featured = sanitize($_POST['featured'] ?? 'no');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $status = sanitize($_POST['status'] ?? 'active');
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($toolUrl)) {
        $errors[] = 'Tool URL is required';
    }
    
    if (empty($categoryId)) {
        $errors[] = 'Category is required';
    }
    
    if (empty($slug)) {
        $slug = generateSlug($name);
    }
    
    $stmt = $pdo->prepare('SELECT id FROM tools WHERE slug = ?');
    $stmt->execute([$slug]);
    if ($stmt->fetch()) {
        $errors[] = 'Slug already exists';
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO tools (name, slug, category_id, description, tool_url, icon, featured, sort_order, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $slug, $categoryId, $description, $toolUrl, $icon, $featured, $sortOrder, $status]);
        setFlash('success', 'Tool created successfully');
        redirect(BASE_URL . '/admin/tools/tools/');
    }
}

include INCLUDES_PATH . '/admin-header.php';
include INCLUDES_PATH . '/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="page-header">
        <div>
            <h1>Add New Tool</h1>
            <p class="page-subtitle">Add a new external tool to your directory</p>
        </div>
        <a href="index.php" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Tools
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/>
            </svg>
            <div>
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" class="admin-form">
            <div class="form-section">
                <div class="form-section-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                    </svg>
                    <h3>Basic Information</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label for="name">Tool Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required placeholder="e.g., JPG to PDF Converter" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <div class="input-with-prefix">
                            <span class="input-prefix">/tools/</span>
                            <input type="text" id="slug" name="slug" placeholder="auto-generated" value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>">
                        </div>
                        <span class="form-hint">Leave empty to auto-generate from name</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Category <span class="required">*</span></label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($_POST['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                    </svg>
                    <h3>Tool Link</h3>
                </div>
                <div class="form-group form-group-full">
                    <label for="tool_url">Tool URL <span class="required">*</span></label>
                    <input type="url" id="tool_url" name="tool_url" required placeholder="https://example.com/tool" value="<?php echo htmlspecialchars($_POST['tool_url'] ?? ''); ?>">
                    <span class="form-hint">The external URL users will be redirected to when clicking this tool</span>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                    <h3>Details</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Brief description of what this tool does..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="icon">Icon</label>
                        <div class="icon-selector">
                            <div class="icon-preview" id="iconPreview">
                                <i class="fa-solid fa-wrench"></i>
                            </div>
                            <select id="icon" name="icon" class="icon-select">
                                <option value="">Select an icon</option>
                                <optgroup label="File & Document">
                                    <option value="fa-file-pdf" <?php echo ($_POST['icon'] ?? '') === 'fa-file-pdf' ? 'selected' : ''; ?>>PDF</option>
                                    <option value="fa-file-word" <?php echo ($_POST['icon'] ?? '') === 'fa-file-word' ? 'selected' : ''; ?>>Word</option>
                                    <option value="fa-file-excel" <?php echo ($_POST['icon'] ?? '') === 'fa-file-excel' ? 'selected' : ''; ?>>Excel</option>
                                    <option value="fa-file-powerpoint" <?php echo ($_POST['icon'] ?? '') === 'fa-file-powerpoint' ? 'selected' : ''; ?>>PowerPoint</option>
                                    <option value="fa-file-image" <?php echo ($_POST['icon'] ?? '') === 'fa-file-image' ? 'selected' : ''; ?>>Image File</option>
                                    <option value="fa-file-video" <?php echo ($_POST['icon'] ?? '') === 'fa-file-video' ? 'selected' : ''; ?>>Video File</option>
                                    <option value="fa-file-audio" <?php echo ($_POST['icon'] ?? '') === 'fa-file-audio' ? 'selected' : ''; ?>>Audio File</option>
                                    <option value="fa-file-code" <?php echo ($_POST['icon'] ?? '') === 'fa-file-code' ? 'selected' : ''; ?>>Code File</option>
                                    <option value="fa-file-alt" <?php echo ($_POST['icon'] ?? '') === 'fa-file-alt' ? 'selected' : ''; ?>>Text File</option>
                                    <option value="fa-file" <?php echo ($_POST['icon'] ?? '') === 'fa-file' ? 'selected' : ''; ?>>File</option>
                                </optgroup>
                                <optgroup label="Images & Media">
                                    <option value="fa-image" <?php echo ($_POST['icon'] ?? '') === 'fa-image' ? 'selected' : ''; ?>>Image</option>
                                    <option value="fa-photo-video" <?php echo ($_POST['icon'] ?? '') === 'fa-photo-video' ? 'selected' : ''; ?>>Photo/Video</option>
                                    <option value="fa-portrait" <?php echo ($_POST['icon'] ?? '') === 'fa-portrait' ? 'selected' : ''; ?>>Portrait</option>
                                    <option value="fa-crop" <?php echo ($_POST['icon'] ?? '') === 'fa-crop' ? 'selected' : ''; ?>>Crop</option>
                                    <option value="fa-magic" <?php echo ($_POST['icon'] ?? '') === 'fa-magic' ? 'selected' : ''; ?>>Magic</option>
                                    <option value="fa-paint-brush" <?php echo ($_POST['icon'] ?? '') === 'fa-paint-brush' ? 'selected' : ''; ?>>Paint Brush</option>
                                    <option value="fa-palette" <?php echo ($_POST['icon'] ?? '') === 'fa-palette' ? 'selected' : ''; ?>>Palette</option>
                                    <option value="fa-eraser" <?php echo ($_POST['icon'] ?? '') === 'fa-eraser' ? 'selected' : ''; ?>>Eraser</option>
                                </optgroup>
                                <optgroup label="PDF Tools">
                                    <option value="fa-compress" <?php echo ($_POST['icon'] ?? '') === 'fa-compress' ? 'selected' : ''; ?>>Compress</option>
                                    <option value="fa-expand" <?php echo ($_POST['icon'] ?? '') === 'fa-expand' ? 'selected' : ''; ?>>Expand</option>
                                    <option value="fa-copy" <?php echo ($_POST['icon'] ?? '') === 'fa-copy' ? 'selected' : ''; ?>>Copy</option>
                                    <option value="fa-scissors" <?php echo ($_POST['icon'] ?? '') === 'fa-scissors' ? 'selected' : ''; ?>>Cut/Split</option>
                                    <option value="fa-object-group" <?php echo ($_POST['icon'] ?? '') === 'fa-object-group' ? 'selected' : ''; ?>>Merge</option>
                                </optgroup>
                                <optgroup label="SEO & Content">
                                    <option value="fa-search" <?php echo ($_POST['icon'] ?? '') === 'fa-search' ? 'selected' : ''; ?>>Search</option>
                                    <option value="fa-globe" <?php echo ($_POST['icon'] ?? '') === 'fa-globe' ? 'selected' : ''; ?>>Globe</option>
                                    <option value="fa-chart-line" <?php echo ($_POST['icon'] ?? '') === 'fa-chart-line' ? 'selected' : ''; ?>>Analytics</option>
                                    <option value="fa-chart-bar" <?php echo ($_POST['icon'] ?? '') === 'fa-chart-bar' ? 'selected' : ''; ?>>Chart</option>
                                    <option value="fa-key" <?php echo ($_POST['icon'] ?? '') === 'fa-key' ? 'selected' : ''; ?>>Key</option>
                                    <option value="fa-shield-alt" <?php echo ($_POST['icon'] ?? '') === 'fa-shield-alt' ? 'selected' : ''; ?>>Shield</option>
                                    <option value="fa-spell-check" <?php echo ($_POST['icon'] ?? '') === 'fa-spell-check' ? 'selected' : ''; ?>>Spell Check</option>
                                    <option value="fa-language" <?php echo ($_POST['icon'] ?? '') === 'fa-language' ? 'selected' : ''; ?>>Language</option>
                                    <option value="fa-text-width" <?php echo ($_POST['icon'] ?? '') === 'fa-text-width' ? 'selected' : ''; ?>>Text Width</option>
                                </optgroup>
                                <optgroup label="QR & Barcode">
                                    <option value="fa-qrcode" <?php echo ($_POST['icon'] ?? '') === 'fa-qrcode' ? 'selected' : ''; ?>>QR Code</option>
                                    <option value="fa-barcode" <?php echo ($_POST['icon'] ?? '') === 'fa-barcode' ? 'selected' : ''; ?>>Barcode</option>
                                </optgroup>
                                <optgroup label="Tools & Utility">
                                    <option value="fa-tools" <?php echo ($_POST['icon'] ?? '') === 'fa-tools' ? 'selected' : ''; ?>>Tools</option>
                                    <option value="fa-wrench" <?php echo ($_POST['icon'] ?? '') === 'fa-wrench' ? 'selected' : ''; ?>>Wrench</option>
                                    <option value="fa-screwdriver-wrench" <?php echo ($_POST['icon'] ?? '') === 'fa-screwdriver-wrench' ? 'selected' : ''; ?>>Wrench/Screwdriver</option>
                                    <option value="fa-hammer" <?php echo ($_POST['icon'] ?? '') === 'fa-hammer' ? 'selected' : ''; ?>>Hammer</option>
                                    <option value="fa-gear" <?php echo ($_POST['icon'] ?? '') === 'fa-gear' ? 'selected' : ''; ?>>Gear</option>
                                    <option value="fa-sliders" <?php echo ($_POST['icon'] ?? '') === 'fa-sliders' ? 'selected' : ''; ?>>Sliders</option>
                                    <option value="fa-sliders-h" <?php echo ($_POST['icon'] ?? '') === 'fa-sliders-h' ? 'selected' : ''; ?>>Sliders H</option>
                                </optgroup>
                                <optgroup label="Download & Upload">
                                    <option value="fa-download" <?php echo ($_POST['icon'] ?? '') === 'fa-download' ? 'selected' : ''; ?>>Download</option>
                                    <option value="fa-upload" <?php echo ($_POST['icon'] ?? '') === 'fa-upload' ? 'selected' : ''; ?>>Upload</option>
                                    <option value="fa-cloud-arrow-down" <?php echo ($_POST['icon'] ?? '') === 'fa-cloud-arrow-down' ? 'selected' : ''; ?>>Cloud Download</option>
                                    <option value="fa-cloud-arrow-up" <?php echo ($_POST['icon'] ?? '') === 'fa-cloud-arrow-up' ? 'selected' : ''; ?>>Cloud Upload</option>
                                </optgroup>
                                <optgroup label="Converter">
                                    <option value="fa-right-left" <?php echo ($_POST['icon'] ?? '') === 'fa-right-left' ? 'selected' : ''; ?>>Exchange</option>
                                    <option value="fa-arrows-rotate" <?php echo ($_POST['icon'] ?? '') === 'fa-arrows-rotate' ? 'selected' : ''; ?>>Rotate</option>
                                    <option value="fa-repeat" <?php echo ($_POST['icon'] ?? '') === 'fa-repeat' ? 'selected' : ''; ?>>Repeat</option>
                                </optgroup>
                                <optgroup label="Office & Productivity">
                                    <option value="fa-calculator" <?php echo ($_POST['icon'] ?? '') === 'fa-calculator' ? 'selected' : ''; ?>>Calculator</option>
                                    <option value="fa-ruler" <?php echo ($_POST['icon'] ?? '') === 'fa-ruler' ? 'selected' : ''; ?>>Ruler</option>
                                    <option value="fa-table" <?php echo ($_POST['icon'] ?? '') === 'fa-table' ? 'selected' : ''; ?>>Table</option>
                                    <option value="fa-clipboard-list" <?php echo ($_POST['icon'] ?? '') === 'fa-clipboard-list' ? 'selected' : ''; ?>>Clipboard</option>
                                    <option value="fa-note-sticky" <?php echo ($_POST['icon'] ?? '') === 'fa-note-sticky' ? 'selected' : ''; ?>>Sticky Note</option>
                                    <option value="fa-list-check" <?php echo ($_POST['icon'] ?? '') === 'fa-list-check' ? 'selected' : ''; ?>>Checklist</option>
                                </optgroup>
                                <optgroup label="General">
                                    <option value="fa-star" <?php echo ($_POST['icon'] ?? '') === 'fa-star' ? 'selected' : ''; ?>>Star</option>
                                    <option value="fa-heart" <?php echo ($_POST['icon'] ?? '') === 'fa-heart' ? 'selected' : ''; ?>>Heart</option>
                                    <option value="fa-bookmark" <?php echo ($_POST['icon'] ?? '') === 'fa-bookmark' ? 'selected' : ''; ?>>Bookmark</option>
                                    <option value="fa-link" <?php echo ($_POST['icon'] ?? '') === 'fa-link' ? 'selected' : ''; ?>>Link</option>
                                    <option value="fa-external-link" <?php echo ($_POST['icon'] ?? '') === 'fa-external-link' ? 'selected' : ''; ?>>External Link</option>
                                    <option value="fa-share" <?php echo ($_POST['icon'] ?? '') === 'fa-share' ? 'selected' : ''; ?>>Share</option>
                                    <option value="fa-print" <?php echo ($_POST['icon'] ?? '') === 'fa-print' ? 'selected' : ''; ?>>Print</option>
                                    <option value="fa-save" <?php echo ($_POST['icon'] ?? '') === 'fa-save' ? 'selected' : ''; ?>>Save</option>
                                </optgroup>
                            </select>
                        </div>
                        <span class="form-hint">Choose an icon from FontAwesome</span>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 20V10M18 20V4M6 20v-4"/>
                    </svg>
                    <h3>Settings</h3>
                </div>
                <div class="form-grid form-grid-3">
                    <div class="form-group">
                        <label for="sort_order">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order" value="<?php echo htmlspecialchars($_POST['sort_order'] ?? 0); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="featured">Featured</label>
                        <select id="featured" name="featured">
                            <option value="no" <?php echo ($_POST['featured'] ?? 'no') === 'no' ? 'selected' : ''; ?>>No</option>
                            <option value="yes" <?php echo ($_POST['featured'] ?? '') === 'yes' ? 'selected' : ''; ?>>Yes</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="active" <?php echo ($_POST['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Create Tool
                </button>
            </div>
        </form>
    </div>
</div>

<?php include INCLUDES_PATH . '/admin-footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const iconSelect = document.getElementById('icon');
    const iconPreview = document.getElementById('iconPreview');
    
    if (iconSelect && iconPreview) {
        iconSelect.addEventListener('change', function() {
            if (this.value) {
                iconPreview.innerHTML = '<i class="fa-solid ' + this.value + '"></i>';
            } else {
                iconPreview.innerHTML = '<i class="fa-solid fa-wrench"></i>';
            }
        });
    }
});
</script>