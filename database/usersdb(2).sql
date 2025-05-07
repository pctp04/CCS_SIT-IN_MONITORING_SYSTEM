-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2025 at 02:07 AM
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
-- Database: `usersdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `ID` int(11) NOT NULL,
  `TITLE` varchar(255) NOT NULL,
  `CONTENT` varchar(255) NOT NULL,
  `CREATED_AT` datetime NOT NULL DEFAULT current_timestamp(),
  `ADMIN_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement`
--

INSERT INTO `announcement` (`ID`, `TITLE`, `CONTENT`, `CREATED_AT`, `ADMIN_ID`) VALUES
(1, '', 'Hello', '2025-03-23 23:42:37', 1000),
(2, '', 'test', '2025-03-24 01:26:14', 1000),
(3, '', 'asd', '2025-03-25 23:00:12', 1000);

-- --------------------------------------------------------

--
-- Table structure for table `computer_status`
--

CREATE TABLE `computer_status` (
  `ID` int(11) NOT NULL,
  `LABORATORY` varchar(50) NOT NULL,
  `COMPUTER_NUMBER` int(11) NOT NULL,
  `STATUS` enum('Available','In Use','Maintenance') NOT NULL DEFAULT 'Available',
  `LAST_UPDATED` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `computer_status`
--

INSERT INTO `computer_status` (`ID`, `LABORATORY`, `COMPUTER_NUMBER`, `STATUS`, `LAST_UPDATED`) VALUES
(1, '517', 1, 'In Use', '2025-05-05 02:18:12'),
(2, '517', 2, 'In Use', '2025-05-07 08:07:16'),
(3, '517', 3, 'Available', '2025-05-04 18:17:10'),
(4, '517', 4, 'Available', '2025-05-04 18:17:10'),
(5, '517', 5, 'Available', '2025-05-04 18:17:10'),
(6, '517', 6, 'Available', '2025-05-04 18:17:10'),
(7, '517', 7, 'Available', '2025-05-04 18:17:10'),
(8, '517', 8, 'Available', '2025-05-04 18:17:10'),
(9, '517', 9, 'Available', '2025-05-04 18:17:10'),
(10, '517', 10, 'Available', '2025-05-04 18:17:10'),
(11, '517', 11, 'Available', '2025-05-04 18:17:10'),
(12, '517', 12, 'Available', '2025-05-04 18:17:10'),
(13, '517', 13, 'Available', '2025-05-04 18:17:10'),
(14, '517', 14, 'Available', '2025-05-04 18:17:10'),
(15, '517', 15, 'Available', '2025-05-04 18:17:10'),
(16, '517', 16, 'Available', '2025-05-04 18:17:10'),
(17, '517', 17, 'Available', '2025-05-04 18:17:10'),
(18, '517', 18, 'Available', '2025-05-04 18:17:10'),
(19, '517', 19, 'In Use', '2025-05-07 08:07:16'),
(20, '517', 20, 'Available', '2025-05-04 18:17:10'),
(21, '517', 21, 'Available', '2025-05-04 18:17:10'),
(22, '517', 22, 'Available', '2025-05-04 18:17:10'),
(23, '517', 23, 'Available', '2025-05-04 18:17:10'),
(24, '517', 24, 'Available', '2025-05-04 18:17:10'),
(25, '517', 25, 'Available', '2025-05-04 18:17:10'),
(26, '517', 26, 'Available', '2025-05-04 18:17:11'),
(27, '517', 27, 'Available', '2025-05-04 18:17:11'),
(28, '517', 28, 'Available', '2025-05-04 18:17:11'),
(29, '517', 29, 'Available', '2025-05-04 18:17:11'),
(30, '517', 30, 'Available', '2025-05-04 18:17:11'),
(31, '517', 31, 'Available', '2025-05-04 18:17:11'),
(32, '517', 32, 'Available', '2025-05-04 18:17:11'),
(33, '517', 33, 'Available', '2025-05-04 18:17:11'),
(34, '517', 34, 'Available', '2025-05-04 18:17:11'),
(35, '517', 35, 'Available', '2025-05-04 18:17:11'),
(36, '517', 36, 'Available', '2025-05-04 18:17:11'),
(37, '517', 37, 'Available', '2025-05-04 18:17:11'),
(38, '517', 38, 'Available', '2025-05-04 18:17:11'),
(39, '517', 39, 'Available', '2025-05-04 18:17:11'),
(40, '517', 40, 'Available', '2025-05-04 18:17:11'),
(41, '517', 41, 'Available', '2025-05-04 18:17:11'),
(42, '517', 42, 'Available', '2025-05-04 18:17:11'),
(43, '517', 43, 'Available', '2025-05-04 18:17:11'),
(44, '517', 44, 'Available', '2025-05-04 18:17:11'),
(45, '517', 45, 'Available', '2025-05-04 18:17:11'),
(46, '517', 46, 'Available', '2025-05-04 18:17:11'),
(47, '517', 47, 'Available', '2025-05-04 18:17:12'),
(48, '517', 48, 'Available', '2025-05-04 18:17:12'),
(49, '517', 49, 'Available', '2025-05-04 18:17:12'),
(50, '517', 50, 'Available', '2025-05-04 18:17:12'),
(51, '524', 1, 'In Use', '2025-05-04 18:17:22'),
(52, '524', 2, 'In Use', '2025-05-04 18:17:22'),
(53, '524', 3, 'In Use', '2025-05-04 18:17:22'),
(54, '524', 4, 'In Use', '2025-05-04 18:17:22'),
(55, '524', 5, 'In Use', '2025-05-04 18:17:22'),
(56, '524', 6, 'In Use', '2025-05-04 18:17:22'),
(57, '524', 7, 'In Use', '2025-05-04 18:17:22'),
(58, '524', 8, 'In Use', '2025-05-04 18:17:22'),
(59, '524', 9, 'In Use', '2025-05-04 18:17:22'),
(60, '524', 10, 'In Use', '2025-05-04 18:17:22'),
(61, '524', 11, 'In Use', '2025-05-04 18:17:22'),
(62, '524', 12, 'In Use', '2025-05-04 18:17:22'),
(63, '524', 13, 'In Use', '2025-05-04 18:17:22'),
(64, '524', 14, 'In Use', '2025-05-04 18:17:22'),
(65, '524', 15, 'In Use', '2025-05-04 18:17:22'),
(66, '524', 16, 'In Use', '2025-05-04 18:17:22'),
(67, '524', 17, 'In Use', '2025-05-04 18:17:22'),
(68, '524', 18, 'In Use', '2025-05-04 18:17:22'),
(69, '524', 19, 'In Use', '2025-05-04 18:17:22'),
(70, '524', 20, 'In Use', '2025-05-04 18:17:22'),
(71, '524', 21, 'In Use', '2025-05-04 18:17:22'),
(72, '524', 22, 'In Use', '2025-05-04 18:17:22'),
(73, '524', 23, 'In Use', '2025-05-04 18:17:22'),
(74, '524', 24, 'In Use', '2025-05-04 18:17:22'),
(75, '524', 25, 'In Use', '2025-05-04 18:17:22'),
(76, '524', 26, 'In Use', '2025-05-04 18:17:22'),
(77, '524', 27, 'In Use', '2025-05-04 18:17:22'),
(78, '524', 28, 'In Use', '2025-05-04 18:17:22'),
(79, '524', 29, 'In Use', '2025-05-04 18:17:22'),
(80, '524', 30, 'In Use', '2025-05-04 18:17:22'),
(81, '524', 31, 'In Use', '2025-05-04 18:17:22'),
(82, '524', 32, 'In Use', '2025-05-04 18:17:22'),
(83, '524', 33, 'In Use', '2025-05-04 18:17:22'),
(84, '524', 34, 'In Use', '2025-05-04 18:17:22'),
(85, '524', 35, 'In Use', '2025-05-04 18:17:22'),
(86, '524', 36, 'In Use', '2025-05-04 18:17:22'),
(87, '524', 37, 'In Use', '2025-05-04 18:17:22'),
(88, '524', 38, 'In Use', '2025-05-04 18:17:22'),
(89, '524', 39, 'In Use', '2025-05-04 18:17:22'),
(90, '524', 40, 'In Use', '2025-05-04 18:17:22'),
(91, '524', 41, 'In Use', '2025-05-04 18:17:22'),
(92, '524', 42, 'In Use', '2025-05-04 18:17:22'),
(93, '524', 43, 'In Use', '2025-05-04 18:17:22'),
(94, '524', 44, 'In Use', '2025-05-04 18:17:22'),
(95, '524', 45, 'In Use', '2025-05-04 18:17:22'),
(96, '524', 46, 'Available', '2025-05-04 18:17:13'),
(97, '524', 47, 'Available', '2025-05-04 18:17:13'),
(98, '524', 48, 'In Use', '2025-05-07 08:07:16'),
(99, '524', 49, 'Available', '2025-05-04 18:17:14'),
(100, '524', 50, 'Available', '2025-05-04 18:17:14'),
(101, '526', 1, 'In Use', '2025-05-05 02:18:42'),
(102, '526', 2, 'Available', '2025-05-04 18:17:14'),
(103, '526', 3, 'Available', '2025-05-04 18:17:14'),
(104, '526', 4, 'Available', '2025-05-04 18:17:14'),
(105, '526', 5, 'Available', '2025-05-04 18:17:14'),
(106, '526', 6, 'Available', '2025-05-04 18:17:14'),
(107, '526', 7, 'Available', '2025-05-04 18:17:14'),
(108, '526', 8, 'Available', '2025-05-04 18:17:14'),
(109, '526', 9, 'Available', '2025-05-04 18:17:14'),
(110, '526', 10, 'Available', '2025-05-04 18:17:14'),
(111, '526', 11, 'Available', '2025-05-04 18:17:14'),
(112, '526', 12, 'Available', '2025-05-04 18:17:14'),
(113, '526', 13, 'Available', '2025-05-04 18:17:14'),
(114, '526', 14, 'Available', '2025-05-04 18:17:14'),
(115, '526', 15, 'Available', '2025-05-04 18:17:14'),
(116, '526', 16, 'Available', '2025-05-04 18:17:14'),
(117, '526', 17, 'Available', '2025-05-04 18:17:14'),
(118, '526', 18, 'Available', '2025-05-04 18:17:14'),
(119, '526', 19, 'Available', '2025-05-04 18:17:14'),
(120, '526', 20, 'Available', '2025-05-04 18:17:14'),
(121, '526', 21, 'Available', '2025-05-04 18:17:14'),
(122, '526', 22, 'Available', '2025-05-04 18:17:15'),
(123, '526', 23, 'Available', '2025-05-04 18:17:15'),
(124, '526', 24, 'Available', '2025-05-04 18:17:15'),
(125, '526', 25, 'Available', '2025-05-04 18:17:15'),
(126, '526', 26, 'Available', '2025-05-04 18:17:15'),
(127, '526', 27, 'Available', '2025-05-04 18:17:15'),
(128, '526', 28, 'Available', '2025-05-04 18:17:15'),
(129, '526', 29, 'Available', '2025-05-04 18:17:15'),
(130, '526', 30, 'Available', '2025-05-04 18:17:15'),
(131, '526', 31, 'Available', '2025-05-04 18:17:15'),
(132, '526', 32, 'Available', '2025-05-04 18:17:15'),
(133, '526', 33, 'Available', '2025-05-04 18:17:15'),
(134, '526', 34, 'Available', '2025-05-04 18:17:15'),
(135, '526', 35, 'Available', '2025-05-04 18:17:15'),
(136, '526', 36, 'Available', '2025-05-04 18:17:15'),
(137, '526', 37, 'Available', '2025-05-04 18:17:15'),
(138, '526', 38, 'Available', '2025-05-04 18:17:15'),
(139, '526', 39, 'Available', '2025-05-04 18:17:15'),
(140, '526', 40, 'Available', '2025-05-04 18:17:15'),
(141, '526', 41, 'Available', '2025-05-04 18:17:15'),
(142, '526', 42, 'Available', '2025-05-04 18:17:15'),
(143, '526', 43, 'Available', '2025-05-04 18:17:15'),
(144, '526', 44, 'Available', '2025-05-04 18:17:15'),
(145, '526', 45, 'Available', '2025-05-04 18:17:15'),
(146, '526', 46, 'Available', '2025-05-04 18:17:15'),
(147, '526', 47, 'Available', '2025-05-04 18:17:15'),
(148, '526', 48, 'Available', '2025-05-04 18:17:15'),
(149, '526', 49, 'Available', '2025-05-04 18:17:15'),
(150, '526', 50, 'Available', '2025-05-04 18:17:16'),
(151, '528', 1, 'Available', '2025-05-04 18:17:16'),
(152, '528', 2, 'Available', '2025-05-04 18:17:16'),
(153, '528', 3, 'Available', '2025-05-04 18:17:16'),
(154, '528', 4, 'Available', '2025-05-04 18:17:16'),
(155, '528', 5, 'Available', '2025-05-04 18:17:16'),
(156, '528', 6, 'Available', '2025-05-04 18:17:16'),
(157, '528', 7, 'Available', '2025-05-04 18:17:16'),
(158, '528', 8, 'Available', '2025-05-04 18:17:16'),
(159, '528', 9, 'Available', '2025-05-04 18:17:16'),
(160, '528', 10, 'Available', '2025-05-04 18:17:16'),
(161, '528', 11, 'Available', '2025-05-04 18:17:16'),
(162, '528', 12, 'Available', '2025-05-04 18:17:16'),
(163, '528', 13, 'Available', '2025-05-04 18:17:16'),
(164, '528', 14, 'Available', '2025-05-04 18:17:16'),
(165, '528', 15, 'Available', '2025-05-04 18:17:16'),
(166, '528', 16, 'Available', '2025-05-04 18:17:16'),
(167, '528', 17, 'Available', '2025-05-04 18:17:16'),
(168, '528', 18, 'Available', '2025-05-04 18:17:16'),
(169, '528', 19, 'Available', '2025-05-04 18:17:16'),
(170, '528', 20, 'Available', '2025-05-04 18:17:16'),
(171, '528', 21, 'Available', '2025-05-04 18:17:16'),
(172, '528', 22, 'Available', '2025-05-04 18:17:16'),
(173, '528', 23, 'Available', '2025-05-04 18:17:16'),
(174, '528', 24, 'Available', '2025-05-04 18:17:16'),
(175, '528', 25, 'Available', '2025-05-04 18:17:16'),
(176, '528', 26, 'Available', '2025-05-04 18:17:16'),
(177, '528', 27, 'Available', '2025-05-04 18:17:16'),
(178, '528', 28, 'Available', '2025-05-04 18:17:16'),
(179, '528', 29, 'Available', '2025-05-04 18:17:16'),
(180, '528', 30, 'Available', '2025-05-04 18:17:17'),
(181, '528', 31, 'Available', '2025-05-04 18:17:17'),
(182, '528', 32, 'Available', '2025-05-04 18:17:17'),
(183, '528', 33, 'Available', '2025-05-04 18:17:17'),
(184, '528', 34, 'Available', '2025-05-04 18:17:17'),
(185, '528', 35, 'Available', '2025-05-04 18:17:17'),
(186, '528', 36, 'Available', '2025-05-04 18:17:17'),
(187, '528', 37, 'Available', '2025-05-04 18:17:17'),
(188, '528', 38, 'Available', '2025-05-04 18:17:17'),
(189, '528', 39, 'Available', '2025-05-04 18:17:17'),
(190, '528', 40, 'Available', '2025-05-04 18:17:17'),
(191, '528', 41, 'Available', '2025-05-04 18:17:17'),
(192, '528', 42, 'Available', '2025-05-04 18:17:17'),
(193, '528', 43, 'Available', '2025-05-04 18:17:17'),
(194, '528', 44, 'Available', '2025-05-04 18:17:17'),
(195, '528', 45, 'Available', '2025-05-04 18:17:17'),
(196, '528', 46, 'Available', '2025-05-04 18:17:17'),
(197, '528', 47, 'Available', '2025-05-04 18:17:17'),
(198, '528', 48, 'Available', '2025-05-04 18:17:17'),
(199, '528', 49, 'Available', '2025-05-04 18:17:17'),
(200, '528', 50, 'Available', '2025-05-04 18:17:17'),
(201, '530', 1, 'In Use', '2025-05-07 08:07:16'),
(202, '530', 2, 'Available', '2025-05-04 18:17:17'),
(203, '530', 3, 'Available', '2025-05-04 18:17:17'),
(204, '530', 4, 'Available', '2025-05-04 18:17:17'),
(205, '530', 5, 'Available', '2025-05-04 18:17:17'),
(206, '530', 6, 'Available', '2025-05-04 18:17:17'),
(207, '530', 7, 'Available', '2025-05-04 18:17:17'),
(208, '530', 8, 'Available', '2025-05-04 18:17:17'),
(209, '530', 9, 'Available', '2025-05-04 18:17:17'),
(210, '530', 10, 'Available', '2025-05-04 18:17:17'),
(211, '530', 11, 'Available', '2025-05-04 18:17:17'),
(212, '530', 12, 'Available', '2025-05-04 18:17:17'),
(213, '530', 13, 'Available', '2025-05-04 18:17:17'),
(214, '530', 14, 'Available', '2025-05-04 18:17:18'),
(215, '530', 15, 'Available', '2025-05-04 18:17:18'),
(216, '530', 16, 'Available', '2025-05-04 18:17:18'),
(217, '530', 17, 'Available', '2025-05-04 18:17:18'),
(218, '530', 18, 'Available', '2025-05-04 18:17:18'),
(219, '530', 19, 'Available', '2025-05-04 18:17:18'),
(220, '530', 20, 'Available', '2025-05-04 18:17:18'),
(221, '530', 21, 'Available', '2025-05-04 18:17:18'),
(222, '530', 22, 'Available', '2025-05-04 18:17:18'),
(223, '530', 23, 'Available', '2025-05-04 18:17:18'),
(224, '530', 24, 'Available', '2025-05-04 18:17:18'),
(225, '530', 25, 'Available', '2025-05-04 18:17:18'),
(226, '530', 26, 'Available', '2025-05-04 18:17:18'),
(227, '530', 27, 'Available', '2025-05-04 18:17:18'),
(228, '530', 28, 'Available', '2025-05-04 18:17:18'),
(229, '530', 29, 'Available', '2025-05-04 18:17:18'),
(230, '530', 30, 'Available', '2025-05-04 18:17:18'),
(231, '530', 31, 'Available', '2025-05-04 18:17:18'),
(232, '530', 32, 'Available', '2025-05-04 18:17:18'),
(233, '530', 33, 'Available', '2025-05-04 18:17:18'),
(234, '530', 34, 'Available', '2025-05-04 18:17:18'),
(235, '530', 35, 'Available', '2025-05-04 18:17:18'),
(236, '530', 36, 'Available', '2025-05-04 18:17:18'),
(237, '530', 37, 'Available', '2025-05-04 18:17:18'),
(238, '530', 38, 'Available', '2025-05-04 18:17:18'),
(239, '530', 39, 'Available', '2025-05-04 18:17:18'),
(240, '530', 40, 'Available', '2025-05-04 18:17:18'),
(241, '530', 41, 'Available', '2025-05-04 18:17:18'),
(242, '530', 42, 'Available', '2025-05-04 18:17:18'),
(243, '530', 43, 'Available', '2025-05-04 18:17:18'),
(244, '530', 44, 'Available', '2025-05-04 18:17:18'),
(245, '530', 45, 'Available', '2025-05-04 18:17:18'),
(246, '530', 46, 'Available', '2025-05-04 18:17:18'),
(247, '530', 47, 'Available', '2025-05-04 18:17:18'),
(248, '530', 48, 'Available', '2025-05-04 18:17:18'),
(249, '530', 49, 'Available', '2025-05-04 18:17:18'),
(250, '530', 50, 'Available', '2025-05-04 18:17:18'),
(251, '542', 1, 'Available', '2025-05-04 18:17:18'),
(252, '542', 2, 'Available', '2025-05-04 18:17:19'),
(253, '542', 3, 'Available', '2025-05-04 18:17:19'),
(254, '542', 4, 'Available', '2025-05-04 18:17:19'),
(255, '542', 5, 'Available', '2025-05-04 18:17:19'),
(256, '542', 6, 'Available', '2025-05-04 18:17:19'),
(257, '542', 7, 'Available', '2025-05-04 18:17:19'),
(258, '542', 8, 'Available', '2025-05-04 18:17:19'),
(259, '542', 9, 'Available', '2025-05-04 18:17:19'),
(260, '542', 10, 'Available', '2025-05-04 18:17:19'),
(261, '542', 11, 'Available', '2025-05-04 18:17:19'),
(262, '542', 12, 'Available', '2025-05-04 18:17:19'),
(263, '542', 13, 'Available', '2025-05-04 18:17:19'),
(264, '542', 14, 'Available', '2025-05-04 18:17:19'),
(265, '542', 15, 'Available', '2025-05-04 18:17:19'),
(266, '542', 16, 'Available', '2025-05-04 18:17:19'),
(267, '542', 17, 'Available', '2025-05-04 18:17:19'),
(268, '542', 18, 'Available', '2025-05-04 18:17:19'),
(269, '542', 19, 'Available', '2025-05-04 18:17:19'),
(270, '542', 20, 'Available', '2025-05-04 18:17:19'),
(271, '542', 21, 'Available', '2025-05-04 18:17:19'),
(272, '542', 22, 'Available', '2025-05-04 18:17:19'),
(273, '542', 23, 'Available', '2025-05-04 18:17:19'),
(274, '542', 24, 'Available', '2025-05-04 18:17:19'),
(275, '542', 25, 'Available', '2025-05-04 18:17:19'),
(276, '542', 26, 'Available', '2025-05-04 18:17:19'),
(277, '542', 27, 'Available', '2025-05-04 18:17:19'),
(278, '542', 28, 'Available', '2025-05-04 18:17:19'),
(279, '542', 29, 'Available', '2025-05-04 18:17:19'),
(280, '542', 30, 'Available', '2025-05-04 18:17:19'),
(281, '542', 31, 'Available', '2025-05-04 18:17:19'),
(282, '542', 32, 'Available', '2025-05-04 18:17:19'),
(283, '542', 33, 'Available', '2025-05-04 18:17:19'),
(284, '542', 34, 'Available', '2025-05-04 18:17:19'),
(285, '542', 35, 'Available', '2025-05-04 18:17:19'),
(286, '542', 36, 'Available', '2025-05-04 18:17:19'),
(287, '542', 37, 'Available', '2025-05-04 18:17:19'),
(288, '542', 38, 'Available', '2025-05-04 18:17:19'),
(289, '542', 39, 'Available', '2025-05-04 18:17:19'),
(290, '542', 40, 'Available', '2025-05-04 18:17:19'),
(291, '542', 41, 'Available', '2025-05-04 18:17:20'),
(292, '542', 42, 'Available', '2025-05-04 18:17:20'),
(293, '542', 43, 'Available', '2025-05-04 18:17:20'),
(294, '542', 44, 'Available', '2025-05-04 18:17:20'),
(295, '542', 45, 'Available', '2025-05-04 18:17:20'),
(296, '542', 46, 'Available', '2025-05-04 18:17:20'),
(297, '542', 47, 'Available', '2025-05-04 18:17:20'),
(298, '542', 48, 'Available', '2025-05-04 18:17:20'),
(299, '542', 49, 'Available', '2025-05-04 18:17:20'),
(300, '542', 50, 'Available', '2025-05-04 18:17:20'),
(301, '544', 1, 'Available', '2025-05-04 18:17:20'),
(302, '544', 2, 'Available', '2025-05-04 18:17:20'),
(303, '544', 3, 'Available', '2025-05-04 18:17:20'),
(304, '544', 4, 'Available', '2025-05-04 18:17:20'),
(305, '544', 5, 'Available', '2025-05-04 18:17:20'),
(306, '544', 6, 'Available', '2025-05-04 18:17:20'),
(307, '544', 7, 'Available', '2025-05-04 18:17:20'),
(308, '544', 8, 'Available', '2025-05-04 18:17:20'),
(309, '544', 9, 'Available', '2025-05-04 18:17:20'),
(310, '544', 10, 'Available', '2025-05-04 18:17:20'),
(311, '544', 11, 'Available', '2025-05-04 18:17:20'),
(312, '544', 12, 'Available', '2025-05-04 18:17:20'),
(313, '544', 13, 'Available', '2025-05-04 18:17:20'),
(314, '544', 14, 'Available', '2025-05-04 18:17:20'),
(315, '544', 15, 'Available', '2025-05-04 18:17:21'),
(316, '544', 16, 'Available', '2025-05-04 18:17:21'),
(317, '544', 17, 'Available', '2025-05-04 18:17:21'),
(318, '544', 18, 'Available', '2025-05-04 18:17:21'),
(319, '544', 19, 'Available', '2025-05-04 18:17:21'),
(320, '544', 20, 'Available', '2025-05-04 18:17:21'),
(321, '544', 21, 'Available', '2025-05-04 18:17:21'),
(322, '544', 22, 'Available', '2025-05-04 18:17:21'),
(323, '544', 23, 'Available', '2025-05-04 18:17:21'),
(324, '544', 24, 'Available', '2025-05-04 18:17:21'),
(325, '544', 25, 'Available', '2025-05-04 18:17:21'),
(326, '544', 26, 'Available', '2025-05-04 18:17:21'),
(327, '544', 27, 'Available', '2025-05-04 18:17:21'),
(328, '544', 28, 'Available', '2025-05-04 18:17:21'),
(329, '544', 29, 'Available', '2025-05-04 18:17:21'),
(330, '544', 30, 'Available', '2025-05-04 18:17:21'),
(331, '544', 31, 'Available', '2025-05-04 18:17:21'),
(332, '544', 32, 'Available', '2025-05-04 18:17:21'),
(333, '544', 33, 'Available', '2025-05-04 18:17:21'),
(334, '544', 34, 'Available', '2025-05-04 18:17:21'),
(335, '544', 35, 'Available', '2025-05-04 18:17:21'),
(336, '544', 36, 'Available', '2025-05-04 18:17:21'),
(337, '544', 37, 'Available', '2025-05-04 18:17:21'),
(338, '544', 38, 'Available', '2025-05-04 18:17:21'),
(339, '544', 39, 'Available', '2025-05-04 18:17:21'),
(340, '544', 40, 'Available', '2025-05-04 18:17:21'),
(341, '544', 41, 'Available', '2025-05-04 18:17:21'),
(342, '544', 42, 'Available', '2025-05-04 18:17:21'),
(343, '544', 43, 'Available', '2025-05-04 18:17:21'),
(344, '544', 44, 'Available', '2025-05-04 18:17:21'),
(345, '544', 45, 'Available', '2025-05-04 18:17:21'),
(346, '544', 46, 'Available', '2025-05-04 18:17:22'),
(347, '544', 47, 'Available', '2025-05-04 18:17:22'),
(348, '544', 48, 'Available', '2025-05-04 18:17:22'),
(349, '544', 49, 'Available', '2025-05-04 18:17:22'),
(350, '544', 50, 'Available', '2025-05-04 18:17:22');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `FEEDBACK_ID` int(11) NOT NULL,
  `STUDENT_ID` int(11) NOT NULL,
  `LABORATORY` varchar(50) NOT NULL,
  `SESSION_DATE` date NOT NULL,
  `FEEDBACK_MSG` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`FEEDBACK_ID`, `STUDENT_ID`, `LABORATORY`, `SESSION_DATE`, `FEEDBACK_MSG`) VALUES
(1, 2000, '524', '2025-03-23', 'hi'),
(2, 2000, '524', '0000-00-00', 'yay'),
(3, 2000, '528', '2025-04-20', 'asdf'),
(4, 2000, '524', '2025-04-20', 'okok'),
(5, 2000, '517', '2025-05-04', 'pppp'),
(6, 2000, '544', '2025-04-23', 'ppp'),
(7, 2000, '542', '2025-04-23', 'ppppp'),
(8, 2000, '530', '2025-04-22', 'popo'),
(9, 2000, '524', '2025-04-23', 'opopop'),
(10, 2000, '517', '2025-05-05', 'fasdfasdfas');

-- --------------------------------------------------------

--
-- Table structure for table `laboratory_schedule`
--

CREATE TABLE `laboratory_schedule` (
  `SCHEDULE_ID` int(11) NOT NULL,
  `LABORATORY` varchar(50) NOT NULL,
  `START_TIME` time NOT NULL,
  `END_TIME` time NOT NULL,
  `DAY_OF_WEEK` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `CREATED_BY` int(11) NOT NULL,
  `CREATED_AT` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `RESERVATION_ID` int(11) NOT NULL,
  `STUDENT_ID` int(11) NOT NULL,
  `LABORATORY` varchar(50) NOT NULL,
  `PURPOSE` varchar(50) NOT NULL,
  `RESERVATION_DATE` date NOT NULL,
  `START_TIME` time NOT NULL,
  `END_TIME` time NOT NULL,
  `STATUS` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `ADMIN_NOTES` text DEFAULT NULL,
  `CREATED_AT` datetime NOT NULL DEFAULT current_timestamp(),
  `PC_NUMBER` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`RESERVATION_ID`, `STUDENT_ID`, `LABORATORY`, `PURPOSE`, `RESERVATION_DATE`, `START_TIME`, `END_TIME`, `STATUS`, `ADMIN_NOTES`, `CREATED_AT`, `PC_NUMBER`) VALUES
(1, 2000, '530', 'C# Programming', '2025-04-22', '00:14:00', '02:14:00', 'Approved', NULL, '2025-04-22 00:14:00', NULL),
(2, 2000, '524', 'C# Programming', '2025-05-05', '02:00:00', '00:00:00', 'Approved', NULL, '2025-05-05 03:02:08', 48),
(3, 3000, '530', 'Computer Application', '2025-05-05', '03:11:00', '00:00:00', 'Approved', NULL, '2025-05-05 03:09:37', 1),
(4, 2000, '517', '.NET Programming', '2025-05-05', '03:20:00', '00:00:00', 'Approved', NULL, '2025-05-05 03:19:40', 2),
(5, 2000, '517', 'Capstone', '2025-05-05', '09:23:00', '00:00:00', 'Approved', NULL, '2025-05-05 03:22:35', 19);

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `RESOURCE_ID` int(11) NOT NULL,
  `TITLE` varchar(255) NOT NULL,
  `DESCRIPTION` text DEFAULT NULL,
  `FILE_NAME` varchar(255) NOT NULL,
  `FILE_PATH` varchar(255) NOT NULL,
  `FILE_TYPE` varchar(50) NOT NULL,
  `UPLOADED_BY` int(11) NOT NULL,
  `UPLOAD_DATE` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`RESOURCE_ID`, `TITLE`, `DESCRIPTION`, `FILE_NAME`, `FILE_PATH`, `FILE_TYPE`, `UPLOADED_BY`, `UPLOAD_DATE`) VALUES
(1, 'Prospectus', 'Yes', '(BSIT)2024-2025.pdf', '6817b5e2df29f_(BSIT)2024-2025.pdf', 'pdf', 1000, '2025-05-05 02:45:54');

-- --------------------------------------------------------

--
-- Table structure for table `sit-in`
--

CREATE TABLE `sit-in` (
  `ID` int(11) NOT NULL,
  `STUDENT_ID` int(11) NOT NULL,
  `LABORATORY` varchar(50) NOT NULL,
  `PURPOSE` varchar(50) NOT NULL,
  `SESSION_DATE` date NOT NULL,
  `LOGIN_TIME` time DEFAULT NULL,
  `LOGOUT_TIME` time DEFAULT NULL,
  `STATUS` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sit-in`
--

INSERT INTO `sit-in` (`ID`, `STUDENT_ID`, `LABORATORY`, `PURPOSE`, `SESSION_DATE`, `LOGIN_TIME`, `LOGOUT_TIME`, `STATUS`) VALUES
(1, 3000, '524', '.NET Programming', '0000-00-00', NULL, NULL, 'Inactive'),
(2, 3000, '544', 'PHP Programming', '0000-00-00', NULL, NULL, 'Inactive'),
(3, 4000, '544', 'JAVA Programming', '0000-00-00', NULL, NULL, 'Inactive'),
(4, 2000, '524', 'C Programming', '0000-00-00', NULL, NULL, 'Inactive'),
(5, 3000, '528', 'C Programming', '0000-00-00', NULL, NULL, 'Inactive'),
(6, 4000, '544', 'JAVA Programming', '0000-00-00', NULL, NULL, 'Inactive'),
(7, 4000, '530', 'JAVA Programming', '2025-03-23', NULL, NULL, 'Inactive'),
(8, 3000, '530', 'C Programming', '2025-03-23', NULL, NULL, 'Inactive'),
(9, 3000, '544', '.NET Programming', '2025-03-23', NULL, NULL, 'Inactive'),
(10, 2000, '524', 'C# Programming', '2025-03-23', '19:04:11', '19:04:29', 'Inactive'),
(11, 5000, '524', 'C Programming', '2025-03-23', '19:04:16', '19:04:25', 'Inactive'),
(12, 2000, '524', 'C Programming', '2025-03-23', '19:26:06', '19:26:08', 'Inactive'),
(13, 4000, '530', 'C Programming', '2025-03-23', '19:26:16', '19:26:19', 'Inactive'),
(14, 4000, '530', 'PHP Programming', '2025-03-23', '19:26:25', '18:09:43', 'Inactive'),
(15, 2000, '528', 'IT Trends', '2025-04-20', '18:09:18', '18:09:41', 'Inactive'),
(16, 2000, '524', 'Database', '2025-04-20', '18:09:33', '18:09:41', 'Inactive'),
(17, 2000, '530', 'C# Programming', '2025-04-22', NULL, '18:27:47', 'Inactive'),
(18, 3000, '544', 'Capstone', '2025-04-23', '05:26:15', '05:26:17', 'Inactive'),
(19, 4000, '544', 'Embedded System and IoT', '2025-04-23', '05:26:23', '05:26:25', 'Inactive'),
(20, 2000, '524', 'Capstone', '2025-04-23', '05:26:32', '05:26:33', 'Inactive'),
(21, 2000, '544', 'Database', '2025-04-23', '05:26:38', '05:26:40', 'Inactive'),
(22, 2000, '544', 'System Integration and Architecture', '2025-04-23', '05:26:45', '05:26:47', 'Inactive'),
(23, 2000, '542', 'Capstone', '2025-04-23', '05:26:53', '05:26:55', 'Inactive'),
(24, 3000, '542', 'Capstone', '2025-04-23', '05:27:00', '05:27:01', 'Inactive'),
(25, 3000, '544', 'Embedded System and IoT', '2025-04-23', '05:27:06', '05:27:10', 'Inactive'),
(26, 3000, '544', 'Database', '2025-04-23', '05:27:16', '05:27:18', 'Inactive'),
(27, 3000, '524', 'Capstone', '2025-04-23', '05:27:23', '05:27:25', 'Inactive'),
(28, 3000, '544', 'C Programming', '2025-04-23', '05:27:31', '05:27:33', 'Inactive'),
(29, 3000, '530', 'Digital Logic and Design', '2025-04-23', '05:27:40', '05:27:41', 'Inactive'),
(30, 5000, '530', 'Project Management', '2025-04-23', '05:27:46', '05:27:47', 'Inactive'),
(31, 5000, '530', 'JAVA Programming', '2025-04-23', '06:04:05', '06:04:07', 'Inactive'),
(32, 5000, '528', 'IT Trends', '2025-04-23', '06:04:14', '06:04:15', 'Inactive'),
(33, 2000, '544', 'IT Trends', '2025-04-23', '06:04:21', '06:04:23', 'Inactive'),
(34, 5000, '542', 'IT Trends', '2025-04-23', '06:04:28', '06:04:29', 'Inactive'),
(35, 2000, '517', 'C# Programming', '2025-05-04', '20:18:12', '20:18:33', 'Inactive'),
(36, 3000, '526', 'C# Programming', '2025-05-04', '20:18:42', '20:18:45', 'Inactive'),
(37, 2000, '524', 'C# Programming', '2025-05-05', NULL, '21:12:13', 'Inactive'),
(38, 2000, '524', 'C# Programming', '2025-05-05', NULL, '21:12:13', 'Inactive'),
(39, 3000, '530', 'Computer Application', '2025-05-05', NULL, '21:20:10', 'Inactive'),
(40, 2000, '517', '.NET Programming', '2025-05-05', NULL, '21:20:12', 'Inactive'),
(41, 2000, '517', 'Capstone', '2025-05-05', NULL, '02:07:08', 'Inactive');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `IDNO` int(11) NOT NULL,
  `LASTNAME` varchar(48) NOT NULL,
  `FIRSTNAME` varchar(48) NOT NULL,
  `MIDDLENAME` varchar(50) DEFAULT NULL,
  `COURSE` varchar(4) NOT NULL,
  `YEAR` int(1) NOT NULL,
  `EMAIL` varchar(255) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `SESSION` int(2) NOT NULL,
  `ROLE` varchar(7) NOT NULL,
  `POINTS` int(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`IDNO`, `LASTNAME`, `FIRSTNAME`, `MIDDLENAME`, `COURSE`, `YEAR`, `EMAIL`, `PASSWORD`, `SESSION`, `ROLE`, `POINTS`) VALUES
(1000, 'Pat', 'Paul', 'T', 'BSIT', 4, 'pat@email.com', 'admin', 30, 'admin', NULL),
(2000, 'DESO', 'LATOR', NULL, 'BSIT', 2, 'lator@email.com', '2000', 29, 'Student', NULL),
(3000, 'eul', 'scepter', 's', 'BSIT', 1, 'eul@email.com', '3000', 14, 'Student', 1),
(4000, 'staff', 'force', 'g', 'NAME', 2, 'force@email.com', '4000', 10, 'Student', NULL),
(5000, 'cape', 'glimmer', 'a', 'BSIT', 1, 'glimmer@email.com', '5000', 25, 'Student', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_ADMIN_ID` (`ADMIN_ID`);

--
-- Indexes for table `computer_status`
--
ALTER TABLE `computer_status`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `LAB_COMP_NUM` (`LABORATORY`,`COMPUTER_NUMBER`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`FEEDBACK_ID`),
  ADD KEY `FK_FEEDBACK_STUDENT_ID` (`STUDENT_ID`);

--
-- Indexes for table `laboratory_schedule`
--
ALTER TABLE `laboratory_schedule`
  ADD PRIMARY KEY (`SCHEDULE_ID`),
  ADD KEY `FK_SCHEDULE_ADMIN_ID` (`CREATED_BY`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`RESERVATION_ID`),
  ADD KEY `FK_RESERVATION_STUDENT_ID` (`STUDENT_ID`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`RESOURCE_ID`),
  ADD KEY `FK_RESOURCES_ADMIN_ID` (`UPLOADED_BY`);

--
-- Indexes for table `sit-in`
--
ALTER TABLE `sit-in`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_STUDENT_ID` (`STUDENT_ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`IDNO`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `computer_status`
--
ALTER TABLE `computer_status`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=351;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `FEEDBACK_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `laboratory_schedule`
--
ALTER TABLE `laboratory_schedule`
  MODIFY `SCHEDULE_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `RESERVATION_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `RESOURCE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sit-in`
--
ALTER TABLE `sit-in`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcement`
--
ALTER TABLE `announcement`
  ADD CONSTRAINT `FK_ADMIN_ID` FOREIGN KEY (`ADMIN_ID`) REFERENCES `user` (`IDNO`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `FK_FEEDBACK_STUDENT_ID` FOREIGN KEY (`STUDENT_ID`) REFERENCES `user` (`IDNO`);

--
-- Constraints for table `laboratory_schedule`
--
ALTER TABLE `laboratory_schedule`
  ADD CONSTRAINT `FK_SCHEDULE_ADMIN_ID` FOREIGN KEY (`CREATED_BY`) REFERENCES `user` (`IDNO`);

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `FK_RESERVATION_STUDENT_ID` FOREIGN KEY (`STUDENT_ID`) REFERENCES `user` (`IDNO`);

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `FK_RESOURCES_ADMIN_ID` FOREIGN KEY (`UPLOADED_BY`) REFERENCES `user` (`IDNO`);

--
-- Constraints for table `sit-in`
--
ALTER TABLE `sit-in`
  ADD CONSTRAINT `FK_STUDENT_ID` FOREIGN KEY (`STUDENT_ID`) REFERENCES `user` (`IDNO`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
