-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2026 at 01:46 PM
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
-- Database: `restaurant_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`menu_id`, `item_name`, `description`, `price`, `image`, `category`, `created_at`) VALUES
(1, 'Grilled Chicken', 'Delicious grilled chicken with herbs and spices', 249.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcToMozkkZv6pQonP3nn1BlXT2uLTWJxeshxYA&s', 'Non-Veg', '2026-02-11 16:38:40'),
(2, 'Chicken Burger', 'Healthy and tasty chicken burger', 129.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ4nt_pBpOqDOjjXCKMlssx5HoDbpNRb2kK-g&s', 'Fast Food', '2026-02-11 16:38:40'),
(3, 'Chicken Biriyani', 'Aromatic chicken biriyani with basmati rice', 150.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQHt-7Z37ZHKCQkhpXTG8Dbl8iaetNxZTO0Hg&s', 'Non-Veg', '2026-02-11 16:38:40'),
(4, 'Chicken Pizza', 'Classic chicken pizza with fresh toppings', 299.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSbbuD81lvZieLqbnB2zR6ZaIVDD_jhmIGe_Q&s', 'Fast Food', '2026-02-11 16:38:40'),
(5, 'Chicken Noodles', 'Chicken noodles with vegetables', 129.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQGDZJTQGKJUPqF2k8YM8eH4aLbYDZ3UBknDg&s', 'Chinese', '2026-02-11 16:38:40'),
(6, 'Kadhai Chicken', 'Spicy kadhai chicken', 200.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzoFG5J--pWWzBuQx1TVbcyNTC9zyyG7Dxyw&s', 'Non-Veg', '2026-02-11 16:38:40'),
(7, 'Egg Biriyani', 'Egg biriyani with spices', 189.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT7mjITLI0dUqlLwHkOYmXM7kKNFLKhlkpeWg&s', 'Non-Veg', '2026-02-11 16:38:40'),
(8, 'Mutton Biriyani', 'Rich mutton biriyani', 259.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQsnOxYBAopwRpwqeoMyI_rUWWeL4KZ9EbQkA&s', 'Non-Veg', '2026-02-11 16:38:40'),
(9, 'Prawns Biriyani', 'Prawns biriyani with herbs', 259.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSwDAMoGOVm1n0n4tfMtLwgMZTDbFKRrcZP1Q&s', 'Seafood', '2026-02-11 16:38:40'),
(10, 'Veg Biriyani', 'Mixed vegetable biriyani', 190.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRI9Tw6cZWpDVACgNwAaqls8uxiPMPrUimMTA&s', 'Veg', '2026-02-11 16:38:40'),
(11, 'Veg Fry Rice', 'Vegetable fried rice', 160.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzxEHCt9OV8UnZ5F74npsNHaWACrPmiU-F1w&s', 'Chinese', '2026-02-11 16:38:40'),
(12, 'Veg Burger', 'Fresh veg burger', 190.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS_FuyUbWUjn6u0FXPnF8eu1BSwtdV4Wkq3Nw&s', 'Fast Food', '2026-02-11 16:38:40'),
(13, 'Veg Sandwich', 'Healthy veg sandwich', 160.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSNG6YHlYs-4zSLpSCWlfWbRNLVBEPemX7PTw&s', 'Fast Food', '2026-02-11 16:38:40'),
(14, 'Normal Tea', 'Regular tea', 12.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRLNPV0B7jETaPiLW1SioT9puRpFsT8mOWBMQ&s', 'Beverage', '2026-02-11 16:38:40'),
(15, 'Elaichi Tea', 'Cardamom flavored tea', 20.00, 'https://5.imimg.com/data5/SELLER/Default/2022/7/AX/FO/LK/764091/tea-with-elaichi-1000x1000-500x500.jpeg', 'Beverage', '2026-02-11 16:38:40'),
(16, 'Masala Tea', 'Spiced tea', 20.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTNz8svxz897FnF-0oqyIHQwbB8VUS0-doVIQ&s', 'Beverage', '2026-02-11 16:38:40'),
(17, 'Black Coffee', 'Strong black coffee', 12.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTpG1tebULvsJ5dnqErM_9Sc8aZxqocasKmkA&s', 'Beverage', '2026-02-11 16:38:40'),
(18, 'Cold Coffee', 'Chilled coffee', 30.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTSVYAb9l2oOQ4iRwqARLLwHoTuyZgY9RKT0Q&s', 'Beverage', '2026-02-11 16:38:40'),
(19, 'Cappuccino', 'Creamy cappuccino', 159.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRjLFKW27XvOrt8xalDBKtctItX1zznfl_BWg&s', 'Beverage', '2026-02-11 16:38:40'),
(20, 'Cafe Latte', 'Smooth cafe latte', 169.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQNABZfMO7-P96K6PBZ58ZSgrrL2guP-8HlCw&s', 'Beverage', '2026-02-11 16:38:40'),
(21, 'Cafe Mocha', 'Chocolate flavored mocha', 189.00, 'https://www.deliciousmeetshealthy.com/wp-content/uploads/2021/03/Mocha-Coffee-6.jpg', 'Beverage', '2026-02-11 16:38:40');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('Pending','Preparing','Completed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_name`, `phone`, `order_date`, `total_amount`, `status`) VALUES
(3, 'user', '0000000000', '2026-03-27 15:53:10', 925.00, 'Pending'),
(4, 'user', 'Guest', '2026-03-29 10:52:11', 189.00, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `menu_id`, `quantity`, `price`) VALUES
(8, 3, 1, 1, 249.00),
(9, 3, 2, 2, 129.00),
(10, 3, 7, 1, 189.00),
(11, 3, 16, 3, 20.00),
(12, 3, 20, 1, 169.00),
(13, 4, 21, 1, 189.00);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `people` int(11) NOT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `special_requests` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Booked','Cancelled','Completed') DEFAULT 'Booked'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'admin', 'admin@gmail.com', '$2y$10$T8vFEN3HekOeMwRxv9Ygwe7OkNmNNL86w69.TAan/Oq9Newqdb66S', 'admin', '2026-02-12 11:00:53'),
(3, 'user', 'user@gmail.com', '$2y$10$ADfKWA.qTFGT0Wgnxp6BEe605JaFBv.NBFTceGzVjUmsNYLEG.ji2', 'customer', '2026-02-12 11:02:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
