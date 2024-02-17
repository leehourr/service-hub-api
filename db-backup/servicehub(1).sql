-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 17, 2024 at 01:56 AM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `servicehub`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_time` datetime NOT NULL,
  `status` enum('pending','confirmed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `service_provider_id` int UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `booking_id` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appointments_user_id_foreign` (`user_id`),
  KEY `appointments_service_provider_id_foreign` (`service_provider_id`),
  KEY `appointments_booking_id_foreign` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `date_time`, `status`, `created_at`, `updated_at`, `user_id`, `service_provider_id`, `deleted_at`, `booking_id`) VALUES
(5, '2024-02-17 01:38:14', 'pending', '2024-02-16 18:38:14', '2024-02-16 18:38:14', 84, 1, NULL, 16);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_time` datetime NOT NULL,
  `status` enum('pending','accepted','declined','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `service_provider_id` int UNSIGNED DEFAULT NULL,
  `service_id` int UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bookings_user_id_foreign` (`user_id`),
  KEY `bookings_service_provider_id_foreign` (`service_provider_id`),
  KEY `bookings_service_id_foreign` (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `date_time`, `status`, `created_at`, `updated_at`, `user_id`, `service_provider_id`, `service_id`, `deleted_at`) VALUES
(7, '2024-01-27 11:22:38', 'cancelled', '2024-01-27 04:22:38', '2024-02-16 18:33:56', 84, 4, 4, NULL),
(12, '2024-02-15 01:38:45', 'cancelled', '2024-02-14 18:38:45', '2024-02-15 21:41:50', 84, 12, 7, NULL),
(16, '2024-02-17 01:34:50', 'accepted', '2024-02-16 18:34:50', '2024-02-16 18:38:14', 84, 1, 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

DROP TABLE IF EXISTS `chats`;
CREATE TABLE IF NOT EXISTS `chats` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`id`, `created_at`, `updated_at`) VALUES
(22, '2024-02-13 23:38:15', '2024-02-13 23:38:15'),
(23, '2024-02-13 23:45:19', '2024-02-13 23:45:19'),
(26, '2024-02-14 21:19:02', '2024-02-14 21:19:02');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `message_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `chat_id` int UNSIGNED DEFAULT NULL,
  `sender_id` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_chat_id_foreign` (`chat_id`),
  KEY `messages_sender_id_foreign` (`sender_id`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `message_text`, `created_at`, `updated_at`, `chat_id`, `sender_id`) VALUES
(88, 'hii', '2024-02-13 23:38:15', '2024-02-13 23:38:15', 22, 84),
(89, 'test', '2024-02-13 23:45:19', '2024-02-13 23:45:19', 23, 84),
(90, 'test', '2024-02-14 00:37:37', '2024-02-14 00:37:37', 23, 84),
(96, 'hii', '2024-02-14 00:38:31', '2024-02-14 00:38:31', 23, 84),
(97, 'is anyone available to chat?', '2024-02-14 00:39:02', '2024-02-14 00:39:02', 22, 84),
(99, 'gg', '2024-02-14 01:26:39', '2024-02-14 01:26:39', 22, 84),
(100, 'hii', '2024-02-14 19:12:15', '2024-02-14 19:12:15', 23, 1),
(101, 'hii', '2024-02-14 21:19:02', '2024-02-14 21:19:02', 26, 12),
(102, 'HEYYY', '2024-02-15 21:49:08', '2024-02-15 21:49:08', 23, 84),
(103, 'HEYY', '2024-02-15 22:08:49', '2024-02-15 22:08:49', 23, 1),
(108, 'is anyone available to chat?', '2024-02-16 18:36:54', '2024-02-16 18:36:54', 23, 84),
(109, 'how can i help u', '2024-02-16 18:37:51', '2024-02-16 18:37:51', 23, 1);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2024_01_24_024720_create_service_listings_table', 1),
(6, '2024_01_24_025345_create_bookings_table', 1),
(7, '2024_01_24_025743_create_ratings_reviews_table', 1),
(8, '2024_01_24_025936_create_notifications_table', 1),
(9, '2024_01_24_031430_create_payments_table', 1),
(10, '2024_01_24_031544_create_appointments_table', 1),
(11, '2024_01_24_034222_create_participants_table', 1),
(12, '2024_01_24_034339_create_chats_table', 1),
(13, '2024_01_24_034433_create_messages_table', 1),
(14, '2024_01_24_043044_add_chat_id_foreign_key_in_participants', 1),
(15, '2024_01_24_073936_add_username_in_users', 1),
(16, '2024_01_24_074557_add_verified_to_users_table', 1),
(17, '2024_01_24_082818_update_username_to_unique_in_users', 2),
(18, '2024_01_24_104045_nullable_password_in_users', 3),
(19, '2024_01_24_104344_create_otp_codes_table', 3),
(20, '2024_01_25_130208_update_number_column_in_users', 4),
(21, '2024_01_26_041201_add_service_name_in_service_listings', 5),
(22, '2024_01_26_041616_add_service_status_in_service_listings', 6),
(23, '2024_01_26_131432_add_service_id_in_bookings', 7),
(24, '2024_01_26_142545_add_soft_delete_in_bookins', 8),
(25, '2024_01_26_144400_add_soft_delete_in_appointments', 9),
(26, '2024_01_26_145639_reference_booking_to_appointments', 10),
(27, '2024_02_12_081828_add_image_column_in_users_table', 11),
(28, '2024_02_13_222935_add_image_path_in_service_listings', 12),
(29, '2024_02_14_194427_update_status_column_in_bookings', 13);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otp_codes`
--

DROP TABLE IF EXISTS `otp_codes`;
CREATE TABLE IF NOT EXISTS `otp_codes` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `otp_codes_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

DROP TABLE IF EXISTS `participants`;
CREATE TABLE IF NOT EXISTS `participants` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `role` enum('client','service_provider') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `chat_id` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `participants_user_id_foreign` (`user_id`),
  KEY `participants_chat_id_foreign` (`chat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`id`, `role`, `created_at`, `updated_at`, `user_id`, `chat_id`) VALUES
(21, NULL, '2024-02-13 23:38:15', '2024-02-13 23:38:15', 4, 22),
(22, NULL, '2024-02-13 23:38:15', '2024-02-13 23:38:15', 84, 22),
(23, NULL, '2024-02-13 23:45:19', '2024-02-13 23:45:19', 1, 23),
(24, NULL, '2024-02-13 23:45:19', '2024-02-13 23:45:19', 84, 23),
(29, NULL, '2024-02-14 21:19:02', '2024-02-14 21:19:02', 84, 26),
(30, NULL, '2024-02-14 21:19:02', '2024-02-14 21:19:02', 12, 26);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `booking_id` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_user_id_foreign` (`user_id`),
  KEY `payments_booking_id_foreign` (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings_reviews`
--

DROP TABLE IF EXISTS `ratings_reviews`;
CREATE TABLE IF NOT EXISTS `ratings_reviews` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `rating` int NOT NULL,
  `review` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `service_provider_id` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ratings_reviews_user_id_foreign` (`user_id`),
  KEY `ratings_reviews_service_provider_id_foreign` (`service_provider_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ratings_reviews`
--

INSERT INTO `ratings_reviews` (`id`, `rating`, `review`, `created_at`, `updated_at`, `user_id`, `service_provider_id`) VALUES
(1, 2, 'good service', '2024-01-26 02:57:08', '2024-01-26 02:57:08', 1, 12),
(2, 4, NULL, '2024-01-26 02:58:05', '2024-01-26 02:58:05', 4, 4),
(3, 4, NULL, '2024-01-27 04:54:30', '2024-01-27 04:54:30', 1, 86);

-- --------------------------------------------------------

--
-- Table structure for table `service_listings`
--

DROP TABLE IF EXISTS `service_listings`;
CREATE TABLE IF NOT EXISTS `service_listings` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pricing` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `service_provider_id` int UNSIGNED DEFAULT NULL,
  `service_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('available','unavailable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_listings_service_provider_id_foreign` (`service_provider_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_listings`
--

INSERT INTO `service_listings` (`id`, `service_description`, `service_category`, `pricing`, `created_at`, `updated_at`, `service_provider_id`, `service_name`, `status`, `image`) VALUES
(3, 'Experienced HVAC technician providing heating and cooling system installation and repair services.', 'HVAC', '120.00', '2024-02-13 11:35:17', '2024-02-13 11:35:17', 1, 'HVAC Services', '', 'https://i.pinimg.com/564x/3a/a7/c1/3aa7c1f66ca28b3797eaad04c4c31790.jpg'),
(4, 'Experienced home tutor providing personalized tutoring sessions for various subjects and grade levels.', 'Education', '50.00', '2024-02-13 11:37:49', '2024-02-13 11:37:49', 4, 'Home Tutoring', '', 'https://i.pinimg.com/564x/f7/ef/32/f7ef32cedfb88c8b4fbab02a43f89aa7.jpg'),
(5, 'Professional plumbing services for both residential and commercial properties. We handle repairs, installations, and maintenance.', 'Home Improvement', '80.00', '2024-02-13 11:50:38', '2024-02-13 11:50:38', 86, 'Plumbing Services', '', 'https://i.pinimg.com/736x/6a/6e/a9/6a6ea97aecad4c7479b21ae87c85ed58.jpg'),
(7, 'Professional computer and IT support services. We provide troubleshooting, software installation, and network setup.', 'Technology', '80.00', '2024-02-13 11:59:37', '2024-02-13 11:59:37', 12, 'Computer and IT Support', '', 'https://i.pinimg.com/564x/8b/78/83/8b7883f20ddb771af92b3559692a8151.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `social_media_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_type` enum('client','service_provider') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'client',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verified` tinyint(1) DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_number_unique` (`phone_number`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone_number`, `password`, `social_media_id`, `account_type`, `created_at`, `updated_at`, `username`, `verified`, `image`) VALUES
(1, 'Leehour', NULL, '012332233', '$2y$12$jDYe1sSLXuZ0Ebb6DHWbHeat1dmR7/9mimUUgYwwDCuHN2LiPJbIC', NULL, 'service_provider', '2024-01-24 01:13:25', '2024-01-24 01:13:25', 'test123', 0, NULL),
(4, 'Elly', NULL, '0788702993', '$2y$12$jDYe1sSLXuZ0Ebb6DHWbHeat1dmR7/9mimUUgYwwDCuHN2LiPJbIC', NULL, 'service_provider', '2024-01-24 01:46:46', '2024-01-24 01:46:46', 'test1234', 0, NULL),
(12, 'Bob', NULL, '012323279', '$2y$12$jDYe1sSLXuZ0Ebb6DHWbHeat1dmR7/9mimUUgYwwDCuHN2LiPJbIC', NULL, 'service_provider', '2024-01-25 06:05:52', '2024-01-25 06:05:52', 'mathtutor939', 0, NULL),
(84, 'Lyhour', NULL, '078870993', '$2y$12$jDYe1sSLXuZ0Ebb6DHWbHeat1dmR7/9mimUUgYwwDCuHN2LiPJbIC', NULL, 'client', '2024-02-08 13:32:16', '2024-02-08 13:32:16', 'lyhour308', 0, NULL),
(86, 'John', NULL, '0123232793', '$2y$12$jDYe1sSLXuZ0Ebb6DHWbHeat1dmR7/9mimUUgYwwDCuHN2LiPJbIC', NULL, 'service_provider', '2024-02-11 14:15:57', '2024-02-11 14:15:57', 'test854', 0, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_service_provider_id_foreign` FOREIGN KEY (`service_provider_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `service_listings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_service_provider_id_foreign` FOREIGN KEY (`service_provider_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_chat_id_foreign` FOREIGN KEY (`chat_id`) REFERENCES `chats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD CONSTRAINT `otp_codes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `participants_chat_id_foreign` FOREIGN KEY (`chat_id`) REFERENCES `chats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings_reviews`
--
ALTER TABLE `ratings_reviews`
  ADD CONSTRAINT `ratings_reviews_service_provider_id_foreign` FOREIGN KEY (`service_provider_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_listings`
--
ALTER TABLE `service_listings`
  ADD CONSTRAINT `service_listings_service_provider_id_foreign` FOREIGN KEY (`service_provider_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Events
--
DROP EVENT IF EXISTS `cleanup otp code`$$
CREATE DEFINER=`root`@`localhost` EVENT `cleanup otp code` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-01-24 16:14:10' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM otp_codes WHERE created_at < NOW() - INTERVAL 5 MINUTE$$

DROP EVENT IF EXISTS `cleaning cancelled bookings`$$
CREATE DEFINER=`root`@`localhost` EVENT `cleaning cancelled bookings` ON SCHEDULE EVERY 1 DAY STARTS '2024-01-26 14:35:22' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM booking WHERE deleted_at < NOW() - INTERVAL 1 DAY$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
