-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2022 at 12:51 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `password`) VALUES
(1, 'admin', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `quantity` int(10) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `message` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- SQL to create a new table for messages
--

-- Ensure proper auto-increment handling and avoid primary key duplication issues
-- Make sure 'messages' table has a unique primary key with 'AUTO_INCREMENT'

-- Step 1: Ensure 'messages' table exists with correct structure
-- Ensure the 'messages' table has the correct structure

CREATE TABLE IF NOT EXISTS messages (
  id INT AUTO_INCREMENT PRIMARY KEY,  -- Primary key and auto-increment
  user_id INT NOT NULL,
  order_id INT,  -- Optional
  name VARCHAR(255) NOT NULL,
  status VARCHAR(50),  -- Optional
  message TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- Default creation time
);

START TRANSACTION;

-- Add 'status' and 'order_id' columns if they don't exist
ALTER TABLE messages 
ADD COLUMN IF NOT EXISTS `status` VARCHAR(50);  -- Add 'status' if it doesn't exist

SELECT id, COUNT(*) AS count FROM messages GROUP BY id HAVING count > 1;  -- Find duplicate IDs

ALTER TABLE messages 
ADD COLUMN IF NOT EXISTS `order_id` INT;  -- Add 'order_id' if it doesn't exist

-- To correct any issues with 'AUTO_INCREMENT' and prevent conflicts
-- Step 1: Get the current maximum 'id' from 'messages'
SELECT IFNULL(MAX(id), 0) AS max_id FROM messages;  -- Returns the maximum ID value

-- Step 2: Set 'AUTO_INCREMENT' to one higher than the current maximum
-- Replace the following with your calculated 'AUTO_INCREMENT' value
-- Example: If max_id is 10, set AUTO_INCREMENT to 11
ALTER TABLE messages AUTO_INCREMENT = 11;

COMMIT;


-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `number` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `total_products` varchar(1000) NOT NULL,
  `total_price` int(100) NOT NULL,
  `placed_on` date NOT NULL DEFAULT current_timestamp(),
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  `tracking_number` varchar(50) -- Comma was missing before this line
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `details` varchar(500) NOT NULL,
  `price` int(10) NOT NULL,
  `image_01` varchar(100) NOT NULL,
  `image_02` varchar(100) NOT NULL,
  `image_03` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Adding new columns to `users`
--

-- Modifying `users` table with corrected column names
ALTER TABLE `users`
  ADD COLUMN `number` VARCHAR(15) AFTER `password`,
  ADD COLUMN `address_line_01` VARCHAR(100) AFTER `number`,
  ADD COLUMN `address_line_02` VARCHAR(100) AFTER `address_line_01`,
  ADD COLUMN `city` VARCHAR(100) AFTER `address_line_02`,
  ADD COLUMN `state` VARCHAR(100) AFTER `city`,
  ADD COLUMN `country` VARCHAR(100) AFTER `state`;



-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(100) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
-- Adding primary keys and ensuring `AUTO_INCREMENT` for `id` columns
ALTER TABLE `admins`
  MODIFY `id` INT(100) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`id`);

-- Indexes for table `cart`
ALTER TABLE `cart`
  MODIFY `id` INT(100) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`id`);

-- Indexes for table `messages`
ALTER TABLE `messages`
  MODIFY `id` INT(100) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`id`);

-- Indexes for table `orders`
ALTER TABLE `orders`
  MODIFY `id` INT(100) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`id`);

-- Indexes for table `products`
ALTER TABLE `products`
  MODIFY `id` INT(100) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`id`);

-- Indexes for table `users`
ALTER TABLE `users`
  MODIFY `id` INT(100) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`id`);

-- Indexes for table `wishlist`
ALTER TABLE `wishlist`
  MODIFY `id` INT(100) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`id`);

