<?php
/**
 * Razorpay API Helper
 * Simple wrapper for Razorpay API calls using cURL
 */

if (!defined('AJOS_INIT')) {
    die('Direct access not allowed');
}

require_once __DIR__ . '/razorpay-config.php';

class RazorpayAPI {
    private $keyId;
    private $keySecret;
    private $baseUrl;
    
    public function __construct() {
        $this->keyId = RAZORPAY_KEY_ID;
        $this->keySecret = RAZORPAY_KEY_SECRET;
        $this->baseUrl = RAZORPAY_API_URL;
    }
    
    /**
     * Make API request
     */
    private function request($endpoint, $method = 'POST', $data = null) {
        $ch = curl_init();
        $url = $this->baseUrl . $endpoint;
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->keyId . ':' . $this->keySecret)
        ];
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode >= 400) {
            $message = $result['error']['description'] ?? 'API Error';
            throw new Exception($message);
        }
        
        return $result;
    }
    
    /**
     * Create Order
     */
    public function createOrder($amount, $currency = 'INR', $receipt = null, $notes = []) {
        $data = [
            'amount' => (int)($amount * 100), // Razorpay expects amount in paise
            'currency' => $currency,
            'receipt' => $receipt ?? uniqid('order_'),
            'notes' => $notes
        ];
        
        return $this->request('/orders', 'POST', $data);
    }
    
    /**
     * Get Order Details
     */
    public function getOrder($orderId) {
        return $this->request('/orders/' . $orderId, 'GET');
    }
    
    /**
     * Verify Payment Signature
     */
    public function verifySignature($attributes) {
        $expectedSignature = hash_hmac('sha256', 
            $attributes['razorpay_order_id'] . '|' . $attributes['razorpay_payment_id'], 
            $this->keySecret
        );
        
        if ($expectedSignature === $attributes['razorpay_signature']) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get Payment Details
     */
    public function getPayment($paymentId) {
        return $this->request('/payments/' . $paymentId, 'GET');
    }
}

/**
 * Create Razorpay Order for Product
 */
function createProductOrder($product, $customerName, $customerEmail) {
    global $pdo;
    
    $razorpay = new RazorpayAPI();
    
    $orderData = $razorpay->createOrder(
        $product['price'],
        'INR',
        'product_' . $product['id'],
        [
            'product_id' => $product['id'],
            'product_name' => $product['name'],
            'customer_name' => $customerName,
            'customer_email' => $customerEmail
        ]
    );
    
    // Save order to database
    $stmt = $pdo->prepare('INSERT INTO orders (order_id, product_id, customer_name, customer_email, amount, razorpay_order_id, status) 
                           VALUES (?, ?, ?, ?, ?, ?, "pending")');
    $stmt->execute([
        $orderData['id'],
        $product['id'],
        $customerName,
        $customerEmail,
        $product['price'],
        $orderData['id']
    ]);
    
    return [
        'razorpay_order_id' => $orderData['id'],
        'amount' => $orderData['amount'],
        'currency' => $orderData['currency']
    ];
}

/**
 * Verify Payment and Update Order
 */
function verifyProductPayment($attributes) {
    global $pdo;
    
    $razorpay = new RazorpayAPI();
    
    // Verify signature
    if (!$razorpay->verifySignature($attributes)) {
        return ['success' => false, 'message' => 'Invalid signature'];
    }
    
    // Get payment details
    $payment = $razorpay->getPayment($attributes['razorpay_payment_id']);
    
    // Update order status
    $stmt = $pdo->prepare('UPDATE orders SET 
                           status = ?, 
                           payment_id = ?, 
                           updated_at = NOW() 
                           WHERE razorpay_order_id = ?');
    $stmt->execute([
        'paid',
        $attributes['razorpay_payment_id'],
        $attributes['razorpay_order_id']
    ]);
    
    return [
        'success' => true,
        'order_id' => $attributes['razorpay_order_id'],
        'payment_id' => $attributes['razorpay_payment_id']
    ];
}

/**
 * Get Order by Order ID
 */
function getOrderByRazorpayId($razorpayOrderId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT o.*, p.name as product_name, p.file_path, p.preview_image 
                           FROM orders o 
                           LEFT JOIN products p ON o.product_id = p.id 
                           WHERE o.razorpay_order_id = ?');
    $stmt->execute([$razorpayOrderId]);
    return $stmt->fetch();
}

/**
 * Check if product is already purchased
 */
function isProductPurchased($productId, $email) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT id FROM orders WHERE product_id = ? AND customer_email = ? AND status = "paid"');
    $stmt->execute([$productId, $email]);
    return $stmt->fetch() !== false;
}

/**
 * Get all orders
 */
function getOrders($limit = 50) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT o.*, p.name as product_name 
                           FROM orders o 
                           LEFT JOIN products p ON o.product_id = p.id 
                           ORDER BY o.created_at DESC 
                           LIMIT ?');
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}