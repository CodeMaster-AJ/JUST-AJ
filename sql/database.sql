-- ================================================
-- JUST AJ Brand Portal - Database Schema v3
-- Complete with Blog Feature & SEO
-- ================================================

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `just_aj` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `just_aj`;

-- ================================================
-- ADMIN USERS TABLE
-- ================================================
CREATE TABLE `admin_users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- SETTINGS TABLE
-- ================================================
CREATE TABLE `settings` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `setting_key` VARCHAR(255) NOT NULL,
    `setting_value` TEXT,
    PRIMARY KEY (`id`),
    UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- PROJECTS TABLE
-- ================================================
CREATE TABLE `projects` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `tech_stack` VARCHAR(500),
    `live_link` VARCHAR(500),
    `github_link` VARCHAR(500),
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `featured` ENUM('yes', 'no') DEFAULT 'no',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- SERVICES TABLE
-- ================================================
CREATE TABLE `services` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `icon` VARCHAR(100),
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `featured` ENUM('yes', 'no') DEFAULT 'no',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- LEADS TABLE
-- ================================================
CREATE TABLE `leads` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(500),
    `message` TEXT NOT NULL,
    `status` ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- BLOG CATEGORIES TABLE
-- ================================================
CREATE TABLE `blog_categories` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- BLOG TAGS TABLE
-- ================================================
CREATE TABLE `blog_tags` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- BLOG POSTS TABLE
-- ================================================
CREATE TABLE `blog_posts` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `content` LONGTEXT,
    `excerpt` TEXT,
    `featured_image` VARCHAR(500),
    `category_id` INT(11),
    `author_id` INT(11),
    `status` ENUM('draft', 'published', 'scheduled', 'archived') DEFAULT 'draft',
    `featured` ENUM('yes', 'no') DEFAULT 'no',
    `view_count` INT(11) DEFAULT 0,
    `published_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `status` (`status`),
    KEY `category_id` (`category_id`),
    KEY `published_at` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- BLOG POST TAGS (Pivot Table)
-- ================================================
CREATE TABLE `blog_post_tags` (
    `post_id` INT(11) NOT NULL,
    `tag_id` INT(11) NOT NULL,
    PRIMARY KEY (`post_id`, `tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- BLOG SEO TABLE
-- ================================================
CREATE TABLE `blog_seo` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `post_id` INT(11) NOT NULL,
    `seo_title` VARCHAR(255),
    `seo_description` TEXT,
    `seo_keywords` VARCHAR(500),
    `og_image` VARCHAR(500),
    `og_title` VARCHAR(255),
    `og_description` TEXT,
    `canonical_url` VARCHAR(500),
    `index_follow` ENUM('index,follow', 'index,nofollow', 'noindex,follow', 'noindex,nofollow') DEFAULT 'index,follow',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- SEED DATA: ADMIN USER
-- Email: aj@justaj.local
-- Password: admin123
-- ================================================
INSERT INTO `admin_users` (`name`, `email`, `password`) VALUES
('AJ', 'aj@justaj.local', '$2y$12$ZUC9g3R6Vntb352I049wa.ygrcgbSgXNDhZ4HHA9voCuIdHiRxLH.');

-- ================================================
-- SEED DATA: SETTINGS
-- ================================================
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'JUST AJ'),
('site_tagline', 'Building tools, content, and systems for creators, students, and founders.'),
('contact_email', 'hello@justaj.local'),
('linkedin_url', '#'),
('github_url', '#'),
('instagram_url', '#'),
('blog_posts_per_page', '10'),
('blog_excerpt_length', '150');

-- ================================================
-- SEED DATA: BLOG CATEGORIES
-- ================================================
INSERT INTO `blog_categories` (`name`, `slug`, `description`) VALUES
('Development', 'development', 'Articles about coding, programming, and technical topics'),
('Design', 'design', 'UI/UX design, graphics, and visual content'),
('Business', 'business', 'Entrepreneurship, marketing, and business growth'),
('Lifestyle', 'lifestyle', 'Personal growth, habits, and life topics');

-- ================================================
-- SEED DATA: BLOG TAGS
-- ================================================
INSERT INTO `blog_tags` (`name`, `slug`) VALUES
('PHP', 'php'),
('JavaScript', 'javascript'),
('WordPress', 'wordpress'),
('Tutorial', 'tutorial'),
('Tips', 'tips'),
('Productivity', 'productivity');

-- ================================================
-- SEED DATA: SAMPLE BLOG POST
-- ================================================
INSERT INTO `blog_posts` (`title`, `slug`, `content`, `excerpt`, `category_id`, `author_id`, `status`, `featured`, `published_at`) VALUES
('Getting Started with Web Development', 'getting-started-web-development', '<p>Web development is an exciting journey that starts with understanding the fundamentals. In this guide, we will explore the core technologies that power the modern web.</p><h2>HTML: The Structure</h2><p>HTML provides the structural foundation of every webpage. It uses tags to define elements like headings, paragraphs, lists, and links.</p><h2>CSS: The Style</h2><p>CSS brings your HTML to life with colors, layouts, and animations. Modern CSS includes features like Flexbox and Grid for creating responsive designs.</p><h2>JavaScript: The Behavior</h2><p>JavaScript adds interactivity to your websites. From simple form validation to complex single-page applications, JavaScript is essential.</p><p>Start learning today and build amazing things for the web!</p>', 'Learn the fundamentals of web development with this comprehensive guide covering HTML, CSS, and JavaScript.', 1, 1, 'published', 'yes', NOW());

-- ================================================
-- SEED DATA: BLOG SEO
-- ================================================
INSERT INTO `blog_seo` (`post_id`, `seo_title`, `seo_description`, `seo_keywords`, `index_follow`) VALUES
(1, 'Web Development Guide for Beginners | JUST AJ', 'Complete guide to learning web development. Learn HTML, CSS, JavaScript and start building websites today.', 'web development, html, css, javascript, tutorial, beginners', 'index,follow');

-- ================================================
-- SEED DATA: SAMPLE PROJECTS
-- ================================================
INSERT INTO `projects` (`title`, `description`, `tech_stack`, `live_link`, `github_link`, `status`, `featured`) VALUES
('Portfolio Website', 'A personal portfolio website showcasing my work and skills.', 'HTML, CSS, JavaScript, PHP', '#', '#', 'active', 'yes'),
('Task Management App', 'A simple task management application for daily productivity.', 'PHP, MySQL, JavaScript', '#', '#', 'active', 'yes');

-- ================================================
-- SEED DATA: SAMPLE SERVICES
-- ================================================
INSERT INTO `services` (`title`, `description`, `icon`, `status`, `featured`) VALUES
('Web Development', 'Building responsive and modern websites using the latest technologies.', 'code', 'active', 'yes'),
('UI/UX Design', 'Creating beautiful and user-friendly interfaces for digital products.', 'paint-brush', 'active', 'yes'),
('Content Creation', 'Developing engaging content for digital platforms and marketing.', 'edit', 'active', 'yes');

-- ================================================
-- FUTURE TABLES (Comments - Do not uncomment in v1)
-- ================================================

/*
-- Products for digital downloads
CREATE TABLE `products` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10,2),
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- AI Tools
CREATE TABLE `tools` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `api_required` BOOLEAN DEFAULT FALSE,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tool usage tracking
CREATE TABLE `tool_usages` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `tool_id` INT(11) NOT NULL,
    `user_input` TEXT,
    `ai_response` TEXT,
    `tokens_used` INT(11),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Testimonials
CREATE TABLE `testimonials` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `role` VARCHAR(255),
    `content` TEXT NOT NULL,
    `rating` INT(1) DEFAULT 5,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Analytics
CREATE TABLE `analytics_events` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `event_name` VARCHAR(255) NOT NULL,
    `properties` JSON,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `event_name` (`event_name`),
    KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/