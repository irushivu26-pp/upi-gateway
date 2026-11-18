-- UPI Payment Gateway Database Schema
-- Compatible with MySQL 5.7+, MySQL 8.0+, MariaDB 10.x (Strict SQL Mode Compatible)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Table structure for table `bharatpe_tokens`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `bharatpe_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_token` longtext DEFAULT NULL,
  `phoneNumber` varchar(255) DEFAULT '',
  `token` varchar(255) DEFAULT '',
  `cookie` varchar(255) DEFAULT '',
  `merchantId` varchar(255) DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(255) DEFAULT 'Deactive',
  `Upiid` varchar(255) DEFAULT '',
  `user_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `callback_report`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `callback_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(255) NOT NULL DEFAULT '',
  `request_url` longtext DEFAULT NULL,
  `response` longtext DEFAULT NULL,
  `user_token` varchar(255) NOT NULL DEFAULT '',
  `mobile` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `hdfc`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `hdfc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(255) NOT NULL DEFAULT '',
  `seassion` varchar(255) NOT NULL DEFAULT '',
  `device_id` varchar(255) NOT NULL DEFAULT '',
  `user_token` varchar(255) NOT NULL DEFAULT '',
  `pin` varchar(255) NOT NULL DEFAULT '',
  `upi_hdfc` varchar(255) NOT NULL DEFAULT '',
  `UPI` varchar(255) NOT NULL DEFAULT '',
  `tidlist` varchar(255) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) NOT NULL DEFAULT 'Deactive',
  `mobile` varchar(255) NOT NULL DEFAULT '',
  `user_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `orders`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(255) NOT NULL DEFAULT '',
  `user_token` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(50) NOT NULL DEFAULT 'PENDING',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `utr` varchar(255) NOT NULL DEFAULT '',
  `plan_id` varchar(255) NOT NULL DEFAULT '',
  `customer_name` varchar(255) NOT NULL DEFAULT '',
  `customer_mobile` varchar(255) NOT NULL DEFAULT '',
  `redirect_url` longtext DEFAULT NULL,
  `remark1` varchar(255) NOT NULL DEFAULT '',
  `remark2` varchar(255) NOT NULL DEFAULT '',
  `gateway_txn` varchar(255) NOT NULL DEFAULT '',
  `method` varchar(50) NOT NULL DEFAULT '',
  `HDFC_TXNID` varchar(255) NOT NULL DEFAULT '',
  `upiLink` mediumtext DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `byteTransactionId` varchar(255) DEFAULT NULL,
  `create_date` datetime DEFAULT current_timestamp(),
  `paytm_txn_ref` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id_idx` (`order_id`),
  KEY `user_token_idx` (`user_token`),
  KEY `byteTransactionId_idx` (`byteTransactionId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `payment_links`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `payment_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_token` varchar(255) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `link_token_unq` (`link_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `paytm_tokens`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `paytm_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_token` varchar(255) NOT NULL DEFAULT '',
  `phoneNumber` varchar(255) DEFAULT '',
  `MID` varchar(255) DEFAULT '',
  `Upiid` varchar(255) DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) DEFAULT 'Deactive',
  `user_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `phonepe_tokens`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `phonepe_tokens` (
  `sl` int(11) NOT NULL AUTO_INCREMENT,
  `user_token` varchar(255) NOT NULL DEFAULT '',
  `phoneNumber` varchar(255) NOT NULL DEFAULT '',
  `userId` varchar(255) NOT NULL DEFAULT '',
  `token` longtext DEFAULT NULL,
  `refreshToken` longtext DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `device_data` longtext DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Deactive',
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`sl`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `reports`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transactionId` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(50) NOT NULL DEFAULT '',
  `order_id` varchar(255) NOT NULL DEFAULT '',
  `vpa` varchar(255) NOT NULL DEFAULT '',
  `paymentApp` varchar(255) NOT NULL DEFAULT '',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `user_token` varchar(255) NOT NULL DEFAULT '',
  `UTR` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `mobile` varchar(255) NOT NULL DEFAULT '',
  `user_name` varchar(255) DEFAULT NULL,
  `merchantTransactionId` varchar(255) DEFAULT NULL,
  `transactionNote` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `merchantTransactionId` (`merchantTransactionId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `sms`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(12) NOT NULL DEFAULT '',
  `msg` mediumtext DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `store_id`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `store_id` (
  `sl` int(11) NOT NULL AUTO_INCREMENT,
  `user_token` varchar(255) NOT NULL DEFAULT '',
  `unitId` varchar(255) NOT NULL DEFAULT '',
  `roleName` varchar(255) NOT NULL DEFAULT '',
  `groupValue` varchar(255) NOT NULL DEFAULT '',
  `groupId` varchar(255) NOT NULL DEFAULT '',
  `user_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`sl`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `url_shortener`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `url_shortener` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `original_url` varchar(800) NOT NULL,
  `short_code` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `short_code` (`short_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `mobile` varchar(255) NOT NULL DEFAULT '',
  `role` enum('User','Admin') NOT NULL DEFAULT 'User',
  `password` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `company` varchar(255) NOT NULL DEFAULT '',
  `pin` varchar(255) NOT NULL DEFAULT '',
  `pan` varchar(255) NOT NULL DEFAULT '',
  `aadhaar` varchar(255) NOT NULL DEFAULT '',
  `location` varchar(255) NOT NULL DEFAULT '',
  `user_token` varchar(255) NOT NULL DEFAULT '',
  `expiry` date NOT NULL,
  `callback_url` longtext DEFAULT NULL,
  `bptoken` longtext DEFAULT NULL,
  `upiid` longtext DEFAULT NULL,
  `acc_lock` int(11) NOT NULL DEFAULT 0,
  `acc_ban` varchar(10) NOT NULL DEFAULT 'off',
  `upi_id` mediumtext DEFAULT NULL COMMENT 'This is the UPI ID for PhonePe',
  `phonepe_connected` varchar(3) DEFAULT 'No',
  `hdfc_connected` varchar(3) DEFAULT 'No',
  `paytm_connected` varchar(3) DEFAULT 'No',
  `bharatpe_connected` varchar(3) DEFAULT 'No',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mobile_unq` (`mobile`),
  KEY `user_token_idx` (`user_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Seed Data: Default Admin & Demo Merchant Accounts
-- Password for both accounts: password
-- --------------------------------------------------------

INSERT INTO `users` (`id`, `name`, `mobile`, `role`, `password`, `email`, `company`, `pin`, `pan`, `aadhaar`, `location`, `user_token`, `expiry`, `callback_url`, `bptoken`, `upiid`, `acc_lock`, `acc_ban`, `upi_id`, `phonepe_connected`, `hdfc_connected`, `paytm_connected`, `bharatpe_connected`) VALUES
(1, 'System Administrator', '9999999999', 'Admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@gateway.local', 'payprintsgateway', '110001', 'ABCDE1234F', '123456789012', 'New Delhi', 'dcb789d237d4f44cb5e4532abaf92866', '2035-12-31', '', '', '', 0, 'off', 'paytmqr5gfmzt@ptys', 'No', 'No', 'Yes', 'No'),
(2, 'Demo Merchant', '8888888888', 'User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'merchant@gateway.local', 'Demo Retailers', '110001', 'XYZPK8888A', '987654321098', 'Mumbai', 'demo_merchant_token_67890', '2035-12-31', 'https://webhook.site/test', '', '', 0, 'off', 'demo@ybl', 'No', 'No', 'No', 'No')
ON DUPLICATE KEY UPDATE `user_token`=VALUES(`user_token`), `paytm_connected`=VALUES(`paytm_connected`), `upi_id`=VALUES(`upi_id`), `company`=VALUES(`company`);

INSERT INTO `paytm_tokens` (`id`, `user_token`, `phoneNumber`, `MID`, `Upiid`, `status`, `user_id`) VALUES
(1, 'dcb789d237d4f44cb5e4532abaf92866', '9999999999', 'PAYTM_MID_101', 'paytmqr5gfmzt@ptys', 'Active', 1)
ON DUPLICATE KEY UPDATE `user_token`=VALUES(`user_token`), `Upiid`=VALUES(`Upiid`), `status`='Active';

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
