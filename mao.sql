-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2022 at 05:49 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mao`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `moodle` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `alcumus` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `webwork` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `competitions`
--

CREATE TABLE `competitions` (
  `competition_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_forms` tinyint(1) NOT NULL DEFAULT 1,
  `show_bus` tinyint(1) NOT NULL DEFAULT 1,
  `show_room` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `competition_data`
--

CREATE TABLE `competition_data` (
  `unique_id` int(11) NOT NULL,
  `id` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `competition_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `forms` tinyint(1) NOT NULL DEFAULT 0,
  `bus` int(11) NOT NULL DEFAULT 1,
  `room` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `competition_selections`
--

CREATE TABLE `competition_selections` (
  `unique_id` int(11) NOT NULL,
  `id` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `competition_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `competitor_info`
--

CREATE TABLE `competitor_info` (
  `id` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `division` int(11) NOT NULL DEFAULT 0,
  `tshirt_size` set('S','M','L','XL') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `mu_student_id` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_famat_member` tinyint(1) NOT NULL DEFAULT 0,
  `is_national_member` tinyint(1) NOT NULL DEFAULT 0,
  `has_medical` tinyint(1) NOT NULL DEFAULT 0,
  `has_insurance` tinyint(1) NOT NULL DEFAULT 0,
  `has_school_insurance` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_cycled` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `id` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci DEFAULT '',
  `email` text COLLATE utf8mb4_unicode_ci DEFAULT '',
  `phone` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `alternate_phone` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `alternate_ride_home` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_details`
--

CREATE TABLE `payment_details` (
  `payment_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `due_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `people`
--

CREATE TABLE `people` (
  `id` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` int(11) NOT NULL DEFAULT 1,
  `first_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_initial` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_code` set('3029','6030','7009','7020') COLLATE utf8mb4_unicode_ci NOT NULL,
  `graduation_year` int(11) DEFAULT NULL,
  `email` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_confirmed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `p1` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `p2` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `p3` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `p4` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `p5` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `p6` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `p7` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `p8` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_p1_koski` tinyint(1) NOT NULL DEFAULT 0,
  `is_p2_koski` tinyint(1) NOT NULL DEFAULT 0,
  `is_p3_koski` tinyint(1) NOT NULL DEFAULT 0,
  `is_p4_koski` tinyint(1) NOT NULL DEFAULT 0,
  `is_p5_koski` tinyint(1) NOT NULL DEFAULT 0,
  `is_p6_koski` tinyint(1) NOT NULL DEFAULT 0,
  `is_p7_koski` tinyint(1) NOT NULL DEFAULT 0,
  `is_p8_koski` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `unique_id` int(11) NOT NULL,
  `id` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `owed` int(11) NOT NULL DEFAULT 0,
  `paid` int(11) NOT NULL DEFAULT 0,
  `modifiers` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `log` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions_archive`
--

CREATE TABLE `transactions_archive` (
  `unique_id` int(11) NOT NULL,
  `timestamp_archived` timestamp NOT NULL DEFAULT current_timestamp(),
  `id` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `owed` int(11) NOT NULL DEFAULT 0,
  `paid` int(11) NOT NULL DEFAULT 0,
  `modifiers` set('S','M','L','XL') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `log` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `competitions`
--
ALTER TABLE `competitions`
  ADD PRIMARY KEY (`competition_name`);

--
-- Indexes for table `competition_data`
--
ALTER TABLE `competition_data`
  ADD PRIMARY KEY (`unique_id`);

--
-- Indexes for table `competition_selections`
--
ALTER TABLE `competition_selections`
  ADD PRIMARY KEY (`unique_id`);

--
-- Indexes for table `competitor_info`
--
ALTER TABLE `competitor_info`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`unique_id`);

--
-- Indexes for table `transactions_archive`
--
ALTER TABLE `transactions_archive`
  ADD PRIMARY KEY (`unique_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `competition_data`
--
ALTER TABLE `competition_data`
  MODIFY `unique_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `competition_selections`
--
ALTER TABLE `competition_selections`
  MODIFY `unique_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `unique_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions_archive`
--
ALTER TABLE `transactions_archive`
  MODIFY `unique_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
