-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 17, 2025 at 08:44 AM
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

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`ann_id`, `title`, `image_path`, `content`, `date_posted`, `posted_by`) VALUES
(6, 'SEPTEMBER 29 FEAST OF THE DAY', 'uploads/Screenshot from 2025-09-29 22-01-11.png', '𝐅𝐞𝐚𝐬𝐭 𝐨𝐟 𝐭𝐡𝐞 𝐀𝐫𝐜𝐡𝐚𝐧𝐠𝐞𝐥𝐬: 𝐒𝐚𝐢𝐧𝐭 𝐌𝐢𝐜𝐡𝐚𝐞𝐥, 𝐒𝐚𝐢𝐧𝐭 𝐆𝐚𝐛𝐫𝐢𝐞𝐥 & 𝐒𝐚𝐢𝐧𝐭 𝐑𝐚𝐩𝐡𝐚𝐞𝐥 ✨🕊🙏', '2025-09-29 14:02:02', 1),
(7, 'SEPTEMBER 28 SAINT OF THE DAY', 'uploads/Screenshot from 2025-09-29 22-02-51.png', '𝐇𝐨𝐧𝐨𝐫𝐢𝐧𝐠 𝐒𝐚𝐢𝐧𝐭 𝐕𝐢𝐧𝐜𝐞𝐧𝐭 𝐝𝐞 𝐏𝐚𝐮𝐥 - 𝐅𝐚𝐭𝐡𝐞𝐫 𝐨𝐟 𝐌𝐨𝐝𝐞𝐫𝐧 𝐂𝐡𝐚𝐫𝐢𝐭𝐲 ✝️🕊️🙌', '2025-09-29 14:03:18', 1),
(8, 'NOVEMBER 17 CHECKING OF PROJECT', 'uploads/Flow_312.png', 'yup its time', '2025-11-16 23:28:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `baptisms`
--

CREATE TABLE `baptisms` (
  `id` int(11) NOT NULL,
  `child_firstname` varchar(30) NOT NULL,
  `child_middlename` varchar(30) NOT NULL,
  `child_lastname` varchar(30) NOT NULL,
  `child_suffix` varchar(30) NOT NULL,
  `mother_firstname` varchar(30) NOT NULL,
  `mother_middlename` varchar(30) NOT NULL,
  `mother_lastname` varchar(30) NOT NULL,
  `mother_suffix` varchar(30) NOT NULL,
  `father_firstname` varchar(30) NOT NULL,
  `father_middlename` varchar(30) NOT NULL,
  `father_lastname` varchar(30) NOT NULL,
  `father_suffix` varchar(30) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `birth_date` date NOT NULL,
  `birth_place` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `reservation_type` varchar(100) NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notif` tinyint(1) NOT NULL DEFAULT 0,
  `time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `baptisms`
--

INSERT INTO `baptisms` (`id`, `child_firstname`, `child_middlename`, `child_lastname`, `child_suffix`, `mother_firstname`, `mother_middlename`, `mother_lastname`, `mother_suffix`, `father_firstname`, `father_middlename`, `father_lastname`, `father_suffix`, `phone`, `birth_date`, `birth_place`, `date`, `reservation_type`, `status`, `created_at`, `notif`, `time`) VALUES
(18, 'Julia', 'Libatique', 'Sanchez', '', 'Elena', 'Dominingo', 'Sanchez', '', 'Anthony', 'Marin', 'Mendez', '', '9725829386', '2025-10-29', 'Bagumbong', '2025-11-10', 'Baptism', 'Pending', '2025-11-16 13:41:46', 0, '10:00:00'),
(19, 'Juan Carlos', 'Angeles', 'Ruiz', '', 'Marie', 'Martin', 'Angeles', '', 'Altor', 'Aguilar', 'Ruiz', '', '9612385922', '2025-11-01', 'Bagumbong', '2025-11-10', 'Baptism', 'Accepted', '2025-11-16 15:50:58', 0, '10:00:00');

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

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`donate_id`, `user_id`, `donor_fullname`, `donor_email`, `donor_contact_no`, `donation_categ`, `donation_specified`, `donation_type`, `mode_of_payment`, `proof_of_donation`, `donation_amount`, `donation_status`, `date_posted`) VALUES
(4, 42, 'Ericka Erick', 'eric@gmail.com', '9186253723', 'Cash', '', 'Parish Donation', 'Gcash', '', 5000.00, 'pending', '2025-11-16 23:32:33'),
(5, 43, 'Tor Vic', 'vvvv@nu.fairview.edu.ph', '9627381627', 'Clothes', '', 'Parish Donation', 'Onsite', '', 0.00, 'pending', '2025-11-16 23:33:29');

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
(4, 4, 1, '2025-09-29-21-47-39.png', 'uploads/1759154953_2025-09-29-21-47-39.png', '2025-09-29 14:09:13', '2025-09-29 14:09:13');

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
(4, 'receipts', 1, 0, '2025-09-29 14:03:36'),
(5, 'banners', 1, 0, '2025-09-29 14:03:43');

-- --------------------------------------------------------

--
-- Table structure for table `funerals`
--

CREATE TABLE `funerals` (
  `id` int(11) NOT NULL,
  `reservation_type` varchar(50) NOT NULL DEFAULT 'funeral',
  `deceased_firstname` varchar(100) DEFAULT NULL,
  `deceased_middlename` varchar(100) DEFAULT NULL,
  `deceased_lastname` varchar(100) DEFAULT NULL,
  `deceased_suffix` varchar(20) DEFAULT NULL,
  `deceased_dob` date NOT NULL,
  `funeral_date` date NOT NULL,
  `funeral_time` time NOT NULL,
  `funeral_address` varchar(255) NOT NULL,
  `contact_person_firstname` varchar(100) NOT NULL,
  `contact_person_middlename` varchar(100) DEFAULT NULL,
  `contact_person_lastname` varchar(100) NOT NULL,
  `contact_person_relation` varchar(100) NOT NULL,
  `contact_person_contact_no` varchar(20) NOT NULL,
  `contact_person_email` varchar(150) DEFAULT NULL,
  `status` enum('Pending','Accepted','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cause_of_death` varchar(255) DEFAULT NULL,
  `presider` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `funerals`
--

INSERT INTO `funerals` (`id`, `reservation_type`, `deceased_firstname`, `deceased_middlename`, `deceased_lastname`, `deceased_suffix`, `deceased_dob`, `funeral_date`, `funeral_time`, `funeral_address`, `contact_person_firstname`, `contact_person_middlename`, `contact_person_lastname`, `contact_person_relation`, `contact_person_contact_no`, `contact_person_email`, `status`, `created_at`, `cause_of_death`, `presider`) VALUES
(11, 'funeral', 'Antonia', 'Roman', 'Mendez', NULL, '2001-09-20', '2025-11-11', '10:00:00', '10 Bagumbong St.', 'Marta', 'Apple', 'Mendez', 'Wife', '9187285533', 'apple4_lives@gmail.com', 'Pending', '2025-11-16 16:00:57', 'Lung Cancer', NULL),
(12, 'funeral', 'Paul', 'Garfield', 'Passion', NULL, '2001-09-02', '2025-11-26', '10:00:00', '13 Bagumbong St.', 'Willard', 'Cortes', 'Reymond', 'Husband', '9625842933', 'willthebes98@gmail.com', 'Accepted', '2025-11-16 16:03:34', 'Head Trauma', NULL);

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

--
-- Dumping data for table `prayer_requests`
--

INSERT INTO `prayer_requests` (`id`, `name`, `username`, `email`, `intention`, `visibility`, `time`, `date`) VALUES
(1, 'Maria Santos', 'msantos', 'maria@example.com', 'For health and healing', 'public', '08:00:00', '2025-09-29'),
(2, 'Jose Reyes', 'jreyes', 'jose@example.com', 'For guidance in exams', 'private', '10:30:00', '2025-09-30'),
(3, 'Ana Lopez', 'alopez', 'ana@example.com', 'For family peace', 'public', '18:45:00', '2025-10-01');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `type` enum('baptism','wedding') NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `data_json` longtext NOT NULL,
  `ip_addr` varchar(64) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
(39, 'Admin', '2025-11-12 05:02:29', 'spk', '$2y$10$3lv/.YJK9bmbgFvHRdD5iOLOWAOs5GCE.0bPREwz8SLK8zszk41g.', 'vicky', 'vic', 'vic', 'f', 'vicky vic vic f', 'vic@gmail.com', '09182374466', '1993-09-21', NULL),
(40, 'Admin', '2025-11-12 05:18:40', 'pauiee', '$2y$10$rTkl4wT.5Tm066MGygLBeug11XCoPbI2YmEqfcmeQT3ZxmXD4XDNa', 'Pauline Jean', 'Garcia', 'Pasion', '', 'Pauline Jean Garcia Pasion', 'pauiee@gmail.com', '09621974275', '2001-09-02', NULL),
(41, 'User', '2025-11-12 07:01:06', 'ehrily', '$2y$10$GTcwx5d7Dw4nread/F/eP.mbEf2AbI3x7zH/aQ5bJQm.B.DsO1J4W', 'Ericka', '', 'Fenis', '', 'Ericka  Fenis', 'erif@gmail.com', '09123859933', '2001-04-13', NULL),
(42, 'User', '2025-11-16 23:32:33', 'meow123', NULL, NULL, NULL, NULL, NULL, 'Ericka Erick', 'eric@gmail.com', NULL, NULL, NULL),
(43, 'User', '2025-11-16 23:33:24', 'vickieindahouse', NULL, NULL, NULL, NULL, NULL, 'Tor Vic', 'vvvv@nu.fairview.edu.ph', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `weddings`
--

CREATE TABLE `weddings` (
  `id` int(11) NOT NULL,
  `bride_firstname` varchar(255) DEFAULT NULL,
  `bride_middlename` varchar(255) DEFAULT NULL,
  `bride_lastname` varchar(255) DEFAULT NULL,
  `bride_suffix` varchar(50) DEFAULT NULL,
  `bride_birthdate` date DEFAULT NULL,
  `bride_age` int(3) DEFAULT NULL,
  `bride_birthplace` varchar(255) DEFAULT NULL,
  `bride_nationality` varchar(100) DEFAULT NULL,
  `bride_address` varchar(255) DEFAULT NULL,
  `bride_contact` varchar(50) DEFAULT NULL,
  `groom_firstname` varchar(255) DEFAULT NULL,
  `groom_middlename` varchar(255) DEFAULT NULL,
  `groom_lastname` varchar(255) DEFAULT NULL,
  `groom_suffix` varchar(50) DEFAULT NULL,
  `groom_birthdate` date DEFAULT NULL,
  `groom_age` int(3) DEFAULT NULL,
  `groom_birthplace` varchar(255) DEFAULT NULL,
  `groom_nationality` varchar(100) DEFAULT NULL,
  `groom_address` varchar(255) DEFAULT NULL,
  `groom_contact` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `best_man_firstname` varchar(255) DEFAULT NULL,
  `best_man_middlename` varchar(255) DEFAULT NULL,
  `best_man_lastname` varchar(255) DEFAULT NULL,
  `best_man_suffix` varchar(50) DEFAULT NULL,
  `maid_of_honor_firstname` varchar(255) DEFAULT NULL,
  `maid_of_honor_middlename` varchar(255) DEFAULT NULL,
  `maid_of_honor_lastname` varchar(255) DEFAULT NULL,
  `maid_of_honor_suffix` varchar(50) DEFAULT NULL,
  `reservation_type` varchar(50) DEFAULT 'wedding',
  `status` varchar(20) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weddings`
--

INSERT INTO `weddings` (`id`, `bride_firstname`, `bride_middlename`, `bride_lastname`, `bride_suffix`, `bride_birthdate`, `bride_age`, `bride_birthplace`, `bride_nationality`, `bride_address`, `bride_contact`, `groom_firstname`, `groom_middlename`, `groom_lastname`, `groom_suffix`, `groom_birthdate`, `groom_age`, `groom_birthplace`, `groom_nationality`, `groom_address`, `groom_contact`, `phone`, `date`, `time`, `best_man_firstname`, `best_man_middlename`, `best_man_lastname`, `best_man_suffix`, `maid_of_honor_firstname`, `maid_of_honor_middlename`, `maid_of_honor_lastname`, `maid_of_honor_suffix`, `reservation_type`, `status`) VALUES
(12, 'Sylvia', 'Molina', 'Ramos', '', '2001-02-05', 24, 'Bagumbong', 'Filipino', '10 Bagumbong St.', '9284278833', 'Rocio', 'Gimenez', 'Pagsisihan', '', '2001-12-13', 24, 'Bagumbong', 'Filipino', '11 Bagumbong St.', '9123849552', '9283284499', '2025-11-13', '20:00:00', 'William', 'Flores', 'Navarro', '', 'Bianca Alyssa', 'Iglesias', 'Rosario', '', 'wedding', 'Pending'),
(13, 'Lorena', 'Velasco', 'Gil', '', '1990-09-02', 30, 'Bagumbong', 'Filipino', '10 Bagumbong St.', '9621839944', 'Alex', 'Yiik', 'Egleston', 'II', '2000-01-01', 25, 'Bagumbong', 'Filipino', '11 Bagumbong St.', '9186682293', '9621839944', '2025-11-30', '11:00:00', 'Rory', 'Cabrera', 'Mancer', '', 'Vela', 'Garcia', 'Herrero', '', 'wedding', 'Pending'),
(15, 'Emily', 'Santos', 'Morales', '', NULL, NULL, NULL, NULL, NULL, '9162273846', 'Alvaro', 'Mercedes', 'Prieto', '', NULL, NULL, NULL, NULL, NULL, '9164728833', '9164728833', '2025-11-28', '13:00:00', 'Jay', 'Campos', 'Delgado', '', 'Kaye', '', 'Carrasco', '', 'wedding', 'Accepted');

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
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
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
  MODIFY `ann_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `baptisms`
--
ALTER TABLE `baptisms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `donate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `funerals`
--
ALTER TABLE `funerals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `prayer_requests`
--
ALTER TABLE `prayer_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `weddings`
--
ALTER TABLE `weddings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
