-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2026
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
-- Database: `cocu_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `dob`, `_password`) VALUES
(2, 'Tester Lee', 'testing123@gmail.com', '2024-04-16', 'ac1c8d64fd23ae5a7eac5b7f7ffee1fa'),
(4, 'tester two', 'tester2@gmail.com', '2026-03-15', '2e9fcf8e3df4d415c96bcf288d5ca4ba'),
(5, 'Tester', 'tester123@gmail.com', '2026-04-02', '8e607a4752fa2e59413e5790536f2b42'),
(7, 'tester999', 'tester999@gmail.com', '2001-01-01', 'f73870686dee24a4d1a123a1d3d0e8f9'),
(8, 'vio', 'vio@example.com', '2001-09-29', 'd438512a6eca89883b99575a9fc6067e'),
(9, 'yong', 'yong@gmail.com', '2008-06-26', '202cb962ac59075b964b07152d234b70'),
(10, 'yew', 'yew@gmail.com', '2006-03-20', 'caf1a3dfb505ffed0d024130f58c5cfa');

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `achievement_type` varchar(50) NOT NULL,
  `date_received` date NOT NULL,
  `organisation` varchar(255) NOT NULL,
  `description` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`id`, `student_id`, `title`, `achievement_type`, `date_received`, `organisation`, `description`) VALUES
(5, 8, 'Certificate of Excellence', 'Certificate', '2025-10-15', 'Universiti Tunku Abdul Rahman (UTAR)', 'Highest grade in Developing Project');

-- --------------------------------------------------------

--
-- Table structure for table `club_tracker`
--

CREATE TABLE `club_tracker` (
  `club_tracker_id` int(12) NOT NULL,
  `student_id` int(12) NOT NULL,
  `club_name` varchar(255) NOT NULL,
  `club_role` varchar(20) NOT NULL,
  `join_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `club_tracker`
--

INSERT INTO `club_tracker` (`club_tracker_id`, `student_id`, `club_name`, `club_role`, `join_date`) VALUES
(1, 9, 'Basketball Club', 'Treasurer', '2026-02-11'),
(2, 10, 'Badminton Club', 'Member', '2025-10-21');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `date_time` datetime NOT NULL,
  `event_loc` varchar(255) NOT NULL,
  `event_type` varchar(30) NOT NULL DEFAULT 'Event',
  `description` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `student_id`, `event_name`, `date_time`, `event_loc`, `event_type`, `description`) VALUES
(8, 8, 'Nekoya helper recruitment', '2026-04-14 22:40:00', 'Block H', 'Event', 'Helper Recruitment Event for the Grand Anime Festival Nekoya'),
(13, 8, 'Chess competition', '2026-04-25 12:00:00', 'Block M', 'Competition', 'Chess Competition hosted by Magnus Carlsen.');

-- --------------------------------------------------------

--
-- Table structure for table `merits`
--

CREATE TABLE `merits` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `hours` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `merits`
--

INSERT INTO `merits` (`id`, `student_id`, `hours`, `description`, `date`) VALUES
(1, 8, 5, 'Volunteer for university open day registration and crowd management.', '2026-04-01'),
(2, 8, 6, 'Participated in a faculty leadership workshop.', '2026-03-20'),
(3, 8, 4, 'Served as a committee member for a community service event organized by the club.', '2026-02-28');

-- --------------------------------------------------------

--
-- Table structure for table `participations`
--

CREATE TABLE `participations` (
  `student_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `registration_date` datetime NOT NULL DEFAULT current_timestamp(),
  `role` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

ALTER TABLE `club_tracker`
  ADD PRIMARY KEY (`club_tracker_id`),
  ADD KEY `student_id` (`student_id`);

ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

ALTER TABLE `merits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

ALTER TABLE `participations`
  ADD KEY `student_id` (`student_id`, `event_id`),
  ADD KEY `fk_event_id` (`event_id`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `club_tracker`
  MODIFY `club_tracker_id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

ALTER TABLE `merits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

ALTER TABLE `achievements`
  ADD CONSTRAINT `achievements_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `club_tracker`
  ADD CONSTRAINT `club_tracker_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `merits`
  ADD CONSTRAINT `merits_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `participations`
  ADD CONSTRAINT `fk_event_id` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;