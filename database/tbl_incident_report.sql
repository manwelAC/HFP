-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2026 at 12:56 PM
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
-- Database: `hfp_prl_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_incident_report`
--

CREATE TABLE `tbl_incident_report` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `case_number` varchar(25) NOT NULL,
  `reported_by` int(10) UNSIGNED NOT NULL,
  `complainant_position` varchar(155) DEFAULT NULL,
  `report_datetime` datetime NOT NULL,
  `incident` text NOT NULL,
  `incident_date` date NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `witnesses` text DEFAULT NULL,
  `status` enum('pending','reviewed') NOT NULL DEFAULT 'pending',
  `date_created` datetime DEFAULT NULL,
  `user_id_added` int(11) NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_incident_report`
--
ALTER TABLE `tbl_incident_report`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_incident_report`
--
ALTER TABLE `tbl_incident_report`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
