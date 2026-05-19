<?php
/**
 * Database Connection
 */

if (!defined('AJOS_INIT')) {
    die('Direct access not allowed');
}

// Load config first
$configFile = __DIR__ . '/config.php';
if (!file_exists($configFile)) {
    die('Config file not found');
}
require_once $configFile;

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=' . DB_NAME . ';charset=utf8mb4;unix_socket=/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    http_response_code(503);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Database Error - JUST AJ</title>
        <style>
            body { font-family: Inter, sans-serif; background: #000; color: #fff; padding: 40px; }
            .error-box { max-width: 500px; margin: 0 auto; padding: 30px; background: #171717; border: 1px solid #262626; border-radius: 8px; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h1>Database Connection Error</h1>
            <p>Unable to connect to the database. Please ensure MySQL is running.</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}