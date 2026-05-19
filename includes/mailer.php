<?php
/**
 * Simple SMTP Mailer
 * Send emails via Gmail SMTP without Composer
 */

if (!defined('AJOS_INIT')) {
    die('Direct access not allowed');
}

require_once __DIR__ . '/smtp-config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

class SMTPmailer {
    private $host;
    private $port;
    private $username;
    private $password;
    private $encryption;
    private $socket;
    
    public function __construct() {
        $this->host = SMTP_HOST;
        $this->port = SMTP_PORT;
        $this->username = SMTP_USERNAME;
        $this->password = SMTP_PASSWORD;
        $this->encryption = SMTP_ENCRYPTION;
    }
    
    private function connect() {
        $context = stream_context_create();
        stream_context_set_option($context, 'ssl', 'verify_peer', false);
        stream_context_set_option($context, 'ssl', 'verify_peer_name', false);
        
        $this->socket = stream_socket_client(
            'tcp://' . $this->host . ':' . $this->port,
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$this->socket) {
            throw new Exception("Connection failed: $errstr ($errno)");
        }
        
        stream_set_timeout($this->socket, 30);
        $this->readResponse();
    }
    
    private function readResponse() {
        $response = '';
        while ($line = fgets($this->socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') break;
        }
        return $response;
    }
    
    private function sendCommand($command, $expectedCode = 250) {
        fputs($this->socket, $command . "\r\n");
        $response = $this->readResponse();
        $code = substr($response, 0, 3);
        if ($code != $expectedCode) {
            throw new Exception("SMTP Error: Command '$command' returned $code, expected $expectedCode\nResponse: $response");
        }
        return $response;
    }
    
    public function send($to, $subject, $body, $isHTML = true) {
        $this->connect();
        
        // EHLO
        $this->sendCommand('EHLO ' . gethostname());
        
        // STARTTLS
        $this->sendCommand('STARTTLS', 220);
        
        // Enable TLS
        stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);
        
        // Re-EHLO after TLS
        $this->sendCommand('EHLO ' . gethostname());
        
        // AUTH LOGIN
        $this->sendCommand('AUTH LOGIN', 334);
        $this->sendCommand(base64_encode($this->username), 334);
        $this->sendCommand(base64_encode($this->password), 235);
        
        // MAIL FROM
        $this->sendCommand('MAIL FROM:<' . SMTP_USERNAME . '>');
        
        // RCPT TO
        $this->sendCommand('RCPT TO:<' . $to . '>');
        
        // DATA
        $this->sendCommand('DATA', 354);
        
        // Build email message
        $headers = [
            'From: ' . EMAIL_FROM_NAME . ' <' . EMAIL_FROM_EMAIL . '>',
            'To: ' . $to,
            'Reply-To: ' . EMAIL_REPLY_TO,
            'MIME-Version: 1.0',
            'Content-Type: ' . ($isHTML ? 'text/html; charset=UTF-8' : 'text/plain; charset=UTF-8'),
            'Subject: ' . $subject,
            'X-Mailer: JUST AJ'
        ];
        
        $message = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n";
        
        fputs($this->socket, $message . "\r\n.\r\n");
        $this->readResponse();
        
        // QUIT
        $this->sendCommand('QUIT', 221);
        fclose($this->socket);
        
        return true;
    }
}

/**
 * Send product download email
 */
function sendDownloadEmail($toEmail, $toName, $productName, $downloadUrl, $isPaid = false) {
    $siteName = getSetting('site_name', 'JUST AJ');
    $subject = $isPaid 
        ? "Your {$productName} Download - {$siteName}" 
        : "Free Download: {$productName}";
    
    $productNameSafe = htmlspecialchars($productName);
    $siteNameSafe = htmlspecialchars($siteName);
    $toNameSafe = htmlspecialchars($toName);
    
    $body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #000; color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .content h2 { margin-top: 0; color: #333; }
        .content p { color: #666; line-height: 1.6; }
        .btn { display: inline-block; background: #000; color: #fff; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .btn:hover { background: #333; }
        .footer { background: #f5f5f5; padding: 20px; text-align: center; color: #999; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>' . $siteNameSafe . '</h1>
        </div>
        <div class="content">
            <h2>Hi ' . $toNameSafe . ',</h2>
            <p>Thank you for ' . ($isPaid ? 'purchasing' : 'downloading') . ' <strong>' . $productNameSafe . '</strong>!</p>
            ' . ($isPaid ? '<p>Your payment has been confirmed. Here is your download link:</p>' : '<p>Here is your download link:</p>') . '
            <p style="text-align: center;">
                <a href="' . $downloadUrl . '" class="btn">Download Now</a>
            </p>
            <p><small>If the button does not work, copy and paste this link into your browser:<br>' . $downloadUrl . '</small></p>
        </div>
        <div class="footer">
            <p>' . $siteNameSafe . ' - Building tools, content, and systems</p>
            <p>This email was sent to ' . htmlspecialchars($toEmail) . '</p>
        </div>
    </div>
</body>
</html>';
    
    try {
        $mailer = new SMTPmailer();
        $mailer->send($toEmail, $subject, $body, true);
        return true;
    } catch (Exception $e) {
        error_log('Email error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Send purchase confirmation email
 */
function sendPurchaseEmail($toEmail, $toName, $productName, $amount, $downloadUrl) {
    $siteName = getSetting('site_name', 'JUST AJ');
    $subject = "Purchase Confirmation - {$productName}";
    
    $productNameSafe = htmlspecialchars($productName);
    $siteNameSafe = htmlspecialchars($siteName);
    $toNameSafe = htmlspecialchars($toName);
    
    $body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #000; color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .content h2 { margin-top: 0; color: #333; }
        .content p { color: #666; line-height: 1.6; }
        .receipt { background: #f5f5f5; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .receipt-row { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #ddd; }
        .receipt-row:last-child { border: none; font-weight: bold; font-size: 18px; }
        .btn { display: inline-block; background: #000; color: #fff; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .btn:hover { background: #333; }
        .footer { background: #f5f5f5; padding: 20px; text-align: center; color: #999; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>' . $siteNameSafe . '</h1>
        </div>
        <div class="content">
            <h2>Thank you for your purchase!</h2>
            <p>Hi ' . $toNameSafe . ',</p>
            <p>Your payment was successful. Here are your order details:</p>
            
            <div class="receipt">
                <div class="receipt-row">
                    <span>Product:</span>
                    <span>' . $productNameSafe . '</span>
                </div>
                <div class="receipt-row">
                    <span>Amount Paid:</span>
                    <span>₹' . number_format($amount, 2) . '</span>
                </div>
            </div>
            
            <p style="text-align: center;">
                <a href="' . $downloadUrl . '" class="btn">Download Now</a>
            </p>
            <p><small>If the button does not work, copy and paste this link:<br>' . $downloadUrl . '</small></p>
        </div>
        <div class="footer">
            <p>' . $siteNameSafe . ' - Building tools, content, and systems</p>
        </div>
    </div>
</body>
</html>';
    
    try {
        $mailer = new SMTPmailer();
        $mailer->send($toEmail, $subject, $body, true);
        return true;
    } catch (Exception $e) {
        error_log('Email error: ' . $e->getMessage());
        return false;
    }
}