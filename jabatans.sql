-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 18, 2026 at 04:48 AM
-- Server version: 8.0.46-0ubuntu0.24.04.5
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `im_system_prod`
--

-- --------------------------------------------------------

--
-- Table structure for table `jabatans`
--

CREATE TABLE `jabatans` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `level` int NOT NULL DEFAULT '1',
  `is_kadiv_utama` tinyint(1) NOT NULL DEFAULT '0',
  `is_agh` tinyint(1) NOT NULL DEFAULT '0',
  `is_gh` tinyint(1) NOT NULL DEFAULT '0',
  `gh_atasan_jabatan_id` bigint UNSIGNED DEFAULT NULL,
  `is_setara_kanit` tinyint(1) NOT NULL DEFAULT '0',
  `locked_unit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `is_direktur_rs` tinyint(1) NOT NULL DEFAULT '0',
  `is_hcm` tinyint(1) NOT NULL DEFAULT '0',
  `is_keuangan` tinyint(1) NOT NULL DEFAULT '0',
  `default_doc_types` json DEFAULT NULL COMMENT 'Default applicable doc_types untuk jabatan ini. Nullable = semua jenis.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jabatans`
--

INSERT INTO `jabatans` (`id`, `nama`, `created_at`, `updated_at`, `level`, `is_kadiv_utama`, `is_agh`, `is_gh`, `gh_atasan_jabatan_id`, `is_setara_kanit`, `locked_unit`, `unit_id`, `is_direktur_rs`, `is_hcm`, `is_keuangan`, `default_doc_types`) VALUES
(1, 'Staff IT', NULL, '2026-08-12 23:19:46', 1, 0, 0, 0, NULL, 0, NULL, 1, 0, 0, 0, NULL),
(2, 'Kepala Unit IT', NULL, '2026-08-13 09:25:22', 2, 0, 0, 0, NULL, 0, NULL, 1, 0, 0, 0, NULL),
(3, 'IT Corporate', NULL, '2026-10-07 08:19:40', 5, 0, 1, 0, NULL, 0, NULL, 1, 0, 0, 0, NULL),
(4, 'Ka Divisi Keperawatan', NULL, '2026-08-21 08:53:59', 4, 1, 0, 0, NULL, 0, NULL, 15, 0, 0, 0, NULL),
(6, 'Ka Divisi Keuangan & Akuntansi', NULL, '2026-08-21 08:48:56', 4, 0, 0, 0, NULL, 0, NULL, 9, 0, 0, 1, NULL),
(7, 'Ka Divisi Marketing', NULL, '2026-09-02 02:27:12', 4, 1, 0, 0, NULL, 0, NULL, 8, 0, 0, 0, NULL),
(8, 'Ka Divisi HCM', NULL, '2026-09-04 07:07:08', 4, 1, 0, 0, NULL, 0, NULL, 11, 0, 1, 0, NULL),
(9, 'Ka Divisi JKN', NULL, '2026-08-21 08:54:23', 4, 0, 0, 0, NULL, 0, NULL, 7, 0, 0, 0, NULL),
(10, 'Ka Divisi Pelayanan Medis', NULL, '2026-08-21 08:54:30', 4, 0, 0, 0, NULL, 0, NULL, 18, 0, 0, 0, NULL),
(14, 'Ketua Komite Mutu', NULL, '2026-08-11 10:25:57', 3, 0, 0, 0, NULL, 0, NULL, 20, 0, 0, 0, NULL),
(15, 'Ketua Komite Mutu & Keselamatan Pasien', NULL, '2026-09-05 04:16:33', 4, 1, 0, 0, NULL, 0, NULL, 20, 0, 0, 0, NULL),
(16, 'Komite Mutu dan Keselamatan Pasien', NULL, NULL, 3, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(17, 'Manajer Keperawatan', NULL, NULL, 3, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(18, 'Manajer Marketing', NULL, '2026-08-14 11:48:45', 3, 0, 0, 0, NULL, 0, NULL, 8, 0, 0, 0, NULL),
(19, 'Manager', NULL, NULL, 4, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(20, 'Direktur', NULL, NULL, 5, 0, 0, 0, NULL, 0, NULL, NULL, 1, 0, 0, NULL),
(21, 'Sekretaris Direktur', NULL, '2026-11-18 01:39:16', 5, 0, 0, 0, NULL, 0, NULL, 25, 0, 1, 0, NULL),
(22, 'GH Pelayanan Medis', NULL, '2026-08-07 08:45:14', 5, 0, 0, 1, NULL, 0, NULL, 18, 0, 0, 0, NULL),
(23, 'GH FMS & GA', NULL, '2026-08-11 09:42:47', 5, 0, 0, 1, NULL, 0, NULL, 77, 0, 0, 0, NULL),
(24, 'GH Mutu', NULL, NULL, 5, 0, 0, 1, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(25, 'GH JKN', NULL, NULL, 5, 0, 0, 1, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(26, 'GH Keperawatan', NULL, '2026-09-21 08:06:13', 5, 0, 0, 1, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(27, 'GH Penunjang Medis', NULL, '2026-09-22 01:39:40', 5, 0, 0, 1, NULL, 0, NULL, 17, 0, 0, 0, NULL),
(28, 'GH HCM', NULL, '2026-08-24 14:31:20', 5, 0, 0, 1, NULL, 0, NULL, 11, 0, 1, 0, NULL),
(29, 'GH Marketing', NULL, NULL, 5, 0, 0, 1, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(30, 'GH Keuangan', NULL, NULL, 5, 0, 0, 1, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(31, 'GH Procurement', NULL, NULL, 5, 0, 0, 1, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(32, 'Direktur RS Awal Bros Group', NULL, NULL, 6, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(33, 'CEO', NULL, NULL, 7, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(35, 'Regional', '2026-07-27 08:37:35', '2026-07-28 00:36:18', 3, 0, 0, 0, NULL, 0, 'Marketing', NULL, 0, 0, 0, NULL),
(36, 'AGH IT', '2026-08-04 08:25:11', '2026-11-05 06:26:12', 5, 0, 1, 0, 23, 0, NULL, 1, 0, 0, 0, NULL),
(37, 'Ka Divisi IT', '2026-08-05 12:32:18', '2026-08-21 08:54:38', 4, 0, 0, 0, NULL, 0, NULL, 1, 0, 0, 0, NULL),
(38, 'Staff Keperawatan', '2026-08-07 08:29:49', '2026-08-07 08:30:14', 1, 0, 0, 0, NULL, 0, NULL, 15, 0, 0, 0, NULL),
(39, 'AGH Product Specialist', '2026-08-07 09:27:50', '2026-11-18 03:22:58', 5, 0, 1, 0, 29, 0, NULL, 8, 0, 0, 0, NULL),
(40, 'AGH FMS & GA', '2026-08-07 09:28:08', '2026-08-14 22:18:35', 5, 0, 1, 0, NULL, 0, NULL, 77, 0, 0, 0, NULL),
(41, 'Corporate Coordinator Ancillary', '2026-08-07 09:29:03', '2026-11-18 03:24:09', 5, 0, 0, 0, NULL, 0, NULL, 17, 0, 0, 0, NULL),
(42, 'Corporate Coordinator Keperawatan', '2026-08-07 09:29:19', '2026-08-07 09:29:19', 5, 0, 0, 0, NULL, 0, NULL, 15, 0, 0, 0, NULL),
(43, 'Corporate Coordinator HC', '2026-08-07 09:29:45', '2026-09-07 03:01:41', 5, 0, 0, 0, NULL, 0, NULL, 11, 0, 1, 0, NULL),
(44, 'Head Marketing Communication', '2026-08-07 09:31:24', '2026-08-07 09:31:24', 4, 0, 0, 0, NULL, 0, NULL, 8, 0, 0, 0, NULL),
(45, 'Head Corporate Communication', '2026-08-07 09:31:38', '2026-08-07 09:31:38', 4, 0, 0, 0, NULL, 0, NULL, 8, 0, 0, 0, NULL),
(46, 'PJ Service Review & AR Control', '2026-08-07 09:31:54', '2026-08-07 09:31:54', 4, 0, 0, 0, NULL, 0, NULL, 8, 0, 0, 0, NULL),
(47, 'Medical Officer', '2026-08-07 09:32:22', '2026-11-18 03:23:13', 5, 0, 0, 0, NULL, 0, 'Medical Officer', 18, 0, 0, 0, NULL),
(48, 'Quality Officer', '2026-08-07 09:32:46', '2026-08-07 09:32:46', 4, 0, 0, 0, NULL, 0, NULL, 14, 0, 0, 0, NULL),
(49, 'Marketing Officer', '2026-08-07 09:33:09', '2026-11-18 03:23:33', 5, 0, 0, 0, NULL, 0, 'Marketing', 8, 0, 0, 0, NULL),
(50, 'Account Receivable Officer', '2026-08-07 09:33:28', '2026-08-07 09:33:28', 4, 0, 0, 0, NULL, 0, NULL, 10, 0, 0, 0, NULL),
(51, 'Tax Officer', '2026-08-07 09:33:49', '2026-08-07 09:33:49', 4, 0, 0, 0, NULL, 0, NULL, 10, 0, 0, 0, NULL),
(52, 'Procurement Officer', '2026-08-07 09:34:10', '2026-10-12 03:41:00', 5, 0, 0, 0, NULL, 0, 'Procurement Officer', 6, 0, 0, 0, NULL),
(53, 'Legal Officer', '2026-08-07 09:34:35', '2026-09-07 13:47:25', 5, 0, 0, 0, NULL, 0, 'Legal', 11, 0, 0, 0, NULL),
(54, 'Ka Divisi Penunjang Medis', '2026-08-11 10:24:40', '2026-09-15 07:46:47', 4, 1, 0, 0, NULL, 0, NULL, 17, 0, 0, 0, NULL),
(55, 'Kepala Unit HC (Pelayanan)', '2026-08-11 10:27:18', '2026-08-24 14:32:50', 2, 0, 0, 0, NULL, 1, NULL, 11, 0, 1, 0, NULL),
(56, 'Kepala Unit HC (Pengembangan)', '2026-08-11 10:27:56', '2026-08-24 14:32:56', 2, 0, 0, 0, NULL, 1, NULL, 11, 0, 1, 0, NULL),
(57, 'Ka Unit FMS & GA', '2026-08-11 10:32:53', '2026-11-05 10:07:59', 2, 0, 0, 0, NULL, 0, NULL, 77, 0, 0, 0, NULL),
(58, 'Ka Unit Pelayanan Medis', '2026-08-11 10:44:13', '2026-08-11 10:44:13', 3, 0, 0, 0, NULL, 0, NULL, 18, 0, 0, 0, NULL),
(59, 'Ka Unit Penunjang Medis', '2026-08-11 10:44:38', '2026-08-21 08:40:08', 2, 0, 0, 0, NULL, 0, NULL, 17, 0, 0, 0, NULL),
(60, 'Staff Keuangan & Akuntansi', '2026-08-11 10:46:41', '2026-11-10 08:39:26', 1, 0, 0, 0, NULL, 0, NULL, 9, 0, 0, 1, NULL),
(61, 'Staff Pelayanan Medis', '2026-08-11 10:47:00', '2026-08-12 23:20:00', 1, 0, 0, 0, NULL, 0, NULL, 18, 0, 0, 0, NULL),
(62, 'Staff Penunjang Medis', '2026-08-11 10:47:17', '2026-08-12 23:20:24', 1, 0, 0, 0, NULL, 0, NULL, 17, 0, 0, 0, NULL),
(63, 'Staff Komite Mutu', '2026-08-11 10:47:48', '2026-09-03 08:44:29', 1, 0, 0, 0, NULL, 1, NULL, 20, 0, 0, 0, NULL),
(64, 'Ka Unit Komite Mutu', '2026-08-11 10:50:54', '2026-08-11 10:50:54', 3, 0, 0, 0, NULL, 0, NULL, 20, 0, 0, 0, NULL),
(65, 'Staff Komite PPI', '2026-08-11 10:51:36', '2026-09-05 02:19:26', 1, 0, 0, 0, NULL, 1, NULL, 21, 0, 0, 0, NULL),
(66, 'Staff HC', '2026-08-11 10:52:44', '2026-08-24 14:32:00', 1, 0, 0, 0, NULL, 0, NULL, 11, 0, 1, 0, NULL),
(67, 'Staff FMS & GA', '2026-08-11 10:53:13', '2026-11-05 10:07:59', 1, 0, 0, 0, NULL, 0, NULL, 77, 0, 0, 0, NULL),
(68, 'Ka Unit Logistik Umum', '2026-08-11 10:55:39', '2026-10-07 08:27:03', 3, 0, 0, 0, NULL, 0, NULL, 77, 0, 0, 0, NULL),
(69, 'Ka Unit URT', '2026-08-11 10:55:50', '2026-08-11 10:55:50', 3, 0, 0, 0, NULL, 0, NULL, 23, 0, 0, 0, NULL),
(70, 'Ka. Dept Keperawatan', '2026-08-11 10:56:43', '2026-08-11 10:56:43', 3, 0, 0, 0, NULL, 0, NULL, 15, 0, 0, 0, NULL),
(71, 'Ka. Dept Marketing', '2026-08-11 10:57:04', '2026-09-02 06:41:51', 3, 0, 0, 0, NULL, 1, NULL, 8, 0, 0, 0, NULL),
(72, 'Projek Manajer', '2026-08-11 10:57:14', '2026-08-11 10:57:14', 3, 0, 0, 0, NULL, 0, NULL, 24, 0, 0, 0, NULL),
(73, 'Staff Marketing', '2026-08-11 10:57:33', '2026-09-21 01:27:41', 1, 0, 0, 0, NULL, 1, NULL, 8, 0, 0, 0, NULL),
(74, 'Ka Divisi FMS & GA', '2026-08-12 11:55:58', '2026-11-05 10:07:59', 4, 1, 0, 0, NULL, 0, NULL, 77, 0, 0, 0, NULL),
(75, 'Kanit Keperawatan', '2026-08-12 22:50:56', '2026-10-28 03:53:25', 2, 0, 0, 0, NULL, 1, NULL, 15, 0, 0, 0, NULL),
(76, 'Ka Unit JKN', '2026-08-13 09:19:26', '2026-09-06 10:47:17', 2, 0, 0, 0, NULL, 1, NULL, 7, 0, 0, 0, NULL),
(77, 'Ka Divisi Pelayanan & Penunjang Medis', '2026-08-14 11:17:01', '2026-08-21 08:55:00', 4, 0, 0, 0, NULL, 0, NULL, 26, 0, 0, 0, NULL),
(79, 'Ka. Departemen Pelayanan Medis', '2026-08-14 11:28:02', '2026-08-14 11:28:02', 3, 0, 0, 0, NULL, 0, NULL, 18, 0, 0, 0, NULL),
(80, 'Ka. Departemen Penunjang Medis', '2026-08-14 11:28:41', '2026-08-21 08:25:40', 3, 0, 0, 0, NULL, 0, NULL, 17, 0, 0, 0, NULL),
(81, 'Ka. Departemen FMS & GA', '2026-08-14 11:36:56', '2026-11-05 10:07:59', 3, 0, 0, 0, NULL, 0, NULL, 77, 0, 0, 0, NULL),
(82, 'Ka. Unit MCU', '2026-08-14 11:37:53', '2026-09-16 03:49:27', 2, 0, 0, 0, NULL, 1, NULL, 18, 0, 0, 0, NULL),
(83, 'Komite Keperawatan', '2026-08-14 11:38:36', '2026-08-24 11:14:10', 2, 0, 0, 0, NULL, 1, NULL, 15, 0, 0, 0, NULL),
(84, 'SOD', '2026-08-14 11:38:59', '2026-08-14 11:38:59', 2, 0, 0, 0, NULL, 0, NULL, 15, 0, 0, 0, NULL),
(85, 'Mutu Keperawatan', '2026-08-14 11:39:44', '2026-08-24 10:36:50', 2, 0, 0, 0, NULL, 0, NULL, 15, 0, 0, 0, NULL),
(86, 'Ka. Unit Farmasi', '2026-08-14 11:40:23', '2026-09-29 08:55:57', 2, 0, 0, 0, NULL, 1, NULL, 47, 0, 0, 0, NULL),
(87, 'Ka. Unit Perbekalan Farmasi', '2026-08-14 11:40:45', '2026-09-29 08:56:20', 2, 0, 0, 0, NULL, 1, NULL, 57, 0, 0, 0, NULL),
(88, 'Ka. Unit Radiologi', '2026-08-14 11:41:14', '2026-09-23 01:56:22', 2, 0, 0, 0, NULL, 1, NULL, 39, 0, 0, 0, NULL),
(89, 'Ka. Unit Laboratorium', '2026-08-14 11:42:56', '2026-09-06 10:46:03', 2, 0, 0, 0, NULL, 1, NULL, 17, 0, 0, 0, NULL),
(90, 'Ka. Unit Rehabilitasi Medik', '2026-08-14 11:43:29', '2026-09-06 10:47:06', 2, 0, 0, 0, NULL, 1, NULL, 17, 0, 0, 0, NULL),
(91, 'Ka. Unit Gizi', '2026-08-14 11:43:54', '2026-09-06 10:46:54', 2, 0, 0, 0, NULL, 1, NULL, 17, 0, 0, 0, NULL),
(92, 'Ka. Unit Rekam Medis', '2026-08-14 11:44:17', '2026-09-06 10:47:41', 2, 0, 0, 0, NULL, 1, NULL, 17, 0, 0, 0, NULL),
(93, 'PIC SMI', '2026-08-14 11:44:31', '2026-08-14 11:44:31', 2, 0, 0, 0, NULL, 0, NULL, 17, 0, 0, 0, NULL),
(94, 'Ka. Unit Keuangan', '2026-08-14 11:46:11', '2026-09-06 10:47:53', 2, 0, 0, 0, NULL, 1, NULL, 9, 0, 0, 0, NULL),
(95, 'Ka. Unit Kasir', '2026-08-14 11:48:22', '2026-09-29 08:37:50', 2, 0, 0, 0, NULL, 1, NULL, 45, 0, 0, 0, NULL),
(96, 'Ka. Unit Marketing', '2026-08-14 11:48:56', '2026-09-02 01:12:02', 2, 0, 0, 0, NULL, 1, NULL, 8, 0, 0, 0, NULL),
(97, 'Ka. Unit Admission', '2026-08-14 11:49:40', '2026-09-29 08:52:36', 2, 0, 0, 0, NULL, 1, NULL, 38, 0, 0, 0, NULL),
(98, 'Ka. Unit CRO & CC', '2026-08-14 11:50:17', '2026-09-06 10:48:20', 2, 0, 0, 0, NULL, 1, NULL, 8, 0, 0, 0, NULL),
(99, 'Ka. Unit Umum', '2026-08-14 11:51:13', '2026-11-05 06:43:40', 2, 0, 0, 0, NULL, 0, NULL, 77, 0, 0, 0, NULL),
(100, 'Ka. Unit Maintenance', '2026-08-14 11:51:28', '2026-08-14 11:51:28', 2, 0, 0, 0, NULL, 0, NULL, 77, 0, 0, 0, NULL),
(101, 'Ka. Unit Human Capital', '2026-08-14 11:52:16', '2026-09-28 06:11:20', 2, 0, 0, 0, NULL, 0, NULL, 11, 0, 1, 0, NULL),
(102, 'PPI', '2026-08-14 11:54:20', '2026-08-14 11:54:20', 2, 0, 0, 0, NULL, 0, NULL, 21, 0, 0, 0, NULL),
(103, 'Staff Mutu', '2026-08-14 11:55:02', '2026-09-14 06:08:59', 1, 0, 0, 0, NULL, 0, NULL, 14, 0, 0, 0, NULL),
(104, 'Staff HCM', '2026-08-14 11:59:29', '2026-09-03 02:42:44', 1, 0, 0, 0, NULL, 1, NULL, 11, 0, 1, 0, NULL),
(105, 'Ka. Unit K3LRS', '2026-08-14 12:00:01', '2026-09-16 02:20:55', 2, 0, 0, 0, NULL, 0, NULL, 73, 0, 0, 0, NULL),
(106, 'Kesehatan Lingkungan', '2026-08-14 12:00:25', '2026-09-16 04:59:30', 2, 0, 0, 0, NULL, 0, NULL, 77, 0, 0, 0, NULL),
(107, 'Admin Medik', '2026-08-14 12:01:05', '2026-10-07 06:29:38', 2, 0, 0, 0, NULL, 1, NULL, 18, 0, 1, 0, NULL),
(108, 'Staff JKN', '2026-08-21 11:44:23', '2026-09-21 07:14:00', 1, 0, 0, 0, NULL, 1, NULL, 7, 0, 0, 0, NULL),
(109, 'Staff Admin MCU', '2026-08-21 11:46:27', '2026-08-22 08:19:56', 1, 0, 0, 0, NULL, 0, NULL, 18, 0, 0, 0, NULL),
(110, 'Sekretaris', '2026-08-21 13:45:00', '2026-08-21 13:45:00', 5, 0, 0, 0, NULL, 0, NULL, 54, 0, 0, 0, NULL),
(111, 'Staff Radiologi', '2026-08-21 19:44:28', '2026-09-23 01:59:10', 1, 0, 0, 0, NULL, 0, NULL, 39, 0, 0, 0, NULL),
(112, 'Staff Prima', '2026-08-21 19:45:28', '2026-08-21 19:45:28', 1, 0, 0, 0, NULL, 0, NULL, 52, 0, 0, 0, NULL),
(113, 'Ka. Unit Driver', '2026-08-21 19:46:38', '2026-08-21 19:46:38', 2, 0, 0, 0, NULL, 0, NULL, 77, 0, 0, 0, NULL),
(114, 'Ka. Unit CC & CRO', '2026-08-21 19:48:26', '2026-08-21 19:48:26', 2, 0, 0, 0, NULL, 0, NULL, 55, 0, 0, 0, NULL),
(115, 'Staff CC & CRO', '2026-08-21 19:48:41', '2026-09-22 06:46:35', 1, 0, 0, 0, NULL, 0, NULL, 8, 0, 0, 0, NULL),
(116, 'Ka. Unit Star Dental', '2026-08-21 19:49:38', '2026-08-21 19:49:38', 2, 0, 0, 0, NULL, 0, NULL, 56, 0, 0, 0, NULL),
(117, 'Ka. Unit PSO', '2026-08-21 19:50:08', '2026-08-21 19:50:08', 2, 0, 0, 0, NULL, 0, NULL, 37, 0, 0, 0, NULL),
(118, 'Ka. Unit CSSD', '2026-08-21 19:58:15', '2026-08-21 19:58:15', 2, 0, 0, 0, NULL, 0, NULL, 28, 0, 0, 0, NULL),
(119, 'Ketua Komite Mutu Keperawatan', '2026-08-24 10:17:17', '2026-08-24 10:17:17', 4, 1, 0, 0, NULL, 0, NULL, 58, 0, 0, 0, NULL),
(120, 'Quality Corporate Officer', '2026-08-31 10:13:25', '2026-08-31 10:13:47', 6, 0, 0, 0, NULL, 0, NULL, 14, 0, 0, 0, NULL),
(121, 'Kepala Departemen IT', '2026-09-01 07:53:13', '2026-09-01 06:52:39', 3, 0, 0, 0, NULL, 1, NULL, 1, 0, 0, 0, NULL),
(122, 'Ka. Unit MPP', '2026-09-01 13:39:03', '2026-09-01 13:40:26', 2, 0, 0, 0, NULL, 0, NULL, 60, 0, 0, 0, NULL),
(123, 'Ka. Unit Tumbang', '2026-09-02 00:45:00', '2026-09-02 00:45:00', 2, 0, 0, 0, NULL, 0, NULL, 61, 0, 0, 0, NULL),
(125, 'Staff Logistik Umum', '2026-09-02 05:19:13', '2026-09-22 06:44:02', 1, 0, 0, 0, NULL, 1, NULL, 77, 0, 0, 0, NULL),
(126, 'Staff Keperawatan ICU', '2026-09-03 05:08:21', '2026-10-06 09:05:30', 1, 0, 0, 0, NULL, 0, NULL, 49, 0, 0, 0, NULL),
(127, 'Staff Inventaris Umum', '2026-09-04 02:40:49', '2026-09-22 06:47:15', 1, 0, 0, 0, NULL, 0, NULL, 77, 0, 0, 0, NULL),
(128, 'Ka Unit Rawat Jalan', '2026-09-04 02:58:22', '2026-10-05 03:35:15', 2, 0, 0, 0, NULL, 0, NULL, 139, 0, 0, 0, NULL),
(129, 'Staff Admission', '2026-09-05 03:24:29', '2026-09-29 08:52:15', 1, 0, 0, 0, NULL, 0, NULL, 38, 0, 0, 0, NULL),
(130, 'Ketua Komite K3', '2026-09-05 04:08:49', '2026-09-05 04:08:49', 2, 0, 0, 0, NULL, 1, NULL, 71, 0, 0, 0, NULL),
(131, 'Pjs Ka Divisi Penunjang Medis', '2026-09-07 04:45:00', '2026-11-07 18:03:56', 4, 0, 0, 0, NULL, 0, NULL, 17, 0, 0, 0, NULL),
(132, 'AGH Keuangan', '2026-09-07 06:58:42', '2026-11-18 01:21:01', 5, 0, 1, 0, 30, 0, NULL, 9, 0, 0, 0, NULL),
(133, 'AGH Bussines Development', '2026-09-08 02:16:30', '2026-10-26 07:44:47', 5, 0, 1, 0, 29, 0, NULL, 8, 0, 0, 0, NULL),
(134, 'Ka. Subkomite Kredensial', '2026-09-08 03:21:18', '2026-09-08 03:21:18', 2, 0, 0, 0, NULL, 0, NULL, 76, 0, 0, 0, NULL),
(135, 'Ketua KSM Penyakit Dalam', '2026-09-08 03:22:10', '2026-09-08 03:22:10', 2, 0, 0, 0, NULL, 0, NULL, 76, 0, 0, 0, NULL),
(136, 'Ketua Komite Medik', '2026-09-08 03:29:29', '2026-09-08 03:29:29', 4, 0, 0, 0, NULL, 0, NULL, 76, 0, 0, 0, NULL),
(137, 'Ka. Dept Keuangan', '2026-09-08 03:30:44', '2026-09-08 03:30:44', 3, 0, 0, 0, NULL, 0, NULL, 9, 0, 0, 0, NULL),
(138, 'Ka. Unit TRB', '2026-09-08 04:05:40', '2026-09-08 04:05:40', 2, 0, 0, 0, NULL, 1, NULL, 18, 0, 0, 0, NULL),
(139, 'Ka Unit ICU', '2026-09-09 04:28:31', '2026-09-09 07:11:52', 2, 0, 0, 0, NULL, 1, NULL, 15, 0, 0, 0, NULL),
(141, 'Ka. Unit VK', '2026-09-10 04:56:34', '2026-10-28 07:54:05', 2, 0, 0, 0, NULL, 1, NULL, 17, 0, 0, 0, NULL),
(142, 'Ka. Unit Humas', '2026-09-10 08:41:20', '2026-09-10 08:41:20', 2, 0, 0, 0, NULL, 1, NULL, 64, 0, 0, 0, NULL),
(143, 'Ka. Pelayanan Laboratorium', '2026-09-10 10:46:59', '2026-09-22 06:55:20', 2, 0, 0, 0, NULL, 0, NULL, 17, 0, 0, 0, NULL),
(144, 'Staff Unit Perbekalan Farmasi', '2026-09-11 07:19:16', '2026-09-29 08:55:09', 1, 0, 0, 0, NULL, 0, NULL, 57, 0, 0, 0, NULL),
(145, 'Ka Unit SMI', '2026-09-11 07:28:39', '2026-09-11 07:28:39', 2, 0, 0, 0, NULL, 1, NULL, 63, 0, 0, 0, NULL),
(146, 'Staff SMI', '2026-09-11 08:43:23', '2026-09-22 06:48:10', 1, 0, 0, 0, NULL, 0, NULL, 17, 0, 0, 0, NULL),
(147, 'Admin SMI', '2026-09-11 08:45:35', '2026-09-22 06:48:27', 1, 0, 0, 0, NULL, 0, NULL, 17, 0, 0, 0, NULL),
(148, 'Ka. Unit HCM', '2026-09-11 10:03:57', '2026-09-11 10:03:57', 2, 0, 0, 0, NULL, 1, NULL, 11, 0, 1, 0, NULL),
(149, 'Staff Farmasi', '2026-09-14 05:53:20', '2026-09-29 08:57:19', 1, 0, 0, 0, NULL, 0, NULL, 47, 0, 0, 0, NULL),
(150, 'Staff Gizi', '2026-09-14 05:57:02', '2026-09-22 03:31:41', 1, 0, 0, 0, NULL, 0, NULL, 17, 0, 0, 0, NULL),
(151, 'Staff Hemodialisa', '2026-09-14 05:58:06', '2026-11-18 02:49:41', 1, 0, 0, 0, NULL, 0, NULL, 53, 0, 0, 0, NULL),
(152, 'Staff Hiperbarik', '2026-09-14 06:01:22', '2026-09-22 06:49:47', 1, 0, 0, 0, NULL, 0, NULL, 15, 0, 0, 0, NULL),
(153, 'Staff Kasir', '2026-09-14 06:03:18', '2026-09-29 08:38:12', 1, 0, 0, 0, NULL, 0, NULL, 45, 0, 0, 0, NULL),
(154, 'Staff Laboratorium', '2026-09-14 06:04:34', '2026-09-22 06:50:25', 1, 0, 0, 0, NULL, 0, NULL, 17, 0, 0, 0, NULL),
(155, 'Staff Maintenance', '2026-09-14 06:05:24', '2026-09-22 06:50:55', 1, 0, 0, 0, NULL, 0, NULL, 77, 0, 0, 0, NULL),
(156, 'Staff MPP', '2026-09-14 06:08:07', '2026-09-14 06:08:07', 1, 0, 0, 0, NULL, 0, NULL, 60, 0, 0, 0, NULL),
(157, 'Staff VK', '2026-09-14 06:10:17', '2026-10-28 08:04:50', 1, 0, 0, 0, NULL, 0, NULL, 17, 0, 0, 0, NULL),
(158, 'Staff Kamar Bedah', '2026-09-14 06:12:46', '2026-09-14 06:12:46', 1, 0, 0, 0, NULL, 0, NULL, 33, 0, 0, 0, NULL),
(159, 'Staff Poliklinik', '2026-09-14 06:19:41', '2026-10-10 07:05:02', 1, 0, 0, 0, NULL, 0, NULL, 29, 0, 0, 0, NULL),
(160, 'Staff Fisioterapi', '2026-09-14 06:22:36', '2026-09-14 06:22:36', 1, 0, 0, 0, NULL, 0, NULL, 35, 0, 0, 0, NULL),
(161, 'Staff Rekam Medis', '2026-09-14 06:23:22', '2026-09-14 06:23:22', 1, 0, 0, 0, NULL, 0, NULL, 36, 0, 0, 0, NULL),
(162, 'Kanit Keperawatan Poliklinik', '2026-09-14 10:09:42', '2026-10-10 07:05:15', 2, 0, 0, 0, NULL, 1, NULL, 29, 0, 0, 0, NULL),
(163, 'Kanit Keperawatan UGD', '2026-09-14 10:19:04', '2026-09-29 02:59:20', 2, 0, 0, 0, NULL, 1, 'Keperawatan', 34, 0, 0, 0, NULL),
(164, 'Kanit Keperawatan ICU', '2026-09-14 10:19:36', '2026-09-14 10:19:36', 2, 0, 0, 0, NULL, 0, 'Keperawatan', 49, 0, 0, 0, NULL),
(165, 'Kanit Keperawatan Rawat Inap', '2026-09-14 10:20:48', '2026-09-14 10:20:48', 2, 0, 0, 0, NULL, 0, 'Keperawatan', 62, 0, 0, 0, NULL),
(166, 'Staff Endoskopi', '2026-09-14 10:24:06', '2026-09-30 02:49:31', 1, 0, 0, 0, NULL, 0, 'Keperawatan', 87, 0, 0, 0, NULL),
(167, 'Kanit Keperawatan Kamar Bedah', '2026-09-14 10:27:25', '2026-09-14 10:27:25', 2, 0, 0, 0, NULL, 0, 'Keperawatan', 15, 0, 0, 0, NULL),
(168, 'Staff Umum', '2026-09-15 09:13:59', '2026-09-15 09:13:59', 1, 0, 0, 0, NULL, 0, NULL, 77, 0, 0, 0, NULL),
(169, 'Staff Kesling', '2026-09-15 09:15:20', '2026-09-15 09:15:20', 1, 0, 0, 0, NULL, 0, NULL, 50, 0, 0, 0, NULL),
(170, 'Ka.Unit UGD', '2026-09-16 03:30:04', '2026-09-16 03:49:16', 2, 0, 0, 0, NULL, 1, NULL, 18, 0, 0, 0, NULL),
(171, 'Ketua SPI', '2026-09-17 01:09:22', '2026-09-17 01:09:22', 5, 0, 0, 0, NULL, 0, NULL, 19, 0, 0, 0, NULL),
(172, 'Anggota SPI', '2026-09-17 01:09:37', '2026-11-10 01:17:36', 2, 0, 0, 0, NULL, 0, NULL, 19, 0, 0, 0, NULL),
(173, 'Ka. Unit CVCU', '2026-09-17 01:42:03', '2026-09-17 01:42:03', 2, 0, 0, 0, NULL, 1, NULL, 89, 0, 0, 0, NULL),
(174, 'Ka. Unit Security & Transportasi', '2026-09-17 08:39:05', '2026-09-17 08:39:21', 2, 0, 0, 0, NULL, 0, NULL, 77, 0, 0, 0, NULL),
(175, 'Kanit Critical & Emergency', '2026-09-18 04:58:28', '2026-09-18 04:58:28', 2, 0, 0, 0, NULL, 0, NULL, 18, 0, 0, 0, NULL),
(176, 'Staff Keperawatan Rawat Inap', '2026-09-20 06:41:54', '2026-09-22 06:56:10', 1, 0, 0, 0, NULL, 0, NULL, 15, 0, 0, 0, NULL),
(177, 'Karu EndosKopi', '2026-09-21 02:05:24', '2026-09-30 02:49:43', 2, 0, 0, 0, NULL, 1, NULL, 87, 0, 0, 0, NULL),
(178, 'Kepala Departemen Humas', '2026-09-21 03:11:45', '2026-09-21 03:11:45', 3, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(179, 'Dokter Umum', '2026-09-21 08:20:24', '2026-09-28 04:52:03', 1, 0, 0, 0, NULL, 0, NULL, 92, 0, 0, 0, NULL),
(180, 'Staff UGD', '2026-09-22 00:58:56', '2026-09-29 02:59:08', 1, 0, 0, 0, NULL, 0, NULL, 34, 0, 0, 0, NULL),
(181, 'Ka Unit Kamar Bedah', '2026-09-22 01:30:50', '2026-09-22 01:30:50', 2, 0, 0, 0, NULL, 0, NULL, 33, 0, 0, 0, NULL),
(182, 'Ka Unit Kedokteran Nuklir', '2026-09-23 04:47:25', '2026-09-23 04:47:25', 2, 0, 0, 0, NULL, 0, NULL, 93, 0, 0, 0, NULL),
(183, 'Ka Unit Dokter Umum', '2026-09-28 04:52:31', '2026-09-28 04:52:31', 2, 0, 0, 0, NULL, 1, NULL, 92, 0, 0, 0, NULL),
(184, 'Karu Mahoni', '2026-10-06 03:05:20', '2026-10-06 03:05:20', 2, 0, 0, 0, NULL, 0, NULL, 42, 0, 0, 0, NULL),
(185, 'Karu Cemara', '2026-10-06 03:05:37', '2026-10-06 03:05:37', 2, 0, 0, 0, NULL, 0, NULL, 40, 0, 0, 0, NULL),
(186, 'Karu Cendana', '2026-10-06 03:05:54', '2026-10-06 03:05:54', 2, 0, 0, 0, NULL, 0, NULL, 41, 0, 0, 0, NULL),
(187, 'Karu Hemodialisa', '2026-10-06 03:06:13', '2026-10-06 03:06:13', 2, 0, 0, 0, NULL, 0, NULL, 53, 0, 0, 0, NULL),
(188, 'General Manager', '2026-10-07 01:19:24', '2026-10-28 05:00:41', 6, 0, 0, 0, NULL, 0, 'General Manager', NULL, 0, 0, 0, NULL),
(189, 'Koordinator', '2026-10-07 01:26:23', '2026-10-07 01:26:23', 4, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(190, 'Admin', '2026-10-07 01:26:37', '2026-10-19 03:30:14', 2, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(191, 'Desainer Grafis', '2026-10-07 01:28:38', '2026-10-07 01:28:38', 3, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(192, 'GM Awal Bros Training Center', '2026-10-08 05:11:15', '2026-10-08 05:11:15', 6, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(193, 'Direktur Awal Bros Training Center', '2026-10-08 05:11:32', '2026-10-08 05:11:32', 6, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(194, 'PT. Awal Bros Bangun Bangsa', '2026-10-08 05:14:52', '2026-10-08 05:14:52', 6, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(195, 'Direktur PT. Awal Bros Bangun Bangsa', '2026-10-08 05:15:05', '2026-10-08 05:15:05', 6, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(196, 'Dokter Spesialis', '2026-10-08 06:56:59', '2026-10-08 06:56:59', 1, 0, 0, 0, NULL, 0, NULL, 18, 0, 0, 0, NULL),
(197, 'Wakil Direktur Klinik WellABe', '2026-10-08 07:54:57', '2026-10-08 07:55:13', 6, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(198, 'Kepala Klinik WellABe', '2026-10-08 07:55:28', '2026-10-08 07:55:28', 6, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(199, 'Penanggung Jawab Pelayanan', '2026-10-08 07:57:59', '2026-10-08 07:57:59', 6, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(200, 'Desainer Grafis Corporate', '2026-10-21 08:57:58', '2026-10-21 08:57:58', 5, 0, 0, 0, NULL, 0, 'Marketing Officer', 8, 0, 0, 0, NULL),
(201, 'Bendahara', '2026-10-23 03:54:49', '2026-10-29 06:26:30', 2, 0, 0, 0, NULL, 0, NULL, 9, 0, 0, 0, NULL),
(202, 'CEO Awal Bros Sisco', '2026-10-24 03:52:56', '2026-10-24 03:52:56', 6, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(203, 'Karu Bunga Kiambang', '2026-10-28 03:56:04', '2026-10-28 03:56:04', 2, 0, 0, 0, NULL, 1, NULL, 81, 0, 0, 0, NULL),
(204, 'Karu Bunga Kesumba', '2026-10-28 03:56:28', '2026-10-28 03:56:28', 2, 0, 0, 0, NULL, 1, NULL, 80, 0, 0, 0, NULL),
(205, 'Karu Pucuk Puteri', '2026-10-28 03:56:55', '2026-10-28 03:56:55', 2, 0, 0, 0, NULL, 1, NULL, 84, 0, 0, 0, NULL),
(206, 'Karu Pucuk Rebung', '2026-10-28 03:57:15', '2026-10-28 03:57:15', 2, 0, 0, 0, NULL, 1, NULL, NULL, 0, 0, 0, NULL),
(207, 'Karu Bintang Bersanding', '2026-10-28 03:57:49', '2026-10-28 03:57:49', 2, 0, 0, 0, NULL, 1, NULL, 79, 0, 0, 0, NULL),
(208, 'Karu ICCU/HCU', '2026-10-28 04:07:46', '2026-10-28 04:07:46', 2, 0, 0, 0, NULL, 1, NULL, 152, 0, 0, 0, NULL),
(209, 'Karu Poli Gigi', '2026-10-28 04:09:29', '2026-10-28 04:09:29', 2, 0, 0, 0, NULL, 1, NULL, 153, 0, 0, 0, NULL),
(210, 'General Manager Wellabe', '2026-10-28 05:00:30', '2026-10-28 05:00:30', 6, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(211, 'Karu Pucuk Bersusun', '2026-10-28 05:03:19', '2026-10-28 05:03:19', 2, 0, 0, 0, NULL, 1, NULL, 83, 0, 0, 0, NULL),
(212, 'Staff Logistik Umum Wellane', '2026-10-28 09:00:59', '2026-10-28 09:00:59', 2, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 0, NULL),
(213, 'Digital Marketing', '2026-10-29 02:00:09', '2026-10-29 02:00:09', 5, 0, 0, 0, NULL, 0, 'Digital Marketing', 8, 0, 0, 0, NULL),
(214, 'Content Writer Officer', '2026-10-29 02:15:49', '2026-10-29 02:15:49', 5, 0, 0, 0, NULL, 0, NULL, 8, 0, 0, 0, NULL),
(215, 'Karu Poliklinik', '2026-10-29 08:36:57', '2026-10-29 08:36:57', 2, 0, 0, 0, NULL, 0, NULL, 15, 0, 0, 0, NULL),
(216, 'Programmer DIH', '2026-10-30 08:19:01', '2026-10-30 08:22:47', 5, 0, 0, 0, NULL, 0, NULL, 155, 0, 0, 0, NULL),
(217, 'Kepala Klinik Wellabe Bandara', '2026-11-02 03:14:43', '2026-11-02 03:14:43', 5, 0, 0, 0, NULL, 0, NULL, 141, 0, 0, 0, NULL),
(218, 'Pjs. Ketua Komite Mutu & Casemix', '2026-11-02 04:38:32', '2026-11-02 04:38:32', 4, 0, 0, 0, NULL, 0, NULL, 20, 0, 0, 0, NULL),
(219, 'Driver', '2026-11-04 03:44:14', '2026-11-04 03:44:14', 1, 0, 0, 0, NULL, 0, NULL, 77, 0, 0, 0, NULL),
(220, 'Pjs. Kamar Bedah', '2026-11-04 07:44:47', '2026-11-04 07:44:47', 1, 0, 0, 0, NULL, 0, NULL, 33, 0, 0, 0, NULL),
(221, 'Staff CPN', '2026-11-11 03:38:06', '2026-11-11 03:38:06', 1, 0, 0, 0, NULL, 0, NULL, 157, 0, 0, 0, NULL),
(222, 'Direktur CPN', '2026-11-12 08:35:43', '2026-11-12 08:35:43', 5, 0, 0, 0, NULL, 0, NULL, 157, 0, 0, 0, NULL),
(223, 'Admin CPN Pekanbaru', '2026-11-13 03:28:52', '2026-11-13 03:28:52', 2, 0, 0, 0, NULL, 0, NULL, 160, 0, 0, 0, NULL),
(224, 'Admin CPN Ujung Batu', '2026-11-13 03:29:11', '2026-11-13 03:29:11', 2, 0, 0, 0, NULL, 0, NULL, 161, 0, 0, 0, NULL),
(225, 'Admin CPN Dumai', '2026-11-13 03:29:29', '2026-11-13 03:29:29', 2, 0, 0, 0, NULL, 0, NULL, 162, 0, 0, 0, NULL),
(226, 'Koordinator Gizi CPN Pekanbaru', '2026-11-13 03:30:16', '2026-11-13 03:30:16', 4, 0, 0, 0, NULL, 0, NULL, 160, 0, 0, 0, NULL),
(227, 'Koordinator Gizi CPN Ujung Batu', '2026-11-13 03:30:29', '2026-11-13 03:30:29', 4, 0, 0, 0, NULL, 0, NULL, 161, 0, 0, 0, NULL),
(228, 'Koordinator Gizi CPN Dumai', '2026-11-13 03:30:42', '2026-11-13 03:30:42', 4, 0, 0, 0, NULL, 0, NULL, 162, 0, 0, 0, NULL),
(229, 'Keuangan CPN Pekanbaru', '2026-11-13 03:32:17', '2026-11-13 03:32:17', 2, 0, 0, 0, NULL, 0, NULL, 160, 0, 0, 0, NULL),
(230, 'Purchasing CPN Pekanbaru', '2026-11-13 03:33:19', '2026-11-13 03:33:19', 2, 0, 0, 0, NULL, 0, NULL, 160, 0, 0, 0, NULL),
(231, 'General Manager CPN', '2026-11-13 03:33:53', '2026-11-13 03:33:53', 5, 0, 0, 0, NULL, 0, NULL, 157, 0, 0, 0, NULL),
(232, 'Ahli Gizi CPN Pekanbaru', '2026-11-13 03:37:02', '2026-11-13 03:37:02', 2, 0, 0, 0, NULL, 0, NULL, 160, 0, 0, 0, NULL),
(233, 'HC CPN', '2026-11-13 03:43:02', '2026-11-13 03:43:02', 4, 0, 0, 0, NULL, 0, NULL, 157, 0, 0, 0, NULL),
(234, 'Admin CPN Bagan Batu', '2026-11-13 06:38:34', '2026-11-13 06:39:19', 2, 0, 0, 0, NULL, 0, NULL, 163, 0, 0, 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jabatans`
--
ALTER TABLE `jabatans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jabatans_unit_id_foreign` (`unit_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jabatans`
--
ALTER TABLE `jabatans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=235;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jabatans`
--
ALTER TABLE `jabatans`
  ADD CONSTRAINT `jabatans_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
