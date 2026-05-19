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

// Send download email after successful payment
if ($result['success']) {
    $order = getOrderByRazorpayId($input['razorpay_order_id']);
    
    if ($order && !empty($order['customer_email'])) {
        $downloadUrl = BASE_URL . '/products/download.php?order=' . $input['razorpay_order_id'];
        
        // Check if product has file path
        if (!empty($order['file_path'])) {
            // Send download email
            sendPurchaseEmail(
                $order['customer_email'],
                $order['customer_name'],
                $order['product_name'],
                $order['amount'],
                $downloadUrl
            );
        }
    }
}

echo json_encode($result);