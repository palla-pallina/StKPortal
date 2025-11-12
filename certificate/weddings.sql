-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2025 at 08:22 AM
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
-- Table structure for table `weddings`
--

CREATE TABLE `weddings` (
  `id` int(11) NOT NULL,
  `bride` varchar(255) DEFAULT NULL,
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
  `groom` varchar(255) DEFAULT NULL,
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
  `best_man` varchar(255) DEFAULT NULL,
  `best_man_firstname` varchar(255) DEFAULT NULL,
  `best_man_middlename` varchar(255) DEFAULT NULL,
  `best_man_lastname` varchar(255) DEFAULT NULL,
  `best_man_suffix` varchar(50) DEFAULT NULL,
  `maid_of_honor` varchar(255) DEFAULT NULL,
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

INSERT INTO `weddings` (`id`, `bride`, `bride_firstname`, `bride_middlename`, `bride_lastname`, `bride_suffix`, `bride_birthdate`, `bride_age`, `bride_birthplace`, `bride_nationality`, `bride_address`, `bride_contact`, `groom`, `groom_firstname`, `groom_middlename`, `groom_lastname`, `groom_suffix`, `groom_birthdate`, `groom_age`, `groom_birthplace`, `groom_nationality`, `groom_address`, `groom_contact`, `phone`, `date`, `time`, `best_man`, `best_man_firstname`, `best_man_middlename`, `best_man_lastname`, `best_man_suffix`, `maid_of_honor`, `maid_of_honor_firstname`, `maid_of_honor_middlename`, `maid_of_honor_lastname`, `maid_of_honor_suffix`, `reservation_type`, `status`) VALUES
(10, 'victor w w wAAAAA', 'victor', 'w', 'w', 'w', '2025-11-08', 2, 'w', 'as', 'ww', '922111', 'GR-arlitos a a a', 'GR-arlitos', 'a', 'a', 'a', '2025-11-08', 1, 'a', 'a', 'w', 'w', '11112345678', '2025-11-11', '15:03:00', 'w wzzz w w', 'w', 'wzzz', 'w', 'w', 'w ww w w', 'w', 'ww', 'w', 'w', 'Full Wedding', 'Cancelled');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `weddings`
--
ALTER TABLE `weddings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `weddings`
--
ALTER TABLE `weddings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
