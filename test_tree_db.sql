-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 27, 2025 at 02:39 AM
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
(7, 'SEPTEMBER 28 SAINT OF THE DAY', 'uploads/svc.png', '𝐇𝐨𝐧𝐨𝐫𝐢𝐧𝐠 𝐒𝐚𝐢𝐧𝐭 𝐕𝐢𝐧𝐜𝐞𝐧𝐭 𝐝𝐞 𝐏𝐚𝐮𝐥 - 𝐅𝐚𝐭𝐡𝐞𝐫 𝐨𝐟 𝐌𝐨𝐝𝐞𝐫𝐧 𝐂𝐡𝐚𝐫𝐢𝐭𝐲 ✝️🕊️🙌', '2025-09-29 14:03:18', 1),
(9, 'SEPTEMBER 29 FEAST OF THE DAY', 'uploads/fect.jpeg', '𝐅𝐞𝐚𝐬𝐭 𝐨𝐟 𝐭𝐡𝐞 𝐀𝐫𝐜𝐡𝐚𝐧𝐠𝐞𝐥𝐬: 𝐒𝐚𝐢𝐧𝐭 𝐌𝐢𝐜𝐡𝐚𝐞𝐥, 𝐒𝐚𝐢𝐧𝐭 𝐆𝐚𝐛𝐫𝐢𝐞𝐥 & 𝐒𝐚𝐢𝐧𝐭 𝐑𝐚𝐩𝐡𝐚𝐞𝐥 ✨🕊🙏', '2025-11-20 00:32:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `as_ninang_tbl`
--

CREATE TABLE `as_ninang_tbl` (
  `as_ninang_id` int(11) NOT NULL,
  `baptism_id` int(11) DEFAULT NULL,
  `as_ninang_firstname` varchar(30) NOT NULL,
  `as_ninang_middlename` varchar(30) DEFAULT NULL,
  `as_ninang_lastname` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `as_ninang_tbl`
--

INSERT INTO `as_ninang_tbl` (`as_ninang_id`, `baptism_id`, `as_ninang_firstname`, `as_ninang_middlename`, `as_ninang_lastname`) VALUES
(7, 42, 'Rita', 'Carlo', 'Moreno'),
(11, 46, 'Pauling', 'Dempsey', 'Pagsisihan'),
(12, 47, 'bb', 'b', 'b'),
(13, 49, 'ggg', 'gg', 'gg'),
(14, 50, 's', 's', 's'),
(15, 51, 'CIanna', 'Grizelda', 'Garais');

-- --------------------------------------------------------

--
-- Table structure for table `as_ninong_tbl`
--

CREATE TABLE `as_ninong_tbl` (
  `as_ninong_id` int(11) NOT NULL,
  `baptism_id` int(11) DEFAULT NULL,
  `as_ninong_firstname` varchar(30) NOT NULL,
  `as_ninong_middlename` varchar(30) DEFAULT NULL,
  `as_ninong_lastname` varchar(30) NOT NULL,
  `as_ninong_suffix` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `as_ninong_tbl`
--

INSERT INTO `as_ninong_tbl` (`as_ninong_id`, `baptism_id`, `as_ninong_firstname`, `as_ninong_middlename`, `as_ninong_lastname`, `as_ninong_suffix`) VALUES
(7, 42, 'Ezekiel', 'Flores', '', ''),
(11, 46, 'Paul', 'John', '', ''),
(12, 47, 'bb', 'b', '', ''),
(13, 49, 'ggg', 'g', '', ''),
(14, 50, 's', 's', '', ''),
(15, 51, 'Samantha', 'Kleire', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `baptisms`
--

CREATE TABLE `baptisms` (
  `id` int(11) NOT NULL,
  `reservation_user_id` int(11) DEFAULT NULL,
  `baptism_type` enum('Regular','Solo') NOT NULL,
  `child_firstname` varchar(30) NOT NULL,
  `child_middlename` varchar(30) DEFAULT NULL,
  `child_lastname` varchar(30) NOT NULL,
  `child_suffix` varchar(30) DEFAULT NULL,
  `child_gender` varchar(6) NOT NULL,
  `child_nat` varchar(12) DEFAULT 'Filipino',
  `mother_firstname` varchar(30) NOT NULL,
  `mother_middlename` varchar(30) DEFAULT NULL,
  `mother_lastname` varchar(30) NOT NULL,
  `father_firstname` varchar(30) NOT NULL,
  `father_middlename` varchar(30) NOT NULL,
  `father_lastname` varchar(30) NOT NULL,
  `father_suffix` varchar(30) DEFAULT NULL,
  `ninong_firstname` varchar(30) DEFAULT NULL,
  `ninong_middlename` varchar(30) DEFAULT NULL,
  `ninong_lastname` varchar(30) DEFAULT NULL,
  `ninong_suffix` varchar(30) DEFAULT NULL,
  `ninang_firstname` varchar(30) DEFAULT NULL,
  `ninang_middlename` varchar(30) DEFAULT NULL,
  `ninang_lastname` varchar(30) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(250) NOT NULL,
  `married` varchar(3) NOT NULL,
  `marriage_type` varchar(15) DEFAULT 'Single',
  `birth_date` date NOT NULL,
  `birth_place` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `reservation_type` varchar(100) NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notif` tinyint(1) NOT NULL DEFAULT 0,
  `time` time DEFAULT NULL,
  `rehearsal_date` date DEFAULT NULL,
  `rehearsal_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `baptisms`
--

INSERT INTO `baptisms` (`id`, `reservation_user_id`, `baptism_type`, `child_firstname`, `child_middlename`, `child_lastname`, `child_suffix`, `child_gender`, `child_nat`, `mother_firstname`, `mother_middlename`, `mother_lastname`, `father_firstname`, `father_middlename`, `father_lastname`, `father_suffix`, `ninong_firstname`, `ninong_middlename`, `ninong_lastname`, `ninong_suffix`, `ninang_firstname`, `ninang_middlename`, `ninang_lastname`, `contact`, `email`, `address`, `married`, `marriage_type`, `birth_date`, `birth_place`, `date`, `reservation_type`, `status`, `created_at`, `notif`, `time`, `rehearsal_date`, `rehearsal_time`) VALUES
(42, 49, 'Regular', 'Angela', 'Libatique', 'Go', '', 'Female', 'Filipino', 'Mandy', 'Sanchez', 'Libatique', 'Andy', 'Ybañez', 'Go', '', 'Paulo', 'Ventura', 'Sullan', '', 'Gina', 'Pepito', 'Franco', '9172837264', 'andy99@gmail.com', '11 Bagumbong St.', 'Yes', 'Church Marriage', '2025-11-01', 'Bagumbong', '2025-11-28', 'Baptism', 'Accepted', '2025-11-23 13:24:51', 0, '11:30:00', '2025-11-26', '10:00:00'),
(46, 45, 'Regular', 'Jose', 'Mari', 'Chan', '', 'Male', 'Filipino', 'Josefa', 'Ynnes', 'Menjola', 'Erlshawn Josh', 'Tandas', 'Rivera', '', 'Victor', 'Garlitos', 'Cruz', '', 'Ericka', 'Jason', 'Chan', '9173628462', 'jerlshaw@gmail.com', '10 Clay Street Bagumbong', 'No', NULL, '2025-11-24', 'Bagumbong', '2025-11-30', 'Baptism', 'Accepted', '2025-11-23 23:26:51', 0, '11:30:00', NULL, NULL),
(47, 52, 'Regular', 'Wynne', 'Rodrigo', 'Sabado', '', 'Female', 'Filipino', 'Beatrice', 'Olivia', 'Rodrigo', 'Theo', 'Omar', 'Sabado', '', 'aa', 'a', 'a', '', 'aa', 'a', 'a', '9175839927', 'bea20@gmail.com', '10 Clay Street Bagumbong', 'No', NULL, '2025-11-21', 'Bagumbong', '2025-11-30', 'Baptism', 'Accepted', '2025-11-26 05:08:14', 0, '11:30:00', '2025-11-26', '10:00:00'),
(49, 53, 'Regular', 'Jocasta Anne', 'Galang', 'Blanco', '', 'Female', 'Filipino', 'Elena', 'Masma', 'Blanco', 'Morris', 'Fuentes', 'Blanco', '', 'ccc', 'cc', 'c', '', 'ccc', 'cc', 'c', '9728392837', 'morris@gmail.com', '100 Bagumbongs', 'Yes', 'Church Marriage', '2025-11-12', 'Bagumbong', '2025-11-30', 'Baptism', 'Accepted', '2025-11-26 09:03:10', 0, '11:30:00', '2025-11-25', '10:00:00'),
(50, 55, 'Regular', 'Paul', 'Lennon', 'Mccartney', 'Jr', 'Male', 'Filipino', 'Pauline', 'Jean', 'Pasion', 'Paul', 'John', 'McCartney', '', 'f', 'f', 'f', '', 'f', 'f', 'f', '9', NULL, 'Liverpool', 'No', NULL, '2025-11-26', 'Bagumbong', '2025-12-07', 'Baptism', 'Accepted', '2025-11-26 18:18:22', 0, '11:30:00', '2025-12-01', '12:00:00'),
(51, 49, 'Solo', 'Charish', 'Datoy', 'Gingo', '', 'Female', 'Filipino', 'Rosalinda', 'Baya', 'Datoy', 'Andy', 'Ybañez', 'Go', '', 'Shanelle', 'Rabago', 'Arguelles', '', 'Betty', 'Perez', 'Condes', '9172837264', NULL, 'kabilang bahay', 'Yes', 'Church Marriage', '2004-07-15', 'bahay nila', '2025-11-12', 'Baptism', 'Accepted', '2025-11-26 23:31:46', 0, '03:00:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bestman_tbl`
--

CREATE TABLE `bestman_tbl` (
  `bestman_id` int(11) NOT NULL,
  `bestman_firstname` varchar(30) NOT NULL,
  `bestman_middlename` varchar(30) DEFAULT NULL,
  `bestman_lastname` varchar(30) NOT NULL
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

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`donate_id`, `user_id`, `donor_fullname`, `donor_email`, `donor_contact_no`, `donation_categ`, `donation_specified`, `donation_type`, `mode_of_payment`, `proof_of_donation`, `donation_amount`, `donation_status`, `date_posted`) VALUES
(6, 54, '', '', '', 'Clothes', '', 'Parish Donation', 'Onsite', '', 0.00, 'pending', '2025-11-26 13:49:50');

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
-- Table structure for table `maidofhonor_tbl`
--

CREATE TABLE `maidofhonor_tbl` (
  `maidofhonor_id` int(11) NOT NULL,
  `maidofhonor_firstname` varchar(30) NOT NULL,
  `maidofhonor_middlename` varchar(30) DEFAULT NULL,
  `maidofhonor_lastname` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `type` varchar(50) DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(11, 45, 'Baptism Request Accepted', 'Your baptism request has been accepted. Please proceed with your donation.', 'donation_reminder', 0, '2025-11-26 12:14:41'),
(12, 49, 'Baptism Rehearsal Schedule', 'Your baptism rehearsal is scheduled on 2025-11-26 at 10:00. Please visit the parish on time.', 'rehearsal_reminder', 0, '2025-11-26 12:55:58'),
(13, 49, 'Baptism Request Accepted', 'Your baptism request has been accepted. Please proceed with your donation.', 'donation_reminder', 0, '2025-11-26 12:56:08'),
(14, 52, 'Baptism Request Accepted', 'Your baptism request has been accepted. Please proceed with your donation.', 'donation_reminder', 0, '2025-11-26 13:16:46'),
(15, 52, 'Baptism Rehearsal Schedule', 'Your baptism rehearsal is scheduled on 2025-11-26 at 10:00:00. Please visit the parish on time.', 'rehearsal_reminder', 0, '2025-11-26 13:17:07'),
(16, 53, 'Baptism Request Accepted', 'Your baptism request has been accepted. Please proceed with your donation.', 'donation_reminder', 0, '2025-11-26 17:04:06'),
(17, 53, 'Baptism Rehearsal Schedule', 'Your baptism rehearsal is scheduled on 2025-11-25 at 10:00. Please visit the parish on time.', 'rehearsal_reminder', 0, '2025-11-26 17:05:47'),
(18, 49, 'Baptism Request Accepted', 'Your baptism request has been accepted. Please proceed with your donation.', 'donation_reminder', 0, '2025-11-26 22:43:10'),
(19, 49, 'Baptism Request Accepted', 'Your baptism request has been accepted. Please proceed with your donation.', 'donation_reminder', 0, '2025-11-27 02:08:44'),
(20, 55, 'Baptism Request Accepted', 'Your baptism request has been accepted. Please proceed with your donation.', 'donation_reminder', 0, '2025-11-27 02:19:31'),
(21, 55, 'Baptism Rehearsal Schedule', 'Your baptism rehearsal is scheduled on 2025-12-01 at 12:00. Please visit the parish on time.', 'rehearsal_reminder', 0, '2025-11-27 02:20:01'),
(22, 49, 'Baptism Request Accepted', 'Your baptism request has been accepted. Please proceed with your donation.', 'donation_reminder', 0, '2025-11-27 07:32:29');

-- --------------------------------------------------------

--
-- Table structure for table `prayer_requests`
--

CREATE TABLE `prayer_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `request_text` text DEFAULT NULL,
  `shared_public` tinyint(1) NOT NULL DEFAULT 0,
  `intention` varchar(50) NOT NULL,
  `anonymous` varchar(30) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `visibility` varchar(20) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prayer_requests`
--

INSERT INTO `prayer_requests` (`id`, `name`, `username`, `email`, `request_text`, `shared_public`, `intention`, `anonymous`, `created_at`, `visibility`, `time`, `date`) VALUES
(4, 'jean', NULL, 'jean@gmail.com', 'dear lord, thank you for healing my family were whole again :D', 1, 'thanksgiving', '0', '2025-11-19 23:23:03', NULL, NULL, NULL),
(5, NULL, NULL, NULL, 'please help me pass my exams thank you lord', 1, 'special', '1', '2025-11-19 23:24:03', NULL, NULL, NULL),
(6, NULL, NULL, NULL, 'lord please pauwiin mo na aso ko miss ko na sya i hope ligtas sya lord thank you', 1, 'special', '1', '2025-11-19 23:26:29', NULL, NULL, NULL),
(7, 'josie', NULL, 'jos@gmail.com', 'please guide me in passing my exams', 1, 'special', '0', '2025-11-19 23:27:10', NULL, NULL, NULL),
(8, 'Dereich', NULL, 'Dereich11@gmail.com', 'Good health', 1, 'special', '0', '2025-11-20 01:03:14', NULL, NULL, NULL),
(9, NULL, NULL, NULL, 'Lord sana sisig ulam mamaya namin', 0, 'special', '1', '2025-11-22 08:41:45', NULL, NULL, NULL),
(10, NULL, NULL, NULL, 'sana bumait na si Shanelle', 1, 'soul', '1', '2025-11-26 23:20:48', NULL, NULL, NULL);

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
  `gender` varchar(7) DEFAULT NULL,
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

INSERT INTO `users` (`user_id`, `user_type`, `created_at`, `username`, `password`, `givenname`, `middlename`, `lastname`, `gender`, `suffix`, `full_name`, `email`, `contact`, `birthday`, `photo`) VALUES
(40, 'Admin', '2025-11-12 05:18:40', 'pauiee', '$2y$10$rTkl4wT.5Tm066MGygLBeug11XCoPbI2YmEqfcmeQT3ZxmXD4XDNa', 'Pauline Jean', 'Garcia', 'Pasion', 'Female', '', 'Pauline Jean Garcia Pasion', 'pauiee@gmail.com', '09621974275', '2001-09-02', 'profilepics/maw.png'),
(41, 'Admin', '2025-11-12 07:01:06', 'ehrily', '$2y$10$GTcwx5d7Dw4nread/F/eP.mbEf2AbI3x7zH/aQ5bJQm.B.DsO1J4W', 'Ericka', 'Sabile', 'Fenis', 'Female', '', 'Ericka  Fenis', 'ehrily@gmail.com', '09123859933', '2001-04-13', ''),
(44, 'User', '2025-11-19 13:08:23', 'vicGar', '$2y$10$lB8WUQ4uL3PSG0NAxXki6.a5kmcCEawCHTiiV9rLzoJuqTMsIBPPO', 'Victor', 'Zabala', 'Garlitos', 'Male', '', NULL, 'vicGar@gmail.com', '9173846627', '2003-02-21', ''),
(45, 'User', '2025-11-19 13:18:58', 'jerlshaw', '$2y$10$oZsJ7Bi4u0l7q6Ts19Z7HONzn/tupBWnGmBIuvIrKGp5F0DJBuuVC', 'Erlshawn Josh', 'Tandas', 'Rivera', 'Male', NULL, NULL, 'jerlshaw@gmail.com', '9173628462', '2003-02-12', NULL),
(47, 'User', '2025-11-20 01:00:41', 'Dereich', '$2y$10$.gMJZ2iA1d1t9XeJ6QCtDOGz78b0lpTBDPstVTi7u18Q7Oo7BCrZa', 'Dereich', 'Carl', 'Rivera', 'Male', '', NULL, 'Dereich11@gmail.com', '09266579870', '2005-11-25', NULL),
(49, 'User', '2025-11-23 12:54:50', 'andy99', '$2y$10$I0TZbQO.6Sfdeyia3ROD1OCwkwcftZY2bwF/ZJH0.7P.Z1YWLwtgW', 'Andy', 'Ybañez', 'Go', 'Male', '', NULL, 'andy99@gmail.com', '9172837264', '1999-11-03', NULL),
(52, 'User', '2025-11-26 04:58:07', 'bea20', '$2y$10$dl/qxjOW26.kSr0Iukdw8eGWSEvoIxplP0GwkxyfG97NcvafAyDca', 'Beatrice', 'Olivia', 'Rodrigo', 'Female', '', NULL, 'bea20@gmail.com', '9175839927', '2001-03-20', NULL),
(53, 'User', '2025-11-26 08:56:42', 'morris', '$2y$10$9eWhFibuflnnl3a.LP5fXe7oLSWu45ROLadCUB.h9/MRDohBlfINW', 'Morris', 'Fuentes', 'Blanco', 'Male', '', NULL, 'morris@gmail.com', '9728392837', '2000-11-26', NULL),
(54, 'User', '2025-11-26 13:49:50', '', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL),
(55, 'User', '2025-11-26 18:15:18', 'pau', '$2y$10$hTHwu8N3.3MUCXoU5Uk4Ee6Xk5w8.c9CNiUUehqox55pKSTFlHAKO', 'Pauline', 'Jean', 'Pasion', 'Female', '', NULL, 'pau@m.com', '9', '2001-09-02', NULL),
(56, 'User', '2025-11-26 23:06:41', 'erlshwn', '$2y$10$Czuk5Xw5j2tJfGM1TRcRSe4mjf8qXSUs8mw0Yc0.GKHcuVjl.zqzi', 'orl', 'tandas', 'rivera', 'Male', '', NULL, 'erlshawn@gmail.com', '0912345678', '2005-02-10', NULL);

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
(15, 'Emily', 'Santos', 'Morales', '', NULL, NULL, NULL, NULL, NULL, '9162273846', 'Alvaro', 'Mercedes', 'Prieto', '', NULL, NULL, NULL, NULL, NULL, '9164728833', '9164728833', '2025-11-28', '13:00:00', 'Jay', 'Campos', 'Delgado', '', 'Kaye', '', 'Carrasco', '', 'wedding', 'Accepted'),
(16, 'Pauline', 'a', 'Pasion', '', NULL, NULL, NULL, NULL, NULL, '0912354382', 'victor', 'a', 'Garlitos', 'III', NULL, NULL, NULL, NULL, NULL, '0912354382', '0912354382', '2025-11-21', '09:00:00', 'josh', 'a', 'rivera', '', 'ericka', '', 'Fenis', '', 'wedding', 'Pending'),
(17, 'Hyacinth', 'Padasas', 'Tajanlangit', NULL, NULL, NULL, NULL, NULL, NULL, '9186284499', 'Victor', 'Gimenez', 'Garlitos', '', NULL, NULL, NULL, NULL, NULL, '9182748829', '9164728833', '2025-11-28', '01:00:00', 'Cyrus', 'Wala', 'Baconawa', '', 'ericka', 'Fucking', 'Fenis', NULL, 'wedding', 'Pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`ann_id`);

--
-- Indexes for table `as_ninang_tbl`
--
ALTER TABLE `as_ninang_tbl`
  ADD PRIMARY KEY (`as_ninang_id`),
  ADD KEY `baptism_id` (`baptism_id`);

--
-- Indexes for table `as_ninong_tbl`
--
ALTER TABLE `as_ninong_tbl`
  ADD PRIMARY KEY (`as_ninong_id`),
  ADD KEY `baptism_id` (`baptism_id`);

--
-- Indexes for table `baptisms`
--
ALTER TABLE `baptisms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reservation_user_id` (`reservation_user_id`);

--
-- Indexes for table `bestman_tbl`
--
ALTER TABLE `bestman_tbl`
  ADD PRIMARY KEY (`bestman_id`);

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
-- Indexes for table `maidofhonor_tbl`
--
ALTER TABLE `maidofhonor_tbl`
  ADD PRIMARY KEY (`maidofhonor_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
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
  MODIFY `ann_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `as_ninang_tbl`
--
ALTER TABLE `as_ninang_tbl`
  MODIFY `as_ninang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `as_ninong_tbl`
--
ALTER TABLE `as_ninong_tbl`
  MODIFY `as_ninong_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `baptisms`
--
ALTER TABLE `baptisms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `bestman_tbl`
--
ALTER TABLE `bestman_tbl`
  MODIFY `bestman_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `donate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- AUTO_INCREMENT for table `maidofhonor_tbl`
--
ALTER TABLE `maidofhonor_tbl`
  MODIFY `maidofhonor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `prayer_requests`
--
ALTER TABLE `prayer_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `weddings`
--
ALTER TABLE `weddings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `as_ninang_tbl`
--
ALTER TABLE `as_ninang_tbl`
  ADD CONSTRAINT `as_ninang_tbl_ibfk_1` FOREIGN KEY (`baptism_id`) REFERENCES `baptisms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `as_ninong_tbl`
--
ALTER TABLE `as_ninong_tbl`
  ADD CONSTRAINT `as_ninong_tbl_ibfk_1` FOREIGN KEY (`baptism_id`) REFERENCES `baptisms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `baptisms`
--
ALTER TABLE `baptisms`
  ADD CONSTRAINT `fk_reservation_user_id` FOREIGN KEY (`reservation_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
