-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 13, 2025 at 10:44 AM
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
(17, '𝟐𝐁 𝐇𝐄𝐀𝐋𝐓𝐇𝐘 @ 𝟔𝟎!💫🪩', 'uploads/1765192262_2bhealthy.png', 'Purihin ang Diyos sa katagumpayan ng naganap na birthday concert ng ating minamahal na Kura Paroko, Rev. Fr. James D. Nitollama , noong nakaraang Martes! 🙏✨ \r\nNais naming pasalamatan ang lahat ng 𝐬𝐩𝐨𝐧𝐬𝐨𝐫𝐬, 𝐩𝐞𝐫𝐟𝐨𝐫𝐦𝐞𝐫𝐬 𝐦𝐮𝐥𝐚 𝐬𝐚 𝐢𝐛𝐚\'𝐭 𝐢𝐛𝐚𝐧𝐠 𝐦𝐢𝐧𝐢𝐬𝐭𝐫𝐲 𝐧𝐠 𝐚𝐭𝐢𝐧𝐠 𝐩𝐚𝐫𝐨𝐤𝐲𝐚, at 𝐥𝐚𝐡𝐚𝐭 𝐧𝐠 𝐛𝐮𝐦𝐢𝐥𝐢 𝐧𝐠 𝐭𝐢𝐜𝐤𝐞𝐭 at 𝐝𝐮𝐦𝐚𝐥𝐨 upang makisaya at suportahan ang pagdiriwang na ito. Naging malaking bahagi kayo sa patuloy na pagpapaayos at pagpapaganda ng ating parokya. 🩷✝️\r\nNais din naming pasalamatan ang lahat ng 𝐯𝐨𝐥𝐮𝐧𝐭𝐞𝐞𝐫𝐬 at mga tao sa likod ng matagumpay na birthday concert ng ating Kura Paroko. Dahil sa inyong oras, talento, at dedikasyon, napuno ng saya, musika, at alaala ang 𝐂𝐚𝐥𝐨𝐨𝐜𝐚𝐧 𝐒𝐩𝐨𝐫𝐭𝐬 𝐂𝐨𝐦𝐩𝐥𝐞𝐱. Mula sa stage setup, sound & lights, ticketing, logistics, hanggang sa bawat maliit na detalye — hindi rin magiging ganito ka-memorable ang bawat selebrasyon ng kaarawan ng ating Kura Paroko kung wala kayo.🤍✨\r\nAt para sa ating minamahal na Kura Paroko, 𝐅𝐫. 𝐉𝐚𝐦𝐞𝐬, maraming salamat po sa iyong walang sawang serbisyo, pagmamahal, at dedikasyon sa ating parokya. Nawa’y nagustuhan mo ang espesyal na pagdiriwang na ito. Ang iyong dedikasyon ay tunay na inspirasyon sa bawat isa sa amin. 💫🙌🏻', '2025-11-11 11:11:02', 40),
(20, 'Diocesan Youth Day 2025 TINIG: Ang Tunay na Pag-Asa ay Hindi Tahimik', 'uploads/1765193514_tinig.png', 'Mga Kalakbay! 🌟\r\n\r\nBilang bahagi ng ating Jubilee Year of Hope for the Youth, sama sama nating ipagdiwang ang Diocesan Youth Day 2025 TINIG: Ang Tunay na Pag-Asa ay Hindi Tahimik ngayong November 15, 2025.\r\n\r\nIsang araw ng pananampalataya, pagkakaisa, at pag-asa kasama ang mga kabataang Katoliko mula sa buong Diocese of Novaliches — hosted by the District of Caloocan.\r\n\r\nKung ikaw ay nasa edad 13-39 na nais sumali, makipag-ugnayan lamang sa inyong Parish Youth Coordinators o Ministry Heads para sa detalye ng inyong pagrehistro.\r\n\r\nRegistration Period: October 08-31, 2025\r\nRegistration Fee: ₱200/each (may lunch, dinner, at DYD kit na kasama!)\r\n\r\nAno pang hinihintay niyo, mga Kalakbay? Bilang isang Kabataang Novaleño, sama-sama nating palakasin ang TINIG ng Pag-asang hindi tahimik!\r\n\r\nMag-register na at kita-kits sa November 15!\r\n\r\nInvitation Package: https://bit.ly/DYDPackage', '2025-10-25 11:31:54', 40),
(21, 'Jubilee of Hope Celebration of the Youth ng Diocese of Novaliches', 'uploads/1765193798_Jubilee Mass for the Youth.png', '\"Let no one despise your youth, but be an example to the believers...\" - 1 Timothy 4:12\r\n\r\nIpinagdiriwang ang Banal na Eukaristiya sa pamumuno ng ating Obispo, His Excellency Roberto Oredain Gaa, D.D. bilang bahagi ng Jubilee of Hope Celebration of the Youth ng Diocese of Novaliches. \r\n\r\n𝗧𝗶𝗴𝗻𝗮𝗻: nagtipon ang mga kabataang anak ng Diyos mula sa iba’t ibang parokya upang magsaya, magpuri, at muling pagtibayin ang kanilang 𝗺𝗶𝘀𝘆𝗼𝗻 𝗯𝗶𝗹𝗮𝗻𝗴 𝗶𝗹𝗮𝘄 𝗻𝗴 𝗦𝗶𝗺𝗯𝗮𝗵𝗮𝗻.\r\nSa bawat sigaw ng papuri, sa bawat ngiti, at sa bawat panalangin, ramdam ang 𝗗𝗶𝘆𝗼𝘀 𝗻𝗮 𝗸𝘂𝗺𝗶𝗸𝗶𝗹𝗼𝘀 𝘀𝗮 𝗽𝘂𝘀𝗼 𝗻𝗴 𝗸𝗮𝗯𝗮𝘁𝗮𝗮𝗻.\r\n🔥 𝘒𝘢𝘣𝘢𝘵𝘢𝘢𝘯𝘨 𝘒𝘢𝘵𝘰𝘭𝘪𝘬𝘰, 𝘱𝘢𝘵𝘶𝘭𝘰𝘺 𝘵𝘢𝘺𝘰𝘯𝘨 𝘮𝘢𝘨𝘯𝘪𝘯𝘨𝘢𝘴!\r\n🌟 𝘛𝘪𝘯𝘢𝘸𝘢𝘨 𝘵𝘢𝘺𝘰𝘯𝘨 𝘮𝘢𝘨𝘪𝘯𝘨 𝘴𝘢𝘬𝘴𝘪 𝘯𝘨 𝘱𝘢𝘨-𝘢𝘴𝘢.\r\n🕊️ 𝘈𝘵 𝘪𝘴𝘪𝘯𝘶𝘨𝘰 𝘵𝘢𝘺𝘰𝘯𝘨 𝘮𝘢𝘨𝘪𝘯𝘨 𝘥𝘢𝘭𝘶𝘺𝘢𝘯 𝘯𝘨 𝘱𝘢𝘨-𝘪𝘣𝘪𝘨 𝘯𝘪 𝘒𝘳𝘪𝘴𝘵𝘰 𝘴𝘢 𝘮𝘶𝘯𝘥𝘰.\r\n\r\n📍 𝐂𝐚𝐥𝐨𝐨𝐜𝐚𝐧 𝐒𝐩𝐨𝐫𝐭𝐬 𝐂𝐨𝐦𝐩𝐥𝐞𝐱 | 🗓️ 𝐍𝐨𝐯𝐞𝐦𝐛𝐞𝐫 𝟏𝟓, 𝟐𝟎𝟐𝟓 (𝐒𝐚𝐭𝐮𝐫𝐝𝐚𝐲)', '2025-11-15 11:36:38', 40),
(22, 'Kawan Ni San Isidro Labrador', 'uploads/1765194038_aa.png', '“𝗔𝗸𝗼 𝗮𝗻𝗴 𝗺𝗮𝗯𝘂𝘁𝗶𝗻𝗴 𝗽𝗮𝘀𝘁𝗼𝗹. 𝗞𝗶𝗹𝗮𝗹𝗮 𝗸𝗼 𝗮𝗻𝗴 𝗮𝗸𝗶𝗻𝗴 𝗺𝗴𝗮 𝘁𝘂𝗽𝗮, 𝗮𝘁 𝗸𝗶𝗹𝗮𝗹𝗮 𝗿𝗶𝗻 𝗻𝗶𝗹𝗮 𝗮𝗸𝗼.” — 𝗝𝘂𝗮𝗻 𝟭𝟬:𝟭𝟰\r\n\r\nIsang pinagpalang Kawan Visit kasama ang ating mahal na Obispo, Most Rev. Roberto Gaa, sa mga pamayanan ng Metropolis North, Carlisle 2, Greenville, Mirabela, Bagumbong Kanto, Alagar/Arsenias, Sampaloc, Villa Alicia, Progressive 2, Recombville 2, Bagumbong Taas, Solar Urban, at Villa Luisa.\r\nNawa’y patuloy tayong maging iisang kawan, pinapatatag ng pag-ibig ni Kristo at ginagabayan ng ating pastol tungo sa mas malalim na pananampalataya at pagkakaisa. 🕊️✨', '2025-11-17 11:40:38', 40),
(23, '✨𝐉𝐎𝐈𝐍 𝐓𝐇𝐄 𝐒𝐎𝐂𝐈𝐀𝐋 𝐂𝐎𝐌𝐌𝐔𝐍𝐈𝐂𝐀𝐓𝐈𝐎𝐍 & 𝐌𝐄𝐃𝐈𝐀 𝐌𝐈𝐍𝐈𝐒𝐓𝐑𝐘✨', 'uploads/1765194234_call.png', 'Open for creative hearts and willing hands!💛🎥🎙️\r\n\r\n\"Pope Leo XIV encourages our generation to be \"digital missionaries\", and use our platforms to spread the Gospel and promote positive values.\"\r\n\r\n\"Go and proclaim the Gospel to all creation.\" - Mark 16:15\r\n\r\nIn this digital age, the Church continues its mission of proclaiming God’s love—not only inside the walls of the parish, but also in the vast and ever-growing world of social media.\r\nAtin pong ipagpatuloy ang misyon ni Kristo sa pamamagitan ng pagiging daluyan ng mabuting balita, gamit ang ating mga talento at kakayahan.\r\n𝙒𝙚 𝙖𝙧𝙚 𝙡𝙤𝙤𝙠𝙞𝙣𝙜 𝙛𝙤𝙧 𝙣𝙚𝙬 𝙢𝙚𝙢𝙗𝙚𝙧𝙨 𝙬𝙝𝙤 𝙬𝙖𝙣𝙩 𝙩𝙤 𝙨𝙚𝙧𝙫𝙚 𝙩𝙝𝙧𝙤𝙪𝙜𝙝:\r\n𝗖𝗥𝗘𝗔𝗧𝗜𝗩𝗘𝗦 𝗖𝗢𝗠𝗠𝗜𝗧𝗧𝗘𝗘\r\n- Pubmat Creator\r\n𝗦𝗣𝗘𝗖𝗜𝗔𝗟 𝗠𝗘𝗗𝗜𝗔 𝗖𝗢𝗠𝗠𝗜𝗧𝗧𝗘𝗘\r\n- Live Operator\r\n- Social Media Manager (SMM)\r\n- Chat Moderator\r\n𝗣𝗥𝗢𝗝𝗘𝗖𝗧𝗢𝗥 𝗖𝗢𝗠𝗠𝗜𝗧𝗧𝗘𝗘\r\n- Projector Operator \r\n𝗗𝗼𝗰𝘂𝗺𝗲𝗻𝘁𝗮𝘁𝗶𝗼𝗻 𝗖𝗼𝗺𝗺𝗶𝘁𝘁𝗲𝗲\r\n- Videographer\r\n- Photographer\r\n- Content Editor\r\nNo experience? 𝗧𝗵𝗮𝘁’𝘀 𝗼𝗸𝗮𝘆!\r\nWhat matters is your willingness to learn and your desire to serve.\r\nYou will be trained, guided, and supported by a loving ministry family.\r\n𝗚𝘂𝘀𝘁𝗼 𝗺𝗼𝗻𝗴 𝘀𝘂𝗺𝗮𝗹𝗶? 🤩\r\n📲 Message us on our Facebook page (Santa Krus Parish, Bagumbong - Social Media Pool)\r\n🔍 Or scan the QR code to register \r\n🔗 : https://forms.gle/8Mmwn6z5sM56ZF6w9\r\nIf you feel this tug in your heart, do not ignore it.\r\n𝗧𝗵𝗲 𝗖𝗵𝘂𝗿𝗰𝗵 𝗻𝗲𝗲𝗱𝘀 𝘆𝗼𝘂𝗿 𝗴𝗶𝗳𝘁𝘀. 𝗧𝗵𝗲 𝗺𝗶𝘀𝘀𝗶𝗼𝗻 𝗻𝗲𝗲𝗱𝘀 𝘆𝗼𝘂𝗿 𝘃𝗼𝗶𝗰𝗲. 𝗚𝗼𝗱 𝗻𝗲𝗲𝗱𝘀 𝘆𝗼𝘂𝗿 “𝘆𝗲𝘀.”\r\nJoin SOCCOMM and help spread Christ’s message in a world that needs it more than ever.\r\nMessage us now to be part of the mission! 🙏🔥✨', '2025-11-20 11:43:54', 40),
(24, 'Christ the King Motorcade Procession', 'uploads/1765194364_christtheking.png', 'Then he said, \"Jesus, remember me when you come into your kingdom.\"\r\n- Luke 23:42\r\n\r\nOn this Solemnity of Our Lord Jesus Christ, King of the Universe, we unite as one community to honor Jesus as our King. 👑✨\r\nWe encourage everyone to take part in the 𝟯:𝟬𝟬 𝗣𝗠 𝗖𝗵𝗿𝗶𝘀𝘁 𝘁𝗵𝗲 𝗞𝗶𝗻𝗴 𝗺𝗼𝘁𝗼𝗿𝗰𝗮𝗱𝗲 𝗽𝗿𝗼𝗰𝗲𝘀𝘀𝗶𝗼𝗻 today. May this celebration remind us of His love, His guidance, and His place at the center of our lives.🙏🏼🌟', '2025-11-22 11:46:04', 40),
(25, 'Red Wednesday | Living Hope Amidst Suffering', 'uploads/1765194468_redwed.png', 'Today, we join the whole Church in remembering our 𝘀𝘂𝗳𝗳𝗲𝗿𝗶𝗻𝗴 𝗮𝗻𝗱 𝗽𝗲𝗿𝘀𝗲𝗰𝘂𝘁𝗲𝗱 𝗖𝗵𝗿𝗶𝘀𝘁𝗶𝗮𝗻 𝗯𝗿𝗼𝘁𝗵𝗲𝗿𝘀 𝗮𝗻𝗱 𝘀𝗶𝘀𝘁𝗲𝗿𝘀 across the world. We wear red, we pray, and we stand in solidarity with those who continue to bear witness to Christ even in the midst of violence, discrimination, and fear. 🕊️✝️\r\n\r\nMay this day open our hearts to the reality of their sacrifices and inspire us to deepen our own faith, courage, and compassion. Let us be a voice for the voiceless, a light in the darkness, and instruments of peace where hope seems lost. ❤️\r\n\r\n“𝗜𝗳 𝗼𝗻𝗲 𝗺𝗲𝗺𝗯𝗲𝗿 𝘀𝘂𝗳𝗳𝗲𝗿𝘀, 𝗮𝗹𝗹 𝘀𝘂𝗳𝗳𝗲𝗿 𝘁𝗼𝗴𝗲𝘁𝗵𝗲𝗿.” — 𝟭 𝗖𝗼𝗿𝗶𝗻𝘁𝗵𝗶𝗮𝗻𝘀 𝟭𝟮:𝟮𝟲\r\nLord, strengthen those who suffer for Your name, and awaken in us the commitment to love, pray, and act for justice. 🙏✨\r\n\r\n𝐇𝐨𝐥𝐲 𝐌𝐚𝐬𝐬 - 6:00 𝐩𝐦\r\n𝙒𝙚𝙖𝙧 𝙧𝙚𝙙 𝙖𝙣𝙙 𝙗𝙧𝙞𝙣𝙜 𝙘𝙖𝙣𝙙𝙡𝙚.', '2025-11-25 11:47:48', 40),
(26, 'ℕ𝕆𝕍𝔼𝕄𝔹𝔼ℝ 𝟚𝟜 | 𝕊𝔸𝕀ℕ𝕋𝕊 𝕆𝔽 𝕋ℍ𝔼 𝔻𝔸𝕐', 'uploads/1765194996_saintsoftheday.png', 'Remembering Saint Andrew Dũng-Lạc & Companions: The Martyrs of Vietnam ✝️\r\n\r\nToday, we honor the Vietnamese Martyrs — brave bishops, priests, religious, men, women, and even children who chose faith over fear. From the 1600s to the 1800s, thousands were arrested, tortured, and killed because they refused to deny their love for Christ. Their courage helped the Catholic faith grow in Vietnam, supported by early missionaries and lay catechists who shared the Gospel even in secret. Among them is Saint Andrew Dũng-Lạc, whose life of simplicity, sacrifice, and steadfast faith represents all the known and unknown martyrs who gave everything for God.\r\n\r\nTheir legacy inspires us today. Even through persecution, the Church in Vietnam continued to flourish — proof that faith is stronger than fear. These martyrs remind us that no matter what hardships we face, our hope in God is worth everything. Their words echo to us now, like the beautiful reflection of St. Théophane Vénard: “My thoughts are fixed on eternity… I thirst for the waters of Life Eternal.” May their courage strengthen our own faith and inspire us to live boldly for Christ. 🙏🔥\r\n\r\n𝙎𝙖𝙣𝙘𝙩𝙚 𝘼𝙣𝙙𝙧𝙚𝙖𝙚 𝘿ũ𝙣𝙜-𝙇ạ𝙘 𝙚𝙩 𝙎𝙤𝙘𝙞𝙞, 𝙤𝙧𝙖𝙩𝙚 𝙥𝙧𝙤 𝙣𝙤𝙗𝙞𝙨!', '2025-11-24 11:56:36', 40);

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
(71, NULL, 9, NULL, 'Angel', 'Gomez', 'Espino'),
(72, NULL, 10, NULL, 'Sylvia', 'Reyes', 'Alejandro'),
(73, NULL, 10, NULL, 'Alex', 'Sarmiento', 'Panes'),
(74, 75, NULL, NULL, 'Justin', 'Marcos', 'Jose'),
(75, 75, NULL, NULL, 'Benny ', 'Raymundo', 'Fajardo'),
(76, 75, NULL, NULL, 'Mueller ', 'Arroyo', 'Bravo'),
(77, 76, NULL, NULL, 'Analyn', 'Valdez', 'Rivera'),
(78, 77, NULL, NULL, 'Ruth', '', 'Carpio'),
(79, 77, NULL, NULL, 'Natalie ', 'Flores', 'Valenzuela'),
(80, NULL, NULL, 75, 'Sally', 'Rivera', 'Villamor'),
(83, NULL, 12, NULL, 'Ericka', 'Sabile', 'Fenis'),
(84, NULL, 12, NULL, 'Pauline Jean', 'Garcia', 'Pasion');

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
(77, NULL, 9, NULL, 'Eugene', 'Parres', 'Guzman', 'III'),
(78, NULL, 9, NULL, 'Javier', 'Magno', 'Solomon', ''),
(79, NULL, 10, NULL, 'Ash', 'Basa', 'Suarez', ''),
(80, NULL, 10, NULL, 'Daniel', 'Ancheta', 'Vicente', 'Jr'),
(81, 75, NULL, NULL, 'Charlene ', 'Espiritu', 'Salinas', ''),
(82, 75, NULL, NULL, 'Jeneth ', 'Arcilla', 'Basilio', ''),
(83, 75, NULL, NULL, 'Aurora ', 'Carlos', 'Pasion', ''),
(84, 76, NULL, NULL, 'Aquino', 'Aragon', 'Fernandez', ''),
(85, 76, NULL, NULL, 'Otto', 'Madrid', 'Garcia', ''),
(86, 77, NULL, NULL, 'Joseph', 'Perez', 'Manzano', ''),
(87, NULL, NULL, 75, 'Rafael ', 'Jimenez', 'Cordova', ''),
(90, NULL, 12, NULL, 'Victor', 'Zabala', 'Garlitos', ''),
(91, NULL, 12, NULL, 'Erlshawn Josh', 'Tandas', 'Rivera', '');

-- --------------------------------------------------------

--
-- Table structure for table `baptisms`
--

CREATE TABLE `baptisms` (
  `id` int(11) NOT NULL,
  `reservation_user_id` int(11) DEFAULT NULL,
  `baptism_type` enum('Regular','Solo') NOT NULL,
  `status` varchar(20) DEFAULT '''Pending''',
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
  `father_middlename` varchar(30) NOT NULL,
  `father_lastname` varchar(30) NOT NULL,
  `father_suffix` varchar(30) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `ninong_firstname` varchar(30) DEFAULT NULL,
  `ninong_middlename` varchar(30) DEFAULT NULL,
  `ninong_lastname` varchar(30) DEFAULT NULL,
  `ninong_suffix` varchar(30) DEFAULT NULL,
  `ninang_firstname` varchar(30) DEFAULT NULL,
  `ninang_middlename` varchar(30) DEFAULT NULL,
  `ninang_lastname` varchar(30) DEFAULT NULL,
  `married` varchar(3) NOT NULL,
  `marriage_type` varchar(15) DEFAULT 'Single',
  `birth_certificate` varchar(300) DEFAULT NULL,
  `marriage_certificate` varchar(300) DEFAULT NULL,
  `province` varchar(30) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `street_no` varchar(30) DEFAULT NULL,
  `barangay` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `baptisms`
--

INSERT INTO `baptisms` (`id`, `reservation_user_id`, `baptism_type`, `status`, `notif`, `email`, `contact`, `date`, `time`, `created_at`, `rehearsal_date`, `rehearsal_time`, `child_firstname`, `child_middlename`, `child_lastname`, `child_suffix`, `child_gender`, `child_nat`, `birth_date`, `birth_place`, `mother_firstname`, `mother_middlename`, `mother_lastname`, `father_firstname`, `father_middlename`, `father_lastname`, `father_suffix`, `address`, `ninong_firstname`, `ninong_middlename`, `ninong_lastname`, `ninong_suffix`, `ninang_firstname`, `ninang_middlename`, `ninang_lastname`, `married`, `marriage_type`, `birth_certificate`, `marriage_certificate`, `province`, `city`, `street_no`, `barangay`) VALUES
(73, 58, 'Regular', '\'Pending\'', 0, 'joviemb@gmail.com', '9183274283', '2025-12-28', '11:30:00', '2025-12-06 13:36:00', NULL, NULL, 'Mary Joy', 'Magbanua', 'Roldan', NULL, 'Female', 'Filipino', '2025-12-15', 'Caloocan, Metro Manila', 'Jovie', 'Banes', 'Magbanua', 'Mark', 'Cuevas', 'Roldan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Yes', 'Church Marriage', 'private/sacraments/uploads/birth_certificate_69343140c4b28.png', 'private/sacraments/uploads/marriage_certificate_69343140c4b7f.png', 'Metro Manila', 'Quezon City', '43 Langit Rd', 'Bagong Silang'),
(74, 60, 'Regular', '\'Pending\'', 0, 'serenity@gmail.com', '9186372233', '2025-12-28', '11:30:00', '2025-12-06 16:04:39', NULL, NULL, 'Kyel', 'Lozano', 'Guzman', '', 'Male', 'Filipino', '2025-11-01', 'Caloocan, Metro Manila', 'Serenity', 'Espino', 'Lozano', 'Allen', 'Martinez', 'Guzman', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', NULL, 'private/sacraments/uploads/birth_certificate_69345417cfa16.png', NULL, 'Metro Manila', 'Quezon City', '12 Kumintang St.', 'Llano'),
(75, 61, 'Solo', 'Accepted', 0, 'jragust@gmail.com', '9185738275', '2025-12-24', '08:00:00', '2025-12-06 16:13:13', NULL, NULL, 'Alice', 'Baguio', 'Agustin', '', 'Female', 'Filipino', '2025-12-01', 'Caloocan, Metro Manila', 'Cynthia', 'Torres', 'Baguio', 'Joshua', 'Moreno', 'Agustin', 'Jr', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Yes', 'Civil Marriage', 'private/sacraments/uploads/birth_certificate_6934561984e1f.png', 'private/sacraments/uploads/marriage_certificate_6934561984e61.png', 'Metro Manila', 'Quezon City', '681 Sunflower St.', 'Kaybiga'),
(76, 62, 'Regular', 'Accepted', 0, 'victoriacara@gmail.com', '9183726183', '2026-01-18', '11:30:00', '2025-12-06 16:40:17', NULL, NULL, 'Erick', '', 'Enriquez', '', 'Male', 'Filipino', '2025-11-25', 'Caloocan, Metro Manila', 'Victoria', 'Cara', 'Enriquez', 'Wayne', 'Mata', 'Borlongan', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', NULL, 'private/sacraments/uploads/birth_certificate_69345c716d5e5.png', NULL, 'Metro Manila', 'Quezon City', '121 Damong Maliit St.', 'Tala'),
(77, 63, 'Regular', 'Cancelled', 0, 'mattmagmal@gmail.com', '9621734829', '2025-12-21', '11:30:00', '2025-12-06 16:54:58', NULL, NULL, 'Jomar', 'Aquino', 'Mallari', '', 'Male', 'Filipino', '2025-12-01', 'Caloocan, Metro Manila', 'Riley ', 'Villegas', 'Mallari', 'Matthew', 'Magtuba', 'Mallari', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Yes', 'Church Marriage', 'private/sacraments/uploads/birth_certificate_69345fe27c152.png', 'private/sacraments/uploads/marriage_certificate_69345fe27c18c.png', 'Metro Manila', 'Quezon City', '15 Diamond St.', 'Bagumbong'),
(78, 65, 'Regular', 'Accepted', 0, 'blank_ink@gmail.com', '9186477722', '2026-01-04', '11:30:00', '2025-12-09 09:27:56', '2025-11-28', '10:00:00', 'Erl', 'Shawn', 'Rivera', '', 'Male', 'Filipino', '2025-11-10', 'Qc', 'Blessica', 'Rosal', 'Pasado', 'n/a', 'n/a', 'n/a', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', NULL, 'private/sacraments/uploads/birth_certificate_6937eb9ca2882.png', NULL, 'Quezon', 'Quezon City', 'Blk 13', 'Gaya-gaya');

-- --------------------------------------------------------

--
-- Table structure for table `certificate_request`
--

CREATE TABLE `certificate_request` (
  `cert_req_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `certificate_type` enum('Baptism','Wedding') NOT NULL,
  `certificate_status` enum('Pending','Declined','Accepted','Ready') NOT NULL DEFAULT 'Pending',
  `pickup_date` date DEFAULT NULL,
  `request_firstname` varchar(30) NOT NULL,
  `request_middlename` varchar(30) DEFAULT NULL,
  `request_lastname` varchar(30) NOT NULL,
  `request_suffix` varchar(30) DEFAULT NULL,
  `owner_firstname` varchar(30) DEFAULT NULL,
  `owner_middlename` varchar(30) DEFAULT NULL,
  `owner_lastname` varchar(30) DEFAULT NULL,
  `owner_suffix` varchar(30) DEFAULT NULL,
  `id_picture` varchar(300) DEFAULT NULL,
  `receipt_picture` varchar(300) DEFAULT NULL,
  `authorization_letter` varchar(300) DEFAULT NULL,
  `authorized_id_picture` varchar(300) DEFAULT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificate_request`
--

INSERT INTO `certificate_request` (`cert_req_id`, `user_id`, `certificate_type`, `certificate_status`, `pickup_date`, `request_firstname`, `request_middlename`, `request_lastname`, `request_suffix`, `owner_firstname`, `owner_middlename`, `owner_lastname`, `owner_suffix`, `id_picture`, `receipt_picture`, `authorization_letter`, `authorized_id_picture`, `contact`, `email`, `created_at`) VALUES
(4, 58, 'Baptism', 'Declined', NULL, 'asd', 'sada', 'sd', '', '', '', '', '', 'private/sacraments/uploads/id_picture_6936d7e250ba0.png', NULL, NULL, NULL, '1', 'a@gmai.com', '2025-12-08 13:51:30'),
(5, 58, 'Baptism', 'Accepted', '2025-12-09', ' Jovie', 'Banes', 'Magbanua', '', ' Mark', 'Cuevas', 'Roldan', '', 'private/sacraments/uploads/id_picture_6936d8ac2dfcf.png', 'private/sacraments/uploads/receipt_picture_6936d8ac2e00e.png', 'private/sacraments/uploads/authorization_letter_6936d8ac2e02d.png', 'private/sacraments/uploads/authorized_id_picture_6936d8ac2e045.png', '9183274283', 'joviemb@gmail.com', '2025-12-08 13:54:52'),
(6, 59, 'Wedding', 'Accepted', '2025-12-15', 'Bryan', 'Lim', 'Quiambao', '', '', '', '', '', 'private/sacraments/uploads/id_picture_6936d9055e184.png', 'private/sacraments/uploads/receipt_picture_6936d9055e219.png', NULL, NULL, '9621937285', 'quia_ryan@gmail.com', '2025-12-08 13:56:21'),
(7, 59, 'Wedding', 'Declined', NULL, 'Dream', 'Padua', 'Velasco', '', 'Bryan', 'Lim', 'Quiambao', '', 'private/sacraments/uploads/id_picture_6936d9468468f.png', 'private/sacraments/uploads/receipt_picture_6936d946846e6.png', NULL, NULL, '9172834722', 'velasco.dp98@gmail.com', '2025-12-08 13:57:26'),
(8, 62, 'Baptism', 'Accepted', '2025-12-10', ' Victoria', 'Cara', 'Enriquez', '', ' Erick', '', 'Enriquez', '', 'private/sacraments/uploads/id_picture_6936da751d7c9.png', NULL, NULL, 'private/sacraments/uploads/authorized_id_picture_6936da751d812.png', '9183726183', 'victoriacara@gmail.com', '2025-12-08 14:02:29'),
(9, 65, 'Wedding', 'Pending', NULL, 'Blessica', 'Rosal', 'Pasado', '', 'Muller', 'Tajo', 'Pasada', '', 'private/sacraments/uploads/id_picture_6937e2e9d3917.png', 'private/sacraments/uploads/receipt_picture_6937e2e9d396c.png', 'private/sacraments/uploads/authorization_letter_6937e2e9d399a.png', 'private/sacraments/uploads/authorized_id_picture_6937e2e9d39c1.png', '9186477722', 'blank_ink@gmail.com', '2025-12-09 08:50:49');

-- --------------------------------------------------------

--
-- Table structure for table `confirmations`
--

CREATE TABLE `confirmations` (
  `id` int(11) NOT NULL,
  `reservation_user_id` int(11) DEFAULT NULL,
  `confirmation_type` enum('Regular','Solo') NOT NULL,
  `status` varchar(20) DEFAULT '''Pending''',
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
  `catachesis_program` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `confirmations`
--

INSERT INTO `confirmations` (`id`, `reservation_user_id`, `confirmation_type`, `status`, `notif`, `email`, `contact`, `date`, `time`, `rehearsal_date`, `rehearsal_time`, `confirm_firstname`, `confirm_middlename`, `confirm_lastname`, `confirm_suffix`, `confirm_gender`, `confirm_nat`, `birth_date`, `birth_place`, `mother_firstname`, `mother_middlename`, `mother_lastname`, `father_firstname`, `father_middlename`, `father_lastname`, `father_suffix`, `birth_certificate`, `baptismal_certificate`, `catachesis_program`) VALUES
(73, 58, 'Solo', 'Cancelled', 0, 'joviemb@gmail.com', '9183274283', '2026-01-08', '15:00:00', NULL, NULL, 'Aaron', 'Magbanua', 'Roldan', '', 'Male', 'Filipino', '2017-03-20', 'Caloocan, Metro Manila', 'Jovie', 'Banes', 'Magbanua', 'Mark', 'Cuevas', 'Roldan', '', 'private/sacraments/uploads/birth_certificate_69343cddae784.png', 'private/sacraments/uploads/baptismal_certificate_69343cddae7d5.png', NULL),
(74, 61, 'Solo', 'Accepted', 0, 'jragust@gmail.com', '9185738275', '2026-02-07', '15:00:00', NULL, NULL, 'Kyle ', ' Baguio', 'Agustin', '', 'Male', 'Filipino', '2003-05-20', 'Caloocan, Metro Manila', 'Cynthia', ' Torres', ' Baguio', 'Joshua', ' Moreno', 'Agustin', 'Jr', 'private/sacraments/uploads/birth_certificate_6934582ae26ce.png', 'private/sacraments/uploads/baptismal_certificate_6934582ae2709.png', NULL),
(75, 63, 'Solo', '\'Pending\'', 0, 'mattmagmal@gmail.com', '9621734829', '2026-01-28', '10:00:00', NULL, NULL, 'Kenneth', ' Aquino', 'Mallari', '', 'Male', 'Filipino', '2015-10-02', 'Caloocan, Metro Manila', 'Riley', ' Villegas', ' Mallari', 'Matthew', ' Magtuba', ' Mallari', '', 'private/sacraments/uploads/birth_certificate_693460b18907c.png', 'private/sacraments/uploads/baptismal_certificate_693460b1890cd.png', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `donate_id` int(11) NOT NULL,
  `donor_email` varchar(100) DEFAULT NULL,
  `donor_contact_no` varchar(15) DEFAULT NULL,
  `donation_type` enum('Cash','Clothes','Goods','Specified') NOT NULL,
  `donation_specified` varchar(100) DEFAULT NULL,
  `donation_cause` enum('Parish Donation','Tithes','Charity') NOT NULL,
  `mode_of_payment` enum('Mass','Onsite','Gcash','Bank Transfer') NOT NULL,
  `proof_of_donation` varchar(300) DEFAULT NULL,
  `donation_amount` decimal(10,2) DEFAULT NULL,
  `donation_status` enum('pending','verified','unverified') DEFAULT 'pending',
  `date_posted` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`donate_id`, `donor_email`, `donor_contact_no`, `donation_type`, `donation_specified`, `donation_cause`, `mode_of_payment`, `proof_of_donation`, `donation_amount`, `donation_status`, `date_posted`) VALUES
(19, '', '', 'Cash', '', 'Parish Donation', 'Mass', NULL, 543.00, 'pending', '2025-12-07'),
(20, '', '', 'Cash', '', 'Parish Donation', 'Mass', NULL, 239.00, 'pending', '2025-12-06'),
(23, '', '9148295738', 'Cash', '', 'Parish Donation', 'Gcash', 'IMG_20251208_140833_6936cdd14f1f2.png', 500.00, 'pending', '2025-12-08'),
(25, '', '', 'Cash', '', 'Parish Donation', 'Onsite', NULL, 500.00, 'pending', '2025-12-08'),
(26, '', '9172849223', 'Cash', '', 'Parish Donation', 'Gcash', 'IMG_20251208_141014_6936ce3602fb2.png', 230.00, 'pending', '2025-12-08'),
(28, '', '', 'Clothes', '', 'Charity', 'Onsite', 'IMG_20251208_142115_6936d0cbc5045.png', 0.00, 'pending', '2025-12-08'),
(31, '', '', 'Goods', '', 'Charity', 'Onsite', 'IMG_20251208_142641_6936d2110f5e8.png', 0.00, 'pending', '2025-12-08'),
(32, '', '', 'Goods', '', 'Charity', 'Onsite', 'IMG_20251208_142651_6936d21b45eb2.png', 0.00, 'pending', '2025-12-08'),
(33, '', '', 'Clothes', '', 'Charity', 'Onsite', 'IMG_20251208_142940_6936d2c48dbbe.png', 0.00, 'pending', '2025-12-08'),
(34, '', '', 'Cash', '', 'Tithes', 'Onsite', NULL, 3000.00, 'pending', '2025-12-08'),
(35, '', '', 'Cash', '', 'Parish Donation', 'Bank Transfer', NULL, 10.00, 'pending', '2025-12-08'),
(36, '', '', 'Cash', '', 'Parish Donation', 'Bank Transfer', NULL, 928.00, 'pending', '2025-12-08');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `date`, `text`) VALUES
(7, '2025-01-01', 'Solemnity of Mary, the Holy Mother of God'),
(8, '2025-01-02', 'Memorial of Saints Basil the Great and Gregory Nazianzen'),
(9, '2025-01-03', 'Optional Memorial of the Most Holy Name of Jesus'),
(10, '2025-01-05', 'Epiphany of the Lord'),
(11, '2025-01-12', 'Feast of the Baptism of the Lord'),
(12, '2025-02-02', 'Feast of the Presentation of the Lord'),
(13, '2025-02-05', 'Memorial of Saint Agatha, Virgin and Martyr'),
(14, '2025-02-06', 'Memorial of Saints Paul Miki and Companions, Martyrs'),
(15, '2025-02-10', 'Memorial of Saint Scholastica, Virgin'),
(16, '2025-02-11', 'Optional Memorial of Our Lady of Lourdes'),
(17, '2025-02-22', 'Feast of the Chair of Saint Peter'),
(18, '2025-03-05', 'Ash Wednesday – Beginning of Lent'),
(19, '2025-03-19', 'Solemnity of Saint Joseph, Spouse of the Blessed Virgin Mary'),
(20, '2025-03-25', 'Solemnity of the Annunciation of the Lord'),
(21, '2025-04-13', 'Palm Sunday of the Passion of the Lord'),
(22, '2025-04-17', 'Holy Thursday – Mass of the Lord’s Supper'),
(23, '2025-04-18', 'Good Friday of the Lord’s Passion'),
(24, '2025-04-19', 'Holy Saturday – Easter Vigil'),
(25, '2025-04-20', 'Easter Sunday – Resurrection of the Lord'),
(26, '2025-04-27', 'Divine Mercy Sunday'),
(27, '2025-05-01', 'Feast of Saint Joseph the Worker'),
(28, '2025-05-03', 'Feast of Saints Philip and James, Apostles'),
(29, '2025-05-13', 'Optional Memorial of Our Lady of Fatima'),
(30, '2025-05-29', 'Solemnity of the Ascension of the Lord'),
(31, '2025-06-08', 'Pentecost Sunday'),
(32, '2025-06-09', 'Memorial of Mary, Mother of the Church'),
(33, '2025-06-15', 'Solemnity of the Most Holy Trinity'),
(34, '2025-06-19', 'Solemnity of the Most Holy Body and Blood of Christ (Corpus Christi)'),
(35, '2025-06-29', 'Solemnity of Saints Peter and Paul, Apostles'),
(36, '2025-08-15', 'Solemnity of the Assumption of the Blessed Virgin Mary'),
(37, '2025-09-08', 'Feast of the Nativity of the Blessed Virgin Mary'),
(38, '2025-09-14', 'Feast of the Exaltation of the Holy Cross'),
(39, '2025-10-01', 'Memorial of Saint Thérèse of the Child Jesus'),
(40, '2025-10-02', 'Memorial of the Holy Guardian Angels'),
(41, '2025-10-07', 'Memorial of Our Lady of the Rosary'),
(42, '2025-11-01', 'Solemnity of All Saints'),
(43, '2025-11-02', 'Commemoration of All the Faithful Departed (All Souls)'),
(44, '2025-11-09', 'Feast of the Dedication of the Lateran Basilica'),
(45, '2025-12-08', 'Solemnity of the Immaculate Conception of the Blessed Virgin Mary'),
(46, '2025-12-25', 'Solemnity of the Nativity of the Lord (Christmas)');

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
  `status` varchar(20) DEFAULT '''Pending''',
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
  `birth_certificate` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `first_communion`
--

INSERT INTO `first_communion` (`id`, `reservation_user_id`, `status`, `time`, `date`, `contact`, `email`, `first_comm_type`, `notif`, `rehearsal_date`, `rehearsal_time`, `child_firstname`, `child_middlename`, `child_lastname`, `child_suffix`, `child_gender`, `child_nat`, `birth_date`, `birth_place`, `mother_firstname`, `mother_middlename`, `mother_lastname`, `father_firstname`, `father_middlename`, `father_lastname`, `father_suffix`, `baptismal_certificate`, `birth_certificate`) VALUES
(2, 55, 'Cancelled', '15:00:00', '2025-12-25', '9', 'pau@m.com', 'Solo', 0, NULL, NULL, 'j', 'j', 'j', NULL, 'Male', 's', '2025-08-02', 's', 'Pauline', 'Jean', 'Pasion', 's', 's', 's', NULL, 'private/sacraments/uploads/baptismal_certificate_692c47e49e8a3.png', 'private/sacraments/uploads/catachesis_program_692c47e49e933.png'),
(5, 61, 'Accepted', '15:00:00', '2026-07-11', '9185738275', 'jragust@gmail.com', 'Solo', 0, NULL, NULL, 'Kyle', ' Baguio', 'Agustin', '', NULL, NULL, '2014-12-09', 'Caloocan, Metro Manila', 'Cynthia', ' Torres', ' Baguio', 'Joshua', 'Moreno', 'Agustin', 'Jr', 'private/sacraments/uploads/baptismal_certificate_693456f18ec80.png', 'private/sacraments/uploads/birth_certificate_693456f18ecb9.png'),
(6, 63, '\'Pending\'', '10:00:00', '2026-01-24', '9621734829', 'mattmagmal@gmail.com', 'Solo', 0, NULL, NULL, 'Ilyana', ' Aquino', 'Mallari', '', NULL, NULL, '2017-02-20', 'Caloocan, Metro Manila', 'Riley', 'Villegas', 'Mallari', 'Matthew', 'Magtuba', 'Mallari', '', 'private/sacraments/uploads/baptismal_certificate_6934610f1121b.png', 'private/sacraments/uploads/birth_certificate_6934610f1125b.png');

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
  `presider` varchar(255) DEFAULT NULL,
  `province` varchar(30) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `street_no` varchar(30) DEFAULT NULL,
  `barangay` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `funerals`
--

INSERT INTO `funerals` (`id`, `reservation_user_id`, `status`, `deceased_firstname`, `deceased_middlename`, `deceased_lastname`, `deceased_suffix`, `funeral_date`, `funeral_time`, `funeral_address`, `contact_person_firstname`, `contact_person_middlename`, `contact_person_lastname`, `contact_person_suffix`, `contact_person_relation`, `contact_person_contact_no`, `contact_person_email`, `created_at`, `cause_of_death`, `presider`, `province`, `city`, `street_no`, `barangay`) VALUES
(6, 58, 'Accepted', 'Sophiel ', 'Banes', 'Trinidad', '', '2025-12-07', '02:00:00', NULL, 'Jovie', 'Banes', 'Magbanua', '', 'Friend', '9183274283', 'joviemb@gmail.com', '2025-12-06 14:45:44', 'Lung Cancer', NULL, 'Metro Manila', 'Quezon City', '681 Sunflower St.', 'Llano'),
(7, 59, 'Accepted', 'Amy', 'Calderon', 'Quiambao', '', '2026-01-01', '17:00:00', NULL, 'Bryan', 'Lim', 'Quiambao', '', 'Relative', '9621937285', 'quia_ryan@gmail.com', '2025-12-06 15:55:03', 'Car Crash', NULL, 'Metro Manila', 'Quezon City', '8 Jupiter St.', 'Bagumbong'),
(8, 62, 'Accepted', 'Sylvia', 'Asis', 'Enriquez', '', '2025-12-13', '12:00:00', NULL, 'Victoria', 'Cara', 'Enriquez', '', 'Parent', '9183726183', 'victoriacara@gmail.com', '2025-12-06 16:47:55', 'Pneumonia', NULL, 'Metro Manila', 'Quezon City', '22 Jupiter St.', 'Bagumbong'),
(9, 63, 'Accepted', 'Marcos', 'Magtuba', 'Mallari', '', '2026-02-19', '07:00:00', NULL, 'Matthew', 'Magtuba', 'Mallari', '', 'Sibling', '9621734829', 'mattmagmal@gmail.com', '2025-12-06 17:02:12', 'Drowning', NULL, 'Metro Manila', 'Quezon City', '16 Kuneg St.', 'Bagumbong');

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
(21, NULL, 9, NULL, 'Titania', 'Sabado', 'Roque'),
(22, NULL, 10, NULL, 'Acquene', '', 'Concepcion'),
(23, 74, NULL, NULL, 'Ashley', 'Cinco', 'Sagun'),
(24, 75, NULL, NULL, 'Skylar', 'Romano', 'Lim'),
(25, NULL, NULL, 74, 'Miles', 'Palahniuk', 'de Leon'),
(26, NULL, 11, NULL, 'Ashley ', 'Ybañez', 'Castor'),
(27, 76, NULL, NULL, 'Amelie', 'Solis', 'Aquino'),
(28, 77, NULL, NULL, 'Aries ', 'Sales', 'Gonzaga'),
(29, NULL, NULL, 75, 'Jessa', 'Martin', 'Palma'),
(30, NULL, 12, NULL, 'Angel', NULL, 'Pascua'),
(31, 78, NULL, NULL, 'Pau', 'line', 'Pasion');

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
(29, NULL, 9, NULL, 'Lester', 'Pagsisihan', 'Custodio', ''),
(30, NULL, 10, NULL, 'Yuji', 'Javier', 'Sali', ''),
(31, 74, NULL, NULL, 'Ricardo', 'Viray', 'Uy', ''),
(32, 75, NULL, NULL, 'Murphy', 'Angeles', 'Robles', ''),
(33, NULL, NULL, 74, 'Bebang', 'Garcia', 'Cabrera', ''),
(34, NULL, 11, NULL, 'Jalen ', 'Delfin', 'Adriano', ''),
(35, 76, NULL, NULL, 'Charles', 'Solis', 'Buenaventura', ''),
(36, 77, NULL, NULL, 'Ernie', 'Baltazar', 'Sotto', ''),
(37, NULL, NULL, 75, 'Charlie ', '', 'Valdez', ''),
(38, NULL, 12, NULL, 'Jose', NULL, 'Lim', NULL),
(39, 78, NULL, NULL, 'Vic', 'tor', 'Garlitos', '');

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
(20, 55, 'Baptism Request Accepted', 'Your baptism request has been accepted. Please proceed with your donation.', 'donation_reminder', 1, '2025-11-27 02:19:31'),
(21, 55, 'Baptism Rehearsal Schedule', 'Your baptism rehearsal is scheduled on 2025-12-01 at 12:00. Please visit the parish on time.', 'rehearsal_reminder', 1, '2025-11-27 02:20:01'),
(22, 49, 'Baptism Request Accepted', 'Your baptism request has been accepted. Please proceed with your donation.', 'donation_reminder', 0, '2025-11-27 07:32:29'),
(23, 55, 'Baptism Request Accepted', 'Your baptism request has been accepted. Please proceed with your donation.', 'donation_reminder', 1, '2025-12-03 13:03:00'),
(24, 55, 'Baptism Rehearsal Schedule', 'Your baptism rehearsal is scheduled on 2025-12-17 at 10:00. Please visit the parish on time.', 'rehearsal_reminder', 1, '2025-12-03 13:03:26'),
(25, 55, 'First Communion Request Accepted', 'Your first communion request has been accepted. Please proceed with your donation.', 'donation_reminder', 1, '2025-12-03 13:55:25'),
(26, 55, 'Confirmation Request Accepted', 'Your confirmation request has been accepted. Please proceed with your donation.', 'donation_reminder', 1, '2025-12-03 13:56:07'),
(27, 55, 'First Communion Request Accepted', 'Your first communion request has been accepted. Please proceed with your donation.', 'donation_reminder', 1, '2025-12-03 14:06:45'),
(28, 55, 'Baptism Rehearsal Schedule', 'Your baptism rehearsal is scheduled on 2025-12-18 at 10:00:00. Please visit the parish on time.', 'rehearsal_reminder', 1, '2025-12-05 23:37:20'),
(29, 55, 'Baptism Rehearsal Schedule', 'Your confirmation rehearsal is scheduled on 2025-12-09 at 11:00. Please visit the parish on time.', 'rehearsal_reminder', 1, '2025-12-05 23:39:38'),
(30, 55, 'Confirmation Request Accepted', 'Your confirmation request has been accepted. Please proceed with your donation.', 'donation_reminder', 1, '2025-12-05 23:39:54'),
(31, 55, 'Baptism Rehearsal Schedule', 'Your first_communion rehearsal is scheduled on 2025-12-16 at 10:00. Please visit the parish on time.', 'rehearsal_reminder', 1, '2025-12-05 23:58:58'),
(32, 55, 'Wedding Pre-cana Interview Schedule', 'Your pre-cana interview is scheduled on 2025-12-02 at 10:00. Please visit the parish on time.', 'precana_semi_reminder', 1, '2025-12-06 15:23:33'),
(33, 55, 'Wedding Canonical Interview Schedule', 'Your canonical interview is scheduled on 2025-11-02 at 10:00. Please visit the parish on time.', 'canon_interview_reminder', 1, '2025-12-06 15:32:55'),
(34, 55, 'Wedding Rehearsal Schedule', 'Your wedding rehearsal is scheduled on 2025-12-16 at 10:00. Please visit the parish on time.', 'rehearsal_reminder', 1, '2025-12-06 15:40:06'),
(35, 55, 'Wedding Canonical Interview Schedule', 'Your canonical interview is scheduled on 2025-12-17 at 11:00. Please visit the parish on time.', 'canon_interview_reminder', 1, '2025-12-06 15:40:06'),
(36, 55, 'Wedding Pre-cana Seminar Schedule', 'Your pre-cana seminar is scheduled on 2025-12-17 at 12:00. Please visit the parish on time.', 'precana_semi_reminder', 1, '2025-12-06 15:40:06'),
(37, 55, 'Wedding Rehearsal Schedule', 'Your wedding rehearsal is scheduled on 2025-12-16 at 11:00:00. Please visit the parish on time.', 'rehearsal_reminder', 1, '2025-12-06 15:41:45'),
(38, 55, 'Wedding Canonical Interview Schedule', 'Your canonical interview is scheduled on 2025-12-17 at 12:00:00. Please visit the parish on time.', 'canon_interview_reminder', 1, '2025-12-06 15:41:45'),
(39, 55, 'Wedding Pre-cana Seminar Schedule', 'Your pre-cana seminar is scheduled on 2025-12-17 at 13:00:00. Please visit the parish on time.', 'precana_semi_reminder', 1, '2025-12-06 15:41:45'),
(41, 55, 'Wedding Canonical Interview Schedule', 'Your canonical interview is scheduled on 2025-12-17 at 11:00:00. Please visit the parish on time.', 'canon_interview_reminder', 1, '2025-12-06 15:45:55'),
(43, 55, 'Wedding Canonical Interview Schedule', 'Your canonical interview is scheduled on 2025-12-17 at 11:01:00. Please visit the parish on time.', 'canon_interview_reminder', 1, '2025-12-06 15:46:46'),
(45, 58, 'Funeral Mass Request Accepted', 'Your funeral mass request has been accepted.', 'funeral_mass_accept', 0, '2025-12-07 01:03:01'),
(47, 55, 'Certificate Ready for Pickup', 'Hello sad sd, your certificate will be ready for pickup on 2025-12-15.', 'certificate', 1, '2025-12-08 03:43:47'),
(48, 55, 'Certificate Ready for Pickup', 'Hello ads sd, your certificate will be ready for pickup on 2025-12-09.', 'certificate', 1, '2025-12-08 03:50:17'),
(49, 55, 'Certificate Ready for Pickup', 'Hello a a, your certificate will be ready for pickup on 2025-12-09.', 'certificate', 1, '2025-12-08 04:51:44'),
(50, 65, 'Wedding Request Accepted', 'Your wedding request has been accepted. Please proceed with your donation.', 'donation_reminder', 0, '2025-12-08 18:48:50'),
(51, 65, 'Wedding Rehearsal Schedule', 'Your wedding rehearsal is scheduled on 2025-01-24 at 10:00. Please visit the parish on time.', 'rehearsal_reminder', 1, '2025-12-08 18:55:10'),
(52, 65, 'Wedding Canonical Interview Schedule', 'Your canonical interview is scheduled on 2025-01-03 at 11:00. Please visit the parish on time.', 'canon_interview_reminder', 1, '2025-12-08 18:55:10'),
(53, 65, 'Wedding Pre-cana Seminar Schedule', 'Your pre-cana seminar is scheduled on 2025-01-15 at 13:00. Please visit the parish on time.', 'precana_semi_reminder', 1, '2025-12-08 18:55:10'),
(54, 61, 'Baptism Request Accepted', 'Your baptism request has been accepted. Please proceed with your donation.', 'donation_reminder', 0, '2025-12-08 21:35:13'),
(55, 62, 'Baptism Request Accepted', 'Your baptism request has been accepted. Please proceed with your donation.', 'donation_reminder', 1, '2025-12-08 21:35:20'),
(56, 61, 'First Communion Request Accepted', 'Your first communion request has been accepted. Please proceed with your donation.', 'donation_reminder', 0, '2025-12-08 21:40:19'),
(57, 61, 'Confirmation Request Accepted', 'Your confirmation request has been accepted. Please proceed with your donation.', 'donation_reminder', 0, '2025-12-08 21:40:29'),
(58, 59, 'Wedding Request Accepted', 'Your wedding request has been accepted. Please proceed with your donation.', 'donation_reminder', 0, '2025-12-08 21:40:35'),
(59, 60, 'Wedding Request Accepted', 'Your wedding request has been accepted. Please proceed with your donation.', 'donation_reminder', 0, '2025-12-08 21:40:40'),
(60, 59, 'Funeral Mass Request Accepted', 'Your funeral mass request has been accepted.', 'funeral_mass_accept', 0, '2025-12-08 21:40:45'),
(61, 62, 'Funeral Mass Request Accepted', 'Your funeral mass request has been accepted.', 'funeral_mass_accept', 1, '2025-12-08 21:40:47'),
(62, 63, 'Funeral Mass Request Accepted', 'Your funeral mass request has been accepted.', 'funeral_mass_accept', 0, '2025-12-08 21:40:49'),
(63, 58, 'Certificate Ready for Pickup', 'Hello  Jovie Magbanua, your certificate will be ready for pickup on 2025-12-09.', 'certificate', 0, '2025-12-08 22:03:56'),
(64, 59, 'Certificate Ready for Pickup', 'Hello Bryan Quiambao, your certificate will be ready for pickup on 2025-12-15.', 'certificate', 0, '2025-12-08 22:04:14'),
(65, 62, 'Certificate Ready for Pickup', 'Hello  Victoria Enriquez, your certificate will be ready for pickup on 2025-12-10.', 'certificate', 1, '2025-12-08 22:04:25'),
(66, 65, 'Baptism Request Accepted', 'Your baptism request has been accepted. Please proceed with your donation.', 'donation_reminder', 1, '2025-12-09 17:35:09'),
(67, 65, 'Baptism Rehearsal Schedule', 'Your baptism rehearsal is scheduled on 2025-11-28 at 10:00. Please visit the parish on time.', 'rehearsal_reminder', 0, '2025-12-09 17:37:19');

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
(6, 'published', NULL, NULL, NULL, 'lord please pauwiin mo na aso ko miss ko na sya i hope ligtas sya lord thank you', 1, 'special', '1', '2025-11-19 23:26:29', NULL, NULL, NULL),
(7, 'published', 'josie', NULL, 'jos@gmail.com', 'please guide me in passing my exams', 1, 'special', '0', '2025-11-19 23:27:10', NULL, NULL, NULL),
(10, 'published', NULL, NULL, NULL, 'sana bumait na si Shanelle', 1, 'soul', '1', '2025-11-26 23:20:48', NULL, NULL, NULL),
(12, 'published', 'Jovie Magbanua', NULL, 'joviemb@gmail.com', 'asking for prayer i want to spread the gospel plant seeds and cast out devils in Jesus name thank you God bless you all', 1, 'soul', '0', '2025-12-06 12:34:23', NULL, NULL, NULL),
(13, 'published', 'Bryan Quiambao', NULL, 'quia_ryan@gmail.com', 'Prayer for Our Family\r\nHeavenly Father, bless my family with love, unity, and good health. Guide our hearts with faith, protect us from harm, and fill our home with peace and understanding always. Amen.', 1, 'special', '0', '2025-12-06 15:34:07', NULL, NULL, NULL),
(14, 'published', 'Adang Family', NULL, 'adang@gmail.com', 'Family and friends, we humbly ask for your prayers for our beloved sister. She is going through a difficult time, and our hearts are heavy but we believe in a God who heals, strengthens, and comforts.', 1, 'special', '0', '2025-12-06 15:34:59', NULL, NULL, NULL),
(15, 'private', NULL, NULL, NULL, 'lord pls help us thank you', 1, 'special', '1', '2025-12-09 07:57:01', NULL, NULL, NULL),
(16, 'private', 'ersjaw', NULL, NULL, 'PASA NIYO KAMI PLEASE UWUU', 1, 'special', '0', '2025-12-09 08:18:52', NULL, NULL, NULL),
(17, 'private', NULL, NULL, NULL, 'wew', 1, 'special', '1', '2025-12-09 08:46:28', NULL, NULL, NULL),
(18, 'private', 'erlshwn', NULL, NULL, 'Please Lord guide us', 1, 'special', '0', '2025-12-09 09:17:48', NULL, NULL, NULL),
(19, 'private', 'erlshwn', NULL, 'erlshwn@gmail.com', 'Please Lord guide us', 1, 'special', '0', '2025-12-09 09:18:39', NULL, NULL, NULL);

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
(39, 'Admin', '2025-12-08 11:01:43', 'stakrusparish', '$2y$10$D1KpMxJFBWP/UCtDIoqF7OKz56oaiOx2PNEabUkRZSGH0tgp.1Hbi', 'Kristan', 'Samson', 'Pepito', 'Male', '', NULL, 'stakrusparish@gmail.com', '9617489918', '2001-12-09', NULL),
(40, 'Admin', '2025-11-12 05:18:40', 'pauiee', '$2y$10$rTkl4wT.5Tm066MGygLBeug11XCoPbI2YmEqfcmeQT3ZxmXD4XDNa', 'Pauline Jean', 'Garcia', 'Pasion', 'Female', '', 'Pauline Jean Garcia Pasion', 'pauiee@gmail.com', '9621974275', '2001-09-02', 'profilepics/maw.png'),
(41, 'Admin', '2025-11-12 07:01:06', 'ehrily', '$2y$10$GTcwx5d7Dw4nread/F/eP.mbEf2AbI3x7zH/aQ5bJQm.B.DsO1J4W', 'Ericka', 'Sabile', 'Fenis', 'Female', '', 'Ericka  Fenis', 'ehrily@gmail.com', '9123859933', '2001-04-13', ''),
(44, 'Admin', '2025-11-19 13:08:23', 'vicGar', '$2y$10$lB8WUQ4uL3PSG0NAxXki6.a5kmcCEawCHTiiV9rLzoJuqTMsIBPPO', 'Victor', 'Zabala', 'Garlitos', 'Male', '', NULL, 'vicGar@gmail.com', '9173846627', '2003-02-21', ''),
(45, 'Admin', '2025-11-19 13:18:58', 'jerlshaw', '$2y$10$oZsJ7Bi4u0l7q6Ts19Z7HONzn/tupBWnGmBIuvIrKGp5F0DJBuuVC', 'Erlshawn Josh', 'Tandas', 'Rivera', 'Male', '', NULL, 'jerlshaw@gmail.com', '9173628462', '2003-02-12', NULL),
(55, 'User', '2025-11-26 18:15:18', 'test', '$2y$10$hTHwu8N3.3MUCXoU5Uk4Ee6Xk5w8.c9CNiUUehqox55pKSTFlHAKO', 'Pauline', 'Jean', 'Pasion', 'Female', '', NULL, 'pau@m.com', '9181234567', '2001-09-02', NULL),
(58, 'User', '2025-12-06 12:31:26', 'joviemb', '$2y$10$VS0xtgnib2YYIrvDZzjXr.B1LsS9F2HarXdoV35iWgaihjWe8pRIG', 'Jovie', 'Banes', 'Magbanua', 'Female', '', NULL, 'joviemb@gmail.com', '9183274283', '1991-11-18', NULL),
(59, 'User', '2025-12-06 15:32:54', 'quia_ryan', '$2y$10$0UKD7DDRFlPM.k9VW1Iniux8B10fTaSHWlrjuYqThWxS5VeC4T.ZC', 'Bryan', 'Lim', 'Quiambao', 'Male', '', NULL, 'quia_ryan@gmail.com', '9621937285', '1996-07-28', NULL),
(60, 'User', '2025-12-06 15:56:05', 'serenity', '$2y$10$n4WhsmNsNLDjO8Km8kOREOvA9s7i9KCYP/PobT4pWVeSl6j1cFRNu', 'Serenity', 'Espino', 'Lozano', 'Female', '', NULL, 'serenity@gmail.com', '9186372233', '2001-09-02', NULL),
(61, 'User', '2025-12-06 16:07:34', 'jragust', '$2y$10$j8g.77nfWwP6I/QKnVu2CeJfCeY8NzzP5EUz2d6ykplcT5BUgtguS', 'Joshua', 'Moreno', 'Agustin', 'Male', 'Jr', NULL, 'jragust@gmail.com', '9185738275', '1989-01-24', NULL),
(62, 'User', '2025-12-06 16:25:07', 'victoriacara', '$2y$10$hBawgfzjVWiKyw2iwB7I4.vcWYh3Wwkvz6x47NhQcjSA7ygCCFTrm', 'Victoria', 'Cara', 'Enriquez', 'Female', '', NULL, 'victoriacara@gmail.com', '9183726183', '2000-08-22', NULL),
(63, 'User', '2025-12-06 16:51:23', 'mattmagmal', '$2y$10$MhBTfjlsM0tTaxAsORBDOuYtoI27GRdEohvuYyMp/JJ72PUH81Mq6', 'Matthew', 'Magtuba', 'Mallari', 'Male', '', NULL, 'mattmagmal@gmail.com', '9621734829', '2001-12-07', NULL),
(65, 'User', '2025-12-08 10:31:58', 'blank_ink', '$2y$10$XLnWMe/jb4pHGr4HapfgkeEqkZfCLB5KL/KEC7fv5QO.w1XaZKGzq', 'Blessica', 'Rosal', 'Pasado', 'Female', '', NULL, 'blank_ink@gmail.com', '9186477722', '1992-12-09', NULL),
(67, 'User', '2025-12-09 09:21:18', 'erily', '$2y$10$fUl/ve4o3fYnb0f4V1Ha.O7DTJ3NsHK/pkyms8Z1HU1D86ooK2wke', 'ericka', 'sabile', 'fenis', 'Female', '', NULL, 'eicafns@gmail.com', '09453093495', '2024-05-14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `weddings`
--

CREATE TABLE `weddings` (
  `id` int(11) NOT NULL,
  `reservation_user_id` int(11) DEFAULT NULL,
  `wedding_type` varchar(15) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
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
  `g_cenomar` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weddings`
--

INSERT INTO `weddings` (`id`, `reservation_user_id`, `wedding_type`, `status`, `email`, `date`, `time`, `notif`, `rehearsal_date`, `rehearsal_time`, `canon_interview_date`, `canon_interview_time`, `precana_semi_date`, `precana_semi_time`, `created_at`, `bride_firstname`, `bride_middlename`, `bride_lastname`, `bride_birthdate`, `bride_nationality`, `bride_address`, `bride_contact`, `bride_email`, `groom_firstname`, `groom_middlename`, `groom_lastname`, `groom_suffix`, `groom_birthdate`, `groom_nationality`, `groom_address`, `groom_contact`, `groom_email`, `b_birth_certificate`, `b_baptismal_certificate`, `b_marriage_license`, `b_confirmation_certificate`, `b_cenomar`, `g_birth_certificate`, `g_baptismal_certificate`, `g_marriage_license`, `g_confirmation_certificate`, `g_cenomar`) VALUES
(9, 59, 'Solo', 'Accepted', NULL, '2026-03-12', '15:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-06 15:51:21', 'Dream', 'Padua', 'Velasco', '1998-05-02', 'Filipino', NULL, '9172834722', 'velasco.dp98@gmail.com', 'Bryan', 'Lim', 'Quiambao', '', '1996-07-28', 'Filipino', NULL, '9621937285', 'quia_ryan@gmail.com', 'private/sacraments/uploads/b_birth_certificate_693450f98f8f6.png', 'private/sacraments/uploads/b_baptismal_certificate_693450f98f924.png', 'private/sacraments/uploads/b_marriage_license_693450f98f94f.png', 'private/sacraments/uploads/b_confirmation_certificate_693450f98f93a.png', 'private/sacraments/uploads/b_cenomar_693450f98f962.png', 'private/sacraments/uploads/g_birth_certificate_693450f98f975.png', 'private/sacraments/uploads/g_baptismal_certificate_693450f98f988.png', 'private/sacraments/uploads/g_marriage_license_693450f98f9ae.png', 'private/sacraments/uploads/g_confirmation_certificate_693450f98f99b.png', 'private/sacraments/uploads/g_cenomar_693450f98f9c0.png'),
(10, 60, 'Solo', 'Accepted', NULL, '2026-03-10', '07:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-06 16:00:17', 'Serenity', 'Espino', 'Lozano', '2001-09-02', 'Filipino', NULL, '9186372233', 'serenity@gmail.com', 'Allen', 'Martinez', 'Guzman', '', '2002-05-01', 'Filipino', NULL, '9183728493', 'allkarma02@gmail.com', 'private/sacraments/uploads/b_birth_certificate_693453111d659.png', 'private/sacraments/uploads/b_baptismal_certificate_693453111d687.png', 'private/sacraments/uploads/b_marriage_license_693453111d6db.png', 'private/sacraments/uploads/b_confirmation_certificate_693453111d6c8.png', 'private/sacraments/uploads/b_cenomar_693453111d6ed.png', 'private/sacraments/uploads/g_birth_certificate_693453111d700.png', 'private/sacraments/uploads/g_baptismal_certificate_693453111d711.png', 'private/sacraments/uploads/g_marriage_license_693453111d733.png', 'private/sacraments/uploads/g_confirmation_certificate_693453111d722.png', 'private/sacraments/uploads/g_cenomar_693453111d744.png'),
(11, 62, 'Batch', 'Pending', NULL, '2026-06-27', '10:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-06 16:33:08', 'Victoria', 'Cara', 'Enriquez', '2000-08-22', 'Filipino', NULL, '9183726183', 'victoriacara@gmail.com', 'Johnny', '', 'Gonzaga', '', '2000-10-24', 'Filipino', NULL, '9178273348', 'jo10_gonzaga@gmail.com', 'private/sacraments/uploads/b_birth_certificate_69345ac465b2a.png', 'private/sacraments/uploads/b_baptismal_certificate_69345ac465b6d.png', 'private/sacraments/uploads/b_marriage_license_69345ac465b99.png', 'private/sacraments/uploads/b_confirmation_certificate_69345ac465b87.png', 'private/sacraments/uploads/b_cenomar_69345ac465bad.png', 'private/sacraments/uploads/g_birth_certificate_69345ac465bc3.png', 'private/sacraments/uploads/g_baptismal_certificate_69345ac465be0.png', 'private/sacraments/uploads/g_marriage_license_69345ac465c13.png', 'private/sacraments/uploads/g_confirmation_certificate_69345ac465bfd.png', 'private/sacraments/uploads/g_cenomar_69345ac465c24.png'),
(12, 65, 'solo', 'Accepted', NULL, '2025-02-08', '09:00:00', NULL, '2025-01-24', '10:00:00', '2025-01-03', '11:00:00', '2025-01-15', '13:00:00', '2025-12-08 10:48:26', 'Blessica', 'Rosal', 'Manansala', '1992-12-09', 'Filipino', NULL, '9186477722', 'blank_ink@gmail.com', 'Mueller', 'Tajo', 'Pasado', NULL, '1990-12-10', 'Filipino', NULL, '9627381849', 'tintaph@gmail.com', 'private/sacraments/uploads/b_birth_certificate_6936acfa61383.png', 'private/sacraments/uploads/b_baptismal_certificate_6936acfa61650.png', 'private/sacraments/uploads/b_marriage_license_6936acfa61816.png', 'private/sacraments/uploads/b_confirmation_certificate_6936acfa61734.png', 'private/sacraments/uploads/b_cenomar_6936acfa6182b.png', 'private/sacraments/uploads/g_birth_certificate_6936acfa6183d.png', 'private/sacraments/uploads/g_baptismal_certificate_6936acfa6184d.png', 'private/sacraments/uploads/g_marriage_license_6936acfa6195c.png', 'private/sacraments/uploads/g_confirmation_certificate_6936acfa61949.png', 'private/sacraments/uploads/g_cenomar_6936acfa6196b.png');

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
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `ann_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `as_ninang_tbl`
--
ALTER TABLE `as_ninang_tbl`
  MODIFY `as_ninang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `as_ninong_tbl`
--
ALTER TABLE `as_ninong_tbl`
  MODIFY `as_ninong_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `baptisms`
--
ALTER TABLE `baptisms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `certificate_request`
--
ALTER TABLE `certificate_request`
  MODIFY `cert_req_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `confirmations`
--
ALTER TABLE `confirmations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `donate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `first_communion`
--
ALTER TABLE `first_communion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `funerals`
--
ALTER TABLE `funerals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ninang_tbl`
--
ALTER TABLE `ninang_tbl`
  MODIFY `ninang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `ninong_tbl`
--
ALTER TABLE `ninong_tbl`
  MODIFY `ninong_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `prayer_requests`
--
ALTER TABLE `prayer_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `weddings`
--
ALTER TABLE `weddings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

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
