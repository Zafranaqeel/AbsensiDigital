-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2026 at 02:08 PM
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
-- Database: `absensidigital`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `jarak` float DEFAULT NULL,
  `status` enum('hadir','terlambat','izin','sakit') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `surat` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `user_id`, `tanggal`, `jam_masuk`, `jam_keluar`, `latitude`, `longitude`, `jarak`, `status`, `created_at`, `surat`) VALUES
(2, 3, '2026-05-15', '06:57:09', NULL, '-6.8444467934278475', '108.6212129660167', 83, '', '2026-05-14 23:57:09', NULL),
(3, 3, '2026-05-16', '08:12:42', NULL, '-6.8444467934278475', '108.6212129660167', 83, '', '2026-05-16 01:12:42', NULL),
(4, 3, '2026-05-16', '08:38:43', NULL, '-6.844441492501955', '108.6212150659229', 84, '', '2026-05-16 01:38:43', NULL),
(5, 3, '2026-05-16', '08:40:46', NULL, '-6.844441492501955', '108.6212150659229', 84, '', '2026-05-16 01:40:46', NULL),
(6, 3, '2026-05-16', '08:40:49', NULL, '-6.844441492501955', '108.6212150659229', 84, '', '2026-05-16 01:40:49', NULL),
(7, 3, '2026-05-16', '08:41:01', NULL, '-6.844446581687517', '108.6212138374988', 84, '', '2026-05-16 01:41:01', NULL),
(8, 3, '2026-05-17', '19:00:15', NULL, '-6.844447279838035', '108.62120800835135', 83, 'terlambat', '2026-05-17 12:00:15', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user','manager') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`) VALUES
(1, 'Admin', 'admin@gmail.com', '200ceb26807d6bf99fd6f4f0d1ca54d4', 'admin'),
(2, 'Manager', 'manager@example.com', '4a7d1ed414474e4033ac29ccb8653d9b', 'manager'),
(3, 'Zafran Aqeel Azizi Agung', 'zafranaqeel62@gmail.com', '879d8048c0828dabdfaff07fd2ea93a2', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2425003;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
