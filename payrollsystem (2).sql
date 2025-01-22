-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 22, 2025 at 04:55 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `payrollsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `check_in` datetime NOT NULL,
  `check_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `check_in`, `check_out`) VALUES
(2, 2, '2024-01-15 08:00:00', '2024-01-15 17:00:00'),
(3, 2, '2024-01-16 09:15:00', '2024-01-16 16:45:00'),
(4, 2, '2024-01-17 08:30:00', NULL),
(5, 2, '2024-01-18 08:45:00', '2024-01-18 17:30:00'),
(6, 2, '2024-01-19 10:00:00', '2024-01-19 15:00:00'),
(7, 2, '2024-01-21 08:10:00', '2024-01-21 17:10:00'),
(8, 2, '2024-01-22 08:10:00', '2024-01-22 12:10:00'),
(9, 2, '2025-01-22 09:19:18', '2025-01-22 09:19:21'),
(10, 7, '2025-01-22 09:38:27', '2025-01-22 09:38:29'),
(11, 9, '2025-01-22 09:41:56', '2025-01-22 09:41:58'),
(12, 10, '2025-01-22 10:07:05', '2025-01-22 10:07:11'),
(13, 11, '2025-01-22 10:18:35', '2025-01-22 10:18:38'),
(14, 12, '2025-01-22 02:58:21', '2025-01-22 02:58:25'),
(15, 13, '2025-01-22 03:04:48', '2025-01-22 03:04:52'),
(16, 14, '2025-01-22 03:09:21', '2025-01-22 03:16:18'),
(17, 15, '2025-01-22 11:20:46', '2025-01-22 03:22:27'),
(18, 17, '2025-01-22 11:25:17', '2025-01-22 11:33:09'),
(19, 18, '2025-01-22 03:50:14', '2025-01-22 03:53:36');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `is_admin` int(11) NOT NULL DEFAULT 0,
  `last_login` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `email`, `password`, `role`, `hourly_rate`, `is_admin`, `last_login`) VALUES
(2, 'brendo2', 'brendo2@gmail.com', '$2y$10$Zj/zFQ1ldq1KlWPlamOLueXGkQ6aNK7Qgv2DZ0jFnuicWkim/5PjO', 'employee', 25.00, 0, '2025-01-22 01:19:50'),
(7, 'brendo5', 'brendo5@gmail.com', '$2y$10$JC5qNeqJLZ95c7ae5OlEr.hV5oCTDY5USLYNcjJxdad1j/E05Ug3q', 'employee', 0.07, 0, '2025-01-22 09:38:25'),
(8, 'admin', 'admin@gmail.com', '$2y$10$hm6nb/71EFFDiTIVHrjMAOEkapnnRaeSBDUCBxY.n1Xn21AX.Q/8e', 'admin', 0.05, 0, '2025-01-22 01:18:59'),
(9, 'brendo6', 'brendo6@gmail.com', '$2y$10$IivjnCL/6Ixdt6CxPamwkOl9ApaOUyMRzsk6uqGgdXbofkJRzFSJK', 'employee', 0.09, 0, '2025-01-22 09:41:53'),
(10, 'brendo7', 'brendo7@gmail.com', '$2y$10$zthnXzfriHjW8plnPvsLBeKU1.T8rCer9CiSYkAoxLJt1dcli9mYm', 'employee', 25.00, 0, '2025-01-22 10:07:02'),
(11, 'brendo8', 'brendo8@gmail.com', '$2y$10$7NvpFyrNvf7VCfsJMgbbluzehT0MELj9nYYi7h2VN6stzng3uC9Ea', 'employee', 25.00, 0, '2025-01-22 10:18:31'),
(12, 'brendo9', 'brendo9@gmail.com', '$2y$10$YW0xRACnNvX2HbXp1tNNsea/G9TR50piQLKuzpEVDvSGdlKQYbd8K', 'employee', 25.00, 0, '2025-01-22 10:55:06'),
(13, 'brendo10', 'brendo10@gmail.com', '$2y$10$4OEzdzVE70MQViT643w05OTQgUbyjjqdUnRoV9A5DfvX5ZvbrixGi', 'employee', 25.00, 0, '2025-01-22 11:04:44'),
(14, 'brendo', 'brendo11@gmail.com', '$2y$10$3PRbjrl35JwyW/ittb5aPOj3YAK6sp/bwvqzp.uW/2Y/zKgCxJw2i', 'employee', 25.00, 0, '2025-01-22 11:08:29'),
(15, 'brendo12', 'brendo12@gmail.com', '$2y$10$DUxuXig0cBDOw9iBfgV6VO5IgfslVcZAKfA7qhgoLBeX4U3xCelAO', 'employee', 25.00, 0, '2025-01-22 11:19:04'),
(17, 'brendo12', 'brendo13@gmail.com', '$2y$10$0hrrbqrmtjEpisGP8z.bYeZPu1PCYmo2ZnN1ZvsILLIQNpOXpXPje', 'employee', 25.00, 0, '2025-01-22 11:25:15'),
(18, 'brendo14', 'brendo14@gmail.com', '$2y$10$gYHxAODYrBBTn2xNFMvXTuHDuUWUb/F90S7PEFL8MNxaFIsEmPysW', 'employee', 25.00, 0, '2025-01-22 11:50:11');

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `pay_period_start` date NOT NULL,
  `pay_period_end` date NOT NULL,
  `gross_pay` decimal(10,2) NOT NULL,
  `tax_deduction` decimal(10,2) NOT NULL,
  `net_pay` decimal(10,2) NOT NULL,
  `sss_deduction` decimal(10,2) DEFAULT NULL,
  `philhealth_deduction` decimal(10,2) DEFAULT NULL,
  `pagibig_deduction` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `employee_id`, `pay_period_start`, `pay_period_end`, `gross_pay`, `tax_deduction`, `net_pay`, `sss_deduction`, `philhealth_deduction`, `pagibig_deduction`) VALUES
(1, 2, '2025-01-23', '2025-01-22', 0.00, 0.00, 0.00, NULL, NULL, NULL),
(2, 2, '2025-01-23', '2025-01-22', 0.00, 0.00, 0.00, NULL, NULL, NULL),
(3, 7, '2025-01-20', '2025-01-23', 0.00, 0.00, -410.00, 135.00, 175.00, 100.00),
(4, 2, '2024-01-15', '2024-01-22', 981.25, 0.00, 571.25, 135.00, 175.00, 100.00),
(5, 2, '2024-01-15', '2024-01-22', 981.25, 0.00, 571.25, 135.00, 175.00, 100.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_attendance_employee` (`employee_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payroll_employee` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_attendance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `fk_payroll_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
