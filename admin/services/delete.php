<?php
/**
 * Delete Service (Admin)
 */
define('AJOS_INIT', true);

require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireLogin();

// Get service ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    setFlash('error', 'Invalid service ID.');
    redirect(SITE_URL . '/admin/services/index.php');
}

// Delete service
try {
    $stmt = $pdo->prepare('DELETE FROM services WHERE id = ?');
    $stmt->execute([$id]);
    setFlash('success', 'Service deleted successfully.');
} catch (PDOException $e) {
    setFlash('error', 'Failed to delete service.');
}

redirect(SITE_URL . '/admin/services/index.php');