# JUST AJ Project Context

## Overview
A personal brand portal built with **native PHP + MySQL** for **XAMPP**. This is a complete website system with blog, tools directory, and digital products store with payment integration.

**Internal System Name:** AJ OS

---

## Tech Stack

| Component | Technology |
|-----------|------------|
| Backend | Native PHP (no framework) |
| Database | MySQL (via XAMPP) |
| Web Server | Apache (XAMPP) |
| Payment | Razorpay (Test mode) |
| Email | Gmail SMTP |
| Styling | Custom CSS |
| Icons | FontAwesome 6 |

---

## Current Version: v7
**Date:** May 20, 2026

---

## URLs

| Page | URL |
|------|-----|
| Website | http://localhost/just_aj/ |
| Blog | http://localhost/just_aj/blog/ |
| Tools | http://localhost/just_aj/tools/ |
| Products | http://localhost/just_aj/products/ |
| Admin Login | http://localhost/just_aj/admin/login.php |

---

## Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | aj@justaj.local | admin123 |

---

## Database: just_aj

**Import File:** `sql/database.sql`

### Tables:
- `admin_users` - Admin accounts (passwords hashed with password_hash())
- `projects` - Portfolio projects
- `services` - Service offerings
- `leads` - Contact form submissions
- `settings` - Site configuration (key-value store)
- `blog_posts` - Blog articles
- `blog_categories` - Blog categories
- `blog_tags` - Blog tags
- `blog_post_tags` - Post-tag relationships
- `blog_seo` - SEO data per post (title, description, OG, twitter cards)
- `tool_categories` - Tool categories
- `tools` - External tools directory (name, url, icon, category, featured)
- `product_categories` - Product categories
- `products` - Digital products (name, price, description, image, file, is_free)
- `orders` - Purchase orders (status: pending/paid, razorpay order_id, payment_id)

---

## Key Files

```
just_aj/
├── index.php              # Home page
├── about.php              # About page
├── projects.php           # Projects (loads from database)
├── services.php           # Services (loads from database)
├── contact.php            # Contact form (saves to leads)
├── blog/
│   ├── index.php          # Blog listing
│   ├── post.php           # Single post (SEO enabled)
│   └── .htaccess          # URL rewriting for /blog/slug
├── tools/
│   ├── index.php          # Tools directory (categories + search)
│   └── redirect.php       # Redirect with click tracking
├── products/
│   ├── index.php          # Products store
│   ├── checkout.php       # Checkout with Razorpay (free/paid products)
│   ├── download.php       # Download handler (verifies order)
│   ├── verify.php         # Payment verification webhook
│   └── success.php        # Payment success page
├── admin/
│   ├── login.php          # Admin login
│   ├── dashboard.php      # Stats dashboard
│   ├── projects/          # CRUD for projects
│   ├── services/          # CRUD for services
│   ├── leads/             # View contact submissions
│   ├── blog/              # Blog CMS (posts, categories, tags)
│   ├── tools/             # Tools CMS (tools, categories)
│   ├── products/          # Products CMS (products, categories, orders)
│   └── settings.php       # Site settings
├── includes/
│   ├── config.php         # Base URL, paths
│   ├── db.php             # PDO connection
│   ├── functions.php      # Helper functions (sanitize, etc)
│   ├── auth.php           # Admin authentication
│   ├── header.php         # Common header (nav, meta)
│   ├── footer.php         # Common footer
│   ├── icon-selector.php  # FontAwesome icon picker component
│   ├── razorpay-config.php # API keys (NOT in git)
│   ├── razorpay.php       # Razorpay API helper
│   ├── smtp-config.php    # Gmail SMTP (NOT in git)
│   └── mailer.php         # Email sender (HTML templates)
├── assets/
│   ├── css/style.css      # Main theme (black/white)
│   └── js/main.js         # JavaScript
├── sql/database.sql       # Database schema + seed data
└── README.md              # Full documentation
```

---

## Configuration Files (NOT in Git - Create Manually)

### 1. includes/razorpay-config.php
```php
<?php
define('RAZORPAY_KEY_ID', 'your_key_id');
define('RAZORPAY_KEY_SECRET', 'your_key_secret');
?>
```

### 2. includes/smtp-config.php
```php
<?php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'your@gmail.com');
define('SMTP_PASSWORD', 'your_app_password');
define('SMTP_FROM_EMAIL', 'your@gmail.com');
define('SMTP_FROM_NAME', 'JUST AJ');
?>
```

---

## Important Code Patterns

### Database Connection (PDO)
```php
require_once __DIR__ . '/includes/db.php';
$pdo->prepare('SELECT * FROM table WHERE id = ?')->execute([$id]);
```

### Admin Check
```php
require_once __DIR__ . '/includes/auth.php';
// Automatically redirects to login if not authenticated
```

### Password Verification
```php
password_verify($input, $hashed) // returns true/false
password_hash($password, PASSWORD_DEFAULT) // returns hash
```

### XSS Protection
```php
htmlspecialchars($string, ENT_QUOTES, 'UTF-8')
```

### Sanitization
```php
sanitize($input) // from includes/functions.php
```

---

## Recent Changes (May 2026)

### v7 - Free Products Fix
**Problem:** Free products tried to create ₹0 order in Razorpay, but Razorpay requires minimum ₹1.

**Solution:** Free products now skip Razorpay entirely:
1. `createProductOrder()` checks `is_free === 'yes'` or `price == 0`
2. If free: creates order directly with `status = 'paid'`
3. Email with download link sent immediately
4. User can download right away

**Files Changed:**
- `includes/functions.php` - createProductOrder() function
- `products/checkout.php` - Free product form and handler

---

## Features Status

### ✅ Working
- Home, About, Projects, Services, Contact pages
- Blog system with full CMS and SEO per post
- Tools directory with categories, icons, search, click tracking
- Products store with checkout and Razorpay payment
- Admin panel with full CRUD for all modules
- Email system (Gmail SMTP) for download links
- Database with seed data

### 🔧 Known Limitations
- AI Writer module is placeholder (future)
- Analytics not implemented (future)
- Rich text editor not included (using plain textarea)
- Razorpay in Test mode only

---

## To Resume Work

1. Start XAMPP (Apache + MySQL)
2. Navigate to: http://localhost/just_aj/admin/login.php
3. Login: aj@justaj.local / admin123
4. Make changes in relevant admin section
5. Test at public URLs

---

## Common Tasks

### Add a New Blog Post
1. Admin → Blog → Add New Post
2. Fill title, content (textarea), category
3. Set SEO data (title, description, OG image)
4. Save as Draft or Publish

### Add a New Product
1. Admin → Products → Add Product
2. Fill name, price, description, image URL
3. If free product: set "Is Free" = Yes
4. Upload file (for downloadable products)

### Update Site Settings
1. Admin → Settings
2. Edit key-value pairs (site_name, tagline, etc.)

---

## Security Notes
- All SQL queries use prepared statements (no SQL injection)
- All user input sanitized with htmlspecialchars() (no XSS)
- Admin sessions use session_regenerate_id() on login
- Sensitive config files excluded from git (.gitignore)

---

## File .gitignore Contents
```
includes/razorpay-config.php
includes/smtp-config.php
error.log
```

---

## Author & Purpose

**JUST AJ** - Building tools, content, and systems for creators, students, and founders.