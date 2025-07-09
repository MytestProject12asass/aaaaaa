-- StarRent.vip - MySQL Database Schema
-- Starlink Router Rental Platform
-- Compatible with MySQL 5.7+ and MariaDB 10.2+

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Create database functions for UUID generation
DELIMITER $$
CREATE FUNCTION IF NOT EXISTS uuid_generate_v4()
RETURNS CHAR(36)
READS SQL DATA
DETERMINISTIC
BEGIN
    RETURN (SELECT UUID());
END$$
DELIMITER ;

-- Create function to update updated_at timestamp
DELIMITER $$
CREATE FUNCTION IF NOT EXISTS update_updated_at_column()
RETURNS TRIGGER
READS SQL DATA
DETERMINISTIC
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END$$
DELIMITER ;

-- --------------------------------------------------------
-- Table structure for table `router_features`
-- --------------------------------------------------------

CREATE TABLE `router_features` (
  `id` char(36) NOT NULL DEFAULT (uuid()),
  `name` text NOT NULL,
  `description` text,
  `icon` text,
  `category` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample router features
INSERT INTO `router_features` (`id`, `name`, `description`, `icon`, `category`) VALUES
(uuid(), 'High-Speed Internet', 'Up to 350 Mbps download speeds', 'fas fa-tachometer-alt', 'performance'),
(uuid(), 'Low Latency', 'Gaming-optimized with <50ms latency', 'fas fa-bolt', 'performance'),
(uuid(), 'Weather Resistant', 'IP54 rated for outdoor use', 'fas fa-cloud-rain', 'durability'),
(uuid(), 'Easy Setup', 'Plug and play installation', 'fas fa-plug', 'usability'),
(uuid(), 'Mobile App Control', 'Manage via Starlink mobile app', 'fas fa-mobile-alt', 'usability'),
(uuid(), 'Global Coverage', 'Works anywhere with clear sky view', 'fas fa-globe', 'coverage');

-- --------------------------------------------------------
-- Table structure for table `payment_methods`
-- --------------------------------------------------------

CREATE TABLE `payment_methods` (
  `id` char(36) NOT NULL DEFAULT (uuid()),
  `name` text NOT NULL,
  `code` text NOT NULL,
  `type` enum('cryptocurrency','fiat','stablecoin') NOT NULL,
  `symbol` text,
  `icon_url` text,
  `min_amount` decimal(20,8),
  `max_amount` decimal(20,8),
  `fee_percentage` decimal(5,4) DEFAULT 0.0000,
  `fee_fixed` decimal(20,8) DEFAULT 0.00000000,
  `confirmation_blocks` int DEFAULT 1,
  `network` text,
  `contract_address` text,
  `decimals` int DEFAULT 8,
  `status` enum('active','inactive','maintenance') DEFAULT 'active',
  `sort_order` int DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample payment methods
INSERT INTO `payment_methods` (`id`, `name`, `code`, `type`, `symbol`, `min_amount`, `max_amount`, `fee_percentage`, `status`, `sort_order`) VALUES
(uuid(), 'Bitcoin', 'BTC', 'cryptocurrency', '₿', 0.00010000, 10.00000000, 0.0050, 'active', 1),
(uuid(), 'Ethereum', 'ETH', 'cryptocurrency', 'Ξ', 0.00100000, 100.00000000, 0.0030, 'active', 2),
(uuid(), 'Tether USD', 'USDT', 'stablecoin', '₮', 10.00000000, 50000.00000000, 0.0020, 'active', 3),
(uuid(), 'USD Coin', 'USDC', 'stablecoin', '$', 10.00000000, 50000.00000000, 0.0020, 'active', 4),
(uuid(), 'Litecoin', 'LTC', 'cryptocurrency', 'Ł', 0.01000000, 1000.00000000, 0.0040, 'active', 5),
(uuid(), 'Dogecoin', 'DOGE', 'cryptocurrency', 'Ð', 100.00000000, 1000000.00000000, 0.0100, 'active', 6);

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE `users` (
  `id` char(36) NOT NULL DEFAULT (uuid()),
  `email` text NOT NULL,
  `name` text NOT NULL,
  `username` text,
  `phone` text,
  `address` text,
  `city` text,
  `country` text,
  `postal_code` text,
  `balance` decimal(20,2) DEFAULT 0.00,
  `total_spent` decimal(20,2) DEFAULT 0.00,
  `referral_code` text,
  `referred_by` char(36),
  `email_verified` boolean DEFAULT false,
  `phone_verified` boolean DEFAULT false,
  `kyc_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `kyc_data` json,
  `status` enum('active','suspended','banned') DEFAULT 'active',
  `avatar_url` text,
  `preferences` json DEFAULT ('{}'),
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample admin user
INSERT INTO `users` (`id`, `email`, `name`, `username`, `balance`, `status`, `email_verified`) VALUES
(uuid(), 'admin@star-rent.vip', 'Admin User', 'admin', 1000.00, 'active', true);

-- --------------------------------------------------------
-- Table structure for table `routers`
-- --------------------------------------------------------

CREATE TABLE `routers` (
  `id` char(36) NOT NULL DEFAULT (uuid()),
  `name` text NOT NULL,
  `model` text NOT NULL,
  `description` text,
  `daily_rate` decimal(10,2) NOT NULL,
  `weekly_rate` decimal(10,2),
  `monthly_rate` decimal(10,2) NOT NULL,
  `deposit_required` decimal(10,2) NOT NULL,
  `max_speed` text NOT NULL,
  `coverage_area` text,
  `weight` text,
  `dimensions` text,
  `power_consumption` text,
  `operating_temperature` text,
  `features` json DEFAULT ('[]'),
  `feature_ids` json DEFAULT ('[]'),
  `images` json DEFAULT ('[]'),
  `primary_image` text,
  `status` enum('available','rented','maintenance','retired') DEFAULT 'available',
  `location` text,
  `serial_number` text,
  `purchase_date` date,
  `warranty_expiry` date,
  `last_maintenance` timestamp NULL,
  `total_rental_days` int DEFAULT 0,
  `total_revenue` decimal(20,2) DEFAULT 0.00,
  `rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int DEFAULT 0,
  `metadata` json DEFAULT ('{}'),
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample routers
INSERT INTO `routers` (`id`, `name`, `model`, `description`, `daily_rate`, `weekly_rate`, `monthly_rate`, `deposit_required`, `max_speed`, `coverage_area`, `weight`, `dimensions`, `power_consumption`, `features`, `primary_image`, `status`) VALUES
(uuid(), 'Starlink Standard', 'Gen 2', 'Perfect for residential use and small businesses. Reliable high-speed internet anywhere.', 25.00, 150.00, 599.00, 500.00, '150 Mbps', '50km radius', '2.9 kg', '30cm x 30cm x 3cm', '50-75W', '["Easy Setup", "Weather Resistant", "24/7 Support"]', 'starlink-standard.jpg', 'available'),
(uuid(), 'Starlink Business', 'High Performance', 'Enterprise-grade solution with priority data and enhanced performance.', 45.00, 270.00, 1099.00, 1000.00, '350 Mbps', '100km radius', '4.2 kg', '35cm x 35cm x 4cm', '100-150W', '["Priority Data", "Enhanced Performance", "Business Support"]', 'starlink-business.jpg', 'available'),
(uuid(), 'Starlink Mobile', 'Portable', 'Compact and portable solution for travelers and mobile applications.', 35.00, 210.00, 799.00, 750.00, '200 Mbps', '75km radius', '1.8 kg', '25cm x 25cm x 2cm', '30-50W', '["Portable Design", "Quick Setup", "Travel Friendly"]', 'starlink-mobile.jpg', 'available'),
(uuid(), 'Starlink Maritime', 'Marine', 'Specialized for boats and maritime applications with enhanced durability.', 55.00, 330.00, 1299.00, 1500.00, '300 Mbps', '200km radius', '5.1 kg', '40cm x 40cm x 5cm', '120-180W', '["Marine Grade", "Salt Water Resistant", "GPS Tracking"]', 'starlink-maritime.jpg', 'available'),
(uuid(), 'Starlink RV', 'Mobile RV', 'Designed for RVs and mobile homes with easy mounting and setup.', 40.00, 240.00, 899.00, 800.00, '250 Mbps', '80km radius', '3.5 kg', '32cm x 32cm x 3.5cm', '60-90W', '["RV Mount", "Mobile Optimized", "Easy Installation"]', 'starlink-rv.jpg', 'available');

-- --------------------------------------------------------
-- Table structure for table `rentals`
-- --------------------------------------------------------

CREATE TABLE `rentals` (
  `id` char(36) NOT NULL DEFAULT (uuid()),
  `rental_number` text NOT NULL,
  `user_id` char(36) NOT NULL,
  `router_id` char(36) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int NOT NULL,
  `daily_rate` decimal(10,2) NOT NULL,
  `subtotal` decimal(20,2) NOT NULL,
  `deposit_amount` decimal(20,2) NOT NULL,
  `tax_amount` decimal(20,2) DEFAULT 0.00,
  `total_amount` decimal(20,2) NOT NULL,
  `currency` text DEFAULT 'USD',
  `status` enum('pending','confirmed','active','completed','cancelled','refunded') DEFAULT 'pending',
  `payment_status` enum('pending','paid','partial','refunded','failed') DEFAULT 'pending',
  `delivery_method` enum('pickup','delivery','shipping') DEFAULT 'pickup',
  `delivery_address` text,
  `delivery_instructions` text,
  `pickup_location` text,
  `notes` text,
  `cancellation_reason` text,
  `cancelled_at` timestamp NULL,
  `confirmed_at` timestamp NULL,
  `started_at` timestamp NULL,
  `completed_at` timestamp NULL,
  `metadata` json DEFAULT ('{}'),
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `payments`
-- --------------------------------------------------------

CREATE TABLE `payments` (
  `id` char(36) NOT NULL DEFAULT (uuid()),
  `payment_number` text NOT NULL,
  `user_id` char(36) NOT NULL,
  `rental_id` char(36),
  `amount` decimal(20,2) NOT NULL,
  `currency` text DEFAULT 'USD',
  `crypto_amount` decimal(20,8),
  `crypto_currency` text,
  `exchange_rate` decimal(20,8),
  `payment_method_id` char(36),
  `payment_type` enum('rental','deposit','refund','fee') NOT NULL,
  `status` enum('pending','processing','completed','failed','expired','cancelled') DEFAULT 'pending',
  `plisio_invoice_id` text,
  `plisio_txn_id` text,
  `blockchain_txn_id` text,
  `blockchain_confirmations` int DEFAULT 0,
  `required_confirmations` int DEFAULT 1,
  `wallet_address` text,
  `callback_url` text,
  `success_url` text,
  `cancel_url` text,
  `expires_at` timestamp NULL,
  `confirmed_at` timestamp NULL,
  `failed_at` timestamp NULL,
  `failure_reason` text,
  `webhook_data` json,
  `metadata` json DEFAULT ('{}'),
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `transactions`
-- --------------------------------------------------------

CREATE TABLE `transactions` (
  `id` char(36) NOT NULL DEFAULT (uuid()),
  `transaction_number` text NOT NULL,
  `user_id` char(36) NOT NULL,
  `rental_id` char(36),
  `payment_id` char(36),
  `type` enum('deposit','rental_payment','refund','withdrawal','fee','bonus','penalty') NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `currency` text DEFAULT 'USD',
  `balance_before` decimal(20,2) NOT NULL,
  `balance_after` decimal(20,2) NOT NULL,
  `description` text NOT NULL,
  `reference` text,
  `status` enum('pending','completed','failed','reversed') DEFAULT 'completed',
  `metadata` json DEFAULT ('{}'),
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `rental_reviews`
-- --------------------------------------------------------

CREATE TABLE `rental_reviews` (
  `id` char(36) NOT NULL DEFAULT (uuid()),
  `rental_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `router_id` char(36) NOT NULL,
  `rating` int NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `title` text,
  `comment` text,
  `pros` json,
  `cons` json,
  `would_recommend` boolean DEFAULT true,
  `verified_rental` boolean DEFAULT false,
  `helpful_votes` int DEFAULT 0,
  `status` enum('draft','published','hidden','flagged') DEFAULT 'published',
  `moderated_at` timestamp NULL,
  `moderated_by` char(36),
  `metadata` json DEFAULT ('{}'),
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `support_tickets`
-- --------------------------------------------------------

CREATE TABLE `support_tickets` (
  `id` char(36) NOT NULL DEFAULT (uuid()),
  `ticket_number` text NOT NULL,
  `user_id` char(36) NOT NULL,
  `rental_id` char(36),
  `subject` text NOT NULL,
  `description` text NOT NULL,
  `category` enum('technical','billing','general','complaint','feature_request') NOT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `status` enum('open','in_progress','waiting_customer','resolved','closed') DEFAULT 'open',
  `assigned_to` char(36),
  `resolution` text,
  `satisfaction_rating` int CHECK (`satisfaction_rating` >= 1 AND `satisfaction_rating` <= 5),
  `satisfaction_comment` text,
  `first_response_at` timestamp NULL,
  `resolved_at` timestamp NULL,
  `closed_at` timestamp NULL,
  `metadata` json DEFAULT ('{}'),
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `support_ticket_messages`
-- --------------------------------------------------------

CREATE TABLE `support_ticket_messages` (
  `id` char(36) NOT NULL DEFAULT (uuid()),
  `ticket_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `message` text NOT NULL,
  `is_internal` boolean DEFAULT false,
  `attachments` json,
  `metadata` json DEFAULT ('{}'),
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `notifications`
-- --------------------------------------------------------

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL DEFAULT (uuid()),
  `user_id` char(36) NOT NULL,
  `type` text NOT NULL,
  `title` text NOT NULL,
  `message` text NOT NULL,
  `data` json DEFAULT ('{}'),
  `read` boolean DEFAULT false,
  `read_at` timestamp NULL,
  `action_url` text,
  `expires_at` timestamp NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `audit_logs`
-- --------------------------------------------------------

CREATE TABLE `audit_logs` (
  `id` char(36) NOT NULL DEFAULT (uuid()),
  `user_id` char(36),
  `action` text NOT NULL,
  `resource_type` text NOT NULL,
  `resource_id` char(36),
  `old_values` json,
  `new_values` json,
  `ip_address` varchar(45),
  `user_agent` text,
  `metadata` json DEFAULT ('{}'),
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `admins`
-- --------------------------------------------------------

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(191) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin user (password: admin123)
INSERT INTO `admins` (`id`, `name`, `email`, `password`, `status`) VALUES
(1, 'Admin', 'admin@star-rent.vip', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- --------------------------------------------------------
-- Table structure for table `generalsettings`
-- --------------------------------------------------------

CREATE TABLE `generalsettings` (
  `id` int(191) NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL DEFAULT 'StarRent.vip',
  `header_email` text,
  `header_phone` text,
  `from_email` varchar(191) NOT NULL DEFAULT 'noreply@star-rent.vip',
  `from_name` varchar(191) NOT NULL DEFAULT 'StarRent.vip',
  `currency_code` varchar(191) DEFAULT 'USD',
  `currency_sign` varchar(191) DEFAULT '$',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default settings
INSERT INTO `generalsettings` (`id`, `title`, `header_email`, `header_phone`, `from_email`, `from_name`) VALUES
(1, 'StarRent.vip', 'info@star-rent.vip', '+1-555-STARLINK', 'noreply@star-rent.vip', 'StarRent.vip');

-- --------------------------------------------------------
-- Add Primary Keys and Indexes
-- --------------------------------------------------------

-- Router Features
ALTER TABLE `router_features`
  ADD PRIMARY KEY (`id`),
  ADD INDEX `idx_router_features_category` (`category`(191));

-- Payment Methods
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE INDEX `payment_methods_code_unique` (`code`(191)),
  ADD INDEX `idx_payment_methods_type` (`type`),
  ADD INDEX `idx_payment_methods_status` (`status`);

-- Users
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE INDEX `users_email_unique` (`email`(191)),
  ADD UNIQUE INDEX `users_username_unique` (`username`(191)),
  ADD UNIQUE INDEX `users_referral_code_unique` (`referral_code`(191)),
  ADD INDEX `idx_users_status` (`status`),
  ADD INDEX `idx_users_email_verified` (`email_verified`),
  ADD FOREIGN KEY (`referred_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- Routers
ALTER TABLE `routers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE INDEX `routers_serial_number_unique` (`serial_number`(191)),
  ADD INDEX `idx_routers_status` (`status`),
  ADD INDEX `idx_routers_rating` (`rating` DESC);

-- Rentals
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE INDEX `rentals_rental_number_unique` (`rental_number`(191)),
  ADD INDEX `idx_rentals_user_id` (`user_id`),
  ADD INDEX `idx_rentals_router_id` (`router_id`),
  ADD INDEX `idx_rentals_status` (`status`),
  ADD INDEX `idx_rentals_dates` (`start_date`, `end_date`),
  ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  ADD FOREIGN KEY (`router_id`) REFERENCES `routers`(`id`) ON DELETE CASCADE;

-- Payments
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE INDEX `payments_payment_number_unique` (`payment_number`(191)),
  ADD INDEX `idx_payments_user_id` (`user_id`),
  ADD INDEX `idx_payments_rental_id` (`rental_id`),
  ADD INDEX `idx_payments_status` (`status`),
  ADD INDEX `idx_payments_plisio_invoice_id` (`plisio_invoice_id`(191)),
  ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  ADD FOREIGN KEY (`rental_id`) REFERENCES `rentals`(`id`) ON DELETE CASCADE,
  ADD FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods`(`id`) ON DELETE SET NULL;

-- Transactions
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE INDEX `transactions_transaction_number_unique` (`transaction_number`(191)),
  ADD INDEX `idx_transactions_user_id` (`user_id`),
  ADD INDEX `idx_transactions_type` (`type`),
  ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  ADD FOREIGN KEY (`rental_id`) REFERENCES `rentals`(`id`) ON DELETE SET NULL,
  ADD FOREIGN KEY (`payment_id`) REFERENCES `payments`(`id`) ON DELETE SET NULL;

-- Rental Reviews
ALTER TABLE `rental_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE INDEX `rental_reviews_rental_user_unique` (`rental_id`, `user_id`),
  ADD INDEX `idx_reviews_router_id` (`router_id`),
  ADD INDEX `idx_reviews_rating` (`rating`),
  ADD FOREIGN KEY (`rental_id`) REFERENCES `rentals`(`id`) ON DELETE CASCADE,
  ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  ADD FOREIGN KEY (`router_id`) REFERENCES `routers`(`id`) ON DELETE CASCADE,
  ADD FOREIGN KEY (`moderated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- Support Tickets
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE INDEX `support_tickets_ticket_number_unique` (`ticket_number`(191)),
  ADD INDEX `idx_tickets_user_id` (`user_id`),
  ADD INDEX `idx_tickets_status` (`status`),
  ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  ADD FOREIGN KEY (`rental_id`) REFERENCES `rentals`(`id`) ON DELETE SET NULL,
  ADD FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- Support Ticket Messages
ALTER TABLE `support_ticket_messages`
  ADD PRIMARY KEY (`id`),
  ADD FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets`(`id`) ON DELETE CASCADE,
  ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;

-- Notifications
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD INDEX `idx_notifications_user_id` (`user_id`),
  ADD INDEX `idx_notifications_read` (`read`),
  ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;

-- Audit Logs
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- --------------------------------------------------------
-- Create Triggers for Updated At
-- --------------------------------------------------------

DELIMITER $$
CREATE TRIGGER `update_payment_methods_updated_at` BEFORE UPDATE ON `payment_methods` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$

CREATE TRIGGER `update_users_updated_at` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$

CREATE TRIGGER `update_routers_updated_at` BEFORE UPDATE ON `routers` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$

CREATE TRIGGER `update_rentals_updated_at` BEFORE UPDATE ON `rentals` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$

CREATE TRIGGER `update_payments_updated_at` BEFORE UPDATE ON `payments` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$

CREATE TRIGGER `update_transactions_updated_at` BEFORE UPDATE ON `transactions` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$

CREATE TRIGGER `update_reviews_updated_at` BEFORE UPDATE ON `rental_reviews` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$

CREATE TRIGGER `update_tickets_updated_at` BEFORE UPDATE ON `support_tickets` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$
DELIMITER ;

-- --------------------------------------------------------
-- Create Trigger to Update Router Rating
-- --------------------------------------------------------

DELIMITER $$
CREATE TRIGGER `update_router_rating_trigger` AFTER INSERT ON `rental_reviews` FOR EACH ROW BEGIN
    UPDATE routers 
    SET rating = (
        SELECT AVG(rating) 
        FROM rental_reviews 
        WHERE router_id = NEW.router_id AND status = 'published'
    ),
    review_count = (
        SELECT COUNT(*) 
        FROM rental_reviews 
        WHERE router_id = NEW.router_id AND status = 'published'
    )
    WHERE id = NEW.router_id;
END$$

CREATE TRIGGER `update_router_rating_on_update` AFTER UPDATE ON `rental_reviews` FOR EACH ROW BEGIN
    UPDATE routers 
    SET rating = (
        SELECT AVG(rating) 
        FROM rental_reviews 
        WHERE router_id = NEW.router_id AND status = 'published'
    ),
    review_count = (
        SELECT COUNT(*) 
        FROM rental_reviews 
        WHERE router_id = NEW.router_id AND status = 'published'
    )
    WHERE id = NEW.router_id;
END$$
DELIMITER ;

COMMIT;