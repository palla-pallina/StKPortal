-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 29, 2025 at 06:19 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `ann_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `image_path` varchar(300) DEFAULT NULL,
  `content` text NOT NULL,
  `date_posted` timestamp NOT NULL DEFAULT current_timestamp(),
  `posted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `baptisms`
--

CREATE TABLE `baptisms` (
  `id` int(11) NOT NULL,
  `child` varchar(255) DEFAULT NULL,
  `mother` varchar(255) DEFAULT NULL,
  `father` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `donate_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `donor_fullname` varchar(300) DEFAULT NULL,
  `donor_email` varchar(100) DEFAULT NULL,
  `donor_contact_no` varchar(15) DEFAULT NULL,
  `donation_categ` enum('Cash','Clothes','Goods','Specified') NOT NULL,
  `donation_specified` varchar(100) DEFAULT NULL,
  `donation_type` enum('Parish Donation','Tithes','Charity') NOT NULL,
  `mode_of_payment` enum('Onsite','Gcash','Bank Transfer') NOT NULL,
  `proof_of_donation` varchar(300) DEFAULT NULL,
  `donation_amount` decimal(10,2) DEFAULT NULL,
  `donation_status` enum('pending','verified','unverified') DEFAULT 'pending',
  `date_posted` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `funerals`
--

CREATE TABLE `funerals` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `cause_of_death` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `presider` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `funerals`
--

INSERT INTO `funerals` (`id`, `name`, `cause_of_death`, `contact_person`, `phone`, `address`, `date`, `time`, `presider`) VALUES
(1, 'blankink', 'PBL', 'pau', '9621974433', 'secrettt', '2025-09-29', '22:00:00', 'secrettt');

-- --------------------------------------------------------

--
-- Table structure for table `prayer_requests`
--

CREATE TABLE `prayer_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `intention` text DEFAULT NULL,
  `visibility` varchar(20) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_type` enum('User','Admin') NOT NULL DEFAULT 'User',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `givenname` varchar(50) DEFAULT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `full_name` varchar(300) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `photo` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_type`, `created_at`, `username`, `password`, `givenname`, `middlename`, `lastname`, `suffix`, `full_name`, `email`, `contact`, `birthday`, `photo`) VALUES
(33, 'User', '2025-09-29 04:13:53', 'vickyyy', '$2y$10$/IOto7CbAxJ9qJ5r1h7DFuyTnLAKbJuS5vqlVuo8VcxM/TQePlkQK', 'Victor', 'Zed', 'Garlitos', '', NULL, 'vic@dot.com', '09182374466', '2001-09-11', NULL),
(34, 'User', '2025-09-29 04:15:19', 'uppau', '$2y$10$fWALUrnEdJ4NwotrVlHc4ueMW8ol7ExtizX1bAQkZYYkEhX6iWDdO', 'Jean', 'Gar', 'Pasion', '', NULL, 'paujp@jott.com', '09182732937', '2001-09-02', NULL),
(35, 'User', '2025-09-29 04:17:55', 'erihy', '$2y$10$2ampgviBZ3ympgQMC9FXQ.XbokqXka/k..auOhem83KztUW8Eomii', 'Ericka', '', 'Fenis', '', NULL, 'erif@gmail.com', '09182374921', '2001-09-11', NULL),
(36, 'User', '2025-09-29 04:18:45', 'wethebestcalories', '$2y$10$/aS1mAUXABs7DoMintAHdeHILWIOXbot8TxnFnd07czEq0lwz88O2', 'Erlshawn Josh', '', 'Rivera', '', NULL, 'ej_Rivera@gmail.com', '091392847592', '2001-09-11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `weddings`
--

CREATE TABLE `weddings` (
  `id` int(11) NOT NULL,
  `bride` varchar(255) DEFAULT NULL,
  `groom` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `best_man` varchar(255) DEFAULT NULL,
  `maid_of_honor` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`ann_id`);

--
-- Indexes for table `baptisms`
--
ALTER TABLE `baptisms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`donate_id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `funerals`
--
ALTER TABLE `funerals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prayer_requests`
--
ALTER TABLE `prayer_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `weddings`
--
ALTER TABLE `weddings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `ann_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `baptisms`
--
ALTER TABLE `baptisms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `donate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `funerals`
--
ALTER TABLE `funerals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `prayer_requests`
--
ALTER TABLE `prayer_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `weddings`
--
ALTER TABLE `weddings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
