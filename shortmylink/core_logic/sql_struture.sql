--sql_struture
-- --------------------------------------------------------
-- SHORTMYLINK.IN MASTER SCHEMA (FINAL V4.0)
-- Scalable, Secure, High-Performance
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0; -- Prevent errors during creation

-- --------------------------------------------------------
-- 1. USERS TABLE
-- Central hub for Authentication, Profile, and Wallet
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  
  -- Auth Credentials
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  
  -- Contact Details (For Withdrawals & KYC)
  `mobile_number` varchar(20) DEFAULT NULL,
  `alt_mobile_number` varchar(20) DEFAULT NULL,
  
  -- Role & Status
  `role` enum('user','admin') DEFAULT 'user',
  `status` enum('active','banned','pending') DEFAULT 'active',
  
  -- The Wallet (Live Balance)
  `wallet_balance` decimal(16,6) DEFAULT 0.000000,
  
  -- Default Payment Preference (Optional, helps auto-fill withdraw form)
  `default_payment_method` enum('UPI','PAYPAL','IMPS') DEFAULT NULL,
  `default_payment_account` text DEFAULT NULL, -- UPI ID / PayPal Email / Bank Details
  
  -- Security & API
  `api_token` varchar(64) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_username` (`username`),
  UNIQUE KEY `idx_email` (`email`),
  UNIQUE KEY `idx_api_token` (`api_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. LINKS TABLE
-- Tracks individual shortened links and their specific revenue.
-- --------------------------------------------------------
DROP TABLE IF EXISTS `links`;
CREATE TABLE `links` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  
  -- The Link Logic
  `original_url` text NOT NULL,
  `short_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL, -- Case Sensitive!
  `title` varchar(255) DEFAULT 'Untitled Link',
  
  -- Aggregated Stats (Updated on every click for Dashboard Speed)
  `total_views` bigint(20) UNSIGNED DEFAULT 0,
  `total_revenue` decimal(16,6) DEFAULT 0.000000, -- Revenue for THIS specific link
  
  -- Settings
  `is_active` tinyint(1) DEFAULT 1,
  `link_type` enum('direct','banner','interstitial') DEFAULT 'direct',
  
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_short_code` (`short_code`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_links_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. CLICKS TABLE (The Big Data)
-- Stores every single visitor interaction.
-- --------------------------------------------------------
DROP TABLE IF EXISTS `clicks`;
CREATE TABLE `clicks` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `link_id` bigint(20) UNSIGNED NOT NULL,
  
  -- Visitor Fingerprint
  `visitor_ip` varchar(45) NOT NULL,
  `user_agent` text,
  `referer_url` text, -- e.g. Facebook, YouTube
  
  -- Analytics Data
  `country_code` char(2) DEFAULT 'XX',
  `device_type` enum('desktop','mobile','tablet','bot') DEFAULT 'desktop',
  `os` varchar(50) DEFAULT NULL,
  `browser` varchar(50) DEFAULT NULL,
  
  -- Revenue Calculation
  `revenue_generated` decimal(16,6) DEFAULT 0.000000,
  `is_unique_24h` tinyint(1) DEFAULT 1, -- 1 = Paid, 0 = Repeat/Bot
  
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_link_id` (`link_id`),
  KEY `idx_created_at` (`created_at`), -- Critical for Date Range Filtering
  KEY `idx_analytics` (`link_id`, `created_at`), -- For "Last 30 Days" charts
  CONSTRAINT `fk_clicks_link` FOREIGN KEY (`link_id`) REFERENCES `links` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. PAYOUTS TABLE (Withdrawals)
-- Strictly handles the money flow.
-- --------------------------------------------------------
DROP TABLE IF EXISTS `payouts`;
CREATE TABLE `payouts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  
  -- Payment Details
  `amount` decimal(10,2) NOT NULL,
  `method` enum('UPI','PAYPAL','IMPS') NOT NULL,
  `account_details` text NOT NULL, -- Stores UPI ID, Email, or Bank Acct No
  
  -- Status Tracking
  `status` enum('pending','processing','completed','rejected','refunded') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL, -- Admin fills this after paying
  `admin_remarks` text DEFAULT NULL,
  
  `requested_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `processed_at` timestamp NULL DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  KEY `idx_payout_user` (`user_id`),
  KEY `idx_payout_status` (`status`),
  CONSTRAINT `fk_payouts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. CPM RATES TABLE
-- Admin controls how much countries pay.
-- --------------------------------------------------------
DROP TABLE IF EXISTS `cpm_rates`;
CREATE TABLE `cpm_rates` (
  `country_code` char(2) NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `rate_desktop` decimal(10,4) NOT NULL, -- Rate per 1000 views
  `rate_mobile` decimal(10,4) NOT NULL,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- INITIAL DATA SEED
-- --------------------------------------------------------
INSERT INTO `cpm_rates` (`country_code`, `country_name`, `rate_desktop`, `rate_mobile`) VALUES
('GL', 'Greenland', 16.80, 16.80),
('US', 'United States', 15.00, 13.50),
('GB', 'United Kingdom', 10.00, 9.00),
('CA', 'Canada', 9.00, 8.50),
('IN', 'India', 3.50, 2.80),
('XX', 'Worldwide Deal', 1.00, 1.00);

SET FOREIGN_KEY_CHECKS = 1;