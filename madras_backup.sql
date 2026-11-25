-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2026 at 12:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `madras`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_number` varchar(255) NOT NULL,
  `guestplan_booking_id` varchar(255) DEFAULT NULL,
  `restaurant_id` varchar(255) DEFAULT NULL,
  `guestplan_error` text DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `idempotency_key` varchar(255) DEFAULT NULL,
  `booking_event_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(255) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `party_size` int(10) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','confirmed','failed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_events`
--

CREATE TABLE `booking_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `starts_on` date NOT NULL,
  `ends_on` date NOT NULL,
  `party_size_min` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `party_size_max` int(10) UNSIGNED NOT NULL DEFAULT 12,
  `capacity` int(10) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `business_hours`
--

CREATE TABLE `business_hours` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `day_of_week` tinyint(3) UNSIGNED NOT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT 0,
  `opens_at` time DEFAULT NULL,
  `closes_at` time DEFAULT NULL,
  `closes_next_day` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_hours`
--

INSERT INTO `business_hours` (`id`, `day_of_week`, `is_closed`, `opens_at`, `closes_at`, `closes_next_day`, `created_at`, `updated_at`) VALUES
(1, 0, 0, '11:00:00', '22:00:00', 0, '2026-09-24 23:04:04', '2026-09-24 23:04:04'),
(2, 1, 0, '11:00:00', '22:00:00', 0, '2026-09-24 23:04:04', '2026-09-24 23:04:04'),
(3, 2, 0, '11:00:00', '22:00:00', 0, '2026-09-24 23:04:04', '2026-09-24 23:04:04'),
(4, 3, 0, '11:00:00', '22:00:00', 0, '2026-09-24 23:04:04', '2026-09-24 23:04:04'),
(5, 4, 0, '11:00:00', '22:00:00', 0, '2026-09-24 23:04:04', '2026-09-24 23:04:04'),
(6, 5, 0, '11:00:00', '22:00:00', 0, '2026-09-24 23:04:04', '2026-09-24 23:04:04'),
(7, 6, 0, '11:00:00', '22:00:00', 0, '2026-09-24 23:04:04', '2026-09-24 23:04:04');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('madrascafe_cache_track:061edcd9-bc51-4dc3-b674-395840a54a5a:6666cd76f96956469e7be39d750cc7d9', 'i:1;', 1795611013);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `row_id` varchar(255) NOT NULL,
  `menu_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `unit_price` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`, `icon`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(3, NULL, 'Snacks', 'snacks', NULL, NULL, 2, 1, '2026-11-20 00:38:39', '2026-11-20 00:38:39'),
(4, NULL, 'Tiffin', 'tiffin', NULL, NULL, 3, 1, '2026-11-20 00:39:03', '2026-11-20 00:39:03'),
(5, NULL, 'Bread', 'bread', NULL, NULL, 5, 1, '2026-11-20 00:39:26', '2026-11-20 00:41:07'),
(6, NULL, 'Main Course', 'main-course', NULL, NULL, 4, 1, '2026-11-20 00:40:56', '2026-11-20 00:40:56'),
(7, 6, 'Biryani', 'biryani', NULL, NULL, 0, 1, '2026-11-20 00:41:56', '2026-11-20 00:41:56'),
(8, 6, 'Rice', 'rice', NULL, NULL, 1, 1, '2026-11-20 00:42:25', '2026-11-20 00:42:25'),
(9, 6, 'Vegetarian Curries', 'vegetarian-curries', NULL, NULL, 2, 1, '2026-11-20 00:43:28', '2026-11-20 00:43:28'),
(10, 6, 'Non Vegetarian Curries', 'non-vegetarian-curries', NULL, NULL, 3, 1, '2026-11-20 00:43:49', '2026-11-20 00:43:49'),
(11, NULL, 'Dosa', 'dosa', NULL, NULL, 6, 1, '2026-11-20 00:44:23', '2026-11-20 00:44:32'),
(12, NULL, 'Drinks', 'drinks', NULL, NULL, 7, 1, '2026-11-20 00:44:52', '2026-11-20 00:44:52'),
(13, NULL, 'Dessert', 'dessert', NULL, NULL, 8, 1, '2026-11-20 00:45:22', '2026-11-20 00:45:34'),
(14, NULL, 'Ice Cream', 'ice-cream', NULL, NULL, 9, 1, '2026-11-20 00:45:57', '2026-11-20 00:45:57'),
(15, NULL, 'MilkShakes', 'milkshakes', NULL, NULL, 10, 1, '2026-11-20 00:46:30', '2026-11-20 00:46:30'),
(16, NULL, 'Staters', 'staters', NULL, NULL, 0, 1, '2026-11-20 00:47:57', '2026-11-20 00:47:57'),
(17, 16, 'Veg Staters', 'veg-staters', NULL, NULL, 0, 1, '2026-11-20 00:48:16', '2026-11-20 00:48:16'),
(18, 16, 'Non Veg Staters', 'non-veg-staters', NULL, NULL, 1, 1, '2026-11-20 00:48:33', '2026-11-20 00:48:43');

-- --------------------------------------------------------

--
-- Table structure for table `category_media`
--

CREATE TABLE `category_media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `pdf_menu` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'new',
  `admin_reply` text DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `apartment` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `email`, `email_verified_at`, `phone`, `address`, `apartment`, `city`, `postcode`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'ali', 'Madrascafe', 'admin@example.com', NULL, 'admin@example.com', NULL, NULL, NULL, NULL, '$2y$12$eVQc7Ls5/bFS.VyAk/mZ9.4qrrJtf6DvyqhYPFSt83P3TODte9rzO', NULL, '2026-11-24 13:45:01', '2026-11-24 13:45:01');

-- --------------------------------------------------------

--
-- Table structure for table `deals`
--

CREATE TABLE `deals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `type` enum('flash_deal','happy_hour','lunch_special','tiered_spend','bogo','free_gift','combo','bundle','promo_code') NOT NULL,
  `discount_type` enum('percentage','fixed_amount','free_delivery','fixed_price','none') NOT NULL DEFAULT 'none',
  `discount_value` decimal(8,2) DEFAULT NULL,
  `combo_price` decimal(8,2) DEFAULT NULL,
  `min_spend` decimal(8,2) DEFAULT NULL,
  `buy_quantity` tinyint(3) UNSIGNED DEFAULT NULL,
  `get_quantity` tinyint(3) UNSIGNED DEFAULT NULL,
  `get_discount_percent` tinyint(3) UNSIGNED DEFAULT NULL,
  `promo_code` varchar(255) DEFAULT NULL,
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `recurring_days` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recurring_days`)),
  `daily_start_time` time DEFAULT NULL,
  `daily_end_time` time DEFAULT NULL,
  `usage_limit` int(10) UNSIGNED DEFAULT NULL,
  `used_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `deals`
--

INSERT INTO `deals` (`id`, `name`, `slug`, `description`, `image`, `type`, `discount_type`, `discount_value`, `combo_price`, `min_spend`, `buy_quantity`, `get_quantity`, `get_discount_percent`, `promo_code`, `starts_at`, `ends_at`, `recurring_days`, `daily_start_time`, `daily_end_time`, `usage_limit`, `used_count`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'family deal', 'family-deal', NULL, 'deals/BWVqfvtHCZGcrIDZdJF6DDSE8VoX6dXW2bXLqOcX.png', 'happy_hour', 'percentage', 10.00, NULL, NULL, 1, 1, 100, NULL, NULL, NULL, '[3,0,1,2,4,5,6]', '03:17:00', '21:00:00', NULL, 0, 1, '2026-10-14 19:27:41', '2026-11-22 03:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `deal_items`
--

CREATE TABLE `deal_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `deal_id` bigint(20) UNSIGNED NOT NULL,
  `menu_item_id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('applies_to','buy','free','bundle_component') NOT NULL,
  `quantity` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `override_price` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `deal_items`
--

INSERT INTO `deal_items` (`id`, `deal_id`, `menu_item_id`, `role`, `quantity`, `override_price`, `created_at`, `updated_at`) VALUES
(1, 1, 82, 'applies_to', 1, NULL, '2026-11-22 03:17:47', '2026-11-22 03:17:47'),
(2, 1, 21, 'applies_to', 1, NULL, '2026-11-22 03:17:47', '2026-11-22 03:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_settings`
--

CREATE TABLE `delivery_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `radius_km` decimal(6,2) NOT NULL DEFAULT 5.00,
  `base_price` decimal(8,2) NOT NULL DEFAULT 2.50,
  `base_km` decimal(8,2) NOT NULL DEFAULT 1.00,
  `per_km_price` decimal(8,2) NOT NULL DEFAULT 1.00,
  `max_delivery_fee` decimal(8,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `delivery_settings`
--

INSERT INTO `delivery_settings` (`id`, `address`, `postcode`, `latitude`, `longitude`, `radius_km`, `base_price`, `base_km`, `per_km_price`, `max_delivery_fee`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '5 Poplar St, Nottingham NG1 1GP, UK', 'NG1 1GP', 52.9506174, -1.1395126, 3.00, 2.50, 1.00, 1.00, NULL, 1, '2026-10-06 17:17:47', '2026-10-14 19:14:49');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_coupons`
--

CREATE TABLE `loyalty_coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `discount_type` varchar(10) NOT NULL,
  `discount_value` decimal(8,2) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used_at` timestamp NULL DEFAULT NULL,
  `used_order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `voided_at` timestamp NULL DEFAULT NULL,
  `emailed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `discount_price` decimal(8,2) DEFAULT NULL,
  `prep_time_minutes` smallint(5) UNSIGNED DEFAULT NULL,
  `spice_level` enum('none','mild','medium','hot') NOT NULL DEFAULT 'none',
  `is_vegetarian` tinyint(1) NOT NULL DEFAULT 0,
  `is_vegan` tinyint(1) NOT NULL DEFAULT 0,
  `is_gluten_free` tinyint(1) NOT NULL DEFAULT 0,
  `ingredients` text DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `slug`, `description`, `category_id`, `price`, `discount_price`, `prep_time_minutes`, `spice_level`, `is_vegetarian`, `is_vegan`, `is_gluten_free`, `ingredients`, `is_available`, `is_featured`, `created_at`, `updated_at`) VALUES
(3, 'Dahi Vada', 'dahi-vada', NULL, 17, 5.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 01:14:59', '2026-11-20 01:14:59'),
(4, 'Chilli Fry Vada', 'chilli-fry-vada', NULL, 17, 5.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 01:17:15', '2026-11-20 01:17:15'),
(5, 'Paneer 65', 'paneer-65', NULL, 17, 7.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 01:18:53', '2026-11-20 01:18:53'),
(6, 'Chilli Paneer', 'chilli-paneer', NULL, 17, 7.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 01:20:41', '2026-11-20 01:20:41'),
(7, 'Gobi 65', 'gobi-65', NULL, 17, 6.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 01:22:19', '2026-11-20 01:22:19'),
(8, 'Gobi Manchurian', 'gobi-manchurian', NULL, 17, 6.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 01:24:23', '2026-11-20 01:24:23'),
(9, 'Samosa', 'samosa', NULL, 17, 4.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 01:25:57', '2026-11-20 01:25:57'),
(10, 'Chilli Fry Idly', 'chilli-fry-idly', NULL, 17, 5.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 01:27:16', '2026-11-20 01:27:16'),
(11, 'Medu Vada', 'medu-vada', NULL, 17, 4.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 01:38:28', '2026-11-20 01:38:28'),
(12, 'Sambar Vada', 'sambar-vada', NULL, 17, 4.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 01:39:13', '2026-11-20 01:39:13'),
(13, 'Chicken 65', 'chicken-65', NULL, 18, 7.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 10:11:35', '2026-11-20 10:11:35'),
(14, 'Spicy Chicken Devil', 'spicy-chicken-devil', NULL, 18, 8.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 10:13:27', '2026-11-20 10:13:27'),
(15, 'Milagu Kozhi Varuval', 'milagu-kozhi-varuval', NULL, 18, 8.90, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 10:15:10', '2026-11-20 10:15:10'),
(16, 'Chicken Lollipop', 'chicken-lollipop', NULL, 18, 8.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 10:19:29', '2026-11-20 10:20:20'),
(17, 'Mutton Devil (Spicy)', 'mutton-devil-spicy', NULL, 18, 9.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 10:22:18', '2026-11-20 10:22:18'),
(18, 'Chettinad Mutton Chukka', 'chettinad-mutton-chukka', NULL, 18, 10.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 10:23:37', '2026-11-20 10:23:37'),
(19, 'Karuveppilai Eral Varuval', 'karuveppilai-eral-varuval', NULL, 18, 11.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 10:25:10', '2026-11-20 10:25:10'),
(20, 'Prawn 65', 'prawn-65', NULL, 18, 10.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 10:26:20', '2026-11-20 10:28:01'),
(21, 'Amritsari Fish Fry', 'amritsari-fish-fry', NULL, 18, 9.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 10:27:41', '2026-11-20 10:27:41'),
(22, 'Butter Garlic Squid', 'butter-garlic-squid', NULL, 18, 11.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 12:05:29', '2026-11-20 12:05:29'),
(23, 'Tawa Fish Fry (King Fish)', 'tawa-fish-fry-king-fish', NULL, 18, 12.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-20 12:08:39', '2026-11-20 12:08:39'),
(24, 'Mutton Rolls', 'mutton-rolls', NULL, 3, 5.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 01:26:27', '2026-11-21 01:26:27'),
(25, 'Egg Bonda', 'egg-bonda', NULL, 3, 4.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 01:27:28', '2026-11-21 01:27:28'),
(26, 'Cutlet', 'cutlet', NULL, 3, 4.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 01:28:26', '2026-11-21 01:28:26'),
(27, 'Pattice', 'pattice', NULL, 3, 4.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 01:29:52', '2026-11-21 01:29:52'),
(28, 'Mallu Pan', 'mallu-pan', NULL, 3, 4.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 01:30:43', '2026-11-21 01:30:43'),
(29, 'Ceylon Pancake', 'ceylon-pancake', NULL, 3, 4.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 01:31:37', '2026-11-21 01:31:37'),
(30, 'Egg Omelette', 'egg-omelette', NULL, 3, 4.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 01:32:20', '2026-11-21 01:32:20'),
(31, 'Idly', 'idly', NULL, 4, 3.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 01:39:03', '2026-11-21 01:39:03'),
(32, 'Chilli Idly', 'chilli-idly', NULL, 4, 4.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 01:42:09', '2026-11-21 01:42:09'),
(33, 'Mini Idly (Available on Weekends)', 'mini-idly-available-on-weekends', NULL, 4, 4.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 07:40:52', '2026-11-21 07:40:52'),
(34, 'Pongal (Available on Weekends)', 'pongal-available-on-weekends', NULL, 4, 5.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 07:41:54', '2026-11-21 07:41:54'),
(35, 'Poori Masala', 'poori-masala', NULL, 4, 5.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 07:42:52', '2026-11-21 07:42:52'),
(36, 'Soft Drink Can', 'soft-drink-can', NULL, 12, 1.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(37, 'Water', 'water', NULL, 12, 1.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 08:00:56', '2026-11-21 08:00:56'),
(38, 'Lassi', 'lassi', NULL, 12, 3.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 08:03:52', '2026-11-21 08:03:52'),
(39, 'Tea', 'tea', NULL, 12, 2.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 08:06:33', '2026-11-21 08:06:33'),
(40, 'Coffee', 'coffee', NULL, 12, 2.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 08:07:23', '2026-11-21 08:07:23'),
(41, 'Gulab Jamun', 'gulab-jamun', NULL, 13, 4.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 09:45:57', '2026-11-21 09:45:57'),
(42, 'Carrot Halwa', 'carrot-halwa', NULL, 13, 4.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 09:46:41', '2026-11-21 09:46:41'),
(43, 'Semiya Payasam', 'semiya-payasam', NULL, 13, 4.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 09:47:31', '2026-11-21 09:47:31'),
(44, 'Kesari', 'kesari', NULL, 13, 4.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 09:48:16', '2026-11-21 09:48:16'),
(45, 'Scoop', 'scoop', NULL, 14, 3.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 09:49:19', '2026-11-21 09:49:19'),
(46, 'Sundae', 'sundae', NULL, 14, 5.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 09:49:57', '2026-11-21 09:49:57'),
(47, 'Falooda', 'falooda', NULL, 14, 5.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 09:50:30', '2026-11-21 09:50:30'),
(48, 'Vanilla', 'vanilla', NULL, 15, 4.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 09:51:35', '2026-11-21 09:51:35'),
(49, 'Oreo', 'oreo', NULL, 15, 5.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 09:51:59', '2026-11-21 09:51:59'),
(50, 'Bueno', 'bueno', NULL, 15, 5.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-21 09:52:30', '2026-11-21 09:52:30'),
(51, 'Chicken Biryani', 'chicken-biryani', NULL, 7, 11.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 01:03:06', '2026-11-22 01:03:06'),
(52, 'Mutton Biryani', 'mutton-biryani', NULL, 7, 13.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 01:04:46', '2026-11-22 01:04:46'),
(53, 'Vegetable Biryani', 'vegetable-biryani', NULL, 7, 8.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 01:05:56', '2026-11-22 01:05:56'),
(54, 'Prawn Biryani', 'prawn-biryani', NULL, 7, 14.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 01:07:03', '2026-11-22 01:07:09'),
(55, 'Paneer Biryani', 'paneer-biryani', NULL, 7, 12.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-22 01:08:24', '2026-11-22 01:08:24'),
(56, 'Plain Rice', 'plain-rice', NULL, 8, 3.00, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 01:09:24', '2026-11-22 01:09:24'),
(57, 'Pulao Rice', 'pulao-rice', NULL, 8, 4.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 01:10:59', '2026-11-22 01:10:59'),
(58, 'Keema Pulao Rice', 'keema-pulao-rice', NULL, 8, 10.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 01:12:24', '2026-11-22 01:12:24'),
(59, 'Chicken Fried Rice', 'chicken-fried-rice', NULL, 8, 8.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 01:13:40', '2026-11-22 01:13:40'),
(60, 'Egg Fried Rice', 'egg-fried-rice', NULL, 8, 7.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 01:14:55', '2026-11-22 01:14:55'),
(61, 'Seafood Fried Rice', 'seafood-fried-rice', NULL, 8, 11.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 01:16:43', '2026-11-22 01:16:43'),
(62, 'Chettinad Paneer Curry', 'chettinad-paneer-curry', NULL, 9, 10.50, NULL, NULL, 'none', 1, 1, 0, NULL, 1, 1, '2026-11-22 01:37:33', '2026-11-22 01:37:33'),
(63, 'Paneer Butter Masala', 'paneer-butter-masala', NULL, 9, 10.50, NULL, NULL, 'none', 1, 1, 0, NULL, 1, 1, '2026-11-22 01:39:08', '2026-11-22 01:39:08'),
(65, 'Kadai Paneer Masala', 'kadai-paneer-masala', NULL, 9, 10.95, NULL, NULL, 'none', 1, 1, 0, NULL, 1, 0, '2026-11-22 01:46:47', '2026-11-22 01:46:47'),
(66, 'Aubergine Chickpea Curry', 'aubergine-chickpea-curry', NULL, 9, 9.95, NULL, NULL, 'none', 1, 1, 0, NULL, 1, 1, '2026-11-22 01:48:18', '2026-11-22 01:48:18'),
(67, 'Parippu Curry', 'parippu-curry', NULL, 9, 8.95, NULL, NULL, 'none', 1, 1, 0, NULL, 1, 1, '2026-11-22 01:49:41', '2026-11-22 01:49:41'),
(68, 'Vegetable Korma', 'vegetable-korma', NULL, 9, 9.50, NULL, NULL, 'none', 1, 1, 0, NULL, 1, 1, '2026-11-22 01:52:05', '2026-11-22 01:52:05'),
(70, 'Chicken Butter', 'chicken-butter', NULL, 10, 11.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 02:04:58', '2026-11-22 03:53:39'),
(71, 'Chettinad Chicken Curry', 'chettinad-chicken-curry', NULL, 10, 11.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 02:06:21', '2026-11-22 02:06:21'),
(72, 'Spicy Kadai Chicken', 'spicy-kadai-chicken', NULL, 10, 11.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 02:07:30', '2026-11-22 02:07:30'),
(73, 'Malabar Chicken Curry', 'malabar-chicken-curry', NULL, 10, 11.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-22 02:08:39', '2026-11-22 02:08:39'),
(74, 'Madras Mutton Curry', 'madras-mutton-curry', NULL, 10, 13.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 02:10:22', '2026-11-22 02:10:53'),
(75, 'Kadai Mutton Masala', 'kadai-mutton-masala', NULL, 10, 13.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-22 02:12:14', '2026-11-22 02:12:14'),
(76, 'Mutton Methi Korma', 'mutton-methi-korma', NULL, 10, 13.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-22 02:14:37', '2026-11-22 02:14:37'),
(77, 'Malabar Prawn Curry', 'malabar-prawn-curry', NULL, 10, 14.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 02:16:22', '2026-11-22 02:16:22'),
(78, 'Madras Fish Curry', 'madras-fish-curry', NULL, 10, 13.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 02:18:32', '2026-11-22 02:18:32'),
(79, 'Naan', 'naan', NULL, 5, 3.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 02:40:21', '2026-11-22 02:40:21'),
(80, 'Parotta', 'parotta', NULL, 5, 4.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-22 02:41:38', '2026-11-22 02:41:38'),
(81, 'Egg Parotta', 'egg-parotta', NULL, 5, 5.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-22 02:42:57', '2026-11-22 02:42:57'),
(82, 'Aloo Parotta', 'aloo-parotta', NULL, 5, 5.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 02:43:59', '2026-11-22 02:43:59'),
(83, 'Cheese Parotta', 'cheese-parotta', NULL, 5, 6.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 02:45:10', '2026-11-22 02:45:17'),
(84, 'Butter Chapathi', 'butter-chapathi', NULL, 5, 5.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-22 02:46:54', '2026-11-22 02:46:54'),
(85, 'Chicken Kothu Parotta', 'chicken-kothu-parotta', NULL, 5, 9.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 02:47:44', '2026-11-22 02:47:44'),
(86, 'Mutton Kothu Parotta', 'mutton-kothu-parotta', NULL, 5, 11.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-22 02:48:48', '2026-11-22 02:48:48'),
(87, 'Seafood Kothu Parotta', 'seafood-kothu-parotta', NULL, 5, 12.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-22 02:49:31', '2026-11-22 02:49:31'),
(88, 'Plain Dosa', 'plain-dosa', NULL, 11, 5.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-23 01:48:13', '2026-11-23 01:48:13'),
(89, 'Paper Dosa', 'paper-dosa', NULL, 11, 5.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-23 01:49:43', '2026-11-23 01:49:43'),
(90, 'Ghee Dosa / Ghee Masala Dosa', 'ghee-dosa-ghee-masala-dosa', NULL, 11, 7.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-23 01:51:13', '2026-11-23 01:51:13'),
(91, 'Egg Omelette Dosa', 'egg-omelette-dosa', NULL, 11, 8.90, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-23 01:53:23', '2026-11-23 01:54:06'),
(92, 'Masala Dosa', 'masala-dosa', NULL, 11, 7.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-23 01:57:01', '2026-11-23 01:57:01'),
(93, 'Onion Dosa / Onion Podi Dosa', 'onion-dosa-onion-podi-dosa', NULL, 11, 7.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-23 02:00:00', '2026-11-23 02:00:00'),
(94, 'Mysore Dosa / Mysore Masala Dosa', 'mysore-dosa-mysore-masala-dosa', NULL, 11, 7.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-23 02:02:14', '2026-11-23 02:02:14'),
(95, 'Chilli Coriander Dosa', 'chilli-coriander-dosa', NULL, 11, 7.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-23 02:06:03', '2026-11-23 02:06:03'),
(96, 'Cheese Dosa / Cheese Podi Dosa', 'cheese-dosa-cheese-podi-dosa', NULL, 11, 8.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-23 02:07:34', '2026-11-23 02:07:34'),
(97, 'Paneer Masala Dosa', 'paneer-masala-dosa', NULL, 11, 8.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 1, '2026-11-23 02:09:23', '2026-11-23 02:09:23'),
(98, 'Chicken Masala Dosa', 'chicken-masala-dosa', NULL, 11, 10.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-23 02:24:40', '2026-11-23 02:24:40'),
(99, 'Kids Dosa', 'kids-dosa', NULL, 11, 4.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-23 02:26:08', '2026-11-23 02:26:08'),
(100, 'Chocolate Dosa', 'chocolate-dosa', NULL, 11, 8.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-23 02:28:08', '2026-11-23 02:28:08'),
(101, 'Onion Chilli Uthappam / Podi Uthappam', 'onion-chilli-uthappam-podi-uthappam', NULL, 11, 8.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-23 02:31:52', '2026-11-23 02:31:52'),
(102, 'Bangalore Uthappam', 'bangalore-uthappam', NULL, 11, 8.50, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-23 02:33:57', '2026-11-23 02:33:57'),
(103, 'Podi Dosa', 'podi-dosa', NULL, 11, 7.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-23 02:36:02', '2026-11-23 02:36:02'),
(105, 'Rava Masala Dosa', 'rava-masala-dosa', NULL, 11, 7.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-23 02:38:24', '2026-11-23 02:38:24'),
(106, 'Onion Rava Dosa', 'onion-rava-dosa', NULL, 11, 7.95, NULL, NULL, 'none', 0, 0, 0, NULL, 1, 0, '2026-11-23 02:39:30', '2026-11-23 02:39:30');

-- --------------------------------------------------------

--
-- Table structure for table `menu_item_images`
--

CREATE TABLE `menu_item_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_item_id` bigint(20) UNSIGNED NOT NULL,
  `path` varchar(255) NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_item_images`
--

INSERT INTO `menu_item_images` (`id`, `menu_item_id`, `path`, `sort_order`, `is_primary`, `created_at`, `updated_at`) VALUES
(3, 3, 'menu-items/3/Nal4vwWk3PutDCMC2oIteBAFUHG3GvSJR6gyYSFO.jpg', 0, 1, '2026-11-20 01:14:59', '2026-11-20 01:14:59'),
(4, 4, 'menu-items/4/BZEy0XGJcM2cM8lOlDxQDiwDHZHgRHp2MskUDtsz.jpg', 0, 1, '2026-11-20 01:17:15', '2026-11-20 01:17:15'),
(5, 5, 'menu-items/5/XrM994STTleqR84SdJ6DDSj2QGAhXJqqnkpHPqIv.jpg', 0, 1, '2026-11-20 01:18:53', '2026-11-20 01:18:53'),
(6, 6, 'menu-items/6/zfDIUl2mO63vpUue46Bcu3DMdMtQD0Pdvw8P45SC.jpg', 0, 1, '2026-11-20 01:20:41', '2026-11-20 01:20:41'),
(7, 7, 'menu-items/7/hRAR1AClvg94JpN6QtZmbLpHDnQ4yeZMyvMFglYx.jpg', 0, 1, '2026-11-20 01:22:20', '2026-11-20 01:22:20'),
(8, 8, 'menu-items/8/DwFJzlxBXulzihaq9OCqVXnjFlnD2Q63SlaZ9ebU.jpg', 0, 1, '2026-11-20 01:24:24', '2026-11-20 01:24:24'),
(9, 9, 'menu-items/9/cjn7631ATdNJklhvGvJ89qQ66TVGnUcqql2UnDBl.jpg', 0, 1, '2026-11-20 01:25:57', '2026-11-20 01:25:57'),
(10, 10, 'menu-items/10/eIcKtsdRgFbh0LWpe9WfR8MA4fLRUr4B49TL5bd3.jpg', 0, 1, '2026-11-20 01:27:16', '2026-11-20 01:27:16'),
(14, 11, 'menu-items/11/tDXUyIYKiBXWzgZRxtTjb4wPoxkCCoU1mBDo2b2o.jpg', 0, 1, '2026-11-20 01:38:29', '2026-11-20 01:38:29'),
(15, 12, 'menu-items/12/1ijhlhNcKrtA4M1joUf2vRNca7u4X9X9RSs8cRIP.jpg', 0, 1, '2026-11-20 01:39:13', '2026-11-20 01:39:13'),
(16, 13, 'menu-items/13/Z5nhVNpsl58GCEM0jG3Ede2rmdtG4qRJjR7yiZQO.jpg', 0, 1, '2026-11-20 10:11:35', '2026-11-20 10:11:35'),
(17, 14, 'menu-items/14/woLBcgsN0IDSvdNE4aE1tim2qr19Cwc7kLoj4wpx.jpg', 0, 1, '2026-11-20 10:13:27', '2026-11-20 10:13:27'),
(18, 15, 'menu-items/15/Fdld4aucTfZSzIx3UdKy7YvHDmeNENTs60LP62Is.jpg', 0, 1, '2026-11-20 10:15:10', '2026-11-20 10:15:10'),
(19, 16, 'menu-items/16/O4EoqvJ9oQ8E1rWi2VqVaZrJE5YGjZrKdJQJG3W7.jpg', 0, 1, '2026-11-20 10:19:29', '2026-11-20 10:19:29'),
(20, 17, 'menu-items/17/3hce3FggSkQCB3PRZDdjgJXl7ykmAqNbYLjuupzG.jpg', 0, 1, '2026-11-20 10:22:18', '2026-11-20 10:22:18'),
(21, 18, 'menu-items/18/KRbDgXTXMQYI4Mf2phgYcKSF8lfNvaVJ1q5oV6dd.jpg', 0, 1, '2026-11-20 10:23:37', '2026-11-20 10:23:37'),
(22, 19, 'menu-items/19/YG8u5U6eZZE56dsFnGpQYmHdSEH3TJTjY2BopkC1.jpg', 0, 1, '2026-11-20 10:25:10', '2026-11-20 10:25:10'),
(23, 20, 'menu-items/20/7ZZSGrb7LAgPn4Ha2b1egVD6KNxctVweQd45EpGk.jpg', 0, 1, '2026-11-20 10:26:20', '2026-11-20 10:26:20'),
(24, 21, 'menu-items/21/sYnKYSJlUwHEeBMIBH6pu5KHVh0O2SmfwG3tCYF6.jpg', 0, 1, '2026-11-20 10:27:41', '2026-11-20 10:27:41'),
(25, 22, 'menu-items/22/kNaeZl2snHgztH5zveEfaifO6cp9PDgKBnyZGpEg.jpg', 0, 1, '2026-11-20 12:05:29', '2026-11-20 12:05:29'),
(26, 23, 'menu-items/23/FUBIeJSN6yi41QKCrAQuuTWVkkvdtgsr6ZMzFJgY.jpg', 0, 1, '2026-11-20 12:08:39', '2026-11-20 12:08:39'),
(27, 24, 'menu-items/24/qK43CYTaCIAvFnq2MysLmRX8CWGcBNdjyBdY8PFU.jpg', 0, 1, '2026-11-21 01:26:27', '2026-11-21 01:26:27'),
(28, 25, 'menu-items/25/lAlgEkFhQcqYVX3LPNsHVyBrOlefVGoqOPJVBR1r.jpg', 0, 1, '2026-11-21 01:27:28', '2026-11-21 01:27:28'),
(29, 26, 'menu-items/26/FXHhCmaYfB9x5tFC2rAfE737y8zPQLlolL17u1a0.jpg', 0, 1, '2026-11-21 01:28:26', '2026-11-21 01:28:26'),
(30, 27, 'menu-items/27/bc1AYdux25zoFcPkCqcHAoMQRJ98edjB8ULbxgbx.jpg', 0, 1, '2026-11-21 01:29:52', '2026-11-21 01:29:52'),
(31, 28, 'menu-items/28/gHzN7XRCmSmAbpfAMX2PB2SIPUFXoyGew9XoAfEE.jpg', 0, 1, '2026-11-21 01:30:43', '2026-11-21 01:30:43'),
(32, 29, 'menu-items/29/o4BCSDORBQfmkcMjKWyhreR6JVaiigkHsSWVXZ2p.jpg', 0, 1, '2026-11-21 01:31:37', '2026-11-21 01:31:37'),
(33, 30, 'menu-items/30/pY9juw24VotBAgT5Gn1vpNEW2HIkcKcDTZf5jYxU.jpg', 0, 1, '2026-11-21 01:32:20', '2026-11-21 01:32:20'),
(34, 31, 'menu-items/31/8HhGa4nD83Clz07MmS8s6K0FiXpkgpONMGk1ydTF.jpg', 0, 1, '2026-11-21 01:39:03', '2026-11-21 01:39:03'),
(35, 32, 'menu-items/32/iUhzbtDeXLI99IelxtUU4BIntbrtCkrXmIYvSrCu.jpg', 0, 1, '2026-11-21 01:42:10', '2026-11-21 01:42:10'),
(36, 33, 'menu-items/33/zjjRhIMGW1JhGMgqREa1jMUSEv2QiMaA5cBvVvxv.jpg', 0, 1, '2026-11-21 07:40:52', '2026-11-21 07:40:52'),
(37, 34, 'menu-items/34/sJX00PLMUJaoImuCzpXakTpVgYkKhl8FOMMQzUJL.jpg', 0, 1, '2026-11-21 07:41:54', '2026-11-21 07:41:54'),
(38, 35, 'menu-items/35/K8Fb6oeXVdaVuMPSYb1gNvrp4HaabL1InMyw0H76.jpg', 0, 1, '2026-11-21 07:42:52', '2026-11-21 07:42:52'),
(39, 36, 'menu-items/36/2qd8iJx19k7bIBA92R7wwyYO3m27Pyb3e6DYdkK2.jpg', 0, 1, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(40, 37, 'menu-items/37/2ALqZ7jenpH69kw2PoeJYNVFwaWkYhrTcjuhsSAw.jpg', 1, 1, '2026-11-21 09:59:09', '2026-11-21 09:59:09'),
(41, 38, 'menu-items/38/efcWR9PstlKhNlY39KfBgXM8NnG7KQKnY9edPHCw.jpg', 1, 1, '2026-11-21 09:59:37', '2026-11-21 09:59:37'),
(42, 39, 'menu-items/39/FQpXhvVfwn5ukgnRgigOrFWCDZW4fJ8QpSFjI9Wn.jpg', 1, 1, '2026-11-21 10:00:02', '2026-11-21 10:00:02'),
(43, 40, 'menu-items/40/u37kUJngh8Yeo7IyppO5AUokAkRbI3GoMWjamPFf.jpg', 1, 1, '2026-11-21 10:00:27', '2026-11-21 10:00:27'),
(44, 41, 'menu-items/41/jrrsRHc7idIJLFuVXy19hJ02kInaybHRyyAybB1m.jpg', 1, 1, '2026-11-21 10:03:11', '2026-11-21 10:03:11'),
(45, 42, 'menu-items/42/fJk5ZqNx4bBW7iJ2MCc1H02GL9Fbvuox2UsvMcZS.jpg', 1, 1, '2026-11-21 10:03:38', '2026-11-21 10:03:38'),
(46, 43, 'menu-items/43/9cxdHxhTU5pmsISS7qmSnNzfLTLZezYwSCTEFsJy.jpg', 1, 1, '2026-11-21 10:04:01', '2026-11-21 10:04:01'),
(47, 44, 'menu-items/44/iPmWFDAThN4YwImDUfO8uiJyDqKU375ZzMZPn9Ei.jpg', 1, 1, '2026-11-21 10:04:22', '2026-11-21 10:04:22'),
(48, 45, 'menu-items/45/yenaiQuQ5zIXQlvA26M9gRSmaevkXj7RAAFM5OhQ.jpg', 1, 1, '2026-11-21 10:46:59', '2026-11-21 10:46:59'),
(49, 46, 'menu-items/46/ddmAKjp50t2xZUnMEDtxduPW8aJ2o3szRYTqmRrB.jpg', 1, 1, '2026-11-21 10:47:46', '2026-11-21 10:47:46'),
(50, 47, 'menu-items/47/nBGfZoDUMioahezVbeiZze225GConO492lC4fftT.jpg', 1, 1, '2026-11-21 10:48:14', '2026-11-21 10:48:14'),
(51, 48, 'menu-items/48/uEZ5yTiPOA1K9hvnDFIUDw42RA1zY11VD1uQrh3d.jpg', 1, 1, '2026-11-21 11:09:04', '2026-11-21 11:09:04'),
(52, 49, 'menu-items/49/kR23sLlULyppxBRd2wHH4Kj6YAIUdE5hjyrSkdqP.jpg', 1, 1, '2026-11-21 11:09:24', '2026-11-21 11:09:24'),
(53, 50, 'menu-items/50/U6nfnB9D8XwXaKz0scOgHdBlpJvu06SJlqlMvqkv.jpg', 1, 1, '2026-11-21 11:09:49', '2026-11-21 11:09:49'),
(54, 51, 'menu-items/51/ZwVgGdLWUQdCNjLf8zpX7SKNzVr1EEncsGy21dDX.jpg', 0, 1, '2026-11-22 01:03:06', '2026-11-22 01:03:06'),
(55, 52, 'menu-items/52/L9pTalJR72lS0R151XmkzaWzVU6fyyBFv50VonZC.jpg', 0, 1, '2026-11-22 01:04:46', '2026-11-22 01:04:46'),
(56, 53, 'menu-items/53/oW9tzFFuMG75CkjTHb93uSPaISsILOqftT7XU6bY.jpg', 0, 1, '2026-11-22 01:05:56', '2026-11-22 01:05:56'),
(57, 54, 'menu-items/54/OM1R1xkJBJOiPozrOT7EBAKWUXWZE0I9XryczHVX.jpg', 0, 1, '2026-11-22 01:07:03', '2026-11-22 01:07:03'),
(58, 55, 'menu-items/55/La9pkf92ZwH8Ia5wHpKlgHbvmHMJEHFzSxKnZFRS.jpg', 0, 1, '2026-11-22 01:08:24', '2026-11-22 01:08:24'),
(59, 56, 'menu-items/56/QOZ48DYyeqqJ3jS30pIP0CzErd354zbNzrhiInS1.jpg', 0, 1, '2026-11-22 01:09:24', '2026-11-22 01:09:24'),
(60, 57, 'menu-items/57/M0bYUVCnM8MMfx6nZuag3nMF7gezXyPQjVUCylYA.jpg', 0, 1, '2026-11-22 01:10:59', '2026-11-22 01:10:59'),
(61, 58, 'menu-items/58/fqCi2XdYsHdun6gToa0Kp6e1C94vnYNU34XJJeqi.jpg', 0, 1, '2026-11-22 01:12:25', '2026-11-22 01:12:25'),
(62, 59, 'menu-items/59/Rr5NQM1ed9P6MV8rrD8mGksHimC5s4dZffDa7NLM.jpg', 0, 1, '2026-11-22 01:13:40', '2026-11-22 01:13:40'),
(63, 60, 'menu-items/60/5l50TaC7mARg1IQzRXziGuk0c183e894NfVMG5oc.jpg', 0, 1, '2026-11-22 01:14:55', '2026-11-22 01:14:55'),
(64, 61, 'menu-items/61/Uc2xcEaw5IUieLl5H1usvLyQ9u1AbbGit3ZxnxBg.jpg', 0, 1, '2026-11-22 01:16:43', '2026-11-22 01:16:43'),
(65, 62, 'menu-items/62/3iSI1cqYnoCZ3FgfdHEbQ1UtVN86LgoQM3TKPRTu.jpg', 0, 1, '2026-11-22 01:37:33', '2026-11-22 01:37:33'),
(66, 63, 'menu-items/63/Dzn8SeOcseDEhQkkFgdFw3j4CJsexYaR1kXHFS25.jpg', 0, 1, '2026-11-22 01:39:08', '2026-11-22 01:39:08'),
(67, 65, 'menu-items/65/lsTpcErvpSlvKbpbtdefFZ1NCZKmxg0rXQeNNrJQ.jpg', 0, 1, '2026-11-22 01:46:47', '2026-11-22 01:46:47'),
(68, 66, 'menu-items/66/952gzcbDX0EhM6AdZNZt8z0wXYrnBkS5uf3195pX.jpg', 0, 1, '2026-11-22 01:48:18', '2026-11-22 01:48:18'),
(69, 67, 'menu-items/67/xAxHygiDCdCwgRgeUNzqgtI6QjKhbkQIQ6HWLj25.jpg', 0, 1, '2026-11-22 01:49:41', '2026-11-22 01:49:41'),
(70, 68, 'menu-items/68/LFn1MChN0PjAczvVgGDRD1Q3MF0e1HbP990UzhRP.jpg', 1, 1, '2026-11-22 01:52:25', '2026-11-22 01:52:25'),
(73, 70, 'menu-items/70/X7gvO3Z2nEw5ahmOnGQ2dPwi4nc52DjvucdIgXDr.jpg', 0, 1, '2026-11-22 02:04:58', '2026-11-22 02:04:58'),
(74, 71, 'menu-items/71/72CMWdNWDHzj9EGamXmE45seo8XpKumjll4evNLj.jpg', 0, 1, '2026-11-22 02:06:21', '2026-11-22 02:06:21'),
(75, 72, 'menu-items/72/OXW0denO4EhiroJ2omoXXyn7qOwIsB12j4fg3dK6.jpg', 0, 1, '2026-11-22 02:07:30', '2026-11-22 02:07:30'),
(76, 73, 'menu-items/73/yp6Io0NBo6c9nTzCQpP75iIZI3uGqPqmg8IMPE14.jpg', 0, 1, '2026-11-22 02:08:39', '2026-11-22 02:08:39'),
(77, 74, 'menu-items/74/0XZCKG4NIKD6TyEQcXvbsoV9uj8iEtbALPihmDof.jpg', 0, 1, '2026-11-22 02:10:22', '2026-11-22 02:10:22'),
(78, 75, 'menu-items/75/zwWlYkr1xxh9VtMWZsCR72MIxL6N2N4Cm6ddh4hY.jpg', 0, 1, '2026-11-22 02:12:14', '2026-11-22 02:12:14'),
(79, 76, 'menu-items/76/d09hea75sZOXM2aBzVxbpJH49IEUGZq4FS44aXPb.jpg', 0, 1, '2026-11-22 02:14:37', '2026-11-22 02:14:37'),
(80, 77, 'menu-items/77/DzYtD7CpoM76aAA5ygP9sayvJPgzLhzaC4XBzd0Q.jpg', 0, 1, '2026-11-22 02:16:22', '2026-11-22 02:16:22'),
(81, 78, 'menu-items/78/1QgNrWwPl1Av5c4IFad4y5BaXPHsGQ0jvHMpzw1l.jpg', 0, 1, '2026-11-22 02:18:32', '2026-11-22 02:18:32'),
(82, 79, 'menu-items/79/7ttxBRgD29I4B0plFCWOHOWaQ4B71Mu7MzJKnJXy.jpg', 0, 1, '2026-11-22 02:40:22', '2026-11-22 02:40:22'),
(83, 80, 'menu-items/80/QBjQOgaQ2Noiudjwo6c2pxHwpbOej54OvfXUmaRj.jpg', 0, 1, '2026-11-22 02:41:38', '2026-11-22 02:41:38'),
(84, 81, 'menu-items/81/jhvXYpkCwwF49qo3IPRzoSNd5UbSmITYaoJZIte7.jpg', 0, 1, '2026-11-22 02:42:57', '2026-11-22 02:42:57'),
(85, 82, 'menu-items/82/YUgrFmWTJMNaNZDgSASiZLjinW7Y31xwkhYCAIR3.jpg', 0, 1, '2026-11-22 02:43:59', '2026-11-22 02:43:59'),
(86, 83, 'menu-items/83/F0VNm756EQ1VMK7Cj6iJxTbISE0y78Cz69soGMTM.jpg', 0, 1, '2026-11-22 02:45:10', '2026-11-22 02:45:10'),
(87, 84, 'menu-items/84/rhU0gy8ig3IDszXr24QGqBcYDCNENrGGN3efgVyC.jpg', 0, 1, '2026-11-22 02:46:54', '2026-11-22 02:46:54'),
(88, 85, 'menu-items/85/IV2MDzmSlfAv3E0Yhcx14LkVpAG0YTiOKA5uNuQm.jpg', 0, 1, '2026-11-22 02:47:44', '2026-11-22 02:47:44'),
(89, 86, 'menu-items/86/nzjWErC5HeLGqFOozfWW35bkK5qBDQ4aiagAx49W.jpg', 0, 1, '2026-11-22 02:48:48', '2026-11-22 02:48:48'),
(90, 87, 'menu-items/87/YaRkF708B7lHVju5u5gNjn4fruB9YB6cn4OGAXcS.jpg', 0, 1, '2026-11-22 02:49:31', '2026-11-22 02:49:31'),
(91, 88, 'menu-items/88/6fc0fIYmK9YkBZXj8zCkRKz6gGGYFBP7pj2bW9W3.jpg', 0, 1, '2026-11-23 01:48:13', '2026-11-23 01:48:13'),
(92, 89, 'menu-items/89/ceAntaffrGMvhfYhiJuHNGyjffkd8f0q6p4i07t4.jpg', 0, 1, '2026-11-23 01:49:43', '2026-11-23 01:49:43'),
(93, 90, 'menu-items/90/C3JhUGphCwAmnVHwRfugWxx5rOWrjjnwHabtl7HP.jpg', 0, 1, '2026-11-23 01:51:13', '2026-11-23 01:51:13'),
(94, 91, 'menu-items/91/himL4WQVwjzszSJvudyNlXdAV0Xd4h9vI39Lcrec.jpg', 0, 1, '2026-11-23 01:53:24', '2026-11-23 01:53:24'),
(95, 92, 'menu-items/92/zAuRMKzLvGNy6x9uUYYb8GdUd6k8b3xE5thPw6d3.jpg', 0, 1, '2026-11-23 01:57:01', '2026-11-23 01:57:01'),
(96, 93, 'menu-items/93/Dw6mjxf4A50v8Rrm8wfTr9WPUboakYpcOwZFbYyT.jpg', 0, 1, '2026-11-23 02:00:00', '2026-11-23 02:00:00'),
(97, 94, 'menu-items/94/fqhzH3rRIHpLXZ6t8FIrmf6XwOlpMh9RVkiUk4Bn.jpg', 0, 1, '2026-11-23 02:02:14', '2026-11-23 02:02:14'),
(98, 95, 'menu-items/95/beAJLoZZOQcP1Ywnj8kSzWC5MaZANNMWnTGODIHp.jpg', 0, 1, '2026-11-23 02:06:03', '2026-11-23 02:06:03'),
(99, 96, 'menu-items/96/vDIOAE0Mf2r66VLuG20gC9EdHfDbVZIVZvbFJv3Y.jpg', 0, 1, '2026-11-23 02:07:34', '2026-11-23 02:07:34'),
(100, 97, 'menu-items/97/GsLH5pPxAlgRWEgYMumlf7eKt8aTLENiezLVEFtK.jpg', 0, 1, '2026-11-23 02:09:24', '2026-11-23 02:09:24'),
(101, 98, 'menu-items/98/1uY9pABhaokDdVnZwioK2JlJq2K2Vx9yXmNftgz8.jpg', 0, 1, '2026-11-23 02:24:41', '2026-11-23 02:24:41'),
(102, 99, 'menu-items/99/mOIwGSQSz5hMbG2ouDrXtYegqZErIVl8O6y1k0Ki.jpg', 0, 1, '2026-11-23 02:26:08', '2026-11-23 02:26:08'),
(103, 100, 'menu-items/100/sv2k4s7ptlXaFcJl6m92G6geNUArlS9kutQBthEP.jpg', 0, 1, '2026-11-23 02:28:08', '2026-11-23 02:28:08'),
(104, 101, 'menu-items/101/Pj88HHmd4mEUiG0jgNuJplj0ERmrJbjvzsAg6XLV.jpg', 0, 1, '2026-11-23 02:31:52', '2026-11-23 02:31:52'),
(105, 102, 'menu-items/102/pRd4nCQKq4Wlx7ONZMBpohkLySvL0j6I7TmXoDlq.jpg', 0, 1, '2026-11-23 02:33:58', '2026-11-23 02:33:58'),
(106, 103, 'menu-items/103/FVcF5llbIvCKc0xTHfsKK77T1XvHwxvFgRZxlkrB.jpg', 0, 1, '2026-11-23 02:36:02', '2026-11-23 02:36:02'),
(108, 105, 'menu-items/105/zjJWwq84dL8tdB0zhvFHG9y4DoFji5I2UIXlB65z.jpg', 0, 1, '2026-11-23 02:38:24', '2026-11-23 02:38:24'),
(109, 106, 'menu-items/106/yL6j4MpKoSaOW3F354M5Z4sVAu6ZKOwPw5Eoo8Os.jpg', 0, 1, '2026-11-23 02:39:30', '2026-11-23 02:39:30');

-- --------------------------------------------------------

--
-- Table structure for table `menu_item_option_group`
--

CREATE TABLE `menu_item_option_group` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_item_id` bigint(20) UNSIGNED NOT NULL,
  `option_group_id` bigint(20) UNSIGNED NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_item_option_group`
--

INSERT INTO `menu_item_option_group` (`id`, `menu_item_id`, `option_group_id`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 16, 1, 0, '2026-11-20 10:19:29', '2026-11-20 10:20:20'),
(2, 36, 2, 0, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(3, 36, 3, 1, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(4, 37, 3, 0, '2026-11-21 08:00:56', '2026-11-21 09:59:13'),
(6, 38, 4, 1, '2026-11-21 08:03:52', '2026-11-21 09:59:40'),
(7, 38, 3, 0, '2026-11-21 08:04:23', '2026-11-21 09:59:40'),
(8, 51, 3, 0, '2026-11-22 01:03:06', '2026-11-22 01:03:06'),
(9, 52, 3, 0, '2026-11-22 01:04:46', '2026-11-22 01:04:46'),
(10, 53, 3, 0, '2026-11-22 01:05:56', '2026-11-22 01:05:56'),
(11, 54, 3, 0, '2026-11-22 01:07:03', '2026-11-22 01:07:03'),
(12, 55, 3, 0, '2026-11-22 01:08:24', '2026-11-22 01:08:24'),
(13, 56, 3, 0, '2026-11-22 01:09:24', '2026-11-22 01:09:24'),
(14, 57, 3, 0, '2026-11-22 01:10:59', '2026-11-22 01:10:59'),
(15, 58, 3, 0, '2026-11-22 01:12:24', '2026-11-22 01:12:24'),
(16, 59, 3, 0, '2026-11-22 01:13:40', '2026-11-22 01:13:40'),
(17, 60, 3, 0, '2026-11-22 01:14:55', '2026-11-22 01:14:55'),
(18, 61, 3, 0, '2026-11-22 01:16:43', '2026-11-22 01:16:43'),
(19, 62, 3, 0, '2026-11-22 01:37:33', '2026-11-22 01:37:33'),
(20, 62, 5, 1, '2026-11-22 01:37:33', '2026-11-22 01:37:33'),
(21, 62, 6, 2, '2026-11-22 01:37:33', '2026-11-22 01:37:33'),
(22, 63, 3, 0, '2026-11-22 01:39:08', '2026-11-22 01:39:08'),
(23, 63, 5, 1, '2026-11-22 01:39:08', '2026-11-22 01:39:08'),
(24, 63, 6, 2, '2026-11-22 01:39:08', '2026-11-22 01:39:08'),
(28, 65, 3, 0, '2026-11-22 01:46:47', '2026-11-22 01:46:47'),
(29, 65, 5, 1, '2026-11-22 01:46:47', '2026-11-22 01:46:47'),
(30, 65, 6, 2, '2026-11-22 01:46:47', '2026-11-22 01:46:47'),
(31, 66, 3, 0, '2026-11-22 01:48:18', '2026-11-22 01:48:18'),
(32, 66, 5, 1, '2026-11-22 01:48:18', '2026-11-22 01:48:18'),
(33, 66, 6, 2, '2026-11-22 01:48:18', '2026-11-22 01:48:18'),
(34, 67, 3, 0, '2026-11-22 01:49:41', '2026-11-22 01:49:41'),
(35, 67, 5, 1, '2026-11-22 01:49:41', '2026-11-22 01:49:41'),
(36, 67, 6, 2, '2026-11-22 01:49:41', '2026-11-22 01:49:41'),
(37, 68, 3, 0, '2026-11-22 01:52:05', '2026-11-22 01:52:28'),
(38, 68, 5, 1, '2026-11-22 01:52:05', '2026-11-22 01:52:28'),
(39, 68, 6, 2, '2026-11-22 01:52:05', '2026-11-22 01:52:28'),
(43, 70, 3, 0, '2026-11-22 02:04:58', '2026-11-22 03:53:39'),
(44, 70, 5, 1, '2026-11-22 02:04:58', '2026-11-22 03:53:39'),
(45, 70, 6, 2, '2026-11-22 02:04:58', '2026-11-22 03:53:39'),
(46, 71, 3, 0, '2026-11-22 02:06:21', '2026-11-22 02:06:21'),
(47, 71, 5, 1, '2026-11-22 02:06:21', '2026-11-22 02:06:21'),
(48, 71, 6, 2, '2026-11-22 02:06:21', '2026-11-22 02:06:21'),
(49, 72, 3, 0, '2026-11-22 02:07:30', '2026-11-22 02:07:30'),
(50, 72, 5, 1, '2026-11-22 02:07:30', '2026-11-22 02:07:30'),
(51, 72, 6, 2, '2026-11-22 02:07:30', '2026-11-22 02:07:30'),
(52, 73, 3, 0, '2026-11-22 02:08:39', '2026-11-22 02:08:39'),
(53, 73, 5, 1, '2026-11-22 02:08:39', '2026-11-22 02:08:39'),
(54, 73, 6, 2, '2026-11-22 02:08:39', '2026-11-22 02:08:39'),
(55, 74, 3, 0, '2026-11-22 02:10:22', '2026-11-22 02:10:53'),
(56, 74, 5, 1, '2026-11-22 02:10:22', '2026-11-22 02:10:53'),
(57, 74, 6, 2, '2026-11-22 02:10:22', '2026-11-22 02:10:53'),
(58, 75, 3, 0, '2026-11-22 02:12:14', '2026-11-22 02:12:14'),
(59, 75, 5, 1, '2026-11-22 02:12:14', '2026-11-22 02:12:14'),
(60, 75, 6, 2, '2026-11-22 02:12:14', '2026-11-22 02:12:14'),
(61, 76, 3, 0, '2026-11-22 02:14:37', '2026-11-22 02:14:37'),
(62, 76, 5, 1, '2026-11-22 02:14:37', '2026-11-22 02:14:37'),
(63, 76, 6, 2, '2026-11-22 02:14:37', '2026-11-22 02:14:37'),
(64, 77, 3, 0, '2026-11-22 02:16:22', '2026-11-22 02:16:22'),
(65, 77, 5, 1, '2026-11-22 02:16:22', '2026-11-22 02:16:22'),
(66, 77, 6, 2, '2026-11-22 02:16:22', '2026-11-22 02:16:22'),
(67, 78, 3, 0, '2026-11-22 02:18:32', '2026-11-22 02:18:32'),
(68, 78, 5, 1, '2026-11-22 02:18:32', '2026-11-22 02:18:32'),
(69, 78, 6, 2, '2026-11-22 02:18:32', '2026-11-22 02:18:32'),
(70, 79, 3, 0, '2026-11-22 02:40:22', '2026-11-22 02:40:22'),
(71, 79, 7, 1, '2026-11-22 02:40:22', '2026-11-22 02:40:22'),
(72, 80, 3, 0, '2026-11-22 02:41:38', '2026-11-22 02:41:38'),
(73, 81, 3, 0, '2026-11-22 02:42:57', '2026-11-22 02:42:57'),
(74, 82, 3, 0, '2026-11-22 02:43:59', '2026-11-22 02:43:59'),
(75, 83, 3, 0, '2026-11-22 02:45:10', '2026-11-22 02:45:10'),
(76, 84, 3, 0, '2026-11-22 02:46:54', '2026-11-22 02:46:54'),
(77, 85, 3, 0, '2026-11-22 02:47:44', '2026-11-22 02:47:44'),
(78, 86, 3, 0, '2026-11-22 02:48:48', '2026-11-22 02:48:48'),
(79, 87, 3, 0, '2026-11-22 02:49:31', '2026-11-22 02:49:31'),
(80, 88, 3, 0, '2026-11-23 01:48:13', '2026-11-23 01:48:13'),
(81, 88, 5, 1, '2026-11-23 01:48:13', '2026-11-23 01:48:13'),
(82, 89, 3, 0, '2026-11-23 01:49:43', '2026-11-23 01:49:43'),
(83, 89, 5, 1, '2026-11-23 01:49:43', '2026-11-23 01:49:43'),
(84, 90, 3, 0, '2026-11-23 01:51:13', '2026-11-23 01:51:13'),
(85, 91, 3, 0, '2026-11-23 01:53:23', '2026-11-23 01:54:06'),
(86, 92, 3, 0, '2026-11-23 01:57:01', '2026-11-23 01:57:01'),
(87, 93, 3, 0, '2026-11-23 02:00:00', '2026-11-23 02:00:00'),
(88, 95, 3, 0, '2026-11-23 02:06:03', '2026-11-23 02:06:03'),
(89, 96, 3, 0, '2026-11-23 02:07:34', '2026-11-23 02:07:34'),
(90, 97, 3, 0, '2026-11-23 02:09:24', '2026-11-23 02:09:24'),
(91, 98, 3, 0, '2026-11-23 02:24:40', '2026-11-23 02:24:40'),
(92, 99, 3, 0, '2026-11-23 02:26:08', '2026-11-23 02:26:08'),
(93, 100, 3, 0, '2026-11-23 02:28:08', '2026-11-23 02:28:08'),
(94, 101, 3, 0, '2026-11-23 02:31:52', '2026-11-23 02:31:52'),
(95, 102, 3, 0, '2026-11-23 02:33:58', '2026-11-23 02:33:58'),
(96, 103, 3, 0, '2026-11-23 02:36:02', '2026-11-23 02:36:02'),
(98, 105, 3, 0, '2026-11-23 02:38:24', '2026-11-23 02:38:24'),
(99, 106, 3, 0, '2026-11-23 02:39:30', '2026-11-23 02:39:30');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_02_01_000001_create_menu_items_table', 1),
(5, '2024_02_10_000001_create_categories_table', 1),
(6, '2024_02_10_000002_update_menu_items_category_to_foreign_key', 1),
(7, '2024_02_20_000001_create_menu_item_images_table', 1),
(8, '2024_02_20_000002_migrate_menu_items_image_to_gallery', 1),
(9, '2024_03_01_000002_create_deals_table', 1),
(10, '2024_03_01_000003_create_deal_items_table', 1),
(11, '2024_05_10_000001_create_category_media_table', 1),
(12, '2026_09_01_115108_add_parent_id_to_categories_table', 1),
(13, '2026_09_02_090315_create_menu_item_option_groups_table', 1),
(14, '2026_09_02_090316_create_menu_item_option_values_table', 1),
(15, '2026_09_02_094118_drop_menu_item_option_tables', 1),
(16, '2026_09_02_094126_create_shared_option_tables', 1),
(17, '2026_09_02_135751_create_sliders_table', 1),
(18, '2026_09_06_143248_cart_items', 1),
(19, '2026_09_07_102121_20250102000000addimagetodealstable', 1),
(20, '2026_09_08_104356_cart_items_add_bunddle', 1),
(21, '2026_09_10_114754_2026_09_06_150000_create_delivery_settings_table', 1),
(22, '2026_09_10_132836_2026_09_06_150000_add_fields_delivery_settings', 1),
(23, '2026_09_10_235325_create_customers_table', 1),
(24, '2026_09_11_092859_add_email_verified_at_to_customers_table', 1),
(25, '2026_09_11_112734_create_orders_table', 1),
(26, '2026_09_11_112735_create_order_items_table', 1),
(27, '2026_09_14_071120_settings', 1),
(28, '2026_09_14_072755_add_email_to_settings_table', 1),
(29, '2026_09_15_092255_add_pre_order_date_to_orders_table', 1),
(30, '2026_09_15_094228_add_fee_to_orders_table', 1),
(31, '2026_09_19_102207_payments', 1),
(32, '2026_09_20_000000_create_visits_table', 1),
(33, '2026_09_20_000100_add_ip_address_to_visits_table', 1),
(34, '2026_09_22_000000_create_booking_events_table', 1),
(35, '2026_09_22_000100_create_bookings_table', 1),
(36, '2026_09_22_003050_contact_messages', 1),
(37, '2026_09_22_005529_add_settings_table_fields', 1),
(38, '2026_09_23_000000_create_business_hours_table', 1),
(39, '2026_09_24_235332_create_personal_access_tokens_table', 1),
(40, '2026_10_11_173029_newsletter_subscribers', 2),
(41, '2026_11_16_235946_loyalty_coupons', 3),
(42, '2026_11_24_124336_2026_11_24_000000_add_minimum_order_to_settings_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `source` varchar(50) NOT NULL DEFAULT 'footer',
  `ip_address` varchar(45) DEFAULT NULL,
  `subscribed_at` timestamp NULL DEFAULT NULL,
  `unsubscribed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `option_groups`
--

CREATE TABLE `option_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `selection_type` varchar(255) NOT NULL DEFAULT 'single',
  `min_select` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `max_select` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `option_groups`
--

INSERT INTO `option_groups` (`id`, `name`, `selection_type`, `min_select`, `max_select`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Preparation Type', 'single', 0, 1, 1, '2026-11-20 10:19:29', '2026-11-20 10:19:29'),
(2, 'chose drink', 'single', 0, 1, 2, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(3, 'Extra Drink', 'multiple', 0, 1, 3, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(4, 'Lassi Favour', 'single', 0, 1, 4, '2026-11-21 08:03:52', '2026-11-21 08:03:52'),
(5, 'Spice Level', 'single', 1, 1, 5, '2026-11-22 01:37:33', '2026-11-22 01:37:33'),
(6, 'Extra Side', 'multiple', 0, 1, 6, '2026-11-22 01:37:33', '2026-11-22 01:37:33'),
(7, 'Select Naan Option', 'single', 1, 1, 7, '2026-11-22 02:40:21', '2026-11-22 02:40:21');

-- --------------------------------------------------------

--
-- Table structure for table `option_values`
--

CREATE TABLE `option_values` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `option_group_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price_delta` decimal(8,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `option_values`
--

INSERT INTO `option_values` (`id`, `option_group_id`, `name`, `price_delta`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Dry', 0.00, 0, '2026-11-20 10:19:29', '2026-11-20 10:19:29'),
(2, 1, 'Saucy', 0.00, 1, '2026-11-20 10:19:29', '2026-11-20 10:19:29'),
(3, 2, 'Pepsi', 0.00, 0, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(4, 2, 'Coke', 0.00, 1, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(5, 2, 'Diet Coke', 0.00, 2, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(6, 2, '7-Up', 0.00, 3, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(7, 2, 'Zero Coke', 0.00, 4, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(8, 2, 'R White Lemonade', 0.00, 5, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(9, 3, 'Pepsi', 1.95, 0, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(10, 3, 'Coke', 1.95, 1, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(11, 3, 'Diet Coke', 1.95, 2, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(12, 3, '7-Up', 1.95, 3, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(13, 3, 'Zero Coke', 1.95, 4, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(14, 3, 'R White Lemonade', 1.95, 5, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(15, 3, 'Water', 1.95, 6, '2026-11-21 07:57:22', '2026-11-21 07:57:22'),
(16, 4, 'Sweet', 0.00, 0, '2026-11-21 08:03:52', '2026-11-21 08:03:52'),
(17, 4, 'Salt', 0.00, 1, '2026-11-21 08:03:52', '2026-11-21 08:03:52'),
(18, 4, 'Mango', 0.00, 2, '2026-11-21 08:03:52', '2026-11-21 08:03:52'),
(19, 5, 'Mild', 0.00, 0, '2026-11-22 01:37:33', '2026-11-22 01:37:33'),
(20, 5, 'Medium', 0.00, 1, '2026-11-22 01:37:33', '2026-11-22 01:37:33'),
(21, 5, 'Hot', 0.00, 2, '2026-11-22 01:37:33', '2026-11-22 01:37:33'),
(22, 5, 'Extra Hot', 0.00, 3, '2026-11-22 01:37:33', '2026-11-22 01:37:33'),
(23, 6, 'Naan', 3.95, 0, '2026-11-22 01:37:33', '2026-11-22 01:37:33'),
(24, 6, 'Parotta', 4.50, 1, '2026-11-22 01:37:33', '2026-11-22 01:37:33'),
(25, 7, 'Plain', 0.00, 0, '2026-11-22 02:40:22', '2026-11-22 02:40:22'),
(26, 7, 'Garlic', 0.00, 1, '2026-11-22 02:40:22', '2026-11-22 02:40:22'),
(27, 7, 'Butter', 0.00, 2, '2026-11-22 02:40:22', '2026-11-22 02:40:22');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `apartment` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `order_type` varchar(255) NOT NULL,
  `is_pre_order` tinyint(1) NOT NULL DEFAULT 0,
  `pre_order_date` date DEFAULT NULL,
  `pre_order_time` time DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `subtotal` decimal(8,2) NOT NULL,
  `total` decimal(8,2) NOT NULL,
  `delivery_fee` decimal(8,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `counted_for_coupon_id` bigint(20) UNSIGNED DEFAULT NULL,
  `coupon_id` bigint(20) UNSIGNED DEFAULT NULL,
  `coupon_code` varchar(20) DEFAULT NULL,
  `discount_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `menu_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `unit_price` decimal(8,2) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `line_total` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `deal_id`, `name`, `options`, `unit_price`, `quantity`, `line_total`, `created_at`, `updated_at`) VALUES
(1, 1, 6, NULL, 'Jpingo\'s Special Chutney', NULL, 3.50, 1, 3.50, '2026-10-14 01:47:15', '2026-10-14 01:47:15'),
(2, 1, 7, NULL, 'Loaded Nachos To Share', NULL, 7.95, 1, 7.95, '2026-10-14 01:47:15', '2026-10-14 01:47:15'),
(3, 2, 7, NULL, 'Loaded Nachos To Share', NULL, 7.95, 2, 15.90, '2026-10-14 16:12:27', '2026-10-14 16:12:27'),
(4, 3, 36, NULL, 'Soft Drink Can', '[{\"name\":\"Pepsi\",\"price_delta\":0},{\"name\":\"Zero Coke\",\"price_delta\":1.95}]', 3.90, 1, 3.90, '2026-11-21 11:19:18', '2026-11-21 11:19:18'),
(5, 3, 38, NULL, 'Lassi', NULL, 3.95, 1, 3.95, '2026-11-21 11:19:18', '2026-11-21 11:19:18'),
(6, 3, 47, NULL, 'Falooda', NULL, 5.50, 1, 5.50, '2026-11-21 11:19:18', '2026-11-21 11:19:18'),
(7, 4, 59, NULL, 'Chicken Fried Rice', NULL, 8.95, 1, 8.95, '2026-11-22 03:24:38', '2026-11-22 03:24:38'),
(8, 4, 57, NULL, 'Pulao Rice', NULL, 4.50, 1, 4.50, '2026-11-22 03:24:38', '2026-11-22 03:24:38'),
(9, 4, 86, NULL, 'Mutton Kothu Parotta', '[{\"name\":\"R White Lemonade\",\"price_delta\":1.95}]', 13.90, 1, 13.90, '2026-11-22 03:24:38', '2026-11-22 03:24:38'),
(10, 5, 3, NULL, 'Dahi Vada', NULL, 5.50, 1, 5.50, '2026-11-24 12:47:22', '2026-11-24 12:47:22'),
(11, 6, 3, NULL, 'Dahi Vada', NULL, 5.50, 1, 5.50, '2026-11-24 12:50:42', '2026-11-24 12:50:42'),
(12, 6, 5, NULL, 'Paneer 65', NULL, 7.50, 1, 7.50, '2026-11-24 12:50:42', '2026-11-24 12:50:42'),
(13, 6, 21, NULL, 'Amritsari Fish Fry', NULL, 9.95, 1, 9.95, '2026-11-24 12:50:42', '2026-11-24 12:50:42'),
(14, 7, 5, NULL, 'Paneer 65', NULL, 7.50, 1, 7.50, '2026-11-25 14:16:59', '2026-11-25 14:16:59'),
(15, 7, 82, 1, 'Aloo Parotta', NULL, 4.95, 1, 4.95, '2026-11-25 14:16:59', '2026-11-25 14:16:59'),
(16, 7, 51, NULL, 'Chicken Biryani', NULL, 11.95, 1, 11.95, '2026-11-25 14:16:59', '2026-11-25 14:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `method` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `amount` decimal(8,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'GBP',
  `gateway` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `method`, `status`, `amount`, `currency`, `gateway`, `transaction_id`, `gateway_response`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'cod', 'paid', 16.93, 'GBP', NULL, NULL, NULL, '2026-10-14 01:49:01', '2026-10-14 01:47:15', '2026-10-14 01:49:01'),
(2, 2, 'cod', 'paid', 15.90, 'GBP', NULL, NULL, NULL, '2026-10-14 16:15:55', '2026-10-14 16:12:27', '2026-10-14 16:15:55'),
(3, 3, 'cod', 'pending', 17.27, 'GBP', NULL, NULL, NULL, NULL, '2026-11-21 11:19:18', '2026-11-21 11:19:18'),
(4, 4, 'cod', 'pending', 31.27, 'GBP', NULL, NULL, NULL, NULL, '2026-11-22 03:24:38', '2026-11-22 03:24:38'),
(5, 5, 'cod', 'pending', 9.42, 'GBP', NULL, NULL, NULL, NULL, '2026-11-24 12:47:22', '2026-11-24 12:47:22'),
(6, 6, 'cod', 'pending', 26.87, 'GBP', NULL, NULL, NULL, NULL, '2026-11-24 12:50:42', '2026-11-24 12:50:42'),
(7, 7, 'cod', 'pending', 28.32, 'GBP', NULL, NULL, NULL, NULL, '2026-11-25 14:16:59', '2026-11-25 14:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('tIKOPYE9PRnZorEbJwhlHnoQQMOOUM39nmYRzsjd', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMWZyN0xaOW10bHlTTjZ4WmJZN2h4NER4VVlsTTNHcVk3eU9ZT3hIWiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wZW5kaW5nLW9yZGVycy9wb2xsIjtzOjU6InJvdXRlIjtzOjI1OiJhZG1pbi5wZW5kaW5nLW9yZGVycy5wb2xsIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1795651078),
('UpLMS3Uakr5OCD8n4SePunojG2uM8bujrsaclzZa', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoib3RRMElwME5CMGtSRTZ0Y0NjdUl0QVlodnhybGdOTDlZbXl5VHpsSCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9lc2Nwb3MtdGVzdCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluIjt9fQ==', 1795612427),
('vRdsGgx8NjFYeR2GTknq1GDMbesJteAiXi5XGAkj', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidm9zZlE0SzEwakEwdWFGMTlodXZZazRqR0xBVmd1ejRYalgzOHY4SyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9lc2Nwb3MtdGVzdCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1795609144);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `banner_one` varchar(255) DEFAULT NULL,
  `banner_two` varchar(255) DEFAULT NULL,
  `vat_number` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `map_url` varchar(500) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `opening_hours` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`opening_hours`)),
  `show_delivery_checker` tinyint(1) NOT NULL DEFAULT 1,
  `minimum_order` decimal(8,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `logo`, `banner_one`, `banner_two`, `vat_number`, `address`, `map_url`, `email`, `phone`, `whatsapp`, `instagram`, `facebook`, `opening_hours`, `show_delivery_checker`, `minimum_order`, `created_at`, `updated_at`) VALUES
(1, 'Madras Cafe', 'settings/nODERn7z3j2kt0C7sF696sesE7DNVvxGzmevIjZI.png', 'settings/wkJCADDnnvkYXR86vc5UIZKBvhq4P195pwdOiFTY.jpg', 'settings/nC6c0t0L8WMItSzIdpE7jKC9nowbsvNUJGzG6TxF.png', NULL, '2-4 Radford Rd, Radford, Nottingham NG7 5FS', 'https://www.google.com/maps/embed?pb=!1m23!1m12!1m3!1d19224.61542997464!2d-1.1625814499999998!3d52.9650305!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m8!3e6!4m0!4m5!1s0x4879c130651edd39%3A0xc414e4626a576862!2sMadras%20Cafe%2C%202-4%20Radford%20Rd%2C%20Radford%2C%20Nottingham%20NG7%205FS!3m2!1d52.9628586!2d-1.1686969999999999!5e0!3m2!1sen!2suk!4v1795346064743!5m2!1sen!2suk', 'madrascafe.business@gmail.com', ' 07377 887720', ' 07377 887720', 'https://www.instagram.com/jpingosflame?stkn=ZTdsYTB1aTN0enIz&utm_source=qr', NULL, '{\"monday\":{\"open\":\"16:00\",\"close\":\"22:30\",\"closed\":false},\"tuesday\":{\"open\":\"16:00\",\"close\":\"22:30\",\"closed\":false},\"wednesday\":{\"open\":\"16:00\",\"close\":\"22:30\",\"closed\":false},\"thursday\":{\"open\":\"16:00\",\"close\":\"22:30\",\"closed\":false},\"friday\":{\"open\":\"16:00\",\"close\":\"22:30\",\"closed\":false},\"saturday\":{\"open\":\"13:00\",\"close\":\"22:30\",\"closed\":false},\"sunday\":{\"open\":\"13:00\",\"close\":\"22:30\",\"closed\":false}}', 0, 20.00, '2026-10-06 16:34:56', '2026-11-24 12:49:03');

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `button_text` varchar(255) DEFAULT 'Shop Now',
  `button_url` varchar(255) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sliders`
--

INSERT INTO `sliders` (`id`, `title`, `subtitle`, `image_path`, `button_text`, `button_url`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'briyani', NULL, 'sliders/G4Rpy4oH1dJSkpCGLUfWxnNquND3prJTSn6MqnN7.png', 'Shop Now', NULL, 2, 1, '2026-10-14 19:09:41', '2026-11-20 00:35:42'),
(2, 'rolls', NULL, 'sliders/BKuQtlVBtSXWF8Y51r69kWw97ofRXwF0YLzAEGa0.png', 'Shop Now', NULL, 3, 1, '2026-10-14 19:10:19', '2026-11-20 00:35:42'),
(4, 'idly', NULL, 'sliders/ZFnURtMk8bAEL92XMzjXNcznMKZdxXI4wa1DS7KL.png', 'Shop Now', NULL, 0, 1, '2026-10-14 19:11:41', '2026-11-20 00:35:42'),
(6, 'dosa', NULL, 'sliders/W8Plsa5eJ4vZQBt6U4PUHTVmduF4tY5bWYwwQmF6.png', 'Shop Now', NULL, 1, 1, '2026-11-20 00:35:35', '2026-11-20 00:35:42');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@example.com', '2026-10-06 16:33:06', '$2y$12$y8zqmiqJre6H8kVZe2m/MeqjNNb/jVZBi6TEkgtJHH0VNMqV9rYsa', NULL, '2026-10-06 16:33:06', '2026-10-06 16:33:06');

-- --------------------------------------------------------

--
-- Table structure for table `visits`
--

CREATE TABLE `visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visitor_uid` char(36) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `path` varchar(255) NOT NULL,
  `route_name` varchar(100) DEFAULT NULL,
  `referrer_host` varchar(190) DEFAULT NULL,
  `device_type` varchar(10) NOT NULL DEFAULT 'desktop',
  `browser` varchar(30) DEFAULT NULL,
  `country_code` varchar(2) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `region` varchar(120) DEFAULT NULL,
  `city` varchar(120) DEFAULT NULL,
  `area` varchar(120) DEFAULT NULL,
  `postal_code` varchar(16) DEFAULT NULL,
  `lat` decimal(8,5) DEFAULT NULL,
  `lng` decimal(8,5) DEFAULT NULL,
  `location_source` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visits`
--

INSERT INTO `visits` (`id`, `visitor_uid`, `ip_address`, `customer_id`, `path`, `route_name`, `referrer_host`, `device_type`, `browser`, `country_code`, `country`, `region`, `city`, `area`, `postal_code`, `lat`, `lng`, `location_source`, `created_at`) VALUES
(1, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-06 17:14:06'),
(2, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu/appetizers', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-06 17:15:14'),
(3, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 15:35:16'),
(4, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu/steak-grill', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 15:35:26'),
(5, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu/extra-dips', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 22:28:16'),
(6, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu/food-menu', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 22:28:24'),
(7, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/dish/chicken-butterfly-steak', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 22:28:36'),
(8, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu/calzone', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 22:29:19'),
(9, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/dish/the-special-one', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 22:29:33'),
(10, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/checkout', 'storefront.checkout', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 22:29:57'),
(11, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 22:30:16'),
(12, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu/sharing-platters', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 22:33:43'),
(13, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/dish/half-metre-platter-2-to-share', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 22:33:53'),
(14, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/dish/one-metre-long-patter-4-to-share', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 22:34:37'),
(15, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 22:34:55'),
(16, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:12:11'),
(17, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:15:14'),
(18, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:17:01'),
(19, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:20:24'),
(20, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:25:48'),
(21, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/dish/cacik', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:26:01'),
(22, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/dish/cacik', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:26:11'),
(23, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu/peri-peri', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:26:17'),
(24, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:32:17'),
(25, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'mobile', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:34:27'),
(26, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/dish/stone-baked-garlic-bread-with-cheese', 'storefront.dish', NULL, 'mobile', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:34:40'),
(27, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'mobile', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:35:25'),
(28, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/dish/stone-baked-garlic-bread-with-cheese', 'storefront.dish', NULL, 'mobile', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:35:38'),
(29, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu', 'storefront.menu.index', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:35:57'),
(30, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu/starters', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:36:17'),
(31, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/dish/stone-baked-garlic-bread-with-cheese', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:37:01'),
(32, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu/peri-peri', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:37:08'),
(33, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/dish/14-chicken', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 23:37:17'),
(34, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-10 00:05:20'),
(35, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/track-order', 'storefront.track-order', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-10 00:05:32'),
(36, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-10 00:05:38'),
(37, '38b7a933-a6a5-478e-b26c-15146e26b3b6', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-10 04:57:09'),
(38, '38b7a933-a6a5-478e-b26c-15146e26b3b6', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-10 04:58:40'),
(39, '38b7a933-a6a5-478e-b26c-15146e26b3b6', '127.0.0.1', NULL, '/deals', 'storefront.deals.index', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-10 04:58:45'),
(40, '38b7a933-a6a5-478e-b26c-15146e26b3b6', '127.0.0.1', NULL, '/track-order', 'storefront.track-order', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-10 04:58:50'),
(41, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-11 16:26:05'),
(42, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-11 17:01:21'),
(43, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-14 15:51:41'),
(44, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/cart', 'storefront.cart', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-14 16:11:12'),
(45, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/checkout', 'storefront.checkout', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-14 16:11:17'),
(46, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/order/ORD-RFNIY0V2/confirmation', 'storefront.order.confirmation', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-14 16:12:32'),
(47, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-14 16:14:01'),
(48, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 16:08:44'),
(49, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/deals', 'storefront.deals.index', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 16:09:56'),
(50, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/search', 'storefront.search', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 16:10:06'),
(51, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-20 16:52:28'),
(52, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu/jpingos-sizzlers', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-20 16:54:25'),
(53, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-20 16:54:33'),
(54, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu/burgers', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-20 16:57:38'),
(55, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu/calzone', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-20 16:57:50'),
(56, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu', 'storefront.menu.index', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-20 16:58:12'),
(57, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu/appetizers', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-23 08:47:05'),
(58, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/dish/cacik', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-23 08:47:25'),
(59, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/track-order', 'storefront.track-order', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-23 08:47:46'),
(60, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-23 08:48:34'),
(61, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/deals', 'storefront.deals.index', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-23 08:48:56'),
(62, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu/extra-dips', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-23 08:49:13'),
(63, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu/food-menu', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-23 08:49:21'),
(64, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/dish/whole-chicken', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-23 08:51:00'),
(65, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/cart', 'storefront.cart', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-23 08:51:29'),
(66, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/checkout', 'storefront.checkout', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-23 08:51:39'),
(67, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-23 08:52:40'),
(68, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-23 08:53:24'),
(69, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-26 19:59:16'),
(70, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/cart', 'storefront.cart', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-26 19:59:58'),
(71, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/checkout', 'storefront.checkout', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-26 20:00:09'),
(72, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-26 20:00:56'),
(73, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu/burgers', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-26 20:02:53'),
(74, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/cart', 'storefront.cart', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-26 20:03:17'),
(75, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/checkout', 'storefront.checkout', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-26 20:03:20'),
(76, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-26 20:03:45'),
(77, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu/wines-00-abv', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-26 20:07:20'),
(78, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-26 20:07:32'),
(79, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-27 13:53:24'),
(80, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:04:39'),
(81, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu/combos', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:08:40'),
(82, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/dish/lamb-steak', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:09:36'),
(83, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/dish/chicken-butterfly', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:11:17'),
(84, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:12:16'),
(85, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:15:38'),
(86, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/dish/cacik', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:19:51'),
(87, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu', 'storefront.menu.index', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:20:31'),
(88, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/dish/the-american-burger', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:21:33'),
(89, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu', 'storefront.menu.index', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:22:32'),
(90, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu', 'storefront.menu.index', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:22:56'),
(91, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/dish/bottle-torres-natuero-red', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:23:31'),
(92, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu/zero-beers-00-abv', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:23:43'),
(93, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu/mocktails', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:24:19'),
(94, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu/hot-chocolates', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:24:28'),
(95, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/dish/rich-hot-swiss-chocolate', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:24:40'),
(96, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/cart', 'storefront.cart', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:26:18'),
(97, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:26:31'),
(98, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu/tres-leches-milk-cake', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:26:44'),
(99, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/dish/ferrero-rocher-1', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:26:48'),
(100, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu/pizza-al-metro-signature-pizza', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:27:11'),
(101, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/dish/pizza-al-metro-signature-pizza', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:27:20'),
(102, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu/seafood', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:27:40'),
(103, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/cart', 'storefront.cart', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:28:07'),
(104, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/checkout', 'storefront.checkout', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:28:11'),
(105, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:28:56'),
(106, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/dish/mix-sizzler', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:29:21'),
(107, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu/pizza-al-metro-signature-pizza', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:30:27'),
(108, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu', 'storefront.menu.index', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:31:17'),
(109, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu', 'storefront.menu.index', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:33:23'),
(110, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu', 'storefront.menu.index', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:34:50'),
(111, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu', 'storefront.menu.index', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:37:38'),
(112, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:43:44'),
(113, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu', 'storefront.menu.index', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:43:48'),
(114, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:45:42'),
(115, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/menu/appetizers', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 10:45:47'),
(116, 'f43c7a8d-5a95-4ec8-ba7f-03d045638938', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-13 12:08:53'),
(117, 'f43c7a8d-5a95-4ec8-ba7f-03d045638938', '127.0.0.1', NULL, '/dish/cacik', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-13 12:09:34'),
(118, 'f43c7a8d-5a95-4ec8-ba7f-03d045638938', '127.0.0.1', NULL, '/menu/peri-peri', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-13 12:09:44'),
(119, 'f43c7a8d-5a95-4ec8-ba7f-03d045638938', '127.0.0.1', NULL, '/cart', 'storefront.cart', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-13 12:09:52'),
(120, 'f43c7a8d-5a95-4ec8-ba7f-03d045638938', '127.0.0.1', NULL, '/dish/12-chicken', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-13 12:09:57'),
(121, 'f43c7a8d-5a95-4ec8-ba7f-03d045638938', '127.0.0.1', NULL, '/menu/peri-peri', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-13 12:10:51'),
(122, 'f43c7a8d-5a95-4ec8-ba7f-03d045638938', '127.0.0.1', NULL, '/dish/12-chicken', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-13 12:10:59'),
(123, 'f43c7a8d-5a95-4ec8-ba7f-03d045638938', '127.0.0.1', NULL, '/track-order', 'storefront.track-order', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-13 12:12:00'),
(124, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-13 12:12:49'),
(125, '7cca7fde-8566-4bad-8d5a-bac17ff81c45', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-15 00:12:39'),
(126, '7cca7fde-8566-4bad-8d5a-bac17ff81c45', '127.0.0.1', NULL, '/menu/sides', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-15 00:13:05'),
(127, '7cca7fde-8566-4bad-8d5a-bac17ff81c45', '127.0.0.1', NULL, '/dish/classic-fries', 'storefront.dish', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-15 00:13:12'),
(128, '7cca7fde-8566-4bad-8d5a-bac17ff81c45', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-15 00:28:55'),
(129, '7cca7fde-8566-4bad-8d5a-bac17ff81c45', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-15 00:32:24'),
(130, '7cca7fde-8566-4bad-8d5a-bac17ff81c45', '127.0.0.1', NULL, '/menu', 'storefront.menu.index', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-15 00:44:40'),
(131, '7cca7fde-8566-4bad-8d5a-bac17ff81c45', '127.0.0.1', NULL, '/menu/food-menu', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-15 00:44:52'),
(132, '7cca7fde-8566-4bad-8d5a-bac17ff81c45', '127.0.0.1', NULL, '/menu/appetizers', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-15 00:45:01'),
(133, '7cca7fde-8566-4bad-8d5a-bac17ff81c45', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-15 00:45:20'),
(134, 'd98d8403-89a4-4a23-962f-b9aeb1508711', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-20 00:29:08'),
(135, 'd98d8403-89a4-4a23-962f-b9aeb1508711', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-20 00:29:24'),
(136, 'd98d8403-89a4-4a23-962f-b9aeb1508711', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-20 00:30:41'),
(137, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-24 09:35:15'),
(138, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/menu/dosa', 'storefront.category', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-24 09:35:26'),
(139, 'f525b80d-d5fd-418f-9c5c-172ea9b3fbdc', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-24 09:35:37'),
(140, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-25 12:19:23'),
(141, '061edcd9-bc51-4dc3-b674-395840a54a5a', '127.0.0.1', NULL, '/', 'storefront.home', NULL, 'desktop', 'Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-25 12:50:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bookings_booking_number_unique` (`booking_number`),
  ADD UNIQUE KEY `bookings_guestplan_booking_id_unique` (`guestplan_booking_id`),
  ADD UNIQUE KEY `bookings_idempotency_key_unique` (`idempotency_key`),
  ADD KEY `bookings_booking_event_id_foreign` (`booking_event_id`),
  ADD KEY `bookings_customer_id_foreign` (`customer_id`),
  ADD KEY `bookings_booking_date_booking_time_index` (`booking_date`,`booking_time`),
  ADD KEY `bookings_status_index` (`status`),
  ADD KEY `bookings_customer_email_index` (`customer_email`),
  ADD KEY `bookings_customer_phone_index` (`customer_phone`);

--
-- Indexes for table `booking_events`
--
ALTER TABLE `booking_events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_events_slug_unique` (`slug`),
  ADD KEY `booking_events_is_active_starts_on_ends_on_index` (`is_active`,`starts_on`,`ends_on`);

--
-- Indexes for table `business_hours`
--
ALTER TABLE `business_hours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `business_hours_day_of_week_unique` (`day_of_week`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cart_items_row_id_unique` (`row_id`),
  ADD KEY `cart_items_user_id_foreign` (`user_id`),
  ADD KEY `cart_items_menu_item_id_foreign` (`menu_item_id`),
  ADD KEY `cart_items_session_id_index` (`session_id`),
  ADD KEY `cart_items_deal_id_foreign` (`deal_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `category_media`
--
ALTER TABLE `category_media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_media_category_id_unique` (`category_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`);

--
-- Indexes for table `deals`
--
ALTER TABLE `deals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `deals_slug_unique` (`slug`),
  ADD UNIQUE KEY `deals_promo_code_unique` (`promo_code`);

--
-- Indexes for table `deal_items`
--
ALTER TABLE `deal_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deal_items_deal_id_foreign` (`deal_id`),
  ADD KEY `deal_items_menu_item_id_foreign` (`menu_item_id`);

--
-- Indexes for table `delivery_settings`
--
ALTER TABLE `delivery_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loyalty_coupons`
--
ALTER TABLE `loyalty_coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loyalty_coupons_code_unique` (`code`),
  ADD KEY `loyalty_coupons_email_index` (`email`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menu_items_slug_unique` (`slug`),
  ADD KEY `menu_items_category_id_foreign` (`category_id`);

--
-- Indexes for table `menu_item_images`
--
ALTER TABLE `menu_item_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_item_images_menu_item_id_foreign` (`menu_item_id`);

--
-- Indexes for table `menu_item_option_group`
--
ALTER TABLE `menu_item_option_group`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menu_item_option_group_menu_item_id_option_group_id_unique` (`menu_item_id`,`option_group_id`),
  ADD KEY `menu_item_option_group_option_group_id_foreign` (`option_group_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `newsletter_subscribers_email_unique` (`email`),
  ADD KEY `newsletter_subscribers_is_active_index` (`is_active`);

--
-- Indexes for table `option_groups`
--
ALTER TABLE `option_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `option_groups_name_unique` (`name`);

--
-- Indexes for table `option_values`
--
ALTER TABLE `option_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `option_values_option_group_id_foreign` (`option_group_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`),
  ADD KEY `orders_counted_for_coupon_id_index` (`counted_for_coupon_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_menu_item_id_foreign` (`menu_item_id`),
  ADD KEY `order_items_deal_id_foreign` (`deal_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  ADD KEY `payments_order_id_foreign` (`order_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `visits`
--
ALTER TABLE `visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visits_created_at_index` (`created_at`),
  ADD KEY `visits_visitor_uid_created_at_index` (`visitor_uid`,`created_at`),
  ADD KEY `visits_country_code_index` (`country_code`),
  ADD KEY `visits_city_index` (`city`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `booking_events`
--
ALTER TABLE `booking_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `business_hours`
--
ALTER TABLE `business_hours`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `category_media`
--
ALTER TABLE `category_media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `deals`
--
ALTER TABLE `deals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `deal_items`
--
ALTER TABLE `deal_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `delivery_settings`
--
ALTER TABLE `delivery_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loyalty_coupons`
--
ALTER TABLE `loyalty_coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `menu_item_images`
--
ALTER TABLE `menu_item_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `menu_item_option_group`
--
ALTER TABLE `menu_item_option_group`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `option_groups`
--
ALTER TABLE `option_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `option_values`
--
ALTER TABLE `option_values`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `visits`
--
ALTER TABLE `visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_booking_event_id_foreign` FOREIGN KEY (`booking_event_id`) REFERENCES `booking_events` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_deal_id_foreign` FOREIGN KEY (`deal_id`) REFERENCES `deals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cart_items_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `category_media`
--
ALTER TABLE `category_media`
  ADD CONSTRAINT `category_media_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `deal_items`
--
ALTER TABLE `deal_items`
  ADD CONSTRAINT `deal_items_deal_id_foreign` FOREIGN KEY (`deal_id`) REFERENCES `deals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deal_items_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `menu_item_images`
--
ALTER TABLE `menu_item_images`
  ADD CONSTRAINT `menu_item_images_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_item_option_group`
--
ALTER TABLE `menu_item_option_group`
  ADD CONSTRAINT `menu_item_option_group_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_item_option_group_option_group_id_foreign` FOREIGN KEY (`option_group_id`) REFERENCES `option_groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `option_values`
--
ALTER TABLE `option_values`
  ADD CONSTRAINT `option_values_option_group_id_foreign` FOREIGN KEY (`option_group_id`) REFERENCES `option_groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_deal_id_foreign` FOREIGN KEY (`deal_id`) REFERENCES `deals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_items_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
