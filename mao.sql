-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 06, 2021 at 02:30 AM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.3.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `id17038408_mao`
--

-- --------------------------------------------------------

--
-- Table structure for table `competitions`
--

CREATE TABLE `competitions` (
  `competition_id` varchar(128) NOT NULL,
  `competition_name` text NOT NULL DEFAULT '',
  `competition_description` text NOT NULL,
  `payment_id` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `competition_forms`
--

CREATE TABLE `competition_forms` (
  `unique_id` int(11) NOT NULL,
  `id` varchar(7) NOT NULL,
  `competition_id` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `competition_selections`
--

CREATE TABLE `competition_selections` (
  `unique_id` int(11) NOT NULL,
  `competition_id` varchar(128) NOT NULL,
  `id` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `start` timestamp NOT NULL DEFAULT current_timestamp(),
  `end` timestamp NOT NULL DEFAULT current_timestamp(),
  `title` text NOT NULL,
  `description` text NOT NULL,
  `location` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` varchar(7) NOT NULL,
  `code` text NOT NULL DEFAULT 'abc123',
  `time_cycled` timestamp NOT NULL DEFAULT current_timestamp(),
  `time_last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `payment_details`
--

CREATE TABLE `payment_details` (
  `payment_id` varchar(128) NOT NULL,
  `cost` double NOT NULL,
  `info` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `people`
--

CREATE TABLE `people` (
  `id` varchar(7) NOT NULL,
  `first_name` text NOT NULL,
  `minitial` varchar(1) NOT NULL DEFAULT '',
  `last_name` text NOT NULL,
  `email` text NOT NULL,
  `phone` text NOT NULL,
  `division` int(11) NOT NULL,
  `grade` int(11) NOT NULL,
  `p1` varchar(128) NOT NULL DEFAULT '',
  `p2` varchar(128) NOT NULL DEFAULT '',
  `p3` varchar(128) NOT NULL DEFAULT '',
  `p4` varchar(128) NOT NULL DEFAULT '',
  `p5` varchar(128) NOT NULL DEFAULT '',
  `p6` varchar(128) NOT NULL DEFAULT '',
  `p7` varchar(128) NOT NULL DEFAULT '',
  `p8` varchar(128) NOT NULL DEFAULT '',
  `permissions` int(11) NOT NULL DEFAULT 1,
  `mu_student_id` varchar(3) NOT NULL DEFAULT '   ',
  `member_famat` tinyint(1) NOT NULL DEFAULT 0,
  `member_nation` tinyint(1) NOT NULL DEFAULT 0,
  `medical` tinyint(1) NOT NULL DEFAULT 0,
  `insurance` tinyint(1) NOT NULL DEFAULT 0,
  `school_insurance` tinyint(1) NOT NULL DEFAULT 0,
  `time_registered` timestamp NOT NULL DEFAULT current_timestamp(),
  `time_updated` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` varchar(7) NOT NULL,
  `payment_id` text NOT NULL,
  `time_paid` timestamp NOT NULL DEFAULT current_timestamp(),
  `unique_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `competitions`
--
ALTER TABLE `competitions`
  ADD PRIMARY KEY (`competition_id`);

--
-- Indexes for table `competition_forms`
--
ALTER TABLE `competition_forms`
  ADD PRIMARY KEY (`unique_id`);

--
-- Indexes for table `competition_selections`
--
ALTER TABLE competition_approvals
  ADD PRIMARY KEY (`unique_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD UNIQUE KEY `events_event_id_uindex` (`event_id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `payment_details`
--
ALTER TABLE `payment_details`
  ADD UNIQUE KEY `payment_id` (`payment_id`);

--
-- Indexes for table `people`
--
ALTER TABLE `people`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`unique_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `competition_forms`
--
ALTER TABLE `competition_forms`
  MODIFY `unique_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `competition_selections`
--
ALTER TABLE competition_approvals
  MODIFY `unique_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `unique_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
