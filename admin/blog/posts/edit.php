<?php
/**
 * Edit Blog Post (Admin)
 */
define('AJOS_INIT', true);
$currentPage = 'blog-posts';
$pageTitle = 'Edit Post';

require_once '../../../includes/config.php';
require_once '../../../includes/db.php';
require_once '../../../includes/functions.php';
require_once '../../../includes/auth.php';

requireLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    setFlash('error', 'Invalid post ID.');
    redirect(SITE_URL . '/admin/blog/posts/');
}

$stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    setFlash('error', 'Post not found.');
    redirect(SITE_URL . '/admin/blog/posts/');
}

// Get SEO data
$stmt = $pdo->prepare('SELECT * FROM blog_seo WHERE post_id = ?');
$stmt->execute([$id]);
$seo = $stmt->fetch();

// Get selected tags
$stmt = $pdo->prepare('SELECT tag_id FROM blog_post_tags WHERE post_id = ?');
$stmt->execute([$id]);
$selectedTags = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Get categories
$categories = $pdo->query('SELECT * FROM blog_categories ORDER BY name')->fetchAll();

// Get tags
$tags = $pdo->query('SELECT * FROM blog_tags ORDER BY name')->fetchAll();

$currentAdmin = getCurrentAdmin();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $slug = sanitize($_POST['slug'] ?? '');
    $content = $_POST['content'] ?? '';
    $excerpt = sanitize($_POST['excerpt'] ?? '');
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT) ?: null;
    $status = sanitize($_POST['status'] ?? 'draft');
    $featured = isset($_POST['featured']) ? 'yes' : 'no';
    $published_at = $_POST['published_at'] ?? null;
    
    // SEO fields
    $seo_title = sanitize($_POST['seo_title'] ?? '');
    $seo_description = sanitize($_POST['seo_description'] ?? '');
    $seo_keywords = sanitize($_POST['seo_keywords'] ?? '');
    $og_image = sanitize($_POST['og_image'] ?? '');
    $og_title = sanitize($_POST['og_title'] ?? '');
    $og_description = sanitize($_POST['og_description'] ?? '');
    $canonical_url = sanitize($_POST['canonical_url'] ?? '');
    $index_follow = sanitize($_POST['index_follow'] ?? 'index,follow');
    
    $selected_tags = isset($_POST['tags']) ? array_map('intval', $_POST['tags']) : [];
    
    // Generate slug if empty
    if (empty($slug)) {
        $slug = generateSlug($title);
    }
    
    // Check slug uniqueness (excluding current post)
    $stmt = $pdo->prepare('SELECT id FROM blog_posts WHERE slug = ? AND id != ?');
    $stmt->execute([$slug, $id]);
    if ($stmt->fetch()) {
        $slug .= '-' . time();
    }
    
    if (empty($title)) {
        $error = 'Title is required.';
    } else {
        try {
            $pdo->beginTransaction();
            
            // Update post
            $stmt = $pdo->prepare('UPDATE blog_posts SET title = ?, slug = ?, content = ?, excerpt = ?, category_id = ?, status = ?, featured = ?, published_at = ? WHERE id = ?');
            $stmt->execute([$title, $slug, $content, $excerpt, $category_id, $status, $featured, $published_at, $id]);
            
            // Update SEO data
            if ($seo) {
                $stmt = $pdo->prepare('UPDATE blog_seo SET seo_title = ?, seo_description = ?, seo_keywords = ?, og_image = ?, og_title = ?, og_description = ?, canonical_url = ?, index_follow = ? WHERE post_id = ?');
                $stmt->execute([$seo_title, $seo_description, $seo_keywords, $og_image, $og_title, $og_description, $canonical_url, $index_follow, $id]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO blog_seo (post_id, seo_title, seo_description, seo_keywords, og_image, og_title, og_description, canonical_url, index_follow) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$id, $seo_title, $seo_description, $seo_keywords, $og_image, $og_title, $og_description, $canonical_url, $index_follow]);
            }
            
            // Update tags
            $stmt = $pdo->prepare('DELETE FROM blog_post_tags WHERE post_id = ?');
            $stmt->execute([$id]);
            
            if (!empty($selected_tags)) {
                $stmt = $pdo->prepare('INSERT INTO blog_post_tags (post_id, tag_id) VALUES (?, ?)');
                foreach ($selected_tags as $tag_id) {
                    $stmt->execute([$id, $tag_id]);
                }
            }
            
            $pdo->commit();
            setFlash('success', 'Post updated successfully.');
            redirect(SITE_URL . '/admin/blog/posts/');
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Failed to update post: ' . $e->getMessage();
        }
    }
}

// Set defaults from post
$_POST = array_merge($post, $seo ?: [], ['tags' => $selectedTags]);
?>
<?php include '../../../includes/admin-header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Edit Post</h1>
    <a href="<?php echo SITE_URL; ?>/blog/<?php echo $post['slug']; ?>" target="_blank" class="btn btn-outline">View Post</a>
</div>

<?php if ($error): ?>
<div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="post-editor">
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="editor-layout">
            <div class="editor-main">
                <div class="form-card">
                    <div class="form-group">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" id="title" name="title" class="form-input" required value="<?php echo htmlspecialchars($post['title']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="slug" class="form-label">Slug (URL)</label>
                        <input type="text" id="slug" name="slug" class="form-input" value="<?php echo htmlspecialchars($post['slug']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="content" class="form-label">Content *</label>
                        <textarea id="content" name="content" class="form-textarea" style="min-height: 400px;" placeholder="Write your blog content here..."><?php echo htmlspecialchars($post['content']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="excerpt" class="form-label">Excerpt</label>
                        <textarea id="excerpt" name="excerpt" class="form-textarea" style="min-height: 100px;"><?php echo htmlspecialchars($post['excerpt']); ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="editor-sidebar">
                <div class="form-card">
                    <h3>Publishing</h3>
                    
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="draft" <?php echo $post['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?php echo $post['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="scheduled" <?php echo $post['status'] === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                            <option value="archived" <?php echo $post['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="published_at" class="form-label">Publish Date</label>
                        <input type="datetime-local" id="published_at" name="published_at" class="form-input" value="<?php echo $post['published_at'] ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="featured" value="yes" <?php echo $post['featured'] === 'yes' ? 'checked' : ''; ?>>
                            <span>Featured Post</span>
                        </label>
                    </div>
                    
                    <div style="margin-top: 15px; padding: 10px; background: var(--color-gray-800); border-radius: 4px;">
                        <small style="color: var(--color-gray-500);">Views: <?php echo number_format($post['view_count']); ?></small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Update Post</button>
                </div>
                
                <div class="form-card">
                    <h3>Category</h3>
                    <div class="form-group">
                        <select name="category_id" class="form-select">
                            <option value="">No Category</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $post['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-card">
                    <h3>Tags</h3>
                    <div class="tags-list">
                        <?php foreach ($tags as $tag): ?>
                        <label class="tag-checkbox">
                            <input type="checkbox" name="tags[]" value="<?php echo $tag['id']; ?>" <?php echo in_array($tag['id'], $selectedTags) ? 'checked' : ''; ?>>
                            <span><?php echo htmlspecialchars($tag['name']); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <!-- SEO Section -->
    <div class="seo-section">
        <div class="form-card">
            <h3>🔍 SEO Settings</h3>
            
            <div class="seo-grid">
                <div class="form-group">
                    <label for="seo_title" class="form-label">SEO Title</label>
                    <input type="text" id="seo_title" name="seo_title" class="form-input" maxlength="255" value="<?php echo htmlspecialchars($seo['seo_title'] ?? ''); ?>">
                    <p class="form-hint">Title shown in search results (max 60 chars)</p>
                </div>
                
                <div class="form-group">
                    <label for="seo_description" class="form-label">SEO Description</label>
                    <textarea id="seo_description" name="seo_description" class="form-textarea" style="min-height: 80px;" maxlength="320"><?php echo htmlspecialchars($seo['seo_description'] ?? ''); ?></textarea>
                    <p class="form-hint">Description shown in search results (max 160 chars)</p>
                </div>
                
                <div class="form-group">
                    <label for="seo_keywords" class="form-label">Keywords</label>
                    <input type="text" id="seo_keywords" name="seo_keywords" class="form-input" placeholder="keyword1, keyword2, keyword3" value="<?php echo htmlspecialchars($seo['seo_keywords'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="canonical_url" class="form-label">Canonical URL</label>
                    <input type="url" id="canonical_url" name="canonical_url" class="form-input" value="<?php echo htmlspecialchars($seo['canonical_url'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="index_follow" class="form-label">Index/Follow</label>
                    <select id="index_follow" name="index_follow" class="form-select">
                        <option value="index,follow" <?php echo ($seo['index_follow'] ?? 'index,follow') === 'index,follow' ? 'selected' : ''; ?>>Index, Follow</option>
                        <option value="index,nofollow" <?php echo ($seo['index_follow'] ?? '') === 'index,nofollow' ? 'selected' : ''; ?>>Index, NoFollow</option>
                        <option value="noindex,follow" <?php echo ($seo['index_follow'] ?? '') === 'noindex,follow' ? 'selected' : ''; ?>>NoIndex, Follow</option>
                        <option value="noindex,nofollow" <?php echo ($seo['index_follow'] ?? '') === 'noindex,nofollow' ? 'selected' : ''; ?>>NoIndex, NoFollow</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="form-card">
            <h3>📱 Open Graph (Social Share)</h3>
            
            <div class="seo-grid">
                <div class="form-group">
                    <label for="og_title" class="form-label">OG Title</label>
                    <input type="text" id="og_title" name="og_title" class="form-input" maxlength="255" value="<?php echo htmlspecialchars($seo['og_title'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="og_description" class="form-label">OG Description</label>
                    <textarea id="og_description" name="og_description" class="form-textarea" style="min-height: 80px;"><?php echo htmlspecialchars($seo['og_description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="og_image" class="form-label">OG Image URL</label>
                    <input type="url" id="og_image" name="og_image" class="form-input" value="<?php echo htmlspecialchars($seo['og_image'] ?? ''); ?>">
                    <p class="form-hint">Recommended size: 1200x630px</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.editor-layout { display: grid; grid-template-columns: 1fr 300px; gap: 20px; margin-bottom: 20px; }
.editor-sidebar h3 { font-size: 14px; margin-bottom: 15px; color: var(--color-gray-400); }
.tags-list { display: flex; flex-wrap: wrap; gap: 8px; }
.tag-checkbox { display: flex; align-items: center; gap: 5px; font-size: 13px; }
.tag-checkbox input { width: 16px; height: 16px; }
.seo-section { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.seo-section h3 { font-size: 14px; margin-bottom: 15px; }
.seo-grid { display: grid; gap: 15px; }
@media (max-width: 768px) {
    .editor-layout, .seo-section { grid-template-columns: 1fr; }
}
</style>

<?php include '../../../includes/admin-footer.php'; ?>