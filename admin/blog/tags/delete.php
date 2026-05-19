<?php
/**
 * Delete Blog Tag (Admin)
 */
define('AJOS_INIT', true);

require_once '../../../includes/config.php';
require_once '../../../includes/db.php';
require_once '../../../includes/functions.php';
require_once '../../../includes/auth.php';

requireLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    $stmt = $pdo->prepare('DELETE FROM blog_tags WHERE id = ?');
    $stmt->execute([$id]);
    setFlash('success', 'Tag deleted.');
}

redirect(SITE_URL . '/admin/blog/tags/');