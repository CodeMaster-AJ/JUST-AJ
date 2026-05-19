<?php
/**
 * Razorpay Configuration
 * 
 * IMPORTANT: This file contains sensitive API keys.
 * Add 'includes/razorpay-config.php' to .gitignore after configuring.
 */

if (!defined('AJOS_INIT')) {
    die('Direct access not allowed');
}

// Razorpay API Keys
define('RAZORPAY_KEY_ID', 'rzp_test_SrJSb3xubhGujp');
define('RAZORPAY_KEY_SECRET', 'Ti2FfndPogqhsx0y5W8Iz5FM');

// Mode (test/live)
define('RAZORPAY_MODE', 'test'); // Change to 'live' for production

// API Endpoint
define('RAZORPAY_API_URL', 'https://api.razorpay.com/v1');

// Webhook Secret (set after creating webhook on Razorpay Dashboard)
define('RAZORPAY_WEBHOOK_SECRET', ''); // Optional: Add your webhook secret here