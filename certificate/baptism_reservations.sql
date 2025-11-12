-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2025 at 07:25 AM
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
-- Database: `test_tree_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `baptism_reservations`
--

CREATE TABLE `baptism_reservations` (
  `id` int(11) NOT NULL,
  `child_name` varchar(255) NOT NULL,
  `birth_date` date NOT NULL,
  `birth_place` varchar(255) NOT NULL,
  `father_name` varchar(255) NOT NULL,
  `mother_name` varchar(255) NOT NULL,
  `preferred_date` date NOT NULL,
  `reservation_type` varchar(100) NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notif` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `baptism_reservations`
--

INSERT INTO `baptism_reservations` (`id`, `child_name`, `birth_date`, `birth_place`, `father_name`, `mother_name`, `preferred_date`, `reservation_type`, `status`, `created_at`, `notif`, `user_id`) VALUES
(14, 'Victor p Pig d', '2025-11-03', 'caloocan', 'Victor Z Garlitos d', 'Victor Z Garlitos d', '2025-11-03', 'Baptism', 'Accepted', '2025-11-10 12:58:03', 1, 0),
(20, 'Victor Z Garlitos ', '2025-11-03', 'caloocan', 'PAPA p PIG d', 'MAMA p PIG ', '2025-11-20', 'Baptism', 'Pending', '2025-11-10 14:13:51', 0, 0),
(21, 'Victor Z Garlitos d', '2025-11-03', 'caloocan', 'PAPA p PIG d', 'MAMA p PIG ', '2025-11-20', 'Baptism', 'Pending', '2025-11-10 14:17:58', 0, 0),
(30, 'Victor Z Garlitos d', '2025-11-03', 'caloocan', 'PAPA p PIG d', 'MAMA p PIG ', '2025-11-20', 'Baptism', 'Pending', '2025-11-10 14:27:58', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `baptism_reservations`
--
ALTER TABLE `baptism_reservations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `baptism_reservations`
--
ALTER TABLE `baptism_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
