-- --------------------------------------------------------
-- SHORTMYLINK.IN MASTER SCHEMA (FINAL V5.0)
-- Consolidated: Auth, Wallet, Links, Ads, Support
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0; -- Disable checks to allow dropping tables

-- --------------------------------------------------------
-- CLEANUP (Drop tables in reverse dependency order)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `support_tickets`;
DROP TABLE IF EXISTS `ad_campaigns`;
DROP TABLE IF EXISTS `clicks`;
DROP TABLE IF EXISTS `payouts`;
DROP TABLE IF EXISTS `links`;
DROP TABLE IF EXISTS `user_ad_settings`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `cpm_rates`;

-- --------------------------------------------------------
-- 1. USERS TABLE
-- Central hub for Authentication, Profile, and Wallet
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  
  -- Auth Credentials
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  
  -- Contact Details
  `mobile_number` varchar(20) DEFAULT NULL,
  
  -- Role & Status
  `role` enum('user','admin') DEFAULT 'user',
  `status` enum('active','banned','pending') DEFAULT 'active',
  
  -- The Wallet (Live Balance)
  `wallet_balance` decimal(16,6) DEFAULT 0.000000,
  
  -- Payment Preference
  `default_payment_method` enum('UPI','PAYPAL','IMPS') DEFAULT NULL,
  `default_payment_account` text DEFAULT NULL,
  
  -- Security & API
  `api_token` varchar(64) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_username` (`username`),
  UNIQUE KEY `idx_email` (`email`),
  UNIQUE KEY `idx_api_token` (`api_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. USER AD SETTINGS (New)
-- Controls what kind of ads are shown on a user's links
-- --------------------------------------------------------
CREATE TABLE `user_ad_settings` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `allow_adult` tinyint(1) DEFAULT 0, -- 0 = No, 1 = Yes (High Yield)
  `allowed_categories` json DEFAULT NULL, -- JSON array e.g. ["gaming", "tech"]
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_settings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. LINKS TABLE
-- Tracks individual shortened links
-- --------------------------------------------------------
CREATE TABLE `links` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  
  `original_url` text NOT NULL,
  `short_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `title` varchar(255) DEFAULT 'Untitled Link',
  
  -- Stats
  `total_views` bigint(20) UNSIGNED DEFAULT 0,
  `total_revenue` decimal(16,6) DEFAULT 0.000000,
  
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_short_code` (`short_code`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_links_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. CLICKS TABLE
-- Stores every visitor interaction
-- --------------------------------------------------------
CREATE TABLE `clicks` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `link_id` bigint(20) UNSIGNED NOT NULL,
  
  `visitor_ip` varchar(45) NOT NULL,
  `referer_url` text,
  
  -- Analytics
  `country_code` char(2) DEFAULT 'XX',
  `device_type` enum('desktop','mobile','tablet','bot') DEFAULT 'desktop',
  
  -- Money
  `revenue_generated` decimal(16,6) DEFAULT 0.000000,
  
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_link_id` (`link_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_clicks_link` FOREIGN KEY (`link_id`) REFERENCES `links` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. PAYOUTS TABLE
-- Withdrawal history
-- --------------------------------------------------------
CREATE TABLE `payouts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  
  `amount` decimal(10,2) NOT NULL,
  `method` enum('UPI','PAYPAL','IMPS') NOT NULL,
  `account_details` text NOT NULL,
  
  `status` enum('pending','processing','completed','rejected','refunded') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  
  `requested_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `processed_at` timestamp NULL DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  KEY `idx_payout_user` (`user_id`),
  CONSTRAINT `fk_payouts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. AD CAMPAIGNS (New)
-- For users who want to BUY ads (Advertisers)
-- --------------------------------------------------------
CREATE TABLE `ad_campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  
  -- Campaign Info
  `advertiser_name` varchar(100) NOT NULL,
  `contact_email` varchar(100) NOT NULL,
  `ad_type` enum('VIDEO','INTERSTITIAL','BANNER','NATIVE','REDIRECT') NOT NULL,
  `destination_url` text NOT NULL,
  
  -- Financials
  `budget` decimal(10,2) NOT NULL,
  `cost_metric` varchar(10) DEFAULT 'CPM',
  
  -- Config
  `targeting_countries` text DEFAULT NULL,
  `is_adult` tinyint(1) DEFAULT 0,
  `media_path` varchar(255) DEFAULT NULL,
  
  -- Status
  `status` enum('pending_review','approved_unpaid','active','rejected','completed') DEFAULT 'pending_review',
  `admin_notes` text DEFAULT NULL,
  
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_campaign_user` (`user_id`),
  CONSTRAINT `fk_campaign_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 7. SUPPORT TICKETS (New)
-- Help center system
-- --------------------------------------------------------
CREATE TABLE `support_tickets` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  
  `status` enum('open','investigating','resolved','closed') DEFAULT 'open',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_ticket_user` (`user_id`),
  CONSTRAINT `fk_ticket_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 8. CPM RATES TABLE
-- Global rates configuration
-- --------------------------------------------------------
CREATE TABLE `cpm_rates` (
  `country_code` char(2) NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `rate_desktop` decimal(10,4) NOT NULL,
  `rate_mobile` decimal(10,4) NOT NULL,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- DATA SEED
-- --------------------------------------------------------
INSERT INTO `cpm_rates` (`country_code`, `country_name`, `rate_desktop`, `rate_mobile`) VALUES
('GL', 'Greenland', 16.80, 16.80),
('US', 'United States', 15.00, 13.50),
('GB', 'United Kingdom', 10.00, 9.00),
('CA', 'Canada', 9.00, 8.50),
('IN', 'India', 3.50, 2.80),
('XX', 'Worldwide Deal', 1.00, 1.00);

SET FOREIGN_KEY_CHECKS = 1; -- Re-enable checks
UPDATE users SET wallet_balance = 0.00 WHERE username = 'OMDHAGE';