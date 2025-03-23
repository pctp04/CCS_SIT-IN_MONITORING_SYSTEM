-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2025 at 04:35 PM
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
(1, 3000, '524', '.NET Programming', '2025-03-23', '11:23:27', '11:23:32', 'Inactive'),
(2, 3000, '544', 'PHP Programming', '2025-03-23', '10:38:45', '10:39:05', 'Inactive'),
(3, 4000, '544', 'JAVA Programming', '2025-03-23', '12:56:44', '12:56:59', 'Inactive'),
(4, 2000, '524', 'C Programming', '2025-03-23', '05:57:27', '05:57:42', 'Inactive'),
(5, 3000, '528', 'C Programming', '2025-03-23', '11:40:07', '11:40:57', 'Inactive'),
(6, 4000, '544', 'JAVA Programming', '2025-03-23', '11:14:46', '12:01:11', 'Inactive'),
(7, 4000, '530', 'JAVA Programming', '2025-03-23', '05:06:27', '05:06:42', 'Inactive'),
(8, 3000, '530', 'C Programming', '2025-03-23', '11:40:07', NULL, 'Active');

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
  `ROLE` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`IDNO`, `LASTNAME`, `FIRSTNAME`, `MIDDLENAME`, `COURSE`, `YEAR`, `EMAIL`, `PASSWORD`, `SESSION`, `ROLE`) VALUES
(1000, 'Pat', 'Paul', 'T', 'BSIT', 4, 'pat@email.com', 'admin', 30, 'admin'),
(2000, 'DESO', 'LATOR', NULL, 'BSEd', 3, 'lator@email.com', '2000', 14, 'Student'),
(3000, 'eul', 'scepter', 's', 'BSIT', 1, 'eul@email.com', '3000', 24, 'Student'),
(4000, 'staff', 'force', 'g', 'NAME', 2, 'force@email.com', '4000', 13, 'Student'),
(5000, 'cape', 'glimmer', 'a', 'BSIT', 1, 'glimmer@email.com', '5000', 30, 'Student');

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `FEEDBACK_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sit-in`
--
ALTER TABLE `sit-in`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- Constraints for table `sit-in`
--
ALTER TABLE `sit-in`
  ADD CONSTRAINT `FK_STUDENT_ID` FOREIGN KEY (`STUDENT_ID`) REFERENCES `user` (`IDNO`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
