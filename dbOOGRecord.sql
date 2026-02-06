-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Feb 06, 2026 at 08:56 PM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbOOGRecord`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblDetail`
--

CREATE TABLE `tblDetail` (
  `sNumber` int NOT NULL,
  `recordID` int NOT NULL,
  `airline` varchar(25) NOT NULL,
  `fNumber` varchar(10) NOT NULL,
  `unit` int NOT NULL,
  `rTime` time NOT NULL,
  `dTime` time DEFAULT NULL,
  `cgTime` time DEFAULT NULL,
  `total` int DEFAULT NULL,
  `remark` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblRecord`
--

CREATE TABLE `tblRecord` (
  `recordID` int NOT NULL,
  `typeID` int NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblType`
--

CREATE TABLE `tblType` (
  `typeID` int NOT NULL,
  `type` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblDetail`
--
ALTER TABLE `tblDetail`
  ADD PRIMARY KEY (`sNumber`),
  ADD KEY `fk_record_detail` (`recordID`);

--
-- Indexes for table `tblRecord`
--
ALTER TABLE `tblRecord`
  ADD PRIMARY KEY (`recordID`),
  ADD KEY `fk_record_type` (`typeID`);

--
-- Indexes for table `tblType`
--
ALTER TABLE `tblType`
  ADD PRIMARY KEY (`typeID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblRecord`
--
ALTER TABLE `tblRecord`
  MODIFY `recordID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblType`
--
ALTER TABLE `tblType`
  MODIFY `typeID` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblDetail`
--
ALTER TABLE `tblDetail`
  ADD CONSTRAINT `fk_record_detail` FOREIGN KEY (`recordID`) REFERENCES `tblRecord` (`recordID`);

--
-- Constraints for table `tblRecord`
--
ALTER TABLE `tblRecord`
  ADD CONSTRAINT `fk_record_type` FOREIGN KEY (`typeID`) REFERENCES `tblType` (`typeID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
