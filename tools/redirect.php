<?php
/**
 * Tools - Redirect to external tool
 */
require_once __DIR__ . '/../includes/config.php';
require_once INCLUDES_PATH . '/functions.php';

if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    redirect(BASE_URL . '/tools/');
}

$slug = sanitize($_GET['slug']);
$tool = getTool($slug);

if (!$tool) {
    setFlash('error', 'Tool not found');
    redirect(BASE_URL . '/tools/');
}

incrementToolClicks($tool['id']);

header('Location: ' . $tool['tool_url']);
exit;