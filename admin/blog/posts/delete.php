<?php
/**
 * Delete Blog Post (Admin)
 */
define('AJOS_INIT', true);

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

try {
    $stmt = $pdo->prepare('DELETE FROM blog_posts WHERE id = ?');
    $stmt->execute([$id]);
    setFlash('success', 'Post deleted successfully.');
} catch (PDOException $e) {
    setFlash('error', 'Failed to delete post.');
}

redirect(SITE_URL . '/admin/blog/posts/');