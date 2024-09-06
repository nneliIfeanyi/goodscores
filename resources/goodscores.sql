-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 06, 2024 at 03:44 AM
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
  `num_rows` varchar(255) NOT NULL,
  `num_rows2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `sch_id`, `user_id`, `classname`, `num_rows`, `num_rows2`) VALUES
(4, 3, 5, 'JS1', '20', 5),
(5, 2, 4, 'Year 7', '10', 4);

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
(1, 2, 4, 'cff9a2172c', 'Who are you', 'me', 'her', 'myself', 'others', ''),
(2, 2, 4, 'cff9a2172c', 'Who are they sef', 'traitors', 'liers', 'set up', 'others', ''),
(3, 2, 4, 'cff9a2172c', 'I will still come abi', 'yes', 'okay', 'alright', 'maybe', ''),
(4, 2, 4, 'cff9a2172c', 'i will be there', 'okay', 'when', 'now', 'hopefully', ''),
(5, 2, 4, 'cff9a2172c', 'I am going to come tommorro', 'A', 'B', 'C', 'D', ''),
(8, 2, 4, 'cff9a2172c', 'this remains six', 'true', 'false', '', '', ''),
(9, 2, 4, 'cff9a2172c', 'exactly seventh question', 'A', 'B', 'opt C', 'D', ''),
(31, 2, 4, '087cc92b34', 'i am here okay', 'yes', 'you', 'there', 'okay', ''),
(32, 2, 4, '087cc92b34', 'yeah i heard u', 'yes', 'you baa', 'okay', 'come fes', '');

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
  `duration` varchar(100) NOT NULL,
  `instructions` varchar(255) NOT NULL,
  `year` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `params`
--

INSERT INTO `params` (`id`, `sch_id`, `user_id`, `paperID`, `class`, `subject`, `term`, `section`, `duration`, `instructions`, `year`, `created_at`) VALUES
(12, 2, 4, '087cc92b34', 'Year 7', 'English Language', '1st term', 'objectives_questions', '', '', '2024/2025', '2024-09-05 23:22:16'),
(13, 2, 4, '087cc92b34', 'Year 7', 'English Language', '1st term', 'theory_questions', '', '', '2024/2025', '2024-09-05 23:22:52'),
(14, 3, 5, '125f0cd744', 'JS1', 'Civic', '1st term', 'objectives_questions', '', '', '2024/2025', '2024-09-06 00:51:46');

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
  `session` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school`
--

INSERT INTO `school` (`id`, `name`, `username`, `motto`, `address`, `session`, `email`, `phone`, `created_at`) VALUES
(2, 'D.M.G.S Grammer School', 'dmgs', '', '', '', 'dmgs@gmail.com', '08122321933', '2024-08-21 03:52:18'),
(3, 'Brilliant Kids Academy', 'Bkids', '', '', '', 'B_kids@mail.com', '08122321932', '2024-08-21 03:59:39');

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
(1, 2, 4, 'English Language'),
(3, 2, 4, 'Civic'),
(4, 3, 5, 'Civic');

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
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `sch_id`, `name`, `email`, `phone`, `username`, `password`) VALUES
(4, 2, 'Nneli Ifeanyi Victor', 'stanvicbest@gmail.com', '08122321931', 'Stanvic', '$2y$10$DH9El2t0vY8bqoT.2eU1mOMqhrrfIlktdJ05hAVvIB26eYQl7jiHC'),
(5, 3, 'Blessing Ogie Ukaekwe', 'stanvicbest@gmail.com', '08122321932', 'teacher1', '$2y$10$CBpxbuT7jz.qUVNJU7Qa2.7PRu6xIfrS44L97OIPn9lOPE9VTYYVS');

-- --------------------------------------------------------

--
-- Table structure for table `theory`
--

CREATE TABLE `theory` (
  `id` int(11) NOT NULL,
  `paperID` varchar(255) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theory`
--

INSERT INTO `theory` (`id`, `paperID`, `sch_id`, `user_id`, `question`, `img`) VALUES
(2, '', 3, 5, 'this is no one\r\ni mean it', NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `objectives`
--
ALTER TABLE `objectives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `params`
--
ALTER TABLE `params`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `school`
--
ALTER TABLE `school`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `theory`
--
ALTER TABLE `theory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
