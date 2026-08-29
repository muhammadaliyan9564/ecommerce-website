-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 29, 2026 at 05:16 PM
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
-- Database: `ecommerce_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_percent` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `discount_percent`) VALUES
(1, 'SAVE10', 10),
(2, 'PAKISTAN20', 20);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_address` text NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending',
  `payment_method` varchar(50) DEFAULT 'Cash on Delivery',
  `discount_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `customer_email`, `customer_address`, `total_price`, `order_date`, `status`, `payment_method`, `discount_amount`) VALUES
(1, 'Muhammad Aliyan', 'muhammadaliyansweetboy@gmail.com', 'House No 8 Street No 4 landhi 4 ,', 75.49, '2026-08-24 15:40:19', 'Pending', 'Cash on Delivery', 0.00),
(2, 'Muhammad Aliyan', 'muhammadaliyansweetboy@gmail.com', 'House No 8 Street No 4 landhi 4 ,', 0.00, '2026-08-25 13:01:20', 'Pending', 'EasyPaisa', 0.00),
(3, 'Muhammad Aliyan', 'muhammadaliyansweetboy@gmail.com', 'House No 8 Street No 4 landhi 4 ,', 0.00, '2026-08-25 13:01:48', 'Delivered', 'JazzCash', 0.00),
(4, 'Muhammad Aliyan', 'muhammadaliyansweetboy@gmail.com', 'House No 8 Street No 4 landhi 4 ,', 680000.00, '2026-08-25 13:03:09', 'Pending', 'Cash on Delivery', 0.00),
(5, 'Ali', 'Ali@gmail.com', 'Shah re Faisal', 3200.00, '2026-08-25 13:07:37', 'Pending', 'Cash on Delivery', 0.00),
(6, 'rehan', 'rehan@gmail.com', 'nazmabad', 68500.00, '2026-08-25 15:30:29', 'Pending', 'Cash on Delivery', 0.00),
(7, 'Bushra ', 'bushra@gmail.com', 'Usman Islamic School Near landhi', 6400.00, '2026-08-25 15:34:13', 'Pending', 'EasyPaisa', 0.00),
(8, 'neha', 'neha@gmail.com', 'gulshion', 6400.00, '2026-08-25 15:52:16', 'Pending', 'Cash on Delivery', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT 'default.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category` varchar(100) DEFAULT 'Electronics',
  `stock_quantity` int(11) DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `image`, `created_at`, `category`, `stock_quantity`) VALUES
(1, 'Audionic Airbuds 550 Wireless', 4499.00, 'Environmental Noise Cancellation with 26 Hours total playtime.', 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=600&auto=format&fit=crop&q=80', '2026-08-25 10:29:31', 'Audio', 10),
(2, 'Sony WH-1000XM5 ANC Headphones', 89999.00, 'Industry leading active noise cancellation with 30h battery.', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop&q=80', '2026-08-25 10:29:31', 'Audio', 10),
(3, 'JBL Flip 6 Portable Speaker', 24500.00, '2-way speaker system delivering loud, crystal clear sound.', 'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=600&auto=format&fit=crop&q=80', '2026-08-25 10:29:31', 'Audio', 10),
(4, 'Apple Watch Series 9 GPS', 115000.00, 'Advanced health sensors and retina always-on display.', 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80', '2026-08-25 10:29:31', 'Wearables', 10),
(5, 'Haylou Solar Plus Smart Watch', 9499.00, 'AMOLED display, Bluetooth calling, and IP68 water resistance.', 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?w=600&auto=format&fit=crop&q=80', '2026-08-25 10:29:31', 'Wearables', 10),
(6, 'Logitech G502 Hero Gaming Mouse', 12499.00, '25K HERO high precision sensor with adjustable RGB lighting.', 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=600&auto=format&fit=crop&q=80', '2026-08-25 10:29:31', 'Gaming', 10),
(7, 'Faster RGB Mechanical Keyboard', 8999.00, 'Blue mechanical switches with customizable RGB LED lighting.', 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=600&auto=format&fit=crop&q=80', '2026-08-25 10:29:31', 'Gaming', 10),
(8, 'PS5 DualSense Wireless Controller', 21999.00, 'Haptic feedback and dynamic adaptive triggers for gaming.', 'https://images.unsplash.com/photo-1600080972464-8e5f35f63d08?w=600&auto=format&fit=crop&q=80', '2026-08-25 10:29:31', 'Gaming', 10),
(9, 'Ultra HD 4K Action Camera Waterproof', 18500.00, 'Dual screen action camera with EIS image stabilization.', 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=600&auto=format&fit=crop&q=80', '2026-08-25 10:29:31', 'Cameras', 10),
(10, 'Canon EF 50mm f/1.8 STM Lens', 34000.00, 'Compact portrait lens for crisp photos with bokeh effect.', 'https://images.unsplash.com/photo-1617005082133-548c4dd27f35?w=600&auto=format&fit=crop&q=80', '2026-08-25 10:29:31', 'Cameras', 10),
(11, 'Ronin 20000mAh 22.5W Fast Power Bank', 6299.00, 'Super fast charging powerbank with digital LED battery display.', 'https://images.unsplash.com/photo-1609592424009-5a1334f40f09?w=600&auto=format&fit=crop&q=80', '2026-08-25 10:29:31', 'Accessories', 10),
(12, 'Samsung 15W Fast Wireless Charger', 4800.00, 'Multi-device fast wireless charging dock with LED indication.', 'https://images.unsplash.com/photo-1622445268465-840246e904b6?w=600&auto=format&fit=crop&q=80', '2026-08-25 10:29:31', 'Accessories', 10),
(13, 'Leather Laptop Backpack Waterproof', 5500.00, 'Premium ergonomic travel bag with dedicated USB charge port.', 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=600&auto=format&fit=crop&q=80', '2026-08-25 10:29:31', 'Accessories', 10),
(14, 'Google Pixel 8 Pro 256GB', 215000.00, 'Advanced AI camera phone with OLED LTPO 120Hz display.', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=600&auto=format&fit=crop&q=80', '2026-08-25 10:29:31', 'Mobiles', 10),
(15, 'MacBook Pro 14 M3 Chip', 465000.00, 'Supercharged performance laptop for developers and creators.', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=600&auto=format&fit=crop&q=80', '2026-08-25 10:29:31', 'Laptops', 10),
(16, 'Smart Eye-Protection LED Desk Lamp', 3200.00, 'Touch control dimmable desk lamp with timer and warm light.', 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=600&auto=format&fit=crop&q=80', '2026-08-25 10:29:31', 'HomeTech', 10),
(17, 'mobile vivo', 22000.00, 'New Mobile ', 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8bW9iaWxlfGVufDB8fDB8fHww', '2026-08-25 13:05:26', 'Mobiles', 10),
(18, 'Spiderman Sticker', 200.00, 'Spiderman Sticker', 'uploads/1787674033_vicamtrong-spider-man-4647893_1920.jpg', '2026-08-25 15:56:59', 'Accessories', -1);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
