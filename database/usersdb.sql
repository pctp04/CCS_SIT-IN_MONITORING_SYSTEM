-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2025 at 05:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `ID` int(11) NOT NULL,
  `IDNO` int(20) NOT NULL,
  `LASTNAME` varchar(255) NOT NULL,
  `FIRSTNAME` varchar(255) NOT NULL,
  `MIDDLENAME` varchar(255) DEFAULT NULL,
  `COURSE` varchar(255) NOT NULL,
  `YEAR` int(11) NOT NULL,
  `EMAIL` varchar(255) NOT NULL,
  `PASSWORD` char(255) NOT NULL,
  `SESSION` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`ID`, `IDNO`, `LASTNAME`, `FIRSTNAME`, `MIDDLENAME`, `COURSE`, `YEAR`, `EMAIL`, `PASSWORD`, `SESSION`) VALUES
(1, 1, 'ss', 'aa', 'tt', 'asdf', 1, 'dfas@email.com', '1', 0),
(3, 2, '2', '2', '2', '2', 2, '2@email.com', '2', 0),
(4, 4, '4', '4', '4', '4', 1, '4@email.com', '4', 0),
(5, 5, '5', '5', '5', '5', 4, '5@email.com', '5', 0),
(6, 6, '6', '6', '6', 'BSIT', 4, '6@email.com', '6', 0),
(8, 7, '7', '7', '7', 'BSIT', 1, '7@email.ccom', '7', 0),
(9, 10, '6', '4', '6', 'BSIT', 1, 'dfas@email.com', '123', 0),
(10, 2222, 'pats', 'pc', 't', 'BSIT', 1, 'dfas@email.com', '2222', 0),
(11, 12345, 'patino', 'paul', 'te', 'ACT', 4, 'patinopc@gmail.com', 'sample', 0),
(12, 12, '12', '12', '12', 'BSIT', 1, '12@email.com', '12', 30);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `IDNO` (`IDNO`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
