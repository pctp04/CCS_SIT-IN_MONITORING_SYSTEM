-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 03, 2025 at 04:55 AM
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
-- Table structure for table `students`
--

CREATE TABLE `students` (
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

INSERT INTO `students` (`LASTNAME`, `FIRSTNAME`, `MIDDLENAME`, `COURSE`, `YEAR`, `EMAIL`, `PASSWORD`, `SESSION`) VALUES
('ss', 'aa', 'tt', 'asdf', 1, 'dfas@email.com', '1', 0),
('studenbt', 'asdfasfdasddfasf', '2asdfaf', 'BSIT', 4, '3@email.com', '2', 0),
('4', '4', '4', '4', 1, '4@email.com', '4', 0),
('5', '5', '5', '5', 4, '5@email.com', '5', 0),
('6', '6', '6', 'BSIT', 4, '6@email.com', '6', 0),
('7', '7', '7', 'BSIT', 1, '7@email.ccom', '7', 0),
('6', '4', '6', 'BSIT', 1, 'dfas@email.com', '123', 0),
('12', '12', '12', 'BSIT', 1, '12@email.com', '12', 30),
('pats', 'pc', 't', 'BSIT', 1, 'dfas@email.com', '2222', 0),
('patino', 'paul', 'te', 'ACT', 4, 'patinopc@gmail.com', 'sample', 0);

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
-- Indexes for dumped tables
--

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`IDNO`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
