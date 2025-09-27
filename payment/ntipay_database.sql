-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 20, 2025 at 12:00 PM
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
-- Database: `ntipay`
--
CREATE DATABASE IF NOT EXISTS `ntipay` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ntipay`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `balance` decimal(10,2) DEFAULT 1000.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `balance`, `created_at`) VALUES
(1, 'Mariam', 'mariam@ntipay.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1500.00, '2025-01-01 10:00:00'),
(2, 'Banan', 'banan@ntipay.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2000.00, '2025-01-02 11:30:00'),
(3, 'Ziad', 'ziad@ntipay.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 750.00, '2025-01-03 14:15:00'),
(4, 'Hager', 'hager@ntipay.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1200.00, '2025-01-04 09:45:00'),
(5, 'Gamal', 'gamal@ntipay.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1200.00, '2025-01-04 09:45:00'),
(6, 'Abubakar', 'abubakar@ntipay.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 950.00, '2025-01-05 16:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Mariam', 'admin1@ntipay.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-01-01 08:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'Print Invoice',
  `number` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `due_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('unpaid','overdue','paid') DEFAULT 'unpaid',
  `description` text DEFAULT NULL,
  `pages` int(11) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `paid_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`id`, `user_id`, `type`, `number`, `date`, `due_date`, `amount`, `status`, `description`, `pages`, `color`, `paid_date`, `created_at`) VALUES
(1, 1, 'Internet Bill', 'INT-2025-001', '2025-01-15', '2025-02-15', 150.00, 'unpaid', 'Monthly internet service 100 Mbps', NULL, NULL, NULL, '2025-01-15 08:30:00'),
(2, 1, 'Electricity Bill', 'ELC-2025-002', '2025-01-20', '2025-02-10', 275.50, 'overdue', 'Monthly electricity consumption 450 kWh', NULL, NULL, NULL, '2025-01-20 10:45:00'),
(3, 1, 'Water Bill', 'WTR-2025-003', '2025-01-25', '2025-02-25', 125.00, 'unpaid', 'Monthly water usage 25 cubic meters', NULL, NULL, NULL, '2025-01-25 12:20:00'),
(4, 2, 'Internet Bill', 'INT-2025-004', '2025-02-01', '2025-03-01', 200.00, 'unpaid', 'Monthly internet service 200 Mbps', NULL, NULL, NULL, '2025-02-01 09:15:00'),
(5, 2, 'Gas Bill', 'GAS-2025-005', '2025-02-05', '2025-03-05', 90.00, 'unpaid', 'Monthly natural gas consumption', NULL, NULL, NULL, '2025-02-05 11:00:00'),
(6, 3, 'Electricity Bill', 'ELC-2025-006', '2025-02-10', '2025-03-10', 320.75, 'unpaid', 'Monthly electricity consumption 520 kWh', NULL, NULL, NULL, '2025-02-10 13:45:00'),
(7, 3, 'Phone Bill', 'PHN-2025-007', '2025-02-12', '2025-02-01', 95.00, 'overdue', 'Monthly mobile phone service', NULL, NULL, NULL, '2025-02-12 15:30:00'),
(8, 4, 'Cable TV', 'TV-2025-008', '2025-02-15', '2025-03-15', 85.00, 'unpaid', 'Monthly cable TV subscription', NULL, NULL, NULL, '2025-02-15 16:00:00'),
(9, 4, 'Insurance', 'INS-2025-009', '2025-02-18', '2025-03-18', 450.00, 'unpaid', 'Monthly car insurance premium', NULL, NULL, NULL, '2025-02-18 09:30:00'),
(10, 5, 'Internet Bill', 'INT-2025-010', '2025-02-20', '2025-03-20', 120.00, 'unpaid', 'Monthly internet service 50 Mbps', NULL, NULL, NULL, '2025-02-20 11:15:00'),
(11, 5, 'Water Bill', 'WTR-2025-011', '2025-02-22', '2025-03-22', 110.00, 'unpaid', 'Monthly water usage 22 cubic meters', NULL, NULL, NULL, '2025-02-22 13:00:00'),
(12, 2, 'Electricity Bill', 'ELC-2025-012', '2025-02-25', '2025-03-25', 298.25, 'unpaid', 'Monthly electricity consumption 480 kWh', NULL, NULL, NULL, '2025-02-25 14:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `receipt_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `bill_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`bill_ids`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_transactions`
--

INSERT INTO `payment_transactions` (`id`, `user_id`, `receipt_number`, `total_amount`, `payment_date`, `bill_ids`) VALUES
(1, 2, 'REC-2025-123456', 290.00, '2025-02-15 10:30:00', '[4,5]'),
(2, 3, 'REC-2025-789012', 95.00, '2025-02-16 14:20:00', '[7]');

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_due_date` (`due_date`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipt_number` (`receipt_number`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `payment_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;