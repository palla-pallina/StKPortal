-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 11, 2025 at 08:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `user`
--

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `folder_id`, `user_id`, `filename`, `filepath`, `uploaded_at`, `modified`) VALUES
(1, 1, 1, 'WIN_20250321_08_25_18_Pro.jpg', 'uploads/vic/WIN_20250321_08_25_18_Pro.jpg', '2025-07-31 08:40:09', '2025-07-31 09:15:47'),
(2, 0, 1, '2.jpg', 'uploads/1753951824_2.jpg', '2025-07-31 08:50:24', '2025-07-31 09:15:47'),
(3, 0, 1, 'WIN_20250404_08_26_24_Pro.jpg', 'uploads/1753952131_WIN_20250404_08_26_24_Pro.jpg', '2025-07-31 08:55:31', '2025-07-31 09:15:47'),
(4, 2, 1, '2.jpg', 'uploads/1753952142_2.jpg', '2025-07-31 08:55:42', '2025-07-31 09:15:47'),
(5, 0, 1, 'WIN_20250404_08_26_24_Pro.jpg', 'uploads/1753952903_WIN_20250404_08_26_24_Pro.jpg', '2025-07-31 09:08:23', '2025-07-31 09:15:47'),
(6, 4, 1, '4.jpg', 'uploads/1753954407_4.jpg', '2025-07-31 09:33:27', '2025-07-31 09:33:27'),
(7, 6, 1, 'WIN_20250404_08_40_32_Pro (3).jpg', 'uploads/1754017190_WIN_20250404_08_40_32_Pro (3).jpg', '2025-08-01 02:59:50', '2025-08-01 02:59:50');

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

CREATE TABLE `folders` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `folders`
--

INSERT INTO `folders` (`id`, `name`, `user_id`, `parent_id`, `created_at`) VALUES
(1, 'vic', 1, 0, '2025-07-31 08:39:54'),
(2, 'bic', 1, 0, '2025-07-31 08:55:23'),
(3, 'a', 1, 2, '2025-07-31 09:22:47'),
(4, 'paw', 1, 0, '2025-07-31 09:33:11'),
(5, 'paw2', 1, 4, '2025-07-31 09:33:37'),
(6, 'aw', 1, 0, '2025-08-01 02:59:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `givenname` varchar(50) DEFAULT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `user_type` varchar(100) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `givenname`, `middlename`, `lastname`, `suffix`, `email`, `username`, `contact`, `birthday`, `password`, `user_type`) VALUES
(3, 'Victor4', 'Z', 'Garlitos', 'q', 'garlitosvz@students.nu-fairview.edu.ph', 'victorgarlitos01@gmail.com', '922111', '2025-08-05', '$2y$10$w1MEfPWDrH42zlV.HZYFMuXR33NmaKXjxfN6RnqpeSP5ZNIrOou3S', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
