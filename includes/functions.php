<?php
/**
 * Helper Functions
 */

if (!defined('AJOS_INIT')) {
    die('Direct access not allowed');
}

/**
 * Sanitize input
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Get setting value
 */
function getSetting($key, $default = '') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : $default;
}

/**
 * Count rows in table
 */
function countRows($table, $condition = '1=1') {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$table} WHERE {$condition}");
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['count'];
}

/**
 * Get all active projects
 */
function getAllProjects($limit = null) {
    global $pdo;
    $sql = 'SELECT * FROM projects WHERE status = "active" ORDER BY created_at DESC';
    if ($limit) {
        $sql .= ' LIMIT ' . (int)$limit;
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

/**
 * Get featured projects
 */
function getFeaturedProjects($limit = 6) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE status = "active" AND featured = "yes" ORDER BY created_at DESC LIMIT ?');
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Get all active services
 */
function getAllServices($limit = null) {
    global $pdo;
    $sql = 'SELECT * FROM services WHERE status = "active" ORDER BY created_at DESC';
    if ($limit) {
        $sql .= ' LIMIT ' . (int)$limit;
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

/**
 * Get featured services
 */
function getFeaturedServices($limit = 6) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM services WHERE status = "active" AND featured = "yes" ORDER BY created_at DESC LIMIT ?');
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Get recent leads
 */
function getRecentLeads($limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM leads ORDER BY created_at DESC LIMIT ?');
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Generate slug from string
 */
function generateSlug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return $slug;
}

/**
 * Format date
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Truncate text
 */
function truncate($text, $length = 100) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

// ================================================
// BLOG HELPER FUNCTIONS
// ================================================

/**
 * Get all published blog posts
 */
function getBlogPosts($page = 1, $perPage = 10, $categoryId = null, $tagId = null) {
    global $pdo;
    
    $offset = ($page - 1) * $perPage;
    $where = 'WHERE bp.status = "published"';
    $params = [];
    
    if ($categoryId) {
        $where .= ' AND bp.category_id = ?';
        $params[] = $categoryId;
    }
    
    if ($tagId) {
        $where .= ' AND bp.id IN (SELECT post_id FROM blog_post_tags WHERE tag_id = ?)';
        $params[] = $tagId;
    }
    
    $sql = "SELECT bp.*, bc.name as category_name, bc.slug as category_slug, au.name as author_name
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.category_id = bc.id
            LEFT JOIN admin_users au ON bp.author_id = au.id
            $where
            ORDER BY bp.published_at DESC
            LIMIT $perPage OFFSET $offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get total blog posts count
 */
function getBlogPostsCount($categoryId = null, $tagId = null) {
    global $pdo;
    
    $where = 'WHERE status = "published"';
    $params = [];
    
    if ($categoryId) {
        $where .= ' AND category_id = ?';
        $params[] = $categoryId;
    }
    
    if ($tagId) {
        $where .= ' AND id IN (SELECT post_id FROM blog_post_tags WHERE tag_id = ?)';
        $params[] = $tagId;
    }
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM blog_posts $where");
    $stmt->execute($params);
    return $stmt->fetch()['count'];
}

/**
 * Get single blog post by slug
 */
function getBlogPost($slug) {
    global $pdo;
    
    $stmt = $pdo->prepare('SELECT bp.*, bc.name as category_name, bc.slug as category_slug, au.name as author_name
                          FROM blog_posts bp
                          LEFT JOIN blog_categories bc ON bp.category_id = bc.id
                          LEFT JOIN admin_users au ON bp.author_id = au.id
                          WHERE bp.slug = ? AND bp.status = "published"');
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Get blog SEO data
 */
function getBlogSEO($postId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM blog_seo WHERE post_id = ?');
    $stmt->execute([$postId]);
    return $stmt->fetch();
}

/**
 * Get post tags
 */
function getPostTags($postId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT bt.* FROM blog_tags bt
                          JOIN blog_post_tags bpt ON bt.id = bpt.tag_id
                          WHERE bpt.post_id = ?');
    $stmt->execute([$postId]);
    return $stmt->fetchAll();
}

/**
 * Get all categories
 */
function getBlogCategories() {
    global $pdo;
    return $pdo->query('SELECT bc.*, COUNT(bp.id) as post_count 
                        FROM blog_categories bc
                        LEFT JOIN blog_posts bp ON bc.id = bp.category_id AND bp.status = "published"
                        GROUP BY bc.id
                        ORDER BY bc.name')->fetchAll();
}

/**
 * Get category by slug
 */
function getBlogCategory($slug) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM blog_categories WHERE slug = ?');
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Get tag by slug
 */
function getBlogTag($slug) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM blog_tags WHERE slug = ?');
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Increment post view count
 */
function incrementPostViews($postId) {
    global $pdo;
    $stmt = $pdo->prepare('UPDATE blog_posts SET view_count = view_count + 1 WHERE id = ?');
    $stmt->execute([$postId]);
}

/**
 * Get featured blog posts
 */
function getFeaturedBlogPosts($limit = 3) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT bp.*, bc.name as category_name, bc.slug as category_slug
                          FROM blog_posts bp
                          LEFT JOIN blog_categories bc ON bp.category_id = bc.id
                          WHERE bp.status = "published" AND bp.featured = "yes"
                          ORDER BY bp.published_at DESC LIMIT ?');
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}