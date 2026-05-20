<?php
/**
 * Work With Me Page
 * I help students, creators, founders, and small businesses build clean websites, landing pages, and simple digital systems.
 */
define('AJOS_INIT', true);
$currentPage = 'contact';
$pageTitle = 'Work With AJ | JUST AJ';

require_once 'includes/header.php';

// Get services from database
$services = getAllServices();

// Handle form submission
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $service = sanitize($_POST['service'] ?? '');
    $budget = sanitize($_POST['budget'] ?? '');
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
            $fullSubject = $service ? "Work Inquiry: $service" : 'Work Inquiry';
            $fullMessage = "Service: $service\nBudget: $budget\n\n$message";
            $stmt->execute([$name, $email, $fullSubject, $fullMessage]);
            $success = true;
        } catch (PDOException $e) {
            $error = 'Something went wrong. Please try again.';
        }
    }
}

$flash = getFlash();
?>

<section class="wwm-hero">
    <div class="container">
        <h1>Need a Website or Digital System?</h1>
        <p class="wwm-hero-sub">I help students, creators, founders, and small businesses build clean websites, landing pages, and simple digital systems.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="wwm-layout">
            <div class="wwm-main">
                <div class="section-header">
                    <h2 class="section-title">Services</h2>
                    <p class="section-subtitle">What I can help you with</p>
                </div>
                
                <div class="services-grid">
                    <div class="service-card-wwm">
                        <div class="service-icon-wwm"><i class="fa-solid fa-user"></i></div>
                        <h3>Portfolio Website</h3>
                        <p>Showcase your work with a clean, professional portfolio that gets you noticed by colleges, employers, or clients.</p>
                        <span class="service-price">Starting ₹3,999</span>
                    </div>
                    <div class="service-card-wwm">
                        <div class="service-icon-wwm"><i class="fa-solid fa-building"></i></div>
                        <h3>Business Website</h3>
                        <p>Get a professional online presence for your business, startup, or personal brand with a modern website.</p>
                        <span class="service-price">Starting ₹5,999</span>
                    </div>
                    <div class="service-card-wwm">
                        <div class="service-icon-wwm"><i class="fa-solid fa-file-signature"></i></div>
                        <h3>Landing Page</h3>
                        <p>High-converting landing pages for your product, service, event, or campaign. Perfect for quick launches.</p>
                        <span class="service-price">Starting ₹2,999</span>
                    </div>
                    <div class="service-card-wwm">
                        <div class="service-icon-wwm"><i class="fa-solid fa-microchip"></i></div>
                        <h3>AI Tool / Automation Setup</h3>
                        <p>Set up AI tools and automations to save time, streamline workflows, and work smarter without coding.</p>
                        <span class="service-price">Starting ₹4,999</span>
                    </div>
                </div>
                
                <div class="process-section">
                    <h2 class="section-title">How It Works</h2>
                    <div class="process-steps">
                        <div class="process-step">
                            <div class="step-num">1</div>
                            <div class="step-content">
                                <h4>Share Your Vision</h4>
                                <p>Tell me about your project, goals, and what you need.</p>
                            </div>
                        </div>
                        <div class="process-step">
                            <div class="step-num">2</div>
                            <div class="step-content">
                                <h4>Get a Plan</h4>
                                <p>I create a clear plan with timeline, pricing, and deliverables.</p>
                            </div>
                        </div>
                        <div class="process-step">
                            <div class="step-num">3</div>
                            <div class="step-content">
                                <h4>Build Together</h4>
                                <p>I build your project while keeping you updated at every step.</p>
                            </div>
                        </div>
                        <div class="process-step">
                            <div class="step-num">4</div>
                            <div class="step-content">
                                <h4>Launch & Support</h4>
                                <p>Final handover with instructions and basic support included.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="who-for-section">
                    <h2 class="section-title">Who Is This For?</h2>
                    <div class="who-grid">
                        <div class="who-item">
                            <i class="fa-solid fa-graduation-cap"></i>
                            <span>Students looking to build a portfolio</span>
                        </div>
                        <div class="who-item">
                            <i class="fa-solid fa-camera"></i>
                            <span>Creators wanting an online presence</span>
                        </div>
                        <div class="who-item">
                            <i class="fa-solid fa-rocket"></i>
                            <span>Founders launching their startup</span>
                        </div>
                        <div class="who-item">
                            <i class="fa-solid fa-store"></i>
                            <span>Small businesses going digital</span>
                        </div>
                        <div class="who-item">
                            <i class="fa-solid fa-palette"></i>
                            <span>Freelancers showcasing their work</span>
                        </div>
                        <div class="who-item">
                            <i class="fa-solid fa-gears"></i>
                            <span>Anyone needing automation setup</span>
                        </div>
                    </div>
                </div>
                
                <div class="wwm-faq">
                    <h2 class="section-title">Frequently Asked Questions</h2>
                    <div class="faq-list">
                        <div class="faq-item">
                            <h3>What's included in the price?</h3>
                            <p>Design, development, basic SEO, mobile responsiveness, and 14 days of support after delivery.</p>
                        </div>
                        <div class="faq-item">
                            <h3>How long does a project take?</h3>
                            <p>Portfolio sites: 3-5 days. Business websites: 1-2 weeks. Landing pages: 2-5 days. Complex projects may take longer.</p>
                        </div>
                        <div class="faq-item">
                            <h3>Do you provide hosting?</h3>
                            <p>I can recommend hosting providers and help set up everything. Hosting cost is separate and typically ₹500-1500/year.</p>
                        </div>
                        <div class="faq-item">
                            <h3>Can I update the site myself?</h3>
                            <p>Yes! I build easy-to-manage websites so you can update content without needing to code.</p>
                        </div>
                        <div class="faq-item">
                            <h3>Do you work with clients outside India?</h3>
                            <p>Yes! I can work with clients worldwide via video calls and async communication.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <aside class="wwm-sidebar">
                <div class="contact-form-box">
                    <h3>Let's Work Together</h3>
                    <p>Tell me about your project and I'll get back to you within 24 hours.</p>
                    
                    <?php if ($success): ?>
                    <div class="alert alert-success">
                        Thank you! I've received your message and will get back to you within 24 hours.
                    </div>
                    <?php elseif ($error): ?>
                    <div class="alert alert-error">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" id="name" name="name" class="form-input" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" id="email" name="email" class="form-input" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="service" class="form-label">Service Interested In</label>
                            <select id="service" name="service" class="form-input">
                                <option value="">Select a service</option>
                                <option value="Portfolio Website">Portfolio Website</option>
                                <option value="Business Website">Business Website</option>
                                <option value="Landing Page">Landing Page</option>
                                <option value="AI Tool / Automation">AI Tool / Automation</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="budget" class="form-label">Budget Range</label>
                            <select id="budget" name="budget" class="form-input">
                                <option value="">Select budget</option>
                                <option value="Under ₹3,000">Under ₹3,000</option>
                                <option value="₹3,000 - ₹5,000">₹3,000 - ₹5,000</option>
                                <option value="₹5,000 - ₹10,000">₹5,000 - ₹10,000</option>
                                <option value="₹10,000 - ₹20,000">₹10,000 - ₹20,000</option>
                                <option value="₹20,000+">₹20,000+</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message" class="form-label">Tell me about your project *</label>
                            <textarea id="message" name="message" class="form-textarea" required placeholder="What do you need? What's your timeline? Any specific requirements?"><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-full">Send Message</button>
                    </form>
                </div>
                
                <div class="quick-contact">
                    <h4>Or reach out directly:</h4>
                    <a href="mailto:hello@justaj.local" class="quick-link">
                        <i class="fa-solid fa-envelope"></i> hello@justaj.local
                    </a>
                    <a href="https://linkedin.com/in/aj" target="_blank" rel="noopener" class="quick-link">
                        <i class="fa-brands fa-linkedin"></i> Connect on LinkedIn
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>

<style>
.wwm-hero {
    padding: var(--spacing-16) 0 var(--spacing-12);
    text-align: center;
    border-bottom: 1px solid var(--color-gray-800);
}

.wwm-hero h1 {
    font-size: var(--font-size-4xl);
    margin-bottom: var(--spacing-4);
}

.wwm-hero-sub {
    font-size: var(--font-size-lg);
    color: var(--color-gray-400);
    max-width: 600px;
    margin: 0 auto;
}

.wwm-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 60px;
}

@media (max-width: 1000px) {
    .wwm-layout {
        grid-template-columns: 1fr;
    }
}

.wwm-main .section-header {
    text-align: left;
}

.wwm-main .section-title {
    font-size: var(--font-size-2xl);
    margin-bottom: var(--spacing-2);
}

.wwm-main .section-subtitle {
    font-size: var(--font-size-base);
    color: var(--color-gray-500);
    margin-bottom: var(--spacing-8);
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-6);
    margin-bottom: var(--spacing-16);
}

@media (max-width: 600px) {
    .services-grid {
        grid-template-columns: 1fr;
    }
}

.service-card-wwm {
    background-color: var(--color-gray-900);
    border: 1px solid var(--color-gray-800);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-6);
    transition: border-color 0.3s ease;
}

.service-card-wwm:hover {
    border-color: var(--color-gray-600);
}

.service-icon-wwm {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--color-gray-800);
    border-radius: var(--border-radius);
    font-size: 24px;
    margin-bottom: var(--spacing-4);
}

.service-card-wwm h3 {
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin-bottom: var(--spacing-3);
}

.service-card-wwm p {
    font-size: var(--font-size-sm);
    color: var(--color-gray-400);
    line-height: 1.6;
    margin-bottom: var(--spacing-4);
}

.service-price {
    display: inline-block;
    font-size: var(--font-size-sm);
    font-weight: 600;
    color: var(--color-gray-300);
}

.process-section {
    margin-bottom: var(--spacing-16);
}

.process-section .section-title {
    margin-bottom: var(--spacing-8);
}

.process-steps {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--spacing-6);
}

@media (max-width: 800px) {
    .process-steps {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 500px) {
    .process-steps {
        grid-template-columns: 1fr;
    }
}

.process-step {
    display: flex;
    gap: var(--spacing-4);
}

.step-num {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--color-white);
    color: var(--color-black);
    border-radius: 50%;
    font-weight: 700;
    font-size: var(--font-size-sm);
    flex-shrink: 0;
}

.step-content h4 {
    font-size: var(--font-size-sm);
    font-weight: 600;
    margin-bottom: var(--spacing-1);
}

.step-content p {
    font-size: var(--font-size-xs);
    color: var(--color-gray-500);
    line-height: 1.5;
}

.who-for-section {
    margin-bottom: var(--spacing-16);
}

.who-for-section .section-title {
    margin-bottom: var(--spacing-8);
}

.who-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-4);
}

@media (max-width: 700px) {
    .who-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 400px) {
    .who-grid {
        grid-template-columns: 1fr;
    }
}

.who-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-3);
    padding: var(--spacing-4);
    background-color: var(--color-gray-900);
    border: 1px solid var(--color-gray-800);
    border-radius: var(--border-radius);
    font-size: var(--font-size-sm);
}

.who-item i {
    font-size: 20px;
    color: var(--color-gray-400);
}

.wwm-faq {
    margin-bottom: var(--spacing-8);
}

.wwm-faq .section-title {
    margin-bottom: var(--spacing-8);
}

.faq-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-4);
}

.faq-item {
    background-color: var(--color-gray-900);
    border: 1px solid var(--color-gray-800);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-6);
}

.faq-item h3 {
    font-size: var(--font-size-base);
    font-weight: 600;
    margin-bottom: var(--spacing-3);
}

.faq-item p {
    font-size: var(--font-size-sm);
    color: var(--color-gray-400);
    line-height: 1.6;
}

/* Sidebar */
.wwm-sidebar {
    position: sticky;
    top: 100px;
    align-self: start;
}

.contact-form-box {
    background-color: var(--color-gray-900);
    border: 1px solid var(--color-gray-800);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-8);
    margin-bottom: var(--spacing-6);
}

.contact-form-box h3 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-2);
}

.contact-form-box > p {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
    margin-bottom: var(--spacing-6);
}

.form-input, .form-textarea {
    width: 100%;
    padding: var(--spacing-3) var(--spacing-4);
    background-color: var(--color-gray-800);
    border: 1px solid var(--color-gray-700);
    border-radius: var(--border-radius);
    color: var(--color-white);
    font-size: var(--font-size-base);
    font-family: var(--font-family);
}

.form-input:focus, .form-textarea:focus {
    outline: none;
    border-color: var(--color-white);
}

.form-textarea {
    min-height: 120px;
    resize: vertical;
}

.form-label {
    display: block;
    font-size: var(--font-size-sm);
    font-weight: 500;
    margin-bottom: var(--spacing-2);
}

.form-group {
    margin-bottom: var(--spacing-5);
}

.btn-full {
    width: 100%;
}

.quick-contact {
    background-color: var(--color-gray-900);
    border: 1px solid var(--color-gray-800);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-6);
}

.quick-contact h4 {
    font-size: var(--font-size-sm);
    font-weight: 600;
    margin-bottom: var(--spacing-4);
    color: var(--color-gray-400);
}

.quick-link {
    display: flex;
    align-items: center;
    gap: var(--spacing-3);
    font-size: var(--font-size-sm);
    color: var(--color-gray-400);
    margin-bottom: var(--spacing-3);
    padding: var(--spacing-3);
    background-color: var(--color-gray-800);
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
}

.quick-link:hover {
    background-color: var(--color-gray-700);
    color: var(--color-white);
}

.quick-link i {
    font-size: 16px;
}
</style>

<?php require_once 'includes/footer.php'; ?>