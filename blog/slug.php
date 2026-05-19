<?php
/**
 * Blog Post by Slug
 * Direct URL: /blog/{slug}
 */

// Get slug from URL path
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$slug = str_replace('/blog/', '', trim($path, '/'));

if (empty($slug) || $slug === 'index.php') {
    header('Location: ' . str_replace('/post.php', '/index.php', $path));
    exit;
}

// Set slug and include post
$_GET['slug'] = $slug;
include __DIR__ . '/post.php';