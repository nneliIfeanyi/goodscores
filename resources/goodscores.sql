-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 26, 2024 at 10:54 PM
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
-- Database: `goodscores`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `classname` varchar(50) NOT NULL,
  `num_rows` int(11) NOT NULL,
  `num_rows2` int(11) NOT NULL,
  `duration` varchar(255) NOT NULL,
  `choice` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `sch_id`, `user_id`, `classname`, `num_rows`, `num_rows2`, `duration`, `choice`) VALUES
(8, 4, 6, 'Jss-3', 7, 2, '1hour', 3),
(9, 4, 6, 'year-9', 5, 2, '1hour', 1);

-- --------------------------------------------------------

--
-- Table structure for table `objectives`
--

CREATE TABLE `objectives` (
  `id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `paperID` varchar(255) NOT NULL,
  `question` text NOT NULL,
  `opt1` varchar(255) NOT NULL,
  `opt2` varchar(255) NOT NULL,
  `opt3` varchar(255) NOT NULL,
  `opt4` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `objectives`
--

INSERT INTO `objectives` (`id`, `sch_id`, `user_id`, `paperID`, `question`, `opt1`, `opt2`, `opt3`, `opt4`, `img`) VALUES
(6, 4, 6, 'bdcaa76759', 'The use of income and expenditure instruction to regulate to regulate the economic activities in a country is know as', 'Money country', 'Public debt', 'Public finance', 'Fiscal policy', 'daigrams/messages-2.jpg'),
(7, 4, 6, 'bdcaa76759', 'Taxes which are imposed on goods and services is called', 'Progress tax', 'Improgress tax', 'Direct tax', 'Indirect tax', 'daigrams/messages-1.jpg'),
(8, 4, 6, 'bdcaa76759', 'Which is not a reason why government borrow money.', 'To provide employment opportunities', 'To marry for civil servants', 'To finance budget deficit', 'To control fluctuations in the national income', ''),
(9, 4, 6, 'bdcaa76759', 'The ways of computing the money value of the total value of goods and services produced in a given country over a period of time usually a year is called', 'National income', 'Personal income', 'Gross domestic', 'Net national product', ''),
(10, 4, 6, 'bdcaa76759', 'The government can borrow from external financial instituition such as IMF and world bank through', 'Bond', 'Share', 'Debenture', 'Negociation', ''),
(11, 4, 6, '9635b35537', 'okay trying something', 'true', 'false', '', '', 'daigrams/card.jpg'),
(12, 4, 6, '9635b35537', 'This is question 2', 'true', 'false', 'doing well', 'i know', ''),
(13, 4, 6, 'a68899ea94', 'its done here and now', 'true', 'false', '', '', ''),
(14, 4, 6, 'a68899ea94', 'indeed', 'indeed', 'must', '', '', ''),
(15, 4, 6, 'a68899ea94', 'inside', 'yeah', 'even me', '', '', ''),
(16, 4, 6, 'a68899ea94', 'interested', 'indeed', 'must', '', '', ''),
(17, 4, 6, 'a68899ea94', 'hhhhh', 'trues', 'myself', '', '', ''),
(18, 4, 6, 'a68899ea94', 'num six', 'trues', 'even me', '', '', ''),
(19, 4, 6, 'a68899ea94', 'yeah finale', 'yeah', 'thanks', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `params`
--

CREATE TABLE `params` (
  `id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `paperID` varchar(255) NOT NULL,
  `class` varchar(100) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `term` varchar(100) NOT NULL,
  `section` varchar(100) NOT NULL,
  `year` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `params`
--

INSERT INTO `params` (`id`, `sch_id`, `user_id`, `paperID`, `class`, `subject`, `term`, `section`, `year`, `created_at`) VALUES
(22, 4, 6, '9635b35537', 'jss-3', 'Civic', '1st term', 'theory_questions', '2024/2025', '2024-09-19 17:36:21'),
(26, 4, 6, '9635b35537', 'jss-3', 'Civic', '1st term', 'objectives_questions', '2024/2025', '2024-09-19 23:18:19'),
(27, 4, 6, 'bdcaa76759', 'year-9', 'Civic', '1st term', 'objectives_questions', '2024/2025', '2024-09-20 00:26:03'),
(28, 4, 6, 'bdcaa76759', 'year-9', 'Civic', '1st term', 'theory_questions', '2024/2025', '2024-09-20 02:13:43'),
(30, 5, 6, 'b6d1e3fa4e', 'Jss-3', 'Civic', '1st term', 'objectives_questions', '2024/2025', '2024-09-26 11:57:00'),
(31, 5, 6, 'b6d1e3fa4e', 'Jss-3', 'Civic', '1st term', 'theory_questions', '2024/2025', '2024-09-26 11:57:08'),
(32, 4, 6, 'a68899ea94', 'Jss-3', 'Mathematics', '1st term', 'objectives_questions', '2024/2025', '2024-09-26 12:00:24'),
(33, 4, 6, 'a68899ea94', 'Jss-3', 'Mathematics', '1st term', 'theory_questions', '2024/2025', '2024-09-26 12:25:53');

-- --------------------------------------------------------

--
-- Table structure for table `school`
--

CREATE TABLE `school` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `motto` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school`
--

INSERT INTO `school` (`id`, `name`, `username`, `motto`, `address`, `email`, `phone`, `img`, `created_at`) VALUES
(4, 'CPM International School', 'Glory Land Academy', 'Knowledge is power', 'No.5 model School road GRA Bida. there', 'therefore@email.com', '08122321931', '', '2024-09-19 11:03:52'),
(5, 'Dennis Memorial Grammer School', 'DMGS', 'Knowledge is power', 'placing me there', 'stanvicbest@yahoo.com', '08122321932', '', '2024-09-24 22:55:57'),
(6, 'Peazy Academy', 'peazyacademy', '', '', 'chineduogar7@gmail.com', '08052764954', '', '2024-09-25 16:29:49');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `sch_id`, `user_id`, `subject`) VALUES
(8, 4, 6, 'Civic'),
(9, 5, 6, 'Mathematics');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `sch_id`, `name`, `email`, `phone`, `username`, `img`, `role`, `password`, `created_at`) VALUES
(6, 4, 'Marcus Musa', 'tested@gmail.com', '08122321931', 'stanvic', '', 'Staff', '$2y$10$jSreeM2fdRZ9PYSpN9UyvO1Pp6wn0GeHFDsFZoVFKg6wCF2ZF2gQG', '2024-09-23 11:40:57'),
(7, 5, 'Mr James', 'tested@gmail.com', '08122321931', 'James1', 'assets/img/profile-img.jpg', 'Staff', '$2y$10$jSreeM2fdRZ9PYSpN9UyvO1Pp6wn0GeHFDsFZoVFKg6wCF2ZF2gQG', '2024-09-25 11:22:26'),
(8, 4, 'Mrs Chioma Okere', 'therefore@email.com', '08122321932', 'chioma', '', '', '$2y$10$jSreeM2fdRZ9PYSpN9UyvO1Pp6wn0GeHFDsFZoVFKg6wCF2ZF2gQG', '2024-09-25 12:00:30'),
(9, 6, 'Chinedu Ogar', 'chineduogar7@gmail.com', '08052764954', 'chinedu', '', '', '$2y$10$qQm30aIJSzMuw2JbG2M/vu2wPzhW33ujr.3UnMyTMLykeVQSqjxKi', '2024-09-25 16:32:58');

-- --------------------------------------------------------

--
-- Table structure for table `theory`
--

CREATE TABLE `theory` (
  `id` int(11) NOT NULL,
  `paperID` varchar(255) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `questionID` varchar(255) NOT NULL,
  `questionA` varchar(255) NOT NULL,
  `questionB` varchar(255) DEFAULT NULL,
  `questionC` varchar(255) DEFAULT NULL,
  `questionD` varchar(255) DEFAULT NULL,
  `Ai` varchar(255) DEFAULT NULL,
  `Aii` varchar(255) DEFAULT NULL,
  `Aiii` varchar(255) DEFAULT NULL,
  `Aiv` varchar(255) DEFAULT NULL,
  `Bi` varchar(255) DEFAULT NULL,
  `Bii` varchar(255) DEFAULT NULL,
  `Biii` varchar(255) DEFAULT NULL,
  `Biv` varchar(255) DEFAULT NULL,
  `Ci` varchar(255) DEFAULT NULL,
  `Cii` varchar(255) DEFAULT NULL,
  `Ciii` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theory`
--

INSERT INTO `theory` (`id`, `paperID`, `sch_id`, `user_id`, `questionID`, `questionA`, `questionB`, `questionC`, `questionD`, `Ai`, `Aii`, `Aiii`, `Aiv`, `Bi`, `Bii`, `Biii`, `Biv`, `Ci`, `Cii`, `Ciii`, `img`) VALUES
(16, 'b251da23ce', 4, 6, 'question 1', ';alQFMKOorpppppppppppppppppppppppppp;', 'lkaecnfek;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;mko/p&#39;&#39;&#39;&#39;&#39;&#39;&#39;ac,l a', 'alkfne;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;lkjjjjadn;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;&#39;kk;lsdmjk;dlvns', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(17, 'b251da23ce', 4, 6, 'question 2', 'Q:LLLcfmk@PPPPPPPPPPPPa,', 'dlk\\l;ds&#39;D', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(21, '9635b35537', 4, 6, 'question 1', 'jkjnllllmk/ lkbhgkhfuvhk hkgvjjjjjjjjjjjjjjjj', 'htmtf jhh,,,h ghfujygik', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(23, 'bdcaa76759', 4, 6, 'question 2', 'What is budget', 'Mention 5 uses or importance of budget', 'Explain the following terms', 'What is national debt', 'Deficit Budget', 'Deficit Finance', 'National Policy', 'National interest', '', '', '', '', 'Surplus budget', 'Deficit budget', 'Balance budget', NULL),
(24, '9635b35537', 4, 6, 'question 2', 'this has an image or daigram', 'yeah i comfirmed it', 'i added C', 'i added D also and children elements', 'A child 1', 'A child 2', 'A child 3', 'Achild 4', 'fullage', 'Dont miss this', 'yea its full', 'included', 'To be working', 'was found', 'C also', 'daigrams/card.jpg'),
(27, 'a68899ea94', 4, 6, '1', 'hi there', 'tis one b', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `objectives`
--
ALTER TABLE `objectives`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `params`
--
ALTER TABLE `params`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `school`
--
ALTER TABLE `school`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `theory`
--
ALTER TABLE `theory`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `objectives`
--
ALTER TABLE `objectives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `params`
--
ALTER TABLE `params`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `school`
--
ALTER TABLE `school`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `theory`
--
ALTER TABLE `theory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
