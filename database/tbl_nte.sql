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
-- Table structure for table `tbl_nte`
--

CREATE TABLE `tbl_nte` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `case_number` varchar(25) NOT NULL,
  `ir_id` int(10) UNSIGNED DEFAULT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `case_details` text NOT NULL,
  `remarks` text DEFAULT NULL,
  `date_served` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `resolution` text DEFAULT NULL,
  `employee_reply` text DEFAULT NULL,
  `reply_date` datetime DEFAULT NULL,
  `status` enum('pending','replied','closed') NOT NULL DEFAULT 'pending',
  `date_created` datetime DEFAULT NULL,
  `user_id_added` int(11) NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_nte`
--
ALTER TABLE `tbl_nte`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tbl_nte_case_number_unique` (`case_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_nte`
--
ALTER TABLE `tbl_nte`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
