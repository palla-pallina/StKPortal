-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2025 at 07:26 AM
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
-- Table structure for table `funeral_reservation`
--

CREATE TABLE `funeral_reservation` (
  `id` int(11) NOT NULL,
  `reservation_type` varchar(50) NOT NULL DEFAULT 'funeral',
  `deceased_fullname` varchar(255) DEFAULT NULL,
  `deceased_firstname` varchar(100) NOT NULL,
  `deceased_middlename` varchar(100) DEFAULT NULL,
  `deceased_lastname` varchar(100) NOT NULL,
  `deceased_suffix` varchar(20) DEFAULT NULL,
  `cause_of_death` varchar(255) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `funeral_reservation`
--

INSERT INTO `funeral_reservation` (`id`, `reservation_type`, `deceased_fullname`, `deceased_firstname`, `deceased_middlename`, `deceased_lastname`, `deceased_suffix`, `cause_of_death`, `deceased_dob`, `funeral_date`, `funeral_time`, `funeral_address`, `contact_person_firstname`, `contact_person_middlename`, `contact_person_lastname`, `contact_person_relation`, `contact_person_contact_no`, `contact_person_email`, `status`, `created_at`) VALUES
(1, 'funeral', NULL, 'Victora', 'Z', 'Garlitos', '', 'ax', '2025-11-10', '2025-11-10', '03:38:00', 'Asashahjsahjs', 'Victor', NULL, 'Garlitos', 'A', '922111', 'garlitosvz@students.nu-fairview.edu.ph', 'Cancelled', '2025-11-11 07:39:00'),
(2, 'funeral', 'Victora Z Garlitos', '', NULL, '', NULL, 'ax', '2025-11-10', '2025-11-10', '03:38:00', 'Asashahjsahjs', 'Victor', NULL, 'Garlitos', 'A', '922111', 'garlitosvz@students.nu-fairview.edu.ph', 'Accepted', '2025-11-11 07:46:34'),
(7, 'funeral', 'Victora Z Garlitos', '', NULL, '', NULL, 'ax', '2025-11-10', '2025-11-10', '21:49:00', 'Asashahjsahjs', 'Victor', NULL, 'Garlitos', 'Aaaaaa', '922111', 'garlitosvz@students.nu-fairview.edu.ph', 'Pending', '2025-11-11 13:47:13'),
(8, 'funeral', 'Victora Z GarlitosSSSSSSSSSSS', '', NULL, '', NULL, 'ax', '2025-11-10', '2025-11-10', '21:54:00', 'Asashahjsahjs', 'Victor', 'b', 'Garlitos', 'Aaaaaa', '922111', 'garlitosvz@students.nu-fairview.edu.ph', 'Accepted', '2025-11-11 13:50:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `funeral_reservation`
--
ALTER TABLE `funeral_reservation`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `funeral_reservation`
--
ALTER TABLE `funeral_reservation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
