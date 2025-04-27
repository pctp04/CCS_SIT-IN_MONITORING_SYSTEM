-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 07:06 PM
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
(4, 2000, '524', '2025-04-20', 'okok');

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
  `STATUS` enum('Pending','Approved','Rejected','') NOT NULL,
  `CREATED_AT` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`RESERVATION_ID`, `STUDENT_ID`, `LABORATORY`, `PURPOSE`, `RESERVATION_DATE`, `START_TIME`, `END_TIME`, `STATUS`, `CREATED_AT`) VALUES
(1, 2000, '530', 'C# Programming', '2025-04-22', '00:14:00', '02:14:00', 'Approved', '0000-00-00 00:00:00');

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
  `STATUS` varchar(50) NOT NULL
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
(34, 5000, '542', 'IT Trends', '2025-04-23', '06:04:28', '06:04:29', 'Inactive');

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
(2000, 'DESO', 'LATOR', NULL, 'BSIT', 2, 'lator@email.com', '2000', 5, 'Student', NULL),
(3000, 'eul', 'scepter', 's', 'BSIT', 1, 'eul@email.com', '3000', 16, 'Student', NULL),
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
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`FEEDBACK_ID`),
  ADD KEY `FK_FEEDBACK_STUDENT_ID` (`STUDENT_ID`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`RESERVATION_ID`),
  ADD KEY `FK_RESERVATION_STUDENT_ID` (`STUDENT_ID`);

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
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `FEEDBACK_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `RESERVATION_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sit-in`
--
ALTER TABLE `sit-in`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

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
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `FK_RESERVATION_STUDENT_ID` FOREIGN KEY (`STUDENT_ID`) REFERENCES `user` (`IDNO`);

--
-- Constraints for table `sit-in`
--
ALTER TABLE `sit-in`
  ADD CONSTRAINT `FK_STUDENT_ID` FOREIGN KEY (`STUDENT_ID`) REFERENCES `user` (`IDNO`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
