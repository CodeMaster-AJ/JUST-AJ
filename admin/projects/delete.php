<?php
/**
 * Delete Project (Admin)
 */
define('AJOS_INIT', true);

require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireLogin();

// Get project ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    setFlash('error', 'Invalid project ID.');
    redirect(SITE_URL . '/admin/projects/index.php');
}

// Delete project
try {
    $stmt = $pdo->prepare('DELETE FROM projects WHERE id = ?');
    $stmt->execute([$id]);
    setFlash('success', 'Project deleted successfully.');
} catch (PDOException $e) {
    setFlash('error', 'Failed to delete project.');
}

redirect(SITE_URL . '/admin/projects/index.php');