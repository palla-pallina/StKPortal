-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 28, 2026 at 02:18 PM
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
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` int(11) NOT NULL,
  `sacrament` varchar(40) DEFAULT NULL,
  `service` varchar(20) DEFAULT NULL,
  `reference_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `status` enum('Unread','Read') DEFAULT 'Unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`id`, `sacrament`, `service`, `reference_id`, `title`, `message`, `status`, `created_at`) VALUES
(16, 'Baptism', NULL, 95, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for GFF F on January 26, 2026 at 08:00 AM.', 'Read', '2026-01-11 15:17:45'),
(17, 'Confirmation', NULL, 78, 'Confirmation Reservation', 'Blessica Pasado submitted a confirmation reservation for NNN NN N on January 27, 2026 at 10:00 AM.', 'Read', '2026-01-11 15:19:16'),
(18, 'Wedding', NULL, 13, 'Wedding Reservation', 'Blessica Pasado submitted a wedding reservation for Groom: H HHH HHHH and Bride: Blessica Rosal Pasado on October 31, 2026 at 10:00 AM.', 'Unread', '2026-01-11 15:24:25'),
(20, 'Wedding', NULL, 15, 'Wedding Reservation', 'Blessica Pasado submitted a wedding reservation for Groom: John Winston Lennon and Bride: Blessica Rosal Pasado on June 01, 2026 at 01:00 PM.', 'Read', '2026-01-11 18:59:17'),
(21, 'Baptism', NULL, 96, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for Meow Maw on February 02, 2026 at 08:00 AM.', 'Read', '2026-01-13 16:46:20'),
(22, 'Certificate Request', NULL, 21, 'Certificate Request', 'Blessica Pasado submitted a certificate request (Confirmation) for Blessica Pasado.', 'Read', '2026-01-14 13:27:56'),
(23, 'Certificate Request', NULL, 21, 'Certificate Request', 'Blessica Pasado submitted a certificate request (First Communion) for Pau Line Jean.', 'Read', '2026-01-15 01:38:00'),
(24, 'Confirmation', NULL, 79, 'Confirmation Reservation', 'Blessica Pasado submitted a confirmation reservation for Pau  line jeeen on January 30, 2026 at 03:00 PM.', 'Read', '2026-01-15 02:01:27'),
(25, 'Anointing of the Sick', NULL, 0, 'Anointing of the Sick Request', 'Blessica Pasado submitted an Anointing of the Sick request for sada sad sd scheduled on 2026-01-19 at 03:00.', 'Read', '2026-01-18 13:33:47'),
(26, 'Anointing of the Sick', NULL, 0, 'Anointing of the Sick Request', 'Blessica Pasado submitted an Anointing of the Sick request for Pauline Jean Pasion scheduled on 2026-01-21 at 07:00.', 'Read', '2026-01-18 13:38:44'),
(27, 'Funeral', NULL, 0, 'Funeral Mass Request', 'Blessica Pasado submitted a Funeral Mass request for sadsa ad dda scheduled on 2026-01-28 at 18:00.', 'Unread', '2026-01-18 13:56:16'),
(28, 'Celebratory Mass', NULL, 14, 'Celebratory Mass Request', 'Blessica Pasado submitted a Celebratory Mass request (Birthday) for ad asd asd scheduled on 2026-01-26 at 14:00 at At Santa Krus Parish.', 'Unread', '2026-01-18 14:20:20'),
(29, 'Celebratory Mass', NULL, 15, 'Celebratory Mass Request', 'Blessica Pasado submitted a Celebratory Mass request (Birthday) for adas dadad sadsada scheduled on 2026-01-27 at 21:00 at At Santa Krus Parish.', 'Unread', '2026-01-18 14:21:55'),
(30, 'Celebratory Mass', NULL, 17, 'Celebratory Mass Request', 'Blessica Pasado submitted a Celebratory Mass request (Birthday) for adas dada da scheduled on 2026-01-22 at 12:00 at At Santa Krus Parish.', 'Unread', '2026-01-18 14:30:34'),
(31, 'Celebratory Mass', NULL, 18, 'Celebratory Mass Request', 'Blessica Pasado submitted a Celebratory Mass request (Special Intention) for adasd scheduled on 2026-01-30 at 02:00 at At Home.', 'Unread', '2026-01-18 14:31:01'),
(32, 'Celebratory Mass', NULL, 19, 'Celebratory Mass Request', 'Blessica Pasado submitted a Celebratory Mass request (Thanksgiving) for adadad scheduled on 2026-01-30 at 08:00 at At Santa Krus Parish.', 'Unread', '2026-01-18 14:31:34'),
(33, 'Celebratory Mass', NULL, 20, 'Celebratory Mass Request', 'Blessica Pasado submitted a Celebratory Mass request (Wedding Anniversary) for da dadasda asdsa and sad s dad scheduled on 2026-01-22 at 02:00 at At Santa Krus Parish.', 'Unread', '2026-01-18 14:32:21'),
(34, 'Funeral', NULL, 0, 'Funeral Mass Request', 'Blessica Pasado submitted a Funeral Mass request for ad sad asd scheduled on 2026-01-28 at 08:00.', 'Unread', '2026-01-18 14:33:40'),
(35, 'House Blessing', NULL, 12, 'House Blessing Request', 'Blessica Pasado submitted a House Blessing request (Family Intention) for Blessica Rosal Pasado scheduled on 2026-01-29 at 12:00 located at asdsad, sadasd, ad, asdsad.', 'Read', '2026-01-18 14:42:00'),
(36, 'Baptism', NULL, 97, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for sadsd dad on February 08, 2026 at 11:30 AM.', 'Unread', '2026-01-23 05:25:52'),
(37, 'Baptism', NULL, 98, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for asd asd on February 08, 2026 at 11:30 AM.', 'Unread', '2026-01-23 05:29:56'),
(38, 'Baptism', NULL, 99, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for dsf dsfsdf on February 11, 2026 at 10:00 AM.', 'Unread', '2026-01-23 05:38:30'),
(39, 'Baptism', NULL, 100, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for asd asd on January 28, 2026 at 12:00 PM.', 'Unread', '2026-01-23 10:19:59'),
(40, 'Baptism', NULL, 101, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for sdad adsad on February 13, 2026 at 10:00 AM.', 'Unread', '2026-01-23 15:42:14'),
(41, 'Baptism', NULL, 102, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for j j on February 26, 2026 at 11:00 AM.', 'Unread', '2026-01-23 17:45:55'),
(42, 'Baptism', NULL, 103, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for ada dsa on February 26, 2026 at 11:00 AM.', 'Unread', '2026-01-23 17:59:26'),
(43, 'Baptism', NULL, 104, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for ads asd on March 01, 2026 at 10:00 AM.', 'Unread', '2026-01-25 06:35:26'),
(44, 'Baptism', NULL, 105, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for ads sad on February 17, 2026 at 10:00 AM.', 'Read', '2026-01-25 06:40:54'),
(45, 'Baptism', NULL, 106, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for asda dsa on February 18, 2026 at 02:00 PM.', 'Read', '2026-01-25 07:08:41'),
(46, 'Baptism', NULL, 107, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for asd dsa on March 01, 2026 at 10:00 AM.', 'Read', '2026-01-25 07:18:05'),
(47, 'First Communion', NULL, 19, 'First Communion Reservation', 'Blessica Pasado submitted a First Communion reservation for sa dsad d on February 15, 2026 at 03:00 PM.', 'Read', '2026-01-25 07:40:21'),
(48, 'First Communion', NULL, 20, 'First Communion Reservation', 'Blessica Pasado submitted a First Communion reservation for asd asd ads on February 15, 2026 at 01:00 PM.', 'Read', '2026-01-25 07:45:07'),
(49, 'First Communion', NULL, 21, 'First Communion Reservation', 'Blessica Pasado submitted a First Communion reservation for asd asd asd on February 22, 2026 at 10:00 AM.', 'Read', '2026-01-25 07:50:49'),
(50, 'Confirmation', NULL, 80, 'Confirmation Reservation', 'Blessica Pasado submitted a confirmation reservation for dsf dsfsdf dsf on March 10, 2026 at 01:00 PM.', 'Read', '2026-01-25 08:19:46'),
(51, 'Wedding', NULL, 16, 'Wedding Reservation', 'Blessica Pasado submitted a wedding reservation for Groom: asdf saf saf and Bride: Blessica Rosal Pasado on April 25, 2026 at 08:00 AM.', 'Read', '2026-01-25 08:49:08'),
(52, 'Wedding', NULL, 17, 'Wedding Reservation', 'Blessica Pasado submitted a wedding reservation for Groom: sdf af saf and Bride: Blessica Rosal Pasado on May 29, 2026 at 03:00 PM.', 'Read', '2026-01-25 08:51:51'),
(53, 'Baptism', NULL, 108, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for Papot Pasada on February 16, 2026 at 09:00 AM.', 'Read', '2026-01-25 15:10:45'),
(54, 'Certificate Request', 'Certificate Request', 0, 'Certificate Request', 'Blessica Pasado submitted a certificate request (Baptism) for Papot Rosal Pasado.', 'Read', '2026-01-25 15:20:06'),
(55, 'Confirmation', NULL, 81, 'Confirmation Reservation', 'Bryan Quiambao submitted a confirmation reservation for Elton Aquino Rico on March 03, 2026 at 01:00 PM.', 'Read', '2026-01-25 15:40:28'),
(56, 'First Communion', NULL, 22, 'First Communion Reservation', 'Matthew Mallari submitted a First Communion reservation for Ilyana Aquino Mallari on May 03, 2026 at 01:00 PM.', 'Read', '2026-01-25 15:46:46'),
(57, 'Baptism', NULL, 109, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for kyle remponi on February 16, 2026 at 03:00 PM.', 'Read', '2026-01-26 02:36:23'),
(58, 'Confirmation', NULL, 82, 'Confirmation Reservation', 'Blessica Pasado submitted a confirmation reservation for Eri Sav Fen on February 16, 2026 at 01:00 PM.', 'Read', '2026-01-26 03:35:23'),
(59, 'Baptism', NULL, 110, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for Kenneth Pasion on February 26, 2026 at 11:00 AM.', 'Read', '2026-01-26 05:12:03'),
(60, 'Baptism', NULL, 110, 'Cancellation Requested: Baptism (#110)', 'User #65 requested cancellation for Baptism (Reference #110). Date: 2026-02-26. Time: 11:00:00.', 'Read', '2026-01-26 19:00:37'),
(61, 'Baptism', NULL, 109, 'Cancellation Requested: Baptism (Reference #109)', 'Blessica Pasado requested the cancellation for \'s Baptism (Reference #109). Date: 2026-02-16. Time: 15:00:00.', 'Read', '2026-01-26 19:07:42'),
(62, 'Confirmation', NULL, 82, 'Cancellation Requested: Confirmation (Reference #82)', 'Blessica Pasado requested the cancellation for Eri Sav Fen\'s Confirmation (Reference #82). Date: 2026-02-16. Time: 13:00:00.', 'Read', '2026-01-26 19:10:44'),
(63, 'Celebratory', NULL, 19, 'Cancellation Requested: Celebratory (Reference #19)', 'Blessica Pasado requested the cancellation for this record\'s Celebratory (Reference #19). Date: 2026-01-30. Time: 08:00:00.', 'Read', '2026-01-26 19:10:51'),
(64, 'Certificate Request', '', 0, 'Certificate Request', 'Blessica Pasado submitted a certificate request (First Communion) for Blessica Pasado.', 'Unread', '2026-01-27 05:03:34'),
(65, NULL, 'Certificate Request', 24, 'Certificate Request', 'Blessica Pasado submitted a certificate request (First Communion) for aaBlessica Pasado.', 'Unread', '2026-01-27 05:07:05'),
(66, 'Certificate Request', NULL, 25, 'Certificate Request', 'Blessica Pasado submitted a certificate request (Confirmation) for Blessicaaaaa Pasado.', 'Unread', '2026-01-27 05:11:07'),
(67, 'Anointing of the Sick', NULL, 28, 'Anointing of the Sick Request', 'Matthew Mallari submitted an Anointing of the Sick request for Joanna Milton Mallari scheduled on 2026-01-30 at 07:00.', 'Unread', '2026-01-27 05:17:56'),
(68, 'Funeral', NULL, 13, 'Funeral Mass Request', 'Matthew Mallari submitted a Funeral Mass request for Joshua Mallari scheduled on 2026-01-30 at 03:00.', 'Unread', '2026-01-27 05:22:03'),
(69, 'First Communion', NULL, 23, 'First Communion Reservation', 'Blessica Pasado submitted a First Communion reservation for John Winston Lennon on February 22, 2026 at 10:00 AM.', 'Unread', '2026-01-27 10:47:57'),
(70, 'First Communion', NULL, 24, 'First Communion Reservation', 'Blessica Pasado submitted a First Communion reservation for ads sad dsa on February 22, 2026 at 01:00 PM.', 'Unread', '2026-01-27 10:48:38'),
(71, 'First Communion', NULL, 23, 'Cancellation Requested: First Communion (Reference #23)', 'Blessica Pasado requested the cancellation for John Winston Lennon\'s First Communion (Reference #23).\nReason: Schedule conflict Date: 2026-02-22. Time: 10:00:00.', 'Unread', '2026-01-27 17:56:33'),
(72, 'Anointing of the Sick', NULL, 23, 'Cancellation Requested: Anointing of the Sick (Reference #23)', 'Blessica Pasado requested the cancellation for sada sad sd\'s Anointing of the Sick (Reference #23).\nReason: pleaseee Date: 2026-01-19. Time: 03:00:00.', 'Unread', '2026-01-27 18:07:50'),
(73, 'House Blessing', NULL, 12, 'Cancellation Requested: House Blessing (Reference #12)', 'Blessica Pasado requested the cancellation for this record\'s House Blessing (Reference #12).<br>Reason: ddddd Date: 2026-01-29. Time: 12:00:00.', 'Unread', '2026-01-27 18:09:21'),
(74, 'First Communion', NULL, 24, 'Reschedule Requested: First Communion (Reference #24)', 'Blessica Pasado requested a reschedule for ads sad dsa\'s First Communion (Reference #24).\nType: Service\nRequested: 2026-01-22 10:00\nReason: dunno', 'Unread', '2026-01-27 18:45:45'),
(75, 'First Communion', NULL, 65, 'Batch Date Request', 'Blessica Rosal Pasado is requesting to open a date for solo First Communion for 2 people, at 2026-01-21 10:00', 'Unread', '2026-01-27 19:19:34'),
(76, 'Wedding', NULL, 65, 'Batch Date Request', 'Blessica Rosal Pasado is requesting to open a date for solo Wedding for 2 people, at 2026-01-29 10:00', 'Unread', '2026-01-27 19:34:42'),
(77, 'Confirmation', NULL, 65, 'Batch Date Request', 'Blessica Rosal Pasado is requesting to open a date for solo Confirmation for 3 people, at 2026-01-30 10:00', 'Read', '2026-01-27 19:35:29'),
(78, 'Anointing of the Sick', NULL, 29, 'Anointing of the Sick Request', 'Jovie Magbanua submitted an Anointing of the Sick request for Katrina Mae Villanueva Ramos scheduled on 2026-01-30 at 02:00.', 'Unread', '2026-01-28 04:44:31'),
(79, 'Certificate Request', NULL, 26, 'Certificate Request', 'Jovie Magbanua submitted a certificate request (First Communion) for Jovie Magbanua.', 'Unread', '2026-01-28 04:45:54'),
(80, 'First Communion', NULL, 25, 'First Communion Reservation', 'Jovie Magbanua submitted a First Communion reservation for Louisa Pineda Magbanua on February 22, 2026 at 01:00 PM.', 'Unread', '2026-01-28 04:50:49'),
(81, 'Celebratory Mass', NULL, 21, 'Celebratory Mass Request', 'Jovie Magbanua submitted a Celebratory Mass request (Birthday) for Andrea Nicole Bautista Torres scheduled on 2026-01-30 at 03:00 at At Santa Krus Parish.', 'Unread', '2026-01-28 04:54:11'),
(82, 'House Blessing', NULL, 13, 'House Blessing Request', 'Jovie Magbanua submitted a House Blessing request (Newly Built House) for Jovie Banes Magbanua scheduled on 2026-01-31 at 17:00 located at 22 Tatalon St., Bagong Silang, Caloocan, Metro Manila.', 'Unread', '2026-01-28 04:55:19'),
(83, 'House Blessing', NULL, 14, 'House Blessing Request', 'Victoria Enriquez submitted a House Blessing request (Newly Built House) for Victoria Cara Enriquez scheduled on 2026-02-28 at 12:00 located at 10 Junio St., Bagumbong, Caloocan, Metro Manila.', 'Unread', '2026-01-28 04:58:47'),
(84, 'Celebratory Mass', NULL, 22, 'Celebratory Mass Request', 'Victoria Enriquez submitted a Celebratory Mass request (Thanksgiving) for Recovery from cancer scheduled on 2026-03-31 at 08:00 at At Santa Krus Parish.', 'Unread', '2026-01-28 04:59:55'),
(85, 'House Blessing', NULL, 15, 'House Blessing Request', 'Joshua Agustin submitted a House Blessing request (Newly Built House) for Joshua Moreno Agustin scheduled on 2026-03-27 at 08:00 located at 15 Chihuahua St., Kaligayahan, Quezon City, Metro Manila.', 'Unread', '2026-01-28 05:00:56'),
(86, 'Baptism', NULL, 111, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for erlshawn rivera on February 22, 2026 at 10:00 AM.', 'Read', '2026-01-28 06:45:45'),
(87, 'Certificate Request', NULL, 27, 'Certificate Request', 'Blessica Pasado submitted a certificate request (Baptism) for Blessica Pasado.', 'Unread', '2026-01-28 07:06:02'),
(88, 'Certificate Request', NULL, 28, 'Certificate Request', 'Blessica Pasado submitted a certificate request (Baptism) for victor z Garlitos.', 'Unread', '2026-01-28 07:14:37'),
(89, 'Baptism', NULL, 112, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for sasas asa on February 22, 2026 at 10:00 AM.', 'Unread', '2026-01-28 07:29:42'),
(90, 'Certificate Request', NULL, 29, 'Certificate Request', 'Blessica Pasado submitted a certificate request (Baptism) for sasas dad asa.', 'Unread', '2026-01-28 07:32:06'),
(91, 'Baptism', NULL, 113, 'Baptism Reservation', 'Blessica Pasado submitted a baptism reservation for Victor Garlitos on February 18, 2026 at 08:00 AM.', 'Unread', '2026-01-28 07:41:12'),
(92, 'Certificate Request', NULL, 30, 'Certificate Request', 'Blessica Pasado submitted a certificate request (Baptism) for Victor Z Garlitos.', 'Unread', '2026-01-28 07:42:27'),
(93, 'Celebratory Mass', NULL, 23, 'Celebratory Mass Request', 'Blessica Pasado submitted a Celebratory Mass request (Thanksgiving) for wsdsdsdsd scheduled on 2026-01-29 at 04:00 at At Santa Krus Parish.', 'Unread', '2026-01-28 07:56:16'),
(94, 'Confirmation', NULL, 83, 'Confirmation Reservation', 'Blessica Pasado submitted a confirmation reservation for A A A on March 10, 2026 at 01:00 PM.', 'Unread', '2026-01-28 08:00:31');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `ann_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `image_path` varchar(300) DEFAULT NULL,
  `ann_categ` varchar(30) DEFAULT 'General',
  `content` text NOT NULL,
  `date_posted` timestamp NOT NULL DEFAULT current_timestamp(),
  `posted_by` int(11) DEFAULT NULL,
  `groom_name` varchar(100) DEFAULT NULL,
  `bride_name` varchar(100) DEFAULT NULL,
  `marriage_place` varchar(100) DEFAULT NULL,
  `marriage_date` date DEFAULT NULL,
  `groom_photo` varchar(300) DEFAULT NULL,
  `bride_photo` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`ann_id`, `title`, `image_path`, `ann_categ`, `content`, `date_posted`, `posted_by`, `groom_name`, `bride_name`, `marriage_place`, `marriage_date`, `groom_photo`, `bride_photo`) VALUES
(17, '𝟐𝐁 𝐇𝐄𝐀𝐋𝐓𝐇𝐘 @ 𝟔𝟎!💫🪩', 'uploads/1765192262_2bhealthy.png', 'General', 'Purihin ang Diyos sa katagumpayan ng naganap na birthday concert ng ating minamahal na Kura Paroko, Rev. Fr. James D. Nitollama , noong nakaraang Martes! 🙏✨ \r\nNais naming pasalamatan ang lahat ng 𝐬𝐩𝐨𝐧𝐬𝐨𝐫𝐬, 𝐩𝐞𝐫𝐟𝐨𝐫𝐦𝐞𝐫𝐬 𝐦𝐮𝐥𝐚 𝐬𝐚 𝐢𝐛𝐚\'𝐭 𝐢𝐛𝐚𝐧𝐠 𝐦𝐢𝐧𝐢𝐬𝐭𝐫𝐲 𝐧𝐠 𝐚𝐭𝐢𝐧𝐠 𝐩𝐚𝐫𝐨𝐤𝐲𝐚, at 𝐥𝐚𝐡𝐚𝐭 𝐧𝐠 𝐛𝐮𝐦𝐢𝐥𝐢 𝐧𝐠 𝐭𝐢𝐜𝐤𝐞𝐭 at 𝐝𝐮𝐦𝐚𝐥𝐨 upang makisaya at suportahan ang pagdiriwang na ito. Naging malaking bahagi kayo sa patuloy na pagpapaayos at pagpapaganda ng ating parokya. 🩷✝️\r\nNais din naming pasalamatan ang lahat ng 𝐯𝐨𝐥𝐮𝐧𝐭𝐞𝐞𝐫𝐬 at mga tao sa likod ng matagumpay na birthday concert ng ating Kura Paroko. Dahil sa inyong oras, talento, at dedikasyon, napuno ng saya, musika, at alaala ang 𝐂𝐚𝐥𝐨𝐨𝐜𝐚𝐧 𝐒𝐩𝐨𝐫𝐭𝐬 𝐂𝐨𝐦𝐩𝐥𝐞𝐱. Mula sa stage setup, sound & lights, ticketing, logistics, hanggang sa bawat maliit na detalye — hindi rin magiging ganito ka-memorable ang bawat selebrasyon ng kaarawan ng ating Kura Paroko kung wala kayo.🤍✨\r\nAt para sa ating minamahal na Kura Paroko, 𝐅𝐫. 𝐉𝐚𝐦𝐞𝐬, maraming salamat po sa iyong walang sawang serbisyo, pagmamahal, at dedikasyon sa ating parokya. Nawa’y nagustuhan mo ang espesyal na pagdiriwang na ito. Ang iyong dedikasyon ay tunay na inspirasyon sa bawat isa sa amin. 💫🙌🏻', '2025-11-11 11:11:02', 40, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'Diocesan Youth Day 2025 TINIG: Ang Tunay na Pag-Asa ay Hindi Tahimik', 'uploads/1765193514_tinig.png', 'General', 'Mga Kalakbay! 🌟\r\n\r\nBilang bahagi ng ating Jubilee Year of Hope for the Youth, sama sama nating ipagdiwang ang Diocesan Youth Day 2025 TINIG: Ang Tunay na Pag-Asa ay Hindi Tahimik ngayong November 15, 2025.\r\n\r\nIsang araw ng pananampalataya, pagkakaisa, at pag-asa kasama ang mga kabataang Katoliko mula sa buong Diocese of Novaliches — hosted by the District of Caloocan.\r\n\r\nKung ikaw ay nasa edad 13-39 na nais sumali, makipag-ugnayan lamang sa inyong Parish Youth Coordinators o Ministry Heads para sa detalye ng inyong pagrehistro.\r\n\r\nRegistration Period: October 08-31, 2025\r\nRegistration Fee: ₱200/each (may lunch, dinner, at DYD kit na kasama!)\r\n\r\nAno pang hinihintay niyo, mga Kalakbay? Bilang isang Kabataang Novaleño, sama-sama nating palakasin ang TINIG ng Pag-asang hindi tahimik!\r\n\r\nMag-register na at kita-kits sa November 15!\r\n\r\nInvitation Package: https://bit.ly/DYDPackage', '2025-10-25 11:31:54', 40, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'Jubilee of Hope Celebration of the Youth ng Diocese of Novaliches', 'uploads/1765193798_Jubilee Mass for the Youth.png', 'General', '\"Let no one despise your youth, but be an example to the believers...\" - 1 Timothy 4:12\r\n\r\nIpinagdiriwang ang Banal na Eukaristiya sa pamumuno ng ating Obispo, His Excellency Roberto Oredain Gaa, D.D. bilang bahagi ng Jubilee of Hope Celebration of the Youth ng Diocese of Novaliches. \r\n\r\n𝗧𝗶𝗴𝗻𝗮𝗻: nagtipon ang mga kabataang anak ng Diyos mula sa iba’t ibang parokya upang magsaya, magpuri, at muling pagtibayin ang kanilang 𝗺𝗶𝘀𝘆𝗼𝗻 𝗯𝗶𝗹𝗮𝗻𝗴 𝗶𝗹𝗮𝘄 𝗻𝗴 𝗦𝗶𝗺𝗯𝗮𝗵𝗮𝗻.\r\nSa bawat sigaw ng papuri, sa bawat ngiti, at sa bawat panalangin, ramdam ang 𝗗𝗶𝘆𝗼𝘀 𝗻𝗮 𝗸𝘂𝗺𝗶𝗸𝗶𝗹𝗼𝘀 𝘀𝗮 𝗽𝘂𝘀𝗼 𝗻𝗴 𝗸𝗮𝗯𝗮𝘁𝗮𝗮𝗻.\r\n🔥 𝘒𝘢𝘣𝘢𝘵𝘢𝘢𝘯𝘨 𝘒𝘢𝘵𝘰𝘭𝘪𝘬𝘰, 𝘱𝘢𝘵𝘶𝘭𝘰𝘺 𝘵𝘢𝘺𝘰𝘯𝘨 𝘮𝘢𝘨𝘯𝘪𝘯𝘨𝘢𝘴!\r\n🌟 𝘛𝘪𝘯𝘢𝘸𝘢𝘨 𝘵𝘢𝘺𝘰𝘯𝘨 𝘮𝘢𝘨𝘪𝘯𝘨 𝘴𝘢𝘬𝘴𝘪 𝘯𝘨 𝘱𝘢𝘨-𝘢𝘴𝘢.\r\n🕊️ 𝘈𝘵 𝘪𝘴𝘪𝘯𝘶𝘨𝘰 𝘵𝘢𝘺𝘰𝘯𝘨 𝘮𝘢𝘨𝘪𝘯𝘨 𝘥𝘢𝘭𝘶𝘺𝘢𝘯 𝘯𝘨 𝘱𝘢𝘨-𝘪𝘣𝘪𝘨 𝘯𝘪 𝘒𝘳𝘪𝘴𝘵𝘰 𝘴𝘢 𝘮𝘶𝘯𝘥𝘰.\r\n\r\n📍 𝐂𝐚𝐥𝐨𝐨𝐜𝐚𝐧 𝐒𝐩𝐨𝐫𝐭𝐬 𝐂𝐨𝐦𝐩𝐥𝐞𝐱 | 🗓️ 𝐍𝐨𝐯𝐞𝐦𝐛𝐞𝐫 𝟏𝟓, 𝟐𝟎𝟐𝟓 (𝐒𝐚𝐭𝐮𝐫𝐝𝐚𝐲)', '2025-11-15 11:36:38', 40, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'Kawan Ni San Isidro Labrador', 'uploads/1765194038_aa.png', 'General', '“𝗔𝗸𝗼 𝗮𝗻𝗴 𝗺𝗮𝗯𝘂𝘁𝗶𝗻𝗴 𝗽𝗮𝘀𝘁𝗼𝗹. 𝗞𝗶𝗹𝗮𝗹𝗮 𝗸𝗼 𝗮𝗻𝗴 𝗮𝗸𝗶𝗻𝗴 𝗺𝗴𝗮 𝘁𝘂𝗽𝗮, 𝗮𝘁 𝗸𝗶𝗹𝗮𝗹𝗮 𝗿𝗶𝗻 𝗻𝗶𝗹𝗮 𝗮𝗸𝗼.” — 𝗝𝘂𝗮𝗻 𝟭𝟬:𝟭𝟰\r\n\r\nIsang pinagpalang Kawan Visit kasama ang ating mahal na Obispo, Most Rev. Roberto Gaa, sa mga pamayanan ng Metropolis North, Carlisle 2, Greenville, Mirabela, Bagumbong Kanto, Alagar/Arsenias, Sampaloc, Villa Alicia, Progressive 2, Recombville 2, Bagumbong Taas, Solar Urban, at Villa Luisa.\r\nNawa’y patuloy tayong maging iisang kawan, pinapatatag ng pag-ibig ni Kristo at ginagabayan ng ating pastol tungo sa mas malalim na pananampalataya at pagkakaisa. 🕊️✨', '2025-11-17 11:40:38', 40, NULL, NULL, NULL, NULL, NULL, NULL),
(23, '✨𝐉𝐎𝐈𝐍 𝐓𝐇𝐄 𝐒𝐎𝐂𝐈𝐀𝐋 𝐂𝐎𝐌𝐌𝐔𝐍𝐈𝐂𝐀𝐓𝐈𝐎𝐍 & 𝐌𝐄𝐃𝐈𝐀 𝐌𝐈𝐍𝐈𝐒𝐓𝐑𝐘✨', 'uploads/1765194234_call.png', 'General', 'Open for creative hearts and willing hands!💛🎥🎙️\r\n\r\n\"Pope Leo XIV encourages our generation to be \"digital missionaries\", and use our platforms to spread the Gospel and promote positive values.\"\r\n\r\n\"Go and proclaim the Gospel to all creation.\" - Mark 16:15\r\n\r\nIn this digital age, the Church continues its mission of proclaiming God’s love—not only inside the walls of the parish, but also in the vast and ever-growing world of social media.\r\nAtin pong ipagpatuloy ang misyon ni Kristo sa pamamagitan ng pagiging daluyan ng mabuting balita, gamit ang ating mga talento at kakayahan.\r\n𝙒𝙚 𝙖𝙧𝙚 𝙡𝙤𝙤𝙠𝙞𝙣𝙜 𝙛𝙤𝙧 𝙣𝙚𝙬 𝙢𝙚𝙢𝙗𝙚𝙧𝙨 𝙬𝙝𝙤 𝙬𝙖𝙣𝙩 𝙩𝙤 𝙨𝙚𝙧𝙫𝙚 𝙩𝙝𝙧𝙤𝙪𝙜𝙝:\r\n𝗖𝗥𝗘𝗔𝗧𝗜𝗩𝗘𝗦 𝗖𝗢𝗠𝗠𝗜𝗧𝗧𝗘𝗘\r\n- Pubmat Creator\r\n𝗦𝗣𝗘𝗖𝗜𝗔𝗟 𝗠𝗘𝗗𝗜𝗔 𝗖𝗢𝗠𝗠𝗜𝗧𝗧𝗘𝗘\r\n- Live Operator\r\n- Social Media Manager (SMM)\r\n- Chat Moderator\r\n𝗣𝗥𝗢𝗝𝗘𝗖𝗧𝗢𝗥 𝗖𝗢𝗠𝗠𝗜𝗧𝗧𝗘𝗘\r\n- Projector Operator \r\n𝗗𝗼𝗰𝘂𝗺𝗲𝗻𝘁𝗮𝘁𝗶𝗼𝗻 𝗖𝗼𝗺𝗺𝗶𝘁𝘁𝗲𝗲\r\n- Videographer\r\n- Photographer\r\n- Content Editor\r\nNo experience? 𝗧𝗵𝗮𝘁’𝘀 𝗼𝗸𝗮𝘆!\r\nWhat matters is your willingness to learn and your desire to serve.\r\nYou will be trained, guided, and supported by a loving ministry family.\r\n𝗚𝘂𝘀𝘁𝗼 𝗺𝗼𝗻𝗴 𝘀𝘂𝗺𝗮𝗹𝗶? 🤩\r\n📲 Message us on our Facebook page (Santa Krus Parish, Bagumbong - Social Media Pool)\r\n🔍 Or scan the QR code to register \r\n🔗 : https://forms.gle/8Mmwn6z5sM56ZF6w9\r\nIf you feel this tug in your heart, do not ignore it.\r\n𝗧𝗵𝗲 𝗖𝗵𝘂𝗿𝗰𝗵 𝗻𝗲𝗲𝗱𝘀 𝘆𝗼𝘂𝗿 𝗴𝗶𝗳𝘁𝘀. 𝗧𝗵𝗲 𝗺𝗶𝘀𝘀𝗶𝗼𝗻 𝗻𝗲𝗲𝗱𝘀 𝘆𝗼𝘂𝗿 𝘃𝗼𝗶𝗰𝗲. 𝗚𝗼𝗱 𝗻𝗲𝗲𝗱𝘀 𝘆𝗼𝘂𝗿 “𝘆𝗲𝘀.”\r\nJoin SOCCOMM and help spread Christ’s message in a world that needs it more than ever.\r\nMessage us now to be part of the mission! 🙏🔥✨', '2025-11-20 11:43:54', 40, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'Christ the King Motorcade Procession', 'uploads/1765194364_christtheking.png', 'General', 'Then he said, \"Jesus, remember me when you come into your kingdom.\"\r\n- Luke 23:42\r\n\r\nOn this Solemnity of Our Lord Jesus Christ, King of the Universe, we unite as one community to honor Jesus as our King. 👑✨\r\nWe encourage everyone to take part in the 𝟯:𝟬𝟬 𝗣𝗠 𝗖𝗵𝗿𝗶𝘀𝘁 𝘁𝗵𝗲 𝗞𝗶𝗻𝗴 𝗺𝗼𝘁𝗼𝗿𝗰𝗮𝗱𝗲 𝗽𝗿𝗼𝗰𝗲𝘀𝘀𝗶𝗼𝗻 today. May this celebration remind us of His love, His guidance, and His place at the center of our lives.🙏🏼🌟', '2025-11-22 11:46:04', 40, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'Red Wednesday | Living Hope Amidst Suffering', 'uploads/1765194468_redwed.png', 'General', 'Today, we join the whole Church in remembering our 𝘀𝘂𝗳𝗳𝗲𝗿𝗶𝗻𝗴 𝗮𝗻𝗱 𝗽𝗲𝗿𝘀𝗲𝗰𝘂𝘁𝗲𝗱 𝗖𝗵𝗿𝗶𝘀𝘁𝗶𝗮𝗻 𝗯𝗿𝗼𝘁𝗵𝗲𝗿𝘀 𝗮𝗻𝗱 𝘀𝗶𝘀𝘁𝗲𝗿𝘀 across the world. We wear red, we pray, and we stand in solidarity with those who continue to bear witness to Christ even in the midst of violence, discrimination, and fear. 🕊️✝️\r\n\r\nMay this day open our hearts to the reality of their sacrifices and inspire us to deepen our own faith, courage, and compassion. Let us be a voice for the voiceless, a light in the darkness, and instruments of peace where hope seems lost. ❤️\r\n\r\n“𝗜𝗳 𝗼𝗻𝗲 𝗺𝗲𝗺𝗯𝗲𝗿 𝘀𝘂𝗳𝗳𝗲𝗿𝘀, 𝗮𝗹𝗹 𝘀𝘂𝗳𝗳𝗲𝗿 𝘁𝗼𝗴𝗲𝘁𝗵𝗲𝗿.” — 𝟭 𝗖𝗼𝗿𝗶𝗻𝘁𝗵𝗶𝗮𝗻𝘀 𝟭𝟮:𝟮𝟲\r\nLord, strengthen those who suffer for Your name, and awaken in us the commitment to love, pray, and act for justice. 🙏✨\r\n\r\n𝐇𝐨𝐥𝐲 𝐌𝐚𝐬𝐬 - 6:00 𝐩𝐦\r\n𝙒𝙚𝙖𝙧 𝙧𝙚𝙙 𝙖𝙣𝙙 𝙗𝙧𝙞𝙣𝙜 𝙘𝙖𝙣𝙙𝙡𝙚.', '2025-11-25 11:47:48', 40, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 'ℕ𝕆𝕍𝔼𝕄𝔹𝔼ℝ 𝟚𝟜 | 𝕊𝔸𝕀ℕ𝕋𝕊 𝕆𝔽 𝕋ℍ𝔼 𝔻𝔸𝕐', 'uploads/1765194996_saintsoftheday.png', 'General', 'Remembering Saint Andrew Dũng-Lạc & Companions: The Martyrs of Vietnam ✝️\r\n\r\nToday, we honor the Vietnamese Martyrs — brave bishops, priests, religious, men, women, and even children who chose faith over fear. From the 1600s to the 1800s, thousands were arrested, tortured, and killed because they refused to deny their love for Christ. Their courage helped the Catholic faith grow in Vietnam, supported by early missionaries and lay catechists who shared the Gospel even in secret. Among them is Saint Andrew Dũng-Lạc, whose life of simplicity, sacrifice, and steadfast faith represents all the known and unknown martyrs who gave everything for God.\r\n\r\nTheir legacy inspires us today. Even through persecution, the Church in Vietnam continued to flourish — proof that faith is stronger than fear. These martyrs remind us that no matter what hardships we face, our hope in God is worth everything. Their words echo to us now, like the beautiful reflection of St. Théophane Vénard: “My thoughts are fixed on eternity… I thirst for the waters of Life Eternal.” May their courage strengthen our own faith and inspire us to live boldly for Christ. 🙏🔥\r\n\r\n𝙎𝙖𝙣𝙘𝙩𝙚 𝘼𝙣𝙙𝙧𝙚𝙖𝙚 𝘿ũ𝙣𝙜-𝙇ạ𝙘 𝙚𝙩 𝙎𝙤𝙘𝙞𝙞, 𝙤𝙧𝙖𝙩𝙚 𝙥𝙧𝙤 𝙣𝙤𝙗𝙞𝙨!', '2025-11-24 11:56:36', 40, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 'Wedding Banns', 'uploads/wedding_banns_697892bb637ac.png', 'Wedding Banns', 'We are pleased to announce the upcoming marriage of Ernie Roxas and Alina Ortega.\r\n', '2026-01-27 10:26:03', 39, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `anointing_of_sick_requests`
--

CREATE TABLE `anointing_of_sick_requests` (
  `id` int(11) NOT NULL,
  `reservation_user_id` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `decline_reason` text DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `venue` varchar(50) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `street_no` varchar(100) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `hospital_name` varchar(255) DEFAULT NULL,
  `sick_firstname` varchar(30) NOT NULL,
  `sick_middlename` varchar(30) DEFAULT NULL,
  `sick_lastname` varchar(30) NOT NULL,
  `sick_suffix` varchar(10) DEFAULT NULL,
  `contact_person_firstname` varchar(30) NOT NULL,
  `contact_person_middlename` varchar(30) DEFAULT NULL,
  `contact_person_lastname` varchar(30) NOT NULL,
  `contact_person_suffix` varchar(10) DEFAULT NULL,
  `age` int(11) NOT NULL,
  `sick_condition` varchar(255) NOT NULL,
  `contact_relation` varchar(100) NOT NULL,
  `contact_number` varchar(30) NOT NULL,
  `contact_email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anointing_of_sick_requests`
--

INSERT INTO `anointing_of_sick_requests` (`id`, `reservation_user_id`, `status`, `decline_reason`, `date`, `time`, `created_at`, `venue`, `province`, `city`, `street_no`, `barangay`, `hospital_name`, `sick_firstname`, `sick_middlename`, `sick_lastname`, `sick_suffix`, `contact_person_firstname`, `contact_person_middlename`, `contact_person_lastname`, `contact_person_suffix`, `age`, `sick_condition`, `contact_relation`, `contact_number`, `contact_email`) VALUES
(26, 63, 'Pending', NULL, '2026-01-30', '07:00:00', '2026-01-27 05:16:26', 'At Home', 'Metro Manila', 'Quezon City', '11 Puno St.', 'Kaligayahan', NULL, 'Joanna', 'Milton', 'Mallari', '', 'Matthew', 'Magtuba', 'Mallari', '', 78, 'Alzheimers', 'Relative', '09621734829', 'mattmagmal@gmail.com'),
(29, 58, 'Pending', NULL, '2026-01-30', '02:00:00', '2026-01-28 04:44:31', 'At Home', 'Metro Manila', 'Caloocan', '18 Verde St.', 'Bagong Silang', NULL, 'Katrina Mae', 'Villanueva', 'Ramos', '', 'Jovie', 'Banes', 'Magbanua', '', 28, 'Cancer', 'Relative', '09183274283', 'joviemb@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `as_ninang_tbl`
--

CREATE TABLE `as_ninang_tbl` (
  `as_ninang_id` int(11) NOT NULL,
  `baptism_id` int(11) DEFAULT NULL,
  `wedding_id` int(11) DEFAULT NULL,
  `confirmation_id` int(11) DEFAULT NULL,
  `as_ninang_firstname` varchar(30) NOT NULL,
  `as_ninang_middlename` varchar(30) DEFAULT NULL,
  `as_ninang_lastname` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `as_ninang_tbl`
--

INSERT INTO `as_ninang_tbl` (`as_ninang_id`, `baptism_id`, `wedding_id`, `confirmation_id`, `as_ninang_firstname`, `as_ninang_middlename`, `as_ninang_lastname`) VALUES
(68, 73, NULL, NULL, 'Giselle', 'Sumera', 'Arroyo'),
(69, 73, NULL, NULL, 'Jeneth', '', 'Pelayo'),
(70, NULL, NULL, 73, 'Lancel', '', 'Navarro'),
(74, 75, NULL, NULL, 'Justin', 'Marcos', 'Jose'),
(75, 75, NULL, NULL, 'Benny ', 'Raymundo', 'Fajardo'),
(76, 75, NULL, NULL, 'Mueller ', 'Arroyo', 'Bravo'),
(77, 76, NULL, NULL, 'Analyn', 'Valdez', 'Rivera'),
(78, 77, NULL, NULL, 'Ruth', '', 'Carpio'),
(79, 77, NULL, NULL, 'Natalie ', 'Flores', 'Valenzuela'),
(80, NULL, NULL, 75, 'Sally', 'Rivera', 'Villamor'),
(93, NULL, NULL, 81, 'Anne', 'Patricia', 'Mendoza'),
(94, 109, NULL, NULL, 'sese', 'sese', 'sse'),
(95, NULL, NULL, 83, 'asa', 'sas', 'asas');

-- --------------------------------------------------------

--
-- Table structure for table `as_ninong_tbl`
--

CREATE TABLE `as_ninong_tbl` (
  `as_ninong_id` int(11) NOT NULL,
  `baptism_id` int(11) DEFAULT NULL,
  `wedding_id` int(11) DEFAULT NULL,
  `confirmation_id` int(11) DEFAULT NULL,
  `as_ninong_firstname` varchar(30) NOT NULL,
  `as_ninong_middlename` varchar(30) DEFAULT NULL,
  `as_ninong_lastname` varchar(30) NOT NULL,
  `as_ninong_suffix` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `as_ninong_tbl`
--

INSERT INTO `as_ninong_tbl` (`as_ninong_id`, `baptism_id`, `wedding_id`, `confirmation_id`, `as_ninong_firstname`, `as_ninong_middlename`, `as_ninong_lastname`, `as_ninong_suffix`) VALUES
(74, 73, NULL, NULL, 'Otto', 'Reyes', 'Agustin', ''),
(75, 73, NULL, NULL, 'Daniel', '', 'Aguirre', 'III'),
(76, NULL, NULL, 73, 'Amelia', '', 'Cortes', ''),
(81, 75, NULL, NULL, 'Charlene ', 'Espiritu', 'Salinas', ''),
(82, 75, NULL, NULL, 'Jeneth ', 'Arcilla', 'Basilio', ''),
(83, 75, NULL, NULL, 'Aurora ', 'Carlos', 'Pasion', ''),
(84, 76, NULL, NULL, 'Aquino', 'Aragon', 'Fernandez', ''),
(85, 76, NULL, NULL, 'Otto', 'Madrid', 'Garcia', ''),
(86, 77, NULL, NULL, 'Joseph', 'Perez', 'Manzano', ''),
(87, NULL, NULL, 75, 'Rafael ', 'Jimenez', 'Cordova', ''),
(100, NULL, NULL, 81, 'Juan', 'Miguel', 'Ramos', ''),
(101, 109, NULL, NULL, 'se', 'sese', 'sesesese', 'seses'),
(102, NULL, NULL, 83, 'asas', 'asa', 'sasas', '');

-- --------------------------------------------------------

--
-- Table structure for table `baptisms`
--

CREATE TABLE `baptisms` (
  `id` int(11) NOT NULL,
  `reservation_user_id` int(11) DEFAULT NULL,
  `baptism_type` enum('Regular','Solo') NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `decline_reason` text DEFAULT NULL,
  `notif` tinyint(1) NOT NULL DEFAULT 0,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rehearsal_date` date DEFAULT NULL,
  `rehearsal_time` time DEFAULT NULL,
  `child_firstname` varchar(30) NOT NULL,
  `child_middlename` varchar(30) DEFAULT NULL,
  `child_lastname` varchar(30) NOT NULL,
  `child_suffix` varchar(30) DEFAULT NULL,
  `child_gender` varchar(6) NOT NULL,
  `child_nat` varchar(12) DEFAULT 'Filipino',
  `birth_date` date NOT NULL,
  `birth_place` varchar(255) NOT NULL,
  `mother_firstname` varchar(30) NOT NULL,
  `mother_middlename` varchar(30) DEFAULT NULL,
  `mother_lastname` varchar(30) NOT NULL,
  `father_firstname` varchar(30) NOT NULL,
  `father_middlename` varchar(30) DEFAULT NULL,
  `father_lastname` varchar(30) NOT NULL,
  `father_suffix` varchar(30) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `married` varchar(3) DEFAULT NULL,
  `marriage_type` varchar(15) DEFAULT 'Single',
  `birth_certificate` varchar(300) DEFAULT NULL,
  `marriage_certificate` varchar(300) DEFAULT NULL,
  `province` varchar(30) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `street_no` varchar(30) DEFAULT NULL,
  `barangay` varchar(30) DEFAULT NULL,
  `presiding_priest` varchar(50) DEFAULT 'Rev. Fr. James Nitollama'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `baptisms`
--

INSERT INTO `baptisms` (`id`, `reservation_user_id`, `baptism_type`, `status`, `decline_reason`, `notif`, `email`, `contact`, `date`, `time`, `created_at`, `rehearsal_date`, `rehearsal_time`, `child_firstname`, `child_middlename`, `child_lastname`, `child_suffix`, `child_gender`, `child_nat`, `birth_date`, `birth_place`, `mother_firstname`, `mother_middlename`, `mother_lastname`, `father_firstname`, `father_middlename`, `father_lastname`, `father_suffix`, `address`, `married`, `marriage_type`, `birth_certificate`, `marriage_certificate`, `province`, `city`, `street_no`, `barangay`, `presiding_priest`) VALUES
(73, 58, 'Regular', 'Done', 'asda', 0, 'joviemb@gmail.com', '9183274283', '2025-12-28', '11:30:00', '2025-12-06 13:36:00', NULL, NULL, 'Mary Joy', 'Magbanua', 'Roldan', NULL, 'Female', 'Filipino', '2025-12-15', 'Caloocan, Metro Manila', 'Jovie', 'Banes', 'Magbanua', 'Mark', 'Cuevas', 'Roldan', NULL, NULL, 'Yes', 'Church Marriage', 'private/sacraments/uploads/birth_certificate_69343140c4b28.png', 'private/sacraments/uploads/marriage_certificate_69343140c4b7f.png', 'Metro Manila', 'Quezon City', '43 Langit Rd', 'Bagong Silang', 'Rev. Fr. James Nitollama'),
(74, 60, 'Regular', 'Done', '', 0, 'serenity@gmail.com', '9186372233', '2025-12-28', '11:30:00', '2025-12-06 16:04:39', NULL, NULL, 'Kyel', 'Lozano', 'Guzman', '', 'Male', 'Filipino', '2025-11-01', 'Caloocan, Metro Manila', 'Serenity', 'Espino', 'Lozano', 'Allen', 'Martinez', 'Guzman', '', NULL, 'No', NULL, 'private/sacraments/uploads/birth_certificate_69345417cfa16.png', NULL, 'Metro Manila', 'Quezon City', '12 Kumintang St.', 'Llano', 'Rev. Fr. James Nitollama'),
(75, 61, 'Solo', 'Done', NULL, 0, 'jragust@gmail.com', '9185738275', '2025-12-24', '08:00:00', '2025-12-06 16:13:13', NULL, NULL, 'Alice', 'Baguio', 'Agustin', '', 'Female', 'Filipino', '2025-12-01', 'Caloocan, Metro Manila', 'Cynthia', 'Torres', 'Baguio', 'Joshua', 'Moreno', 'Agustin', 'Jr', NULL, 'Yes', 'Civil Marriage', 'private/sacraments/uploads/birth_certificate_6934561984e1f.png', 'private/sacraments/uploads/marriage_certificate_6934561984e61.png', 'Metro Manila', 'Quezon City', '681 Sunflower St.', 'Kaybiga', 'Rev. Fr. James Nitollama'),
(76, 62, 'Regular', 'Done', '', 0, 'victoriacara@gmail.com', '9183726183', '2026-01-18', '11:30:00', '2025-12-06 16:40:17', NULL, NULL, 'Erick', '', 'Enriquez', '', 'Male', 'Filipino', '2025-11-25', 'Caloocan, Metro Manila', 'Victoria', 'Cara', 'Enriquez', 'Wayne', 'Mata', 'Borlongan', '', NULL, 'No', NULL, 'private/sacraments/uploads/birth_certificate_69345c716d5e5.png', NULL, 'Metro Manila', 'Quezon City', '121 Damong Maliit St.', 'Tala', 'Rev. Fr. James Nitollama'),
(77, 63, 'Regular', 'Done', NULL, 0, 'mattmagmal@gmail.com', '9621734829', '2026-01-20', '11:30:00', '2025-12-06 16:54:58', NULL, NULL, 'Jomar', 'Aquino', 'Mallari', '', 'Male', 'Filipino', '2025-12-20', 'Caloocan, Metro Manila', 'Riley ', 'Villegas', 'Mallari', 'Matthew', 'Magtuba', 'Mallari', '', NULL, 'Yes', 'Church Marriage', 'private/sacraments/uploads/birth_certificate_69345fe27c152.png', 'private/sacraments/uploads/marriage_certificate_69345fe27c18c.png', 'Metro Manila', 'Quezon City', '15 Diamond St.', 'Bagumbong', 'Rev. Fr. James Nitollama'),
(108, 65, 'Solo', 'Done', NULL, 0, 'blank_ink@gmail.com', '09186477722', '2026-01-05', '09:00:00', '2025-12-25 15:10:45', '2026-01-20', '10:00:00', 'Papot', 'Rosal', 'Pasado', '', 'Female', 'Filipino', '2025-12-01', 'Caloocan, Metro Manila', 'Blessica', 'Rosal', 'Pasado', 'Mueller', 'Tajo', 'Pasado', '', NULL, 'Yes', 'Church Marriage', 'private/sacraments/uploads/birth_certificate_697632754bf5e.png', 'private/sacraments/uploads/marriage_certificate_697632754bfc4.png', 'Metro Manila', 'Quezon City', '12 Tatalon St.', 'Bagumbong', 'Rev. Fr. James Nitollama'),
(109, 65, 'Solo', 'Declined', NULL, 0, 'blank_ink@gmail.com', '09186477722', '2026-02-16', '15:00:00', '2026-01-26 02:36:23', '2026-01-23', '07:06:00', 'kyle', 'eris', 'remponi', 'Jr.', 'Male', 'Filipino', '2026-01-14', 'Quezon CIty', 'Kylie', 'Caer', 'Remponi', 'Carlo', 'maeden', 'Remponi', 'Jr.', NULL, 'Yes', 'Church Marriage', 'private/sacraments/uploads/birth_certificate_6976d32747620.png', 'private/sacraments/uploads/marriage_certificate_6976d327476f4.png', NULL, NULL, NULL, NULL, 'Rev. Fr. James Nitollama'),
(110, 65, 'Regular', 'Cancelled', 'Schedule conflict or fully booked date', 0, NULL, NULL, '2026-02-26', '11:00:00', '2026-01-26 05:12:03', '2026-01-26', '10:00:00', 'Kenneth', 'Gilid', 'Pasion', '', 'Female', 'Filipino', '2026-01-02', 'Caloocan, Metro Manila', 'Erlina', 'Shauna', 'Rivera', 'Victor', 'Victor', 'Garlic', '', NULL, NULL, NULL, 'private/sacraments/uploads/birth_certificate_6976f7a36bfbd.png', NULL, NULL, NULL, NULL, NULL, 'Rev. Fr. James Nitollama'),
(111, 65, 'Regular', 'Accepted', NULL, 0, NULL, NULL, '2026-02-22', '10:00:00', '2026-01-28 06:45:45', '2026-02-06', '10:30:00', 'erlshawn', 'josh', 'rivera', NULL, 'Male', 'Filipino', '2026-01-08', 'Quezon CIty', 'Ma. Jane', 'orcales', 'RIvera', 'Eduard', 'mark', 'RIvera', NULL, NULL, NULL, NULL, 'private/sacraments/uploads/birth_certificate_6979b09923f3f.png', NULL, NULL, NULL, NULL, NULL, 'Rev. Fr. James Nitollama'),
(112, 65, 'Regular', 'Accepted', NULL, 0, 'blank_ink@gmail.com', '09186477722', '2026-02-22', '10:00:00', '2026-01-28 07:29:42', NULL, NULL, 'sasas', 'dad', 'asa', '', 'Male', 'Filipino', '2026-01-12', 'sas', 'j', 'j', 'j', 'j', 'j', 'j', '', NULL, 'No', NULL, 'private/sacraments/uploads/birth_certificate_6979bae633e26.png', NULL, NULL, NULL, NULL, NULL, 'Rev. Fr. James Nitollama'),
(113, 65, 'Solo', 'Pending', NULL, 0, 'blank_ink@gmail.com', '09186477722', '2026-02-18', '08:00:00', '2026-01-28 07:41:12', NULL, NULL, 'Victor', 'Z', 'Garlitos', '', 'Male', 'Filipino', '2026-01-22', 'CALOCAN', 'MAMA', 'PAPA', 'A', 'sas', 'asa', 'sasa', 'sasa', NULL, 'No', NULL, 'private/sacraments/uploads/birth_certificate_6979bd98253f7.pdf', NULL, NULL, NULL, NULL, NULL, 'Rev. Fr. James Nitollama');

-- --------------------------------------------------------

--
-- Table structure for table `calendar_control`
--

CREATE TABLE `calendar_control` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `type` varchar(10) DEFAULT 'Regular',
  `service_type` varchar(20) NOT NULL,
  `time` time DEFAULT NULL,
  `is_enabled` tinyint(1) DEFAULT 1,
  `max_slots` int(11) DEFAULT 15,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `calendar_control`
--

INSERT INTO `calendar_control` (`id`, `date`, `type`, `service_type`, `time`, `is_enabled`, `max_slots`, `note`) VALUES
(1, '2026-01-30', 'Regular', 'Baptism', '11:00:00', 1, 15, NULL),
(2, '2026-01-28', 'Regular', 'Baptism', '12:00:00', 1, 15, NULL),
(3, '2026-02-26', 'Regular', 'Baptism', '11:00:00', 1, 15, NULL),
(4, '2026-02-07', 'Regular', 'Baptism', '11:00:00', 1, 15, NULL),
(5, '2026-02-04', 'Solo', 'Baptism', '13:00:00', 1, 15, ''),
(6, '2026-03-01', 'Solo', 'Baptism', '10:00:00', 1, 15, ''),
(8, '2026-01-14', 'Regular', 'Baptism', '13:00:00', 1, 15, ''),
(9, '2026-02-22', 'Regular', 'First Communion', '10:00:00', 1, 2, ''),
(10, '2026-02-22', 'Regular', 'First Communion', '13:00:00', 1, 2, ''),
(11, '2026-02-22', 'Regular', 'Baptism', '10:00:00', 1, 15, '');

-- --------------------------------------------------------

--
-- Table structure for table `celebratory`
--

CREATE TABLE `celebratory` (
  `id` int(11) NOT NULL,
  `reservation_user_id` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `date` date NOT NULL,
  `time` time NOT NULL,
  `celebration_type` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `decline_reason` text DEFAULT NULL,
  `mass_venue` varchar(20) DEFAULT NULL,
  `province` varchar(30) DEFAULT 'Metro Manila',
  `city` varchar(30) DEFAULT 'Caloocan',
  `street_no` varchar(30) DEFAULT '205 Deparo Rd',
  `barangay` varchar(30) DEFAULT NULL,
  `thanksgiving` text DEFAULT NULL,
  `special_intention` text DEFAULT NULL,
  `celebrant_firstname` varchar(30) DEFAULT NULL,
  `celebrant_middlename` varchar(30) DEFAULT NULL,
  `celebrant_lastname` varchar(30) DEFAULT NULL,
  `celebrant_suffix` varchar(10) DEFAULT NULL,
  `celebrant_birthdate` date DEFAULT NULL,
  `wife_firstname` varchar(30) DEFAULT NULL,
  `wife_middlename` varchar(30) DEFAULT NULL,
  `wife_lastname` varchar(30) DEFAULT NULL,
  `husband_firstname` varchar(30) DEFAULT NULL,
  `husband_middlename` varchar(30) DEFAULT NULL,
  `husband_lastname` varchar(30) DEFAULT NULL,
  `husband_suffix` varchar(10) DEFAULT NULL,
  `wedding_date` date DEFAULT NULL,
  `contact_firstname` varchar(30) DEFAULT NULL,
  `contact_middlename` varchar(30) DEFAULT NULL,
  `contact_lastname` varchar(30) DEFAULT NULL,
  `contact_suffix` varchar(20) DEFAULT NULL,
  `contact_relation` varchar(50) NOT NULL,
  `contact_number` varchar(30) NOT NULL,
  `contact_email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `celebratory`
--

INSERT INTO `celebratory` (`id`, `reservation_user_id`, `status`, `date`, `time`, `celebration_type`, `created_at`, `decline_reason`, `mass_venue`, `province`, `city`, `street_no`, `barangay`, `thanksgiving`, `special_intention`, `celebrant_firstname`, `celebrant_middlename`, `celebrant_lastname`, `celebrant_suffix`, `celebrant_birthdate`, `wife_firstname`, `wife_middlename`, `wife_lastname`, `husband_firstname`, `husband_middlename`, `husband_lastname`, `husband_suffix`, `wedding_date`, `contact_firstname`, `contact_middlename`, `contact_lastname`, `contact_suffix`, `contact_relation`, `contact_number`, `contact_email`) VALUES
(21, 58, 'Pending', '2026-01-30', '03:00:00', 'Birthday', '2026-01-28 04:54:11', NULL, 'At Santa Krus Parish', NULL, NULL, NULL, NULL, NULL, NULL, 'Andrea Nicole', 'Bautista', 'Torres', '', '1993-01-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jovie', 'Banes', 'Magbanua', '', 'Relative', '09183274283', 'joviemb@gmail.com'),
(22, 62, 'Accepted', '2026-03-31', '08:00:00', 'Thanksgiving', '2026-01-28 04:59:55', NULL, 'At Santa Krus Parish', NULL, NULL, NULL, NULL, 'Recovery from cancer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Victoria', 'Cara', 'Enriquez', '', 'Spouse', '09183726183', 'victoriacara@gmail.com'),
(23, 65, 'Accepted', '2026-01-29', '04:00:00', 'Thanksgiving', '2026-01-28 07:56:16', NULL, 'At Santa Krus Parish', NULL, NULL, NULL, NULL, 'wsdsdsdsd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Blessica', 'Rosal', 'Pasado', '', 'Child', '09186477722', 'blank_ink@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `certificate_request`
--

CREATE TABLE `certificate_request` (
  `cert_req_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `certificate_type` varchar(20) DEFAULT NULL,
  `claiming_for` varchar(10) DEFAULT NULL,
  `certificate_owner` varchar(10) NOT NULL,
  `certificate_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `request_reason` varchar(21) DEFAULT NULL,
  `decline_reason` text DEFAULT NULL,
  `pickup_date` date DEFAULT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `request_firstname` varchar(30) NOT NULL,
  `request_middlename` varchar(30) DEFAULT NULL,
  `request_lastname` varchar(30) NOT NULL,
  `request_suffix` varchar(30) DEFAULT NULL,
  `owner_firstname` varchar(30) DEFAULT NULL,
  `owner_middlename` varchar(30) DEFAULT NULL,
  `owner_lastname` varchar(30) DEFAULT NULL,
  `owner_suffix` varchar(30) DEFAULT NULL,
  `id_picture` varchar(300) DEFAULT NULL,
  `owner_id_picture` varchar(300) DEFAULT NULL,
  `authorized_id_picture` varchar(300) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `receipt_picture` varchar(300) DEFAULT NULL,
  `authorization_letter` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificate_request`
--

INSERT INTO `certificate_request` (`cert_req_id`, `user_id`, `certificate_type`, `claiming_for`, `certificate_owner`, `certificate_status`, `request_reason`, `decline_reason`, `pickup_date`, `contact`, `email`, `request_firstname`, `request_middlename`, `request_lastname`, `request_suffix`, `owner_firstname`, `owner_middlename`, `owner_lastname`, `owner_suffix`, `id_picture`, `owner_id_picture`, `authorized_id_picture`, `created_at`, `receipt_picture`, `authorization_letter`) VALUES
(21, 65, 'Baptism', 'other', 'Child', 'Accepted', 'Personal record', NULL, '2026-01-28', '09186477722', 'blank_ink@gmail.com', 'Blessica', 'Rosal', 'Pasado', '', 'Papot', 'Rosal', 'Pasado', '', NULL, 'owner_id_picture_697634a67d7a1.png', 'authorized_id_picture_697634a67d85b.png', '2026-01-25 15:20:06', NULL, NULL),
(22, 65, 'First Communion', 'self', 'Self', 'Cancelled', 'Personal record', 'Requested certificate is not available in parish records', NULL, '09186477722', 'blank_ink@gmail.com', 'Blessica', '', 'Pasado', '', 'Blessica', '', 'Pasado', '', '1id_picture_697846e2c52a9.png', NULL, NULL, '2026-01-27 05:02:26', NULL, NULL),
(26, 58, 'First Communion', 'self', 'Self', 'Accepted', 'Church requirement', NULL, NULL, '09183274283', 'joviemb@gmail.com', 'Jovie', '', 'Magbanua', '', 'Jovie', '', 'Magbanua', '', '1id_picture_69799482d1cb5.png', NULL, NULL, '2026-01-28 04:45:54', NULL, NULL),
(27, 65, 'Baptism', 'self', 'Self', 'Cancelled', 'Personal record', 'Requested certificate is not available in parish records', NULL, '09186477722', 'blank_ink@gmail.com', 'Blessica', '', 'Pasado', '', 'Blessica', '', 'Pasado', '', '1id_picture_6979b55a4bc58.png', NULL, NULL, '2026-01-28 07:06:02', NULL, NULL),
(29, 65, 'Baptism', 'other', 'Child', 'Accepted', 'Personal record', NULL, NULL, '09186477722', 'blank_ink@gmail.com', 'Blessica', 'Rosal', 'Pasado', '', 'sasas', 'dad', 'asa', '', NULL, 'owner_id_picture_6979bb76a2e8f.png', 'authorized_id_picture_6979bb76a74a0.png', '2026-01-28 07:32:06', NULL, NULL),
(30, 65, 'Baptism', 'other', 'Child', 'Accepted', 'Personal record', NULL, '2026-01-29', '09186477722', 'blank_ink@gmail.com', 'Blessica', 'Rosal', 'Pasado', '', 'Victor', 'Z', 'Garlitos', '', NULL, 'owner_id_picture_6979bde3e555d.png', 'authorized_id_picture_6979bde3e5b37.png', '2026-01-28 07:42:27', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `confirmations`
--

CREATE TABLE `confirmations` (
  `id` int(11) NOT NULL,
  `reservation_user_id` int(11) DEFAULT NULL,
  `confirmation_type` enum('Regular','Solo') NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `decline_reason` text DEFAULT NULL,
  `notif` tinyint(1) DEFAULT 0,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time DEFAULT NULL,
  `rehearsal_date` date DEFAULT NULL,
  `rehearsal_time` time DEFAULT NULL,
  `confirm_firstname` varchar(30) NOT NULL,
  `confirm_middlename` varchar(30) DEFAULT NULL,
  `confirm_lastname` varchar(30) NOT NULL,
  `confirm_suffix` varchar(30) DEFAULT NULL,
  `confirm_gender` varchar(6) NOT NULL,
  `confirm_nat` varchar(12) DEFAULT 'Filipino',
  `birth_date` date NOT NULL,
  `birth_place` varchar(255) NOT NULL,
  `mother_firstname` varchar(30) NOT NULL,
  `mother_middlename` varchar(30) DEFAULT NULL,
  `mother_lastname` varchar(30) NOT NULL,
  `father_firstname` varchar(30) NOT NULL,
  `father_middlename` varchar(30) NOT NULL,
  `father_lastname` varchar(30) NOT NULL,
  `father_suffix` varchar(30) DEFAULT NULL,
  `birth_certificate` varchar(300) DEFAULT NULL,
  `baptismal_certificate` varchar(300) DEFAULT NULL,
  `catachesis_program` varchar(300) DEFAULT NULL,
  `presiding_priest` varchar(50) NOT NULL DEFAULT 'Rev. Fr. James Nitollama'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `confirmations`
--

INSERT INTO `confirmations` (`id`, `reservation_user_id`, `confirmation_type`, `status`, `created_at`, `decline_reason`, `notif`, `email`, `contact`, `date`, `time`, `rehearsal_date`, `rehearsal_time`, `confirm_firstname`, `confirm_middlename`, `confirm_lastname`, `confirm_suffix`, `confirm_gender`, `confirm_nat`, `birth_date`, `birth_place`, `mother_firstname`, `mother_middlename`, `mother_lastname`, `father_firstname`, `father_middlename`, `father_lastname`, `father_suffix`, `birth_certificate`, `baptismal_certificate`, `catachesis_program`, `presiding_priest`) VALUES
(73, 58, 'Solo', 'Cancelled', '2026-01-13 17:50:44', NULL, 0, 'joviemb@gmail.com', '9183274283', '2026-01-08', '15:00:00', NULL, NULL, 'Aaron', 'Magbanua', 'Roldan', '', 'Male', 'Filipino', '2017-03-20', 'Caloocan, Metro Manila', 'Jovie', 'Banes', 'Magbanua', 'Mark', 'Cuevas', 'Roldan', '', 'private/sacraments/uploads/birth_certificate_69343cddae784.png', 'private/sacraments/uploads/baptismal_certificate_69343cddae7d5.png', NULL, 'Rev. Fr. James Nitollama'),
(74, 61, 'Solo', 'Accepted', '2026-01-13 17:50:44', NULL, 0, 'jragust@gmail.com', '9185738275', '2026-02-07', '15:00:00', NULL, NULL, 'Kyle ', ' Baguio', 'Agustin', '', 'Male', 'Filipino', '2003-05-20', 'Caloocan, Metro Manila', 'Cynthia', ' Torres', ' Baguio', 'Joshua', ' Moreno', 'Agustin', 'Jr', 'private/sacraments/uploads/birth_certificate_6934582ae26ce.png', 'private/sacraments/uploads/baptismal_certificate_6934582ae2709.png', NULL, 'Rev. Fr. James Nitollama'),
(75, 63, 'Solo', 'Accepted', '2026-01-13 17:50:44', NULL, 0, 'mattmagmal@gmail.com', '9621734829', '2026-01-28', '10:00:00', NULL, NULL, 'Kenneth', ' Aquino', 'Mallari', '', 'Male', 'Filipino', '2015-10-02', 'Caloocan, Metro Manila', 'Riley', ' Villegas', ' Mallari', 'Matthew', ' Magtuba', ' Mallari', '', 'private/sacraments/uploads/birth_certificate_693460b18907c.png', 'private/sacraments/uploads/baptismal_certificate_693460b1890cd.png', NULL, 'Rev. Fr. James Nitollama'),
(81, 59, 'Solo', 'Pending', '2026-01-25 15:40:28', NULL, 0, NULL, '09183274283', '2026-03-03', '13:00:00', NULL, NULL, 'Elton', 'Aquino', 'Rico', NULL, 'Male', 'Filipino', '2014-02-02', 'Metro Manila, Caloocan', 'Mia', 'Lim', 'Rico', 'Jonathan', 'Bancs', 'Rico', NULL, 'private/sacraments/uploads/birth_certificate_6976396c089d8.png', 'private/sacraments/uploads/baptismal_certificate_6976396c08a28.png', NULL, 'Rev. Fr. James Nitollama'),
(83, 65, 'Solo', 'Pending', '2026-01-28 08:00:31', NULL, 0, 'blank_ink@gmail.com', '09186477722', '2026-03-10', '13:00:00', NULL, NULL, 'A', 'A', 'A', '', 'Male', 'Filipino', '2009-02-04', 'A', 'as', 'asa', 'sas', 'asa', 'as', 'asas', '', 'private/sacraments/uploads/birth_certificate_6979c21fb8de1.png', 'private/sacraments/uploads/baptismal_certificate_6979c21fb8e89.png', NULL, 'Rev. Fr. James Nitollama');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `donate_id` int(11) NOT NULL,
  `donor_email` varchar(100) DEFAULT NULL,
  `donor_contact_no` varchar(15) DEFAULT NULL,
  `donation_type` varchar(50) NOT NULL,
  `donation_specified` varchar(100) DEFAULT NULL,
  `donation_cause` varchar(50) NOT NULL,
  `mode_of_payment` varchar(50) NOT NULL,
  `proof_of_donation` varchar(300) DEFAULT NULL,
  `donation_amount` decimal(10,2) DEFAULT NULL,
  `donation_status` varchar(20) DEFAULT 'verified',
  `date_posted` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`donate_id`, `donor_email`, `donor_contact_no`, `donation_type`, `donation_specified`, `donation_cause`, `mode_of_payment`, `proof_of_donation`, `donation_amount`, `donation_status`, `date_posted`) VALUES
(23, '', '9148295738', 'Cash', '', 'Parish Donation', 'Gcash', 'IMG_20251208_140833_6936cdd14f1f2.png', 500.00, 'verified', '2025-12-08'),
(25, '', '', 'Cash', '', 'Parish Donation', 'Onsite', NULL, 500.00, 'verified', '2025-12-08'),
(26, '', '9172849223', 'Cash', '', 'Parish Donation', 'Gcash', 'IMG_20251208_141014_6936ce3602fb2.png', 230.00, 'verified', '2025-12-08'),
(28, '', '', 'Clothes', '', 'Charity', 'Onsite', 'IMG_20251208_142115_6936d0cbc5045.png', 0.00, 'verified', '2025-12-08'),
(31, '', '', 'Goods', '', 'Charity', 'Onsite', 'IMG_20251208_142641_6936d2110f5e8.png', 0.00, 'verified', '2025-12-08'),
(32, '', '', 'Goods', '', 'Charity', 'Onsite', 'IMG_20251208_142651_6936d21b45eb2.png', 0.00, 'verified', '2025-12-08'),
(33, '', '', 'Clothes', '', 'Charity', 'Onsite', 'IMG_20251208_142940_6936d2c48dbbe.png', 0.00, 'verified', '2025-12-08'),
(34, '', '', 'Cash', '', 'Tithes', 'Onsite', NULL, 3000.00, 'verified', '2025-12-08'),
(35, '', '', 'Cash', '', 'Parish Donation', 'Bank Transfer', NULL, 10.00, 'verified', '2025-12-08'),
(36, '', '', 'Cash', '', 'Parish Donation', 'Bank Transfer', NULL, 928.00, 'verified', '2025-12-08'),
(44, NULL, NULL, 'Cash', '', 'Parish Donation', 'GCash', 'private/donation_proof/1769383648_gcash.png', 100.00, 'unverified', '2026-01-26'),
(45, NULL, NULL, 'Cash', '', 'Charity', 'Metrobank', 'private/donation_proof/1769383666_gcash.png', 199.00, 'unverified', '2026-01-26'),
(46, NULL, NULL, 'Cash', '', 'Parish Donations', 'GCash', 'private/donation_proof/1769544829_gcash.png', 100.00, 'unverified', '2026-01-27'),
(48, NULL, NULL, 'Cash', '', 'House Blessing Payment', 'Metrobank', 'private/donation_proof/1769571026_gcash.png', 2000.00, 'verified', '2026-01-28'),
(50, NULL, NULL, 'Cash', '', 'Certificate Payment', 'Metrobank', 'private/donation_proof/1769571185_gcash.png', 50.00, 'verified', '2026-01-28'),
(51, NULL, NULL, 'Cash', '', 'Parish Donation', 'Mass', NULL, 100.00, 'pending', '2026-01-28'),
(52, NULL, NULL, 'Cash', '', 'Parish Donation', 'GCash', '../private/donation_proof/1769573683_gcash.png', 1000.00, 'unverified', '2026-01-28'),
(53, NULL, NULL, 'Cash', '', 'Parish Donation', 'GCash', 'private/donation_proof/1769573745_gcash.png', 200.00, 'unverified', '2026-01-28'),
(54, NULL, NULL, 'Cash', '', 'Parish Donation', 'Metrobank', '/private/donation_proof/1769573770_gcash.png', 1.00, 'verified', '2026-01-28'),
(56, NULL, NULL, 'Cash', '', 'Parish Donation', 'GCash', '1769583309_gcash.png', 1000.00, 'verified', '2026-01-28'),
(57, NULL, NULL, 'Cash', '', 'Parish Donation', 'Mass', NULL, 10000.00, 'pending', '2026-01-28'),
(58, NULL, NULL, 'Cash', '', 'Parish Donation', 'Onsite', NULL, 100.00, 'pending', '2026-01-28'),
(59, NULL, NULL, 'Cash', '', 'Certificate Payment', 'GCash', '1769584794_gcash.png', 50.00, 'verified', '2026-01-28');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `announcement_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `text` text NOT NULL,
  `repeat_type` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `announcement_id`, `date`, `text`, `repeat_type`) VALUES
(7, NULL, '2026-01-06', 'Solemnity of Mary, the Holy Mother of God', 'year'),
(8, NULL, '2026-01-06', 'Memorial of Saints Basil the Great and Gregory Nazianzen', 'year'),
(9, NULL, '2026-02-03', 'Optional Memorial of the Most Holy Name of Jesus', 'year'),
(10, NULL, '2026-01-05', 'Epiphany of the Lord', 'year'),
(11, NULL, '2026-01-12', 'Feast of the Baptism of the Lord', 'year'),
(12, NULL, '2026-02-02', 'Feast of the Presentation of the Lord', 'year'),
(13, NULL, '2026-02-05', 'Memorial of Saint Agatha, Virgin and Martyr', 'year'),
(14, NULL, '2026-02-06', 'Memorial of Saints Paul Miki and Companions, Martyrs', 'year'),
(15, NULL, '2026-02-10', 'Memorial of Saint Scholastica, Virgin', 'year'),
(16, NULL, '2026-02-11', 'Optional Memorial of Our Lady of Lourdes', 'year'),
(17, NULL, '2026-02-22', 'Feast of the Chair of Saint Peter', 'year'),
(18, NULL, '2026-03-05', 'Ash Wednesday – Beginning of Lent', 'year'),
(19, NULL, '2026-03-19', 'Solemnity of Saint Joseph, Spouse of the Blessed Virgin Mary', 'year'),
(20, NULL, '2026-03-25', 'Solemnity of the Annunciation of the Lord', 'year'),
(21, NULL, '2026-04-13', 'Palm Sunday of the Passion of the Lord', 'year'),
(22, NULL, '2026-04-17', 'Holy Thursday – Mass of the Lord’s Supper', 'year'),
(23, NULL, '2026-04-18', 'Good Friday of the Lord’s Passion', 'year'),
(24, NULL, '2026-04-19', 'Holy Saturday – Easter Vigil', 'year'),
(25, NULL, '2026-04-20', 'Easter Sunday – Resurrection of the Lord', 'year'),
(26, NULL, '2026-04-27', 'Divine Mercy Sunday', 'year'),
(27, NULL, '2026-05-01', 'Feast of Saint Joseph the Worker', 'year'),
(28, NULL, '2026-05-03', 'Feast of Saints Philip and James, Apostles', 'year'),
(29, NULL, '2026-05-13', 'Optional Memorial of Our Lady of Fatima', 'year'),
(30, NULL, '2026-05-29', 'Solemnity of the Ascension of the Lord', 'year'),
(31, NULL, '2026-06-08', 'Pentecost Sunday', 'year'),
(32, NULL, '2026-06-09', 'Memorial of Mary, Mother of the Church', 'yearly'),
(33, NULL, '2026-06-15', 'Solemnity of the Most Holy Trinity', 'yearly'),
(34, NULL, '2026-06-19', 'Solemnity of the Most Holy Body and Blood of Christ (Corpus Christi)', 'yearly'),
(35, NULL, '2026-06-29', 'Solemnity of Saints Peter and Paul, Apostles', 'yearly'),
(36, NULL, '2026-08-15', 'Solemnity of the Assumption of the Blessed Virgin Mary', 'yearly'),
(37, NULL, '2026-09-08', 'Feast of the Nativity of the Blessed Virgin Mary', 'yearly'),
(38, NULL, '2026-09-14', 'Feast of the Exaltation of the Holy Cross', 'yearly'),
(39, NULL, '2026-10-01', 'Memorial of Saint Thérèse of the Child Jesus', 'yearly'),
(40, NULL, '2026-10-02', 'Memorial of the Holy Guardian Angels', 'yearly'),
(41, NULL, '2026-10-07', 'Memorial of Our Lady of the Rosary', 'yearly'),
(42, NULL, '2026-11-01', 'Solemnity of All Saints', 'yearly'),
(43, NULL, '2026-11-02', 'Commemoration of All the Faithful Departed (All Souls)', 'yearly'),
(44, NULL, '2026-11-09', 'Feast of the Dedication of the Lateran Basilica', 'yearly'),
(45, NULL, '2026-12-08', 'Solemnity of the Immaculate Conception of the Blessed Virgin Mary', 'yearly'),
(46, NULL, '2026-12-25', 'Solemnity of the Nativity of the Lord (Christmas)', 'yearly');

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
-- Table structure for table `first_communion`
--

CREATE TABLE `first_communion` (
  `id` int(11) NOT NULL,
  `reservation_user_id` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `decline_reason` text DEFAULT NULL,
  `time` time DEFAULT NULL,
  `date` date NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `first_comm_type` enum('Regular','Solo') NOT NULL,
  `notif` tinyint(1) DEFAULT 0,
  `rehearsal_date` date DEFAULT NULL,
  `rehearsal_time` time DEFAULT NULL,
  `child_firstname` varchar(30) NOT NULL,
  `child_middlename` varchar(30) DEFAULT NULL,
  `child_lastname` varchar(30) NOT NULL,
  `child_suffix` varchar(30) DEFAULT NULL,
  `child_gender` varchar(6) DEFAULT NULL,
  `child_nat` varchar(12) DEFAULT 'Filipino',
  `birth_date` date NOT NULL,
  `birth_place` varchar(255) NOT NULL,
  `mother_firstname` varchar(30) NOT NULL,
  `mother_middlename` varchar(30) DEFAULT NULL,
  `mother_lastname` varchar(30) NOT NULL,
  `father_firstname` varchar(30) NOT NULL,
  `father_middlename` varchar(30) NOT NULL,
  `father_lastname` varchar(30) NOT NULL,
  `father_suffix` varchar(30) DEFAULT NULL,
  `baptismal_certificate` varchar(300) DEFAULT NULL,
  `birth_certificate` varchar(300) DEFAULT NULL,
  `presiding_priest` varchar(50) NOT NULL DEFAULT 'Rev. Fr. James Nitollama	'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `first_communion`
--

INSERT INTO `first_communion` (`id`, `reservation_user_id`, `status`, `created_at`, `decline_reason`, `time`, `date`, `contact`, `email`, `first_comm_type`, `notif`, `rehearsal_date`, `rehearsal_time`, `child_firstname`, `child_middlename`, `child_lastname`, `child_suffix`, `child_gender`, `child_nat`, `birth_date`, `birth_place`, `mother_firstname`, `mother_middlename`, `mother_lastname`, `father_firstname`, `father_middlename`, `father_lastname`, `father_suffix`, `baptismal_certificate`, `birth_certificate`, `presiding_priest`) VALUES
(5, 61, 'Accepted', '2026-01-13 17:54:47', NULL, '15:00:00', '2026-07-11', '9185738275', 'jragust@gmail.com', 'Solo', 0, NULL, NULL, 'Kyle', ' Baguio', 'Agustin', '', NULL, NULL, '2014-12-09', 'Caloocan, Metro Manila', 'Cynthia', ' Torres', ' Baguio', 'Joshua', 'Moreno', 'Agustin', 'Jr', 'private/sacraments/uploads/baptismal_certificate_693456f18ec80.png', 'private/sacraments/uploads/birth_certificate_693456f18ecb9.png', 'Rev. Fr. James Nitollama	'),
(22, 63, 'Accepted', '2026-01-25 15:46:46', NULL, '13:00:00', '2026-05-03', '09621734829', 'mattmagmal@gmail.com', 'Solo', 0, '2026-01-30', '10:00:00', 'Ilyana', 'Aquino', 'Mallari', '', 'Female', NULL, '2017-02-20', 'Caloocan, Metro Manila', 'Riley', 'Villegas', 'Mallari', 'Matthew', 'Magtuba', 'Mallari', '', 'private/sacraments/uploads/baptismal_certificate_69763ae67c524.png', 'private/sacraments/uploads/birth_certificate_69763ae67c575.png', 'Rev. Fr. James Nitollama	'),
(25, 58, 'Accepted', '2026-01-28 04:50:49', NULL, '13:00:00', '2026-02-22', '09183274283', 'joviemb@gmail.com', 'Solo', 0, NULL, NULL, 'Louisa', 'Pineda', 'Magbanua', '', 'Female', NULL, '2015-02-02', 'Caloocan, Metro Manila', 'Janice', 'Lou', 'Magbanua', 'Jovie ', 'Banes', 'Magbanua', '', 'private/sacraments/uploads/baptismal_certificate_697995a9a994f.png', 'private/sacraments/uploads/birth_certificate_697995a9a99d3.png', 'Rev. Fr. James Nitollama	');

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
  `reservation_user_id` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `decline_reason` text DEFAULT NULL,
  `mass_venue` varchar(30) NOT NULL,
  `deceased_firstname` varchar(100) DEFAULT NULL,
  `deceased_middlename` varchar(100) DEFAULT NULL,
  `deceased_lastname` varchar(100) DEFAULT NULL,
  `deceased_suffix` varchar(20) DEFAULT NULL,
  `funeral_date` date NOT NULL,
  `funeral_time` time NOT NULL,
  `funeral_address` varchar(255) DEFAULT NULL,
  `contact_person_firstname` varchar(30) NOT NULL,
  `contact_person_middlename` varchar(30) DEFAULT NULL,
  `contact_person_lastname` varchar(30) NOT NULL,
  `contact_person_suffix` varchar(30) DEFAULT NULL,
  `contact_person_relation` varchar(100) NOT NULL,
  `contact_person_contact_no` varchar(20) NOT NULL,
  `contact_person_email` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cause_of_death` varchar(255) DEFAULT NULL,
  `presider` varchar(255) DEFAULT 'Rev. Fr. James Nitollama',
  `province` varchar(30) DEFAULT 'Metro Manila',
  `city` varchar(30) DEFAULT 'Caloocan',
  `street_no` varchar(30) DEFAULT '205 Deparo Rd',
  `barangay` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `funerals`
--

INSERT INTO `funerals` (`id`, `reservation_user_id`, `status`, `decline_reason`, `mass_venue`, `deceased_firstname`, `deceased_middlename`, `deceased_lastname`, `deceased_suffix`, `funeral_date`, `funeral_time`, `funeral_address`, `contact_person_firstname`, `contact_person_middlename`, `contact_person_lastname`, `contact_person_suffix`, `contact_person_relation`, `contact_person_contact_no`, `contact_person_email`, `created_at`, `cause_of_death`, `presider`, `province`, `city`, `street_no`, `barangay`) VALUES
(6, 58, 'Done', NULL, '', 'Sophiel ', 'Banes', 'Trinidad', '', '2025-12-07', '02:00:00', NULL, 'Jovie', 'Banes', 'Magbanua', '', 'Friend', '9183274283', 'joviemb@gmail.com', '2025-12-06 14:45:44', 'Lung Cancer', NULL, 'Metro Manila', 'Quezon City', '681 Sunflower St.', 'Llano'),
(7, 59, 'Done', NULL, '', 'Amy', 'Calderon', 'Quiambao', '', '2026-01-01', '17:00:00', NULL, 'Bryan', 'Lim', 'Quiambao', '', 'Relative', '9621937285', 'quia_ryan@gmail.com', '2025-12-06 15:55:03', 'Car Crash', NULL, 'Metro Manila', 'Quezon City', '8 Jupiter St.', 'Bagumbong'),
(8, 62, 'Done', NULL, '', 'Sylvia', 'Asis', 'Enriquez', '', '2025-12-13', '12:00:00', NULL, 'Victoria', 'Cara', 'Enriquez', '', 'Parent', '9183726183', 'victoriacara@gmail.com', '2025-12-06 16:47:55', 'Pneumonia', NULL, 'Metro Manila', 'Quezon City', '22 Jupiter St.', 'Bagumbong'),
(13, 63, 'Accepted', NULL, 'At Home', 'Joshua', '', 'Mallari', '', '2026-01-30', '03:00:00', NULL, 'Matthew', 'Magtuba', 'Mallari', '', 'Sibling', '09621734829', 'mattmagmal@gmail.com', '2026-01-27 05:22:03', 'Drowning', 'Rev. Fr. James Nitollama', 'Metro Manila', 'Quezon City', '34 Mango Grove', 'Bagong Silang');

-- --------------------------------------------------------

--
-- Table structure for table `guardian_tbl`
--

CREATE TABLE `guardian_tbl` (
  `guardian_id` int(11) NOT NULL,
  `baptism_id` int(11) DEFAULT NULL,
  `first_comm_id` int(11) DEFAULT NULL,
  `confirmation_id` int(11) DEFAULT NULL,
  `guardian_firstname` varchar(30) NOT NULL,
  `guardian_middlename` varchar(30) DEFAULT NULL,
  `guardian_lastname` varchar(30) NOT NULL,
  `guardian_suffix` varchar(30) DEFAULT NULL,
  `guardian_type` varchar(30) DEFAULT NULL,
  `guardian_address` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guardian_tbl`
--

INSERT INTO `guardian_tbl` (`guardian_id`, `baptism_id`, `first_comm_id`, `confirmation_id`, `guardian_firstname`, `guardian_middlename`, `guardian_lastname`, `guardian_suffix`, `guardian_type`, `guardian_address`) VALUES
(8, NULL, NULL, NULL, 'Blessica', 'Rosal', 'Pasado', '', 'Mother', NULL),
(12, NULL, NULL, 81, 'Bryan', 'Lim', 'Quiambao', '', 'Uncle', NULL),
(13, 110, NULL, NULL, 'Blessica', 'Rosal', 'Pasado', '', 'Aunt', NULL),
(14, 111, NULL, NULL, 'Blessica', 'Rosal', 'Pasado', '', 'Aunt', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `house_blessings`
--

CREATE TABLE `house_blessings` (
  `id` int(11) NOT NULL,
  `reservation_user_id` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `date` date NOT NULL,
  `time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `decline_reason` text DEFAULT NULL,
  `province` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `street_no` varchar(100) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `notes` varchar(100) NOT NULL,
  `special_intention` text DEFAULT NULL,
  `requesting_firstname` varchar(30) DEFAULT NULL,
  `requesting_middlename` varchar(30) DEFAULT NULL,
  `requesting_lastname` varchar(30) DEFAULT NULL,
  `requesting_suffix` varchar(10) DEFAULT NULL,
  `requesting_contact` varchar(30) NOT NULL,
  `requesting_email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `house_blessings`
--

INSERT INTO `house_blessings` (`id`, `reservation_user_id`, `status`, `date`, `time`, `created_at`, `decline_reason`, `province`, `city`, `street_no`, `barangay`, `notes`, `special_intention`, `requesting_firstname`, `requesting_middlename`, `requesting_lastname`, `requesting_suffix`, `requesting_contact`, `requesting_email`) VALUES
(13, 58, 'Pending', '2026-01-31', '17:00:00', '2026-01-28 04:55:19', NULL, 'Metro Manila', 'Caloocan', '22 Tatalon St.', 'Bagong Silang', 'Newly Built House', NULL, 'Jovie', 'Banes', 'Magbanua', '', '09183274283', 'joviemb@gmail.com'),
(14, 62, 'Accepted', '2026-02-28', '12:00:00', '2026-01-28 04:58:47', NULL, 'Metro Manila', 'Caloocan', '10 Junio St.', 'Bagumbong', 'Newly Built House', NULL, 'Victoria', 'Cara', 'Enriquez', '', '09183726183', 'victoriacara@gmail.com'),
(15, 61, 'Pending', '2026-03-27', '08:00:00', '2026-01-28 05:00:56', NULL, 'Metro Manila', 'Quezon City', '15 Chihuahua St.', 'Kaligayahan', 'Newly Built House', NULL, 'Joshua', 'Moreno', 'Agustin', '', '09185738275', 'jragust@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `live_streams`
--

CREATE TABLE `live_streams` (
  `id` int(11) NOT NULL,
  `embed` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `live_streams`
--

INSERT INTO `live_streams` (`id`, `embed`, `created_at`) VALUES
(1, '<iframe src=\"https://www.facebook.com/plugins/video.php?height=314&href=https%3A%2F%2Fwww.facebook.com%2FDioNova.StaKrusParish%2Fvideos%2F25550228207983016%2F&show_text=false&width=560&t=0\" width=\"560\" height=\"314\" style=\"border:none;overflow:hidden\" scrolling=\"no\" frameborder=\"0\" allowfullscreen=\"true\" allow=\"autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share\" allowFullScreen=\"true\"></iframe>', '2026-01-28 05:04:50');

-- --------------------------------------------------------

--
-- Table structure for table `mass_schedule`
--

CREATE TABLE `mass_schedule` (
  `id` int(11) NOT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `mass_time` time NOT NULL,
  `priest_name` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `time_in` time NOT NULL,
  `time_out` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mass_schedule`
--

INSERT INTO `mass_schedule` (`id`, `day`, `mass_time`, `priest_name`, `status`, `time_in`, `time_out`) VALUES
(117, 'Monday', '06:00:00', NULL, 'active', '06:00:00', '07:00:00'),
(118, 'Tuesday', '06:00:00', NULL, 'active', '06:00:00', '07:00:00'),
(119, 'Wednesday', '06:00:00', NULL, 'active', '06:00:00', '07:00:00'),
(120, 'Thursday', '06:00:00', NULL, 'active', '06:00:00', '07:00:00'),
(121, 'Friday', '06:00:00', NULL, 'active', '06:00:00', '07:00:00'),
(122, 'Saturday', '06:00:00', NULL, 'active', '06:00:00', '07:00:00'),
(123, 'Sunday', '05:30:00', NULL, 'active', '05:30:00', '06:30:00'),
(124, 'Sunday', '07:30:00', NULL, 'active', '07:30:00', '08:30:00'),
(125, 'Sunday', '10:30:00', NULL, 'active', '10:30:00', '11:30:00'),
(126, 'Sunday', '03:30:00', NULL, 'active', '03:30:00', '04:30:00'),
(127, 'Sunday', '17:30:00', NULL, 'active', '17:30:00', '18:30:00'),
(128, 'Sunday', '15:30:00', NULL, 'active', '15:30:00', '16:30:00'),
(129, 'Tuesday', '18:00:00', NULL, 'active', '18:00:00', '19:00:00'),
(131, 'Thursday', '18:00:00', NULL, 'active', '18:00:00', '19:00:00'),
(132, 'Friday', '18:00:00', NULL, 'active', '18:00:00', '19:00:00'),
(133, 'Saturday', '18:00:00', NULL, 'active', '18:00:00', '19:00:00'),
(134, 'Wednesday', '14:00:00', NULL, 'active', '14:00:00', '15:00:00'),
(135, 'Monday', '03:43:00', NULL, 'active', '03:43:00', '03:43:00');

-- --------------------------------------------------------

--
-- Table structure for table `ninang_tbl`
--

CREATE TABLE `ninang_tbl` (
  `ninang_id` int(11) NOT NULL,
  `baptism_id` int(11) DEFAULT NULL,
  `wedding_id` int(11) DEFAULT NULL,
  `confirmation_id` int(11) DEFAULT NULL,
  `ninang_firstname` varchar(30) NOT NULL,
  `ninang_middlename` varchar(30) DEFAULT NULL,
  `ninang_lastname` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ninang_tbl`
--

INSERT INTO `ninang_tbl` (`ninang_id`, `baptism_id`, `wedding_id`, `confirmation_id`, `ninang_firstname`, `ninang_middlename`, `ninang_lastname`) VALUES
(17, 73, NULL, NULL, 'Rosa', '', 'Gaspar'),
(18, 73, NULL, NULL, 'Rosa', 'Pineda', 'Gaspar'),
(19, 73, NULL, NULL, 'Rosa', NULL, 'Gaspar'),
(20, NULL, NULL, 73, 'Michelle', 'Abella', 'Libatique'),
(23, 74, NULL, NULL, 'Ashley', 'Cinco', 'Sagun'),
(24, 75, NULL, NULL, 'Skylar', 'Romano', 'Lim'),
(25, NULL, NULL, 74, 'Miles', 'Palahniuk', 'de Leon'),
(27, 76, NULL, NULL, 'Amelie', 'Solis', 'Aquino'),
(28, 77, NULL, NULL, 'Aries ', 'Sales', 'Gonzaga'),
(29, NULL, NULL, 75, 'Jessa', 'Martin', 'Palma'),
(70, 108, NULL, NULL, 'Veronica', 'Torres', 'Esguerra'),
(71, NULL, NULL, 81, 'Maria Angela', 'Reyes', 'Cruz'),
(72, NULL, NULL, 81, 'Maria Angela', 'Reyes', 'Cruz'),
(73, 109, NULL, NULL, '2323', 'seese', '2323'),
(75, 110, NULL, NULL, 'ewan', 'ewan', 'ewan'),
(76, 111, NULL, NULL, 'Jerimia', 'shane', 'Martinez'),
(77, 111, NULL, NULL, 'Jerimia', 'shane', 'Martinez'),
(78, 112, NULL, NULL, 'h', 'h', 'h'),
(79, 113, NULL, NULL, 'asa', 'asa', 'sa'),
(80, NULL, NULL, 83, 'asas', 'saas', 'asa');

-- --------------------------------------------------------

--
-- Table structure for table `ninong_tbl`
--

CREATE TABLE `ninong_tbl` (
  `ninong_id` int(11) NOT NULL,
  `baptism_id` int(11) DEFAULT NULL,
  `wedding_id` int(11) DEFAULT NULL,
  `confirmation_id` int(11) DEFAULT NULL,
  `ninong_firstname` varchar(30) NOT NULL,
  `ninong_middlename` varchar(30) DEFAULT NULL,
  `ninong_lastname` varchar(30) NOT NULL,
  `ninong_suffix` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ninong_tbl`
--

INSERT INTO `ninong_tbl` (`ninong_id`, `baptism_id`, `wedding_id`, `confirmation_id`, `ninong_firstname`, `ninong_middlename`, `ninong_lastname`, `ninong_suffix`) VALUES
(25, 73, NULL, NULL, 'John Rey', '', 'Cariño', ''),
(26, 73, NULL, NULL, 'John Rey', NULL, 'Cariño', NULL),
(27, 73, NULL, NULL, 'John Rey', 'Pineda', 'Cariño', NULL),
(28, NULL, NULL, 73, 'Dexter', 'Apostol', 'Molina', ''),
(31, 74, NULL, NULL, 'Ricardo', 'Viray', 'Uy', ''),
(32, 75, NULL, NULL, 'Murphy', 'Angeles', 'Robles', ''),
(33, NULL, NULL, 74, 'Bebang', 'Garcia', 'Cabrera', ''),
(35, 76, NULL, NULL, 'Charles', 'Solis', 'Buenaventura', ''),
(36, 77, NULL, NULL, 'Ernie', 'Baltazar', 'Sotto', ''),
(37, NULL, NULL, 75, 'Charlie ', '', 'Valdez', ''),
(78, 108, NULL, NULL, 'Caelan', '', 'Cinco', ''),
(79, NULL, NULL, 81, 'Daniel', ' Enrique', 'Flores', ''),
(80, NULL, NULL, 81, 'Daniel', 'Enrique', 'Flores', NULL),
(81, 109, NULL, NULL, '2323', '2323', '2323', ''),
(83, 110, NULL, NULL, 'ewan', 'ewan', 'ewan', ''),
(84, 111, NULL, NULL, 'Jerome', 'Carlo', 'Rivera', ''),
(85, 111, NULL, NULL, 'Jerome', 'Carlo', 'Rivera', NULL),
(86, 112, NULL, NULL, 'h', 'h', 'h', 'h'),
(87, 113, NULL, NULL, 'sa', 'sas', 'sa', 'as'),
(88, NULL, NULL, 83, 'sa', 'asas', 'asas', '');

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
  `created_at` datetime DEFAULT current_timestamp(),
  `category` enum('sacrament','service','general') DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`, `category`) VALUES
(360, 62, 'House Blessing Service Payment', 'Thank you for allowing us to serve you through our church. We kindly ask for a payment of at least ₱2000 for priest\'s service stipend.<br><br>\n        You can make your payment via <a href=\"http://localhost/stkportal_src/src/public/service_payment.php\">GCash</a> or at the church office. God bless!', 'service_payment', 0, '2026-01-28 12:58:57', 'general'),
(361, 62, 'Celebratory Mass Request Submitted', 'Good day, Victoria! We have received your Celebratory Mass request (Thanksgiving) for Recovery from cancer. Your request is currently under review by the parish office. You will be notified once it has been approved. Thank you and God bless!', 'successful_request', 0, '2026-01-28 12:59:55', 'general'),
(362, 61, 'House Blessing Request Submitted', 'Good day, Joshua! We have received your House Blessing request scheduled on 2026-03-27 at 08:00 located at 15 Chihuahua St., Kaligayahan, Quezon City, Metro Manila. Your request is currently under review by the parish office. You will be notified once it has been approved. Thank you and God bless!', 'successful_request', 0, '2026-01-28 13:00:56', 'general'),
(363, 65, 'Baptism Reservation Submitted', 'Good day, Blessica! We’ve received your request for baptism for erlshawn josh rivera, and it’s currently being reviewed. We’ll get back to you soon. Thank you and God bless!', 'successful_request', 0, '2026-01-28 14:45:45', 'general'),
(364, 65, 'Baptism Request Accepted', 'Good day! We\'re happy to let you know that your baptism request for erlshawn rivera has been accepted. Stay tuned for more details on the baptismal rehearsal schedule.<br><br>If you have any questions or need more info, don’t hesitate to reach out or drop by the church office. We look forward to celebrating this special moment with you! Thank you and God bless!', 'reservation_accepted', 0, '2026-01-28 14:50:58', 'general'),
(365, 65, 'Rehearsal Scheduled', 'Good day! This is to inform you that the rehearsal for erlshawn josh rivera \'s Baptism has been scheduled for February 06, 2026 at 10:30 AM. Kindly arrive on time to avoid any delays. We appreciate your cooperation. If you have any questions, feel free to contact or visit the church office. Thank you and God bless!', 'rehearsal_schedule', 0, '2026-01-28 15:00:29', 'general'),
(366, 65, 'Donation Reminder', 'Thank you for allowing us to serve you through our church. To help cover the costs of the Baptism service, we kindly ask for a donation of at least ₱500. You can make your donation via <a href=\"http://localhost/stkportal_src/src/public/donation_reservation.php\">GCash</a> or at the church office. God bless!', 'donation_reminder', 0, '2026-01-28 15:00:29', 'general'),
(367, 65, 'Certificate Request Submitted', 'Good day, Blessica! We have received your request for a Baptism certificate for Blessica Pasado. Your request is currently under review. You will be notified once it is approved for pickup. Thank you and God bless!', 'successful_request', 0, '2026-01-28 15:06:02', 'general'),
(368, 65, 'Certificate Request Update', 'Your certificate request for Blessica  Pasado  is now marked as Accepted.', 'certificate_update', 0, '2026-01-28 15:06:21', 'general'),
(369, 65, 'Certificate Request Update', 'Your certificate request for Blessica  Pasado  is now marked as Accepted.', 'certificate_update', 0, '2026-01-28 15:06:37', 'general'),
(370, 65, 'Certificate Pick-up Date Set', 'Good day! Your pick up date for <strong>Blessica  Pasado </strong>\'s <strong>Baptism</strong> has been set to <strong>January 30, 2026</strong>. You may visit the parish at that that date for the releasing of the certificate.<br><br>Please submit a payment of ₱50 for the release of the certificate. You may pay via <a href=\"http://localhost/stkportal_src/src/public/certificate_payment.php\">GCash</a> or at the church office. God Bless!', 'certificate_pickup_set', 0, '2026-01-28 15:06:37', 'general'),
(371, 58, 'Certificate Request Update', 'Your certificate request for Jovie  Magbanua  is now marked as Accepted.', 'certificate_update', 0, '2026-01-28 15:06:53', 'general'),
(372, 65, 'Certificate Request Update', 'Your certificate request for Blessica  Pasado  is now marked as Cancelled. Reason: Requested certificate is not available in parish records', 'certificate_update', 0, '2026-01-28 15:07:27', 'general'),
(373, 65, 'Certificate Request Submitted', 'Good day, Blessica! We have received your request for a Baptism certificate for victor z Garlitos. Your request is currently under review. You will be notified once it is approved for pickup. Thank you and God bless!', 'successful_request', 0, '2026-01-28 15:14:37', 'general'),
(374, 65, 'Certificate Request Update', 'Your certificate request for victor z Garlitos  is now marked as Accepted.', 'certificate_update', 0, '2026-01-28 15:14:48', 'general'),
(375, 65, 'Certificate Request Update', 'Your certificate request for victor z Garlitos  is now marked as Accepted.', 'certificate_update', 1, '2026-01-28 15:15:03', 'general'),
(376, 65, 'Certificate Pick-up Date Set', 'Good day! Your pick up date for <strong>victor z Garlitos </strong>\'s <strong>Baptism</strong> has been set to <strong>January 31, 2026</strong>. You may visit the parish at that that date for the releasing of the certificate.<br><br>Please submit a payment of ₱50 for the release of the certificate. You may pay via <a href=\"http://localhost/stkportal_src/src/public/certificate_payment.php\">GCash</a> or at the church office. God Bless!', 'certificate_pickup_set', 1, '2026-01-28 15:15:03', 'general'),
(377, 65, 'Baptism Reservation Submitted', 'Good day, Blessica! We’ve received your request for baptism for sasas dad asa, and it’s currently being reviewed. We’ll get back to you soon. Thank you and God bless!', 'successful_request', 0, '2026-01-28 15:29:42', 'general'),
(378, 65, 'Baptism Request Accepted', 'Good day! We\'re happy to let you know that your baptism request for sasas asa has been accepted. Stay tuned for more details on the baptismal rehearsal schedule.<br><br>If you have any questions or need more info, don’t hesitate to reach out or drop by the church office. We look forward to celebrating this special moment with you! Thank you and God bless!', 'reservation_accepted', 0, '2026-01-28 15:30:59', 'general'),
(379, 65, 'Certificate Request Submitted', 'Good day, Blessica! We have received your request for a Baptism certificate for sasas dad asa. Your request is currently under review. You will be notified once it is approved for pickup. Thank you and God bless!', 'successful_request', 0, '2026-01-28 15:32:06', 'general'),
(380, 65, 'Certificate Request Update', 'Your certificate request for sasas dad asa  is now marked as Accepted.', 'certificate_update', 0, '2026-01-28 15:32:45', 'general'),
(381, 65, 'Baptism Reservation Submitted', 'Good day, Blessica! We’ve received your request for baptism for Victor Z Garlitos, and it’s currently being reviewed. We’ll get back to you soon. Thank you and God bless!', 'successful_request', 0, '2026-01-28 15:41:12', 'general'),
(382, 65, 'Certificate Request Submitted', 'Good day, Blessica! We have received your request for a Baptism certificate for Victor Z Garlitos. Your request is currently under review. You will be notified once it is approved for pickup. Thank you and God bless!', 'successful_request', 0, '2026-01-28 15:42:27', 'general'),
(383, 65, 'Certificate Request Update', 'Your certificate request for Victor Z Garlitos  is now marked as Accepted.', 'certificate_update', 0, '2026-01-28 15:43:24', 'general'),
(384, 65, 'Certificate Request Update', 'Your certificate request for Victor Z Garlitos  is now marked as Accepted.', 'certificate_update', 0, '2026-01-28 15:43:30', 'general'),
(385, 65, 'Certificate Pick-up Date Set', 'Good day! Your pick up date for <strong>Victor Z Garlitos </strong>\'s <strong>Baptism</strong> has been set to <strong>January 29, 2026</strong>. You may visit the parish at that that date for the releasing of the certificate.<br><br>Please submit a payment of ₱50 for the release of the certificate. You may pay via <a href=\"http://localhost/stkportal_src/src/public/certificate_payment.php\">GCash</a> or at the church office. God Bless!', 'certificate_pickup_set', 0, '2026-01-28 15:43:30', 'general'),
(386, 62, 'Celebratory Mass Request Accepted', 'Good day! Your celebratory mass request for <strong>Thanksgiving</strong> has been accepted.<br><br>If you have questions, feel free to contact or visit the church office. Thank you and God bless!', 'reservation_accepted', 0, '2026-01-28 15:46:12', 'general'),
(387, 65, 'Celebratory Mass Request Submitted', 'Good day, Blessica! We have received your Celebratory Mass request (Thanksgiving) for wsdsdsdsd. Your request is currently under review by the parish office. You will be notified once it has been approved. Thank you and God bless!', 'successful_request', 0, '2026-01-28 15:56:16', 'general'),
(388, 65, 'Celebratory Mass Request Accepted', 'Good day! Your celebratory mass request for <strong>Thanksgiving</strong> has been accepted.<br><br>If you have questions, feel free to contact or visit the church office. Thank you and God bless!', 'reservation_accepted', 0, '2026-01-28 15:56:33', 'general'),
(389, 65, 'Confirmation Reservation Submitted', 'Good day, Blessica! We’ve received your request for confirmation for A A A, and it’s currently being reviewed. We’ll get back to you soon. Thank you and God bless!', 'successful_request', 0, '2026-01-28 16:00:31', 'general'),
(390, 58, 'First Communion Request Accepted', 'Good day! We\'re happy to let you know that your first communion request for Louisa Magbanua has been accepted. Stay tuned for more details on the first communion rehearsal schedule.<br><br>If you have any questions or need more info, don’t hesitate to reach out or drop by the church office. We look forward to celebrating this special moment with you! Thank you and God bless!', 'reservation_accepted', 0, '2026-01-28 16:01:26', 'general');

-- --------------------------------------------------------

--
-- Table structure for table `prayer_requests`
--

CREATE TABLE `prayer_requests` (
  `id` int(11) NOT NULL,
  `status` varchar(30) DEFAULT NULL,
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

INSERT INTO `prayer_requests` (`id`, `status`, `name`, `username`, `email`, `request_text`, `shared_public`, `intention`, `anonymous`, `created_at`, `visibility`, `time`, `date`) VALUES
(5, 'published', NULL, NULL, NULL, 'please help me pass my exams thank you lord', 1, 'special', '1', '2025-11-19 23:24:03', NULL, NULL, NULL),
(6, 'private', NULL, NULL, NULL, 'lord please pauwiin mo na aso ko miss ko na sya i hope ligtas sya lord thank you', 0, 'special', '1', '2025-11-19 23:26:29', NULL, NULL, NULL),
(7, 'published', 'josie', NULL, 'jos@gmail.com', 'please guide me in passing my exams', 1, 'special', '0', '2025-11-19 23:27:10', NULL, NULL, NULL),
(10, 'private', NULL, NULL, NULL, 'sana bumait na si Shanelle', 1, 'soul', '1', '2025-11-26 23:20:48', NULL, NULL, NULL),
(12, 'published', 'Jovie Magbanua', NULL, 'joviemb@gmail.com', 'asking for prayer i want to spread the gospel plant seeds and cast out devils in Jesus name thank you God bless you all', 1, 'soul', '0', '2025-12-06 12:34:23', NULL, NULL, NULL),
(13, 'published', 'Bryan Quiambao', NULL, 'quia_ryan@gmail.com', 'Prayer for Our Family\r\nHeavenly Father, bless my family with love, unity, and good health. Guide our hearts with faith, protect us from harm, and fill our home with peace and understanding always. Amen.', 1, 'special', '0', '2025-12-06 15:34:07', NULL, NULL, NULL),
(14, 'published', 'Adang Family', NULL, 'adang@gmail.com', 'Family and friends, we humbly ask for your prayers for our beloved sister. She is going through a difficult time, and our hearts are heavy but we believe in a God who heals, strengthens, and comforts.', 1, 'special', '0', '2025-12-06 15:34:59', NULL, NULL, NULL),
(22, 'published', 'Blessica Pasado', NULL, 'blank_ink@gmail.com', 'Prayer for Our Family Heavenly Father, bless my family with love, unity, and good health. Guide our hearts with faith, protect us from harm, and fill our home with peace and understanding always. Amen.Prayer for Our Family Heavenly Father, bless my family with love, unity, and good health. Guide our hearts with faith, protect us from harm, Prayer for Our Family Heavenly Father, bless my family with love, unity, and good health. Guide our hearts with faith, protect us from harm, and fill our home', 1, 'thanksgiving', '0', '2026-01-10 03:08:11', NULL, NULL, NULL),
(25, 'private', 'victor', NULL, 'victorgarlits01@gmail.com', 'Sana Gumaling si paw para ma ka defence sya', 0, 'special', '0', '2026-01-26 01:33:16', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reschedule_requests`
--

CREATE TABLE `reschedule_requests` (
  `id` int(11) NOT NULL,
  `sacrament` varchar(40) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `change_type` enum('Service','Rehearsal','Canon Interview','Pre-Cana') NOT NULL,
  `date_current` date DEFAULT NULL,
  `time_current` time DEFAULT NULL,
  `requested_date` date DEFAULT NULL,
  `requested_time` time DEFAULT NULL,
  `reason` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reschedule_requests`
--

INSERT INTO `reschedule_requests` (`id`, `sacrament`, `reference_id`, `user_id`, `change_type`, `date_current`, `time_current`, `requested_date`, `requested_time`, `reason`, `status`, `created_at`) VALUES
(1, 'First Communion', 24, 65, 'Service', '2026-02-22', '13:00:00', '2026-01-22', '10:00:00', 'dunno', 'Pending', '2026-01-27 18:45:45');

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
  `user_type` enum('User','Admin','Priest') NOT NULL DEFAULT 'User',
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
  `photo` varchar(300) DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verify_token_hash` varchar(255) DEFAULT NULL,
  `verify_token_expires` datetime DEFAULT NULL,
  `reset_token_hash` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_type`, `created_at`, `username`, `password`, `givenname`, `middlename`, `lastname`, `gender`, `suffix`, `full_name`, `email`, `contact`, `birthday`, `photo`, `email_verified`, `verify_token_hash`, `verify_token_expires`, `reset_token_hash`, `reset_token_expires`) VALUES
(39, 'Admin', '2025-12-08 11:01:43', 'stakrusparish', '$2y$10$D1KpMxJFBWP/UCtDIoqF7OKz56oaiOx2PNEabUkRZSGH0tgp.1Hbi', 'Kristan', 'Samson', 'Pepito', 'Male', '', NULL, 'stakrusparish@gmail.com', '09617489918', '2001-12-09', NULL, 1, NULL, NULL, NULL, NULL),
(40, 'Admin', '2025-11-12 05:18:40', 'pauiee', '$2y$10$rTkl4wT.5Tm066MGygLBeug11XCoPbI2YmEqfcmeQT3ZxmXD4XDNa', 'Pauline Jean', 'Garcia', 'Pasion', 'Female', '', 'Pauline Jean Garcia Pasion', 'pauiee@gmail.com', '09621974275', '2001-09-02', 'profilepics/maw.png', 1, NULL, NULL, NULL, NULL),
(41, 'Admin', '2025-11-12 07:01:06', 'ehrily', '$2y$10$GTcwx5d7Dw4nread/F/eP.mbEf2AbI3x7zH/aQ5bJQm.B.DsO1J4W', 'Ericka', 'Sabile', 'Fenis', 'Female', '', 'Ericka  Fenis', 'ehrily@gmail.com', '09123859933', '2001-04-13', '', 1, NULL, NULL, NULL, NULL),
(44, 'Admin', '2025-11-19 13:08:23', 'vicGar', '$2y$10$lB8WUQ4uL3PSG0NAxXki6.a5kmcCEawCHTiiV9rLzoJuqTMsIBPPO', 'Victor', 'Zabala', 'Garlitos', 'Male', '', NULL, 'vicGar@gmail.com', '09173846627', '2003-02-21', '', 1, NULL, NULL, NULL, NULL),
(45, 'Admin', '2025-11-19 13:18:58', 'jerlshaw', '$2y$10$oZsJ7Bi4u0l7q6Ts19Z7HONzn/tupBWnGmBIuvIrKGp5F0DJBuuVC', 'Erlshawn Josh', 'Tandas', 'Rivera', 'Male', '', NULL, 'jerlshaw@gmail.com', '09173628462', '2003-02-12', NULL, 1, NULL, NULL, NULL, NULL),
(55, 'User', '2025-11-26 18:15:18', 'test', '$2y$10$hTHwu8N3.3MUCXoU5Uk4Ee6Xk5w8.c9CNiUUehqox55pKSTFlHAKO', 'Pauline', 'Jean', 'Pasion', 'Female', '', NULL, 'pau@m.com', '09181234567', '2001-09-02', NULL, 1, NULL, NULL, NULL, NULL),
(58, 'User', '2025-12-06 12:31:26', 'joviemb', '$2y$10$VS0xtgnib2YYIrvDZzjXr.B1LsS9F2HarXdoV35iWgaihjWe8pRIG', 'Jovie', 'Banes', 'Magbanua', 'Female', '', NULL, 'joviemb@gmail.com', '09183274283', '1991-11-18', NULL, 1, NULL, NULL, NULL, NULL),
(59, 'User', '2025-12-06 15:32:54', 'quia_ryan', '$2y$10$0UKD7DDRFlPM.k9VW1Iniux8B10fTaSHWlrjuYqThWxS5VeC4T.ZC', 'Bryan', 'Lim', 'Quiambao', 'Male', '', NULL, 'quia_ryan@gmail.com', '09621937285', '1996-07-28', NULL, 1, NULL, NULL, NULL, NULL),
(60, 'User', '2025-12-06 15:56:05', 'serenity', '$2y$10$n4WhsmNsNLDjO8Km8kOREOvA9s7i9KCYP/PobT4pWVeSl6j1cFRNu', 'Serenity', 'Espino', 'Lozano', 'Female', '', NULL, 'serenity@gmail.com', '09186372233', '2001-09-02', NULL, 1, NULL, NULL, NULL, NULL),
(61, 'User', '2025-12-06 16:07:34', 'jragust', '$2y$10$j8g.77nfWwP6I/QKnVu2CeJfCeY8NzzP5EUz2d6ykplcT5BUgtguS', 'Joshua', 'Moreno', 'Agustin', 'Male', 'Jr', NULL, 'jragust@gmail.com', '09185738275', '1989-01-24', NULL, 1, NULL, NULL, NULL, NULL),
(62, 'User', '2025-12-06 16:25:07', 'victoriacara', '$2y$10$hBawgfzjVWiKyw2iwB7I4.vcWYh3Wwkvz6x47NhQcjSA7ygCCFTrm', 'Victoria', 'Cara', 'Enriquez', 'Female', '', NULL, 'victoriacara@gmail.com', '09183726183', '2000-08-22', NULL, 1, NULL, NULL, NULL, NULL),
(63, 'User', '2025-12-06 16:51:23', 'mattmagmal', '$2y$10$MhBTfjlsM0tTaxAsORBDOuYtoI27GRdEohvuYyMp/JJ72PUH81Mq6', 'Matthew', 'Magtuba', 'Mallari', 'Male', '', NULL, 'mattmagmal@gmail.com', '09621734829', '2001-12-07', NULL, 1, NULL, NULL, NULL, NULL),
(65, 'User', '2025-12-08 10:31:58', 'blank_ink', '$2y$10$XLnWMe/jb4pHGr4HapfgkeEqkZfCLB5KL/KEC7fv5QO.w1XaZKGzq', 'Blessica', 'Rosal', 'Pasado', 'Female', '', NULL, 'blank_ink@gmail.com', '09186477722', '1992-12-09', NULL, 1, NULL, NULL, NULL, NULL),
(67, 'User', '2025-12-09 09:21:18', 'erily', '$2y$10$fUl/ve4o3fYnb0f4V1Ha.O7DTJ3NsHK/pkyms8Z1HU1D86ooK2wke', 'ericka', 'sabile', 'fenis', 'Female', '', NULL, 'eicafns@gmail.com', '09453093495', '2024-05-14', NULL, 1, NULL, NULL, NULL, NULL),
(75, 'Priest', '2026-01-07 12:48:44', '', '$2y$10$oeS5tP2kSfkkCRXXIIqSUe33MMuifQutan/m.7cahDAixG8NhK8YK', 'James', '', 'Nitollama', 'Male', '', NULL, 'nitollama.j@gmail.com', '09186283341', '1987-11-25', NULL, 1, NULL, NULL, NULL, NULL),
(82, 'User', '2026-01-27 15:04:00', NULL, '$2y$10$4ybGaBgPlU0CeVCZHKTFCuQeVnOSMv5wU9ULipL7OH1rjPTMr.un6', 'pau', 'line', 'jean', 'Male', '', NULL, 'jonannamommina@gmail.com', '09621974275', '1999-09-02', NULL, 1, NULL, NULL, NULL, NULL),
(83, 'User', '2026-01-27 15:10:49', NULL, '$2y$10$tvO3YtqHuJRQiNDVwq2u1OGHvVh0sX4wNRSif8F3A4CE8nc4D5DJW', 'Pauline', 'Jean', 'Garcia', 'Female', '', NULL, 'atsaratom@gmail.com', '09621974275', '2001-04-03', NULL, 1, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `weddings`
--

CREATE TABLE `weddings` (
  `id` int(11) NOT NULL,
  `reservation_user_id` int(11) DEFAULT NULL,
  `wedding_type` varchar(15) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `decline_reason` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `notif` tinyint(4) DEFAULT NULL,
  `rehearsal_date` date DEFAULT NULL,
  `rehearsal_time` time DEFAULT NULL,
  `canon_interview_date` date DEFAULT NULL,
  `canon_interview_time` time DEFAULT NULL,
  `precana_semi_date` date DEFAULT NULL,
  `precana_semi_time` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `bride_firstname` varchar(30) DEFAULT NULL,
  `bride_middlename` varchar(30) DEFAULT NULL,
  `bride_lastname` varchar(30) DEFAULT NULL,
  `bride_birthdate` date DEFAULT NULL,
  `bride_nationality` varchar(100) DEFAULT NULL,
  `bride_address` varchar(255) DEFAULT NULL,
  `bride_contact` varchar(50) DEFAULT NULL,
  `bride_email` varchar(150) DEFAULT NULL,
  `groom_firstname` varchar(30) DEFAULT NULL,
  `groom_middlename` varchar(30) DEFAULT NULL,
  `groom_lastname` varchar(30) DEFAULT NULL,
  `groom_suffix` varchar(30) DEFAULT NULL,
  `groom_birthdate` date DEFAULT NULL,
  `groom_nationality` varchar(30) DEFAULT NULL,
  `groom_address` varchar(255) DEFAULT NULL,
  `groom_contact` varchar(50) DEFAULT NULL,
  `groom_email` varchar(150) DEFAULT NULL,
  `b_birth_certificate` varchar(300) DEFAULT NULL,
  `b_baptismal_certificate` varchar(300) DEFAULT NULL,
  `b_marriage_license` varchar(300) DEFAULT NULL,
  `b_confirmation_certificate` varchar(300) DEFAULT NULL,
  `b_cenomar` varchar(300) DEFAULT NULL,
  `g_birth_certificate` varchar(300) DEFAULT NULL,
  `g_baptismal_certificate` varchar(300) DEFAULT NULL,
  `g_marriage_license` varchar(300) DEFAULT NULL,
  `g_confirmation_certificate` varchar(300) DEFAULT NULL,
  `g_cenomar` varchar(300) DEFAULT NULL,
  `presiding_priest` varchar(50) NOT NULL DEFAULT 'Rev. Fr. James Nitollama'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weddings`
--

INSERT INTO `weddings` (`id`, `reservation_user_id`, `wedding_type`, `status`, `decline_reason`, `email`, `date`, `time`, `notif`, `rehearsal_date`, `rehearsal_time`, `canon_interview_date`, `canon_interview_time`, `precana_semi_date`, `precana_semi_time`, `created_at`, `bride_firstname`, `bride_middlename`, `bride_lastname`, `bride_birthdate`, `bride_nationality`, `bride_address`, `bride_contact`, `bride_email`, `groom_firstname`, `groom_middlename`, `groom_lastname`, `groom_suffix`, `groom_birthdate`, `groom_nationality`, `groom_address`, `groom_contact`, `groom_email`, `b_birth_certificate`, `b_baptismal_certificate`, `b_marriage_license`, `b_confirmation_certificate`, `b_cenomar`, `g_birth_certificate`, `g_baptismal_certificate`, `g_marriage_license`, `g_confirmation_certificate`, `g_cenomar`, `presiding_priest`) VALUES
(9, 59, 'Solo', 'Accepted', NULL, NULL, '2026-03-12', '15:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-06 07:51:21', 'Dream', 'Padua', 'Velasco', '1998-05-02', 'Filipino', NULL, '9172834722', 'velasco.dp98@gmail.com', 'Bryan', 'Lim', 'Quiambao', '', '1996-07-28', 'Filipino', NULL, '9621937285', 'quia_ryan@gmail.com', 'private/sacraments/uploads/b_birth_certificate_693450f98f8f6.png', 'private/sacraments/uploads/b_baptismal_certificate_693450f98f924.png', 'private/sacraments/uploads/b_marriage_license_693450f98f94f.png', 'private/sacraments/uploads/b_confirmation_certificate_693450f98f93a.png', 'private/sacraments/uploads/b_cenomar_693450f98f962.png', 'private/sacraments/uploads/g_birth_certificate_693450f98f975.png', 'private/sacraments/uploads/g_baptismal_certificate_693450f98f988.png', 'private/sacraments/uploads/g_marriage_license_693450f98f9ae.png', 'private/sacraments/uploads/g_confirmation_certificate_693450f98f99b.png', 'private/sacraments/uploads/g_cenomar_693450f98f9c0.png', 'Rev. Fr. James Nitollama'),
(10, 60, 'Solo', 'Accepted', NULL, NULL, '2026-03-10', '07:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-06 08:00:17', 'Serenity', 'Espino', 'Lozano', '2001-09-02', 'Filipino', NULL, '9186372233', 'serenity@gmail.com', 'Allen', 'Martinez', 'Guzman', '', '2002-05-01', 'Filipino', NULL, '9183728493', 'allkarma02@gmail.com', 'private/sacraments/uploads/b_birth_certificate_693453111d659.png', 'private/sacraments/uploads/b_baptismal_certificate_693453111d687.png', 'private/sacraments/uploads/b_marriage_license_693453111d6db.png', 'private/sacraments/uploads/b_confirmation_certificate_693453111d6c8.png', 'private/sacraments/uploads/b_cenomar_693453111d6ed.png', 'private/sacraments/uploads/g_birth_certificate_693453111d700.png', 'private/sacraments/uploads/g_baptismal_certificate_693453111d711.png', 'private/sacraments/uploads/g_marriage_license_693453111d733.png', 'private/sacraments/uploads/g_confirmation_certificate_693453111d722.png', 'private/sacraments/uploads/g_cenomar_693453111d744.png', 'Rev. Fr. James Nitollama'),
(11, 62, 'Batch', 'Pending', NULL, NULL, '2026-06-27', '10:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-06 08:33:08', 'Victoria', 'Cara', 'Enriquez', '2000-08-22', 'Filipino', NULL, '9183726183', 'victoriacara@gmail.com', 'Johnny', '', 'Gonzaga', '', '2000-10-24', 'Filipino', NULL, '9178273348', 'jo10_gonzaga@gmail.com', 'private/sacraments/uploads/b_birth_certificate_69345ac465b2a.png', 'private/sacraments/uploads/b_baptismal_certificate_69345ac465b6d.png', 'private/sacraments/uploads/b_marriage_license_69345ac465b99.png', 'private/sacraments/uploads/b_confirmation_certificate_69345ac465b87.png', 'private/sacraments/uploads/b_cenomar_69345ac465bad.png', 'private/sacraments/uploads/g_birth_certificate_69345ac465bc3.png', 'private/sacraments/uploads/g_baptismal_certificate_69345ac465be0.png', 'private/sacraments/uploads/g_marriage_license_69345ac465c13.png', 'private/sacraments/uploads/g_confirmation_certificate_69345ac465bfd.png', 'private/sacraments/uploads/g_cenomar_69345ac465c24.png', 'Rev. Fr. James Nitollama'),
(12, 65, 'Solo', 'Done', NULL, NULL, '2025-02-08', '09:00:00', NULL, '2025-01-24', '10:00:00', '2025-01-03', '11:00:00', '2025-01-15', '13:00:00', '2025-12-08 02:48:26', 'Blessica', 'Rosal', 'Manansala', '1992-12-09', 'Filipino', NULL, '9186477722', 'blank_ink@gmail.com', 'Mueller', 'Tajo', 'Pasado', NULL, '1990-12-10', 'Filipino', NULL, '9627381849', 'tintaph@gmail.com', 'private/sacraments/uploads/b_birth_certificate_6936acfa61383.png', 'private/sacraments/uploads/b_baptismal_certificate_6936acfa61650.png', 'private/sacraments/uploads/b_marriage_license_6936acfa61816.png', 'private/sacraments/uploads/b_confirmation_certificate_6936acfa61734.png', 'private/sacraments/uploads/b_cenomar_6936acfa6182b.png', 'private/sacraments/uploads/g_birth_certificate_6936acfa6183d.png', 'private/sacraments/uploads/g_baptismal_certificate_6936acfa6184d.png', 'private/sacraments/uploads/g_marriage_license_6936acfa6195c.png', 'private/sacraments/uploads/g_confirmation_certificate_6936acfa61949.png', 'private/sacraments/uploads/g_cenomar_6936acfa6196b.png', 'Rev. Fr. James Nitollama');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`ann_id`);

--
-- Indexes for table `anointing_of_sick_requests`
--
ALTER TABLE `anointing_of_sick_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `anointing_of_sick_requests_ibfk_1` (`reservation_user_id`);

--
-- Indexes for table `as_ninang_tbl`
--
ALTER TABLE `as_ninang_tbl`
  ADD PRIMARY KEY (`as_ninang_id`),
  ADD KEY `wedding_id` (`wedding_id`) USING BTREE,
  ADD KEY `fk_as_ninang_confirmation` (`confirmation_id`),
  ADD KEY `fk_as_ninang_baptism` (`baptism_id`);

--
-- Indexes for table `as_ninong_tbl`
--
ALTER TABLE `as_ninong_tbl`
  ADD PRIMARY KEY (`as_ninong_id`),
  ADD KEY `wedding_id` (`wedding_id`) USING BTREE,
  ADD KEY `fk_as_ninong_confirmation` (`confirmation_id`),
  ADD KEY `fk_as_ninong_baptism` (`baptism_id`);

--
-- Indexes for table `baptisms`
--
ALTER TABLE `baptisms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reservation_user_id` (`reservation_user_id`);

--
-- Indexes for table `calendar_control`
--
ALTER TABLE `calendar_control`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `celebratory`
--
ALTER TABLE `celebratory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `celebratory_ibfk_1` (`reservation_user_id`);

--
-- Indexes for table `certificate_request`
--
ALTER TABLE `certificate_request`
  ADD PRIMARY KEY (`cert_req_id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `confirmations`
--
ALTER TABLE `confirmations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reservation_user_id` (`reservation_user_id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`donate_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcement_id_ibfk_1` (`announcement_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `first_communion`
--
ALTER TABLE `first_communion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `first_communion_ibfk_1` (`reservation_user_id`);

--
-- Indexes for table `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `funerals`
--
ALTER TABLE `funerals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `funerals_ibfk_1` (`reservation_user_id`);

--
-- Indexes for table `guardian_tbl`
--
ALTER TABLE `guardian_tbl`
  ADD PRIMARY KEY (`guardian_id`),
  ADD KEY `fk_guardian_first_comm` (`first_comm_id`),
  ADD KEY `fk_guardian_confirmation` (`confirmation_id`),
  ADD KEY `fk_guardian_baptism` (`baptism_id`);

--
-- Indexes for table `house_blessings`
--
ALTER TABLE `house_blessings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `house_blessings_ibfk_1` (`reservation_user_id`);

--
-- Indexes for table `live_streams`
--
ALTER TABLE `live_streams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mass_schedule`
--
ALTER TABLE `mass_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ninang_tbl`
--
ALTER TABLE `ninang_tbl`
  ADD PRIMARY KEY (`ninang_id`),
  ADD KEY `fk_ninang_wedding` (`wedding_id`),
  ADD KEY `fk_ninang_confirmation` (`confirmation_id`),
  ADD KEY `fk_ninang_baptism` (`baptism_id`);

--
-- Indexes for table `ninong_tbl`
--
ALTER TABLE `ninong_tbl`
  ADD PRIMARY KEY (`ninong_id`),
  ADD KEY `fk_ninong_wedding` (`wedding_id`),
  ADD KEY `fk_ninong_confirmation` (`confirmation_id`),
  ADD KEY `fk_ninong_baptism` (`baptism_id`);

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
-- Indexes for table `reschedule_requests`
--
ALTER TABLE `reschedule_requests`
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
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_user_id` (`reservation_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `ann_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `anointing_of_sick_requests`
--
ALTER TABLE `anointing_of_sick_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `as_ninang_tbl`
--
ALTER TABLE `as_ninang_tbl`
  MODIFY `as_ninang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `as_ninong_tbl`
--
ALTER TABLE `as_ninong_tbl`
  MODIFY `as_ninong_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `baptisms`
--
ALTER TABLE `baptisms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `calendar_control`
--
ALTER TABLE `calendar_control`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `celebratory`
--
ALTER TABLE `celebratory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `certificate_request`
--
ALTER TABLE `certificate_request`
  MODIFY `cert_req_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `confirmations`
--
ALTER TABLE `confirmations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `donate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `first_communion`
--
ALTER TABLE `first_communion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `funerals`
--
ALTER TABLE `funerals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `guardian_tbl`
--
ALTER TABLE `guardian_tbl`
  MODIFY `guardian_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `house_blessings`
--
ALTER TABLE `house_blessings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `live_streams`
--
ALTER TABLE `live_streams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mass_schedule`
--
ALTER TABLE `mass_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `ninang_tbl`
--
ALTER TABLE `ninang_tbl`
  MODIFY `ninang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `ninong_tbl`
--
ALTER TABLE `ninong_tbl`
  MODIFY `ninong_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=391;

--
-- AUTO_INCREMENT for table `prayer_requests`
--
ALTER TABLE `prayer_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `reschedule_requests`
--
ALTER TABLE `reschedule_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `weddings`
--
ALTER TABLE `weddings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anointing_of_sick_requests`
--
ALTER TABLE `anointing_of_sick_requests`
  ADD CONSTRAINT `anointing_of_sick_requests_ibfk_1` FOREIGN KEY (`reservation_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `as_ninang_tbl`
--
ALTER TABLE `as_ninang_tbl`
  ADD CONSTRAINT `as_ninang_tbl_ibfk_1` FOREIGN KEY (`baptism_id`) REFERENCES `baptisms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_as_ninang_baptism` FOREIGN KEY (`baptism_id`) REFERENCES `baptisms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_as_ninang_confirmation` FOREIGN KEY (`confirmation_id`) REFERENCES `confirmations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_as_ninang_wedding` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `as_ninong_tbl`
--
ALTER TABLE `as_ninong_tbl`
  ADD CONSTRAINT `as_ninong_tbl_ibfk_1` FOREIGN KEY (`baptism_id`) REFERENCES `baptisms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_as_ninong_baptism` FOREIGN KEY (`baptism_id`) REFERENCES `baptisms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_as_ninong_confirmation` FOREIGN KEY (`confirmation_id`) REFERENCES `confirmations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_as_ninong_wedding` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `baptisms`
--
ALTER TABLE `baptisms`
  ADD CONSTRAINT `fk_reservation_user_id` FOREIGN KEY (`reservation_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `celebratory`
--
ALTER TABLE `celebratory`
  ADD CONSTRAINT `celebratory_ibfk_1` FOREIGN KEY (`reservation_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `certificate_request`
--
ALTER TABLE `certificate_request`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `confirmations`
--
ALTER TABLE `confirmations`
  ADD CONSTRAINT `reservation_user_id` FOREIGN KEY (`reservation_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `announcement_id_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`ann_id`) ON DELETE CASCADE;

--
-- Constraints for table `first_communion`
--
ALTER TABLE `first_communion`
  ADD CONSTRAINT `first_communion_ibfk_1` FOREIGN KEY (`reservation_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `funerals`
--
ALTER TABLE `funerals`
  ADD CONSTRAINT `funerals_ibfk_1` FOREIGN KEY (`reservation_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `guardian_tbl`
--
ALTER TABLE `guardian_tbl`
  ADD CONSTRAINT `fk_guardian_baptism` FOREIGN KEY (`baptism_id`) REFERENCES `baptisms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_guardian_confirmation` FOREIGN KEY (`confirmation_id`) REFERENCES `confirmations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_guardian_first_comm` FOREIGN KEY (`first_comm_id`) REFERENCES `first_communion` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guardian_tbl_ibfk_1` FOREIGN KEY (`baptism_id`) REFERENCES `baptisms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `house_blessings`
--
ALTER TABLE `house_blessings`
  ADD CONSTRAINT `house_blessings_ibfk_1` FOREIGN KEY (`reservation_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `ninang_tbl`
--
ALTER TABLE `ninang_tbl`
  ADD CONSTRAINT `fk_ninang_baptism` FOREIGN KEY (`baptism_id`) REFERENCES `baptisms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ninang_confirmation` FOREIGN KEY (`confirmation_id`) REFERENCES `confirmations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ninang_wedding` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ninong_tbl`
--
ALTER TABLE `ninong_tbl`
  ADD CONSTRAINT `fk_ninong_baptism` FOREIGN KEY (`baptism_id`) REFERENCES `baptisms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ninong_confirmation` FOREIGN KEY (`confirmation_id`) REFERENCES `confirmations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ninong_wedding` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `weddings`
--
ALTER TABLE `weddings`
  ADD CONSTRAINT `weddings_ibfk_1` FOREIGN KEY (`reservation_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
