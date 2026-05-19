<?php
/**
 * Contact Page
 */
define('AJOS_INIT', true);
$currentPage = 'contact';
$pageTitle = 'Contact';

require_once 'includes/header.php';

// Handle form submission
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Insert into database
        try {
            $stmt = $pdo->prepare('INSERT INTO leads (name, email, subject, message) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, $subject, $message]);
            $success = true;
        } catch (PDOException $e) {
            $error = 'Something went wrong. Please try again.';
        }
    }
}

// Get flash message
$flash = getFlash();
?>

<section class="contact-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Get In Touch</h2>
            <p class="section-subtitle">Have a question or want to work together? I'd love to hear from you.</p>
        </div>
        
        <?php if ($success): ?>
        <div class="alert alert-success">
            Thank you for your message! I'll get back to you as soon as possible.
        </div>
        <?php elseif ($error): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <div class="contact-grid">
            <div class="contact-info">
                <h3>Let's Connect</h3>
                <p>Feel free to reach out if you have a project in mind, want to collaborate, or just want to say hello.</p>
                
                <div class="contact-detail">
                    <span>✉</span>
                    <span><?php echo htmlspecialchars(getSetting('contact_email', 'hello@justaj.local')); ?></span>
                </div>
                
                <div class="contact-detail">
                    <span>◇</span>
                    <a href="<?php echo getSetting('linkedin_url', '#'); ?>" target="_blank" rel="noopener">LinkedIn</a>
                </div>
                
                <div class="contact-detail">
                    <span>◇</span>
                    <a href="<?php echo getSetting('github_url', '#'); ?>" target="_blank" rel="noopener">GitHub</a>
                </div>
            </div>
            
            <div class="contact-form">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name" class="form-label">Name *</label>
                        <input type="text" id="name" name="name" class="form-input" required value="<?php echo $_POST['name'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" id="email" name="email" class="form-input" required value="<?php echo $_POST['email'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-input" value="<?php echo $_POST['subject'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="message" class="form-label">Message *</label>
                        <textarea id="message" name="message" class="form-textarea" required><?php echo $_POST['message'] ?? ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary form-submit">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>