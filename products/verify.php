<?php
/**
 * Payment Verification Handler
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/razorpay.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['razorpay_order_id']) || !isset($input['razorpay_payment_id']) || !isset($input['razorpay_signature'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$result = verifyProductPayment($input);

echo json_encode($result);