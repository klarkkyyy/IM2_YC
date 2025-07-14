-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 15, 2025 at 01:45 AM
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
-- Database: `im2_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `application`
--

CREATE TABLE `application` (
  `ApplicationID` int(11) NOT NULL,
  `ClientID` int(11) DEFAULT NULL,
  `ApplicationType` enum('Project','Rental') NOT NULL,
  `Description` text DEFAULT NULL,
  `SubmissionDate` date DEFAULT NULL,
  `Status` enum('Pending','Approved','Denied') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `ClientID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `CompanyName` varchar(100) DEFAULT NULL,
  `ContactInfo` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `EmployeeID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `Role` enum('Site Engineer','Document Controller','Project Manager') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `EquipmentID` int(11) NOT NULL,
  `EquipmentName` varchar(100) DEFAULT NULL,
  `RentalStartDate` date DEFAULT NULL,
  `RentalEndDate` date DEFAULT NULL,
  `NeedsOperator` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipmentrental`
--

CREATE TABLE `equipmentrental` (
  `RentalID` int(11) NOT NULL,
  `EquipmentID` int(11) DEFAULT NULL,
  `ApplicationID` int(11) DEFAULT NULL,
  `RentalDuration` int(11) DEFAULT NULL,
  `DeliveryLocation` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `ProjectID` int(11) NOT NULL,
  `ApplicationID` int(11) DEFAULT NULL,
  `ProposalID` int(11) DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `Status` enum('Ongoing','Completed','Cancelled') DEFAULT 'Ongoing',
  `CurrentBalance` decimal(12,2) DEFAULT NULL,
  `IsFullyPaid` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projectassign`
--

CREATE TABLE `projectassign` (
  `AssignmentID` int(11) NOT NULL,
  `ProjectID` int(11) DEFAULT NULL,
  `AssigneeEmployeeID` int(11) DEFAULT NULL,
  `Role` varchar(50) DEFAULT NULL,
  `AssignmentDate` date DEFAULT NULL,
  `LaborersAssigned` int(11) DEFAULT NULL,
  `Laborer_FullName` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projectproposal`
--

CREATE TABLE `projectproposal` (
  `ProposalID` int(11) NOT NULL,
  `ConstructionType` enum('Residential','Commercial','Flood Control','Road Construction') DEFAULT NULL,
  `Terrain` varchar(100) DEFAULT NULL,
  `ProjectLocation` varchar(150) DEFAULT NULL,
  `EstimatedBudget` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projectupdate`
--

CREATE TABLE `projectupdate` (
  `UpdateID` int(11) NOT NULL,
  `ProjectID` int(11) DEFAULT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `SubmittedBy` varchar(100) DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `FullName` varchar(100) DEFAULT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `UserType` enum('Client','Admin','Project Manager') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Email`, `FullName`, `Username`, `Password`, `UserType`) VALUES
(5, 'karl123@gmail.com', 'Karl Medina', 'kark', '$2y$10$.wdWOquVv63WBxqbvFL73.vYlB2ntr/XHRTtX9BhbRmuKm9a192Ym', 'Client'),
(6, 'karl12@gmail.com', 'Karl Medina', 'karol', '$2y$10$Yo6EEWeOEfD5C2H8qPtJJOnXRX9vE3xqGvQE1vvLA1rwVQnTJa3em', 'Client');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`ApplicationID`),
  ADD KEY `ClientID` (`ClientID`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`ClientID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`EmployeeID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`EquipmentID`);

--
-- Indexes for table `equipmentrental`
--
ALTER TABLE `equipmentrental`
  ADD PRIMARY KEY (`RentalID`),
  ADD KEY `EquipmentID` (`EquipmentID`),
  ADD KEY `ApplicationID` (`ApplicationID`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`ProjectID`),
  ADD KEY `ApplicationID` (`ApplicationID`),
  ADD KEY `ProposalID` (`ProposalID`);

--
-- Indexes for table `projectassign`
--
ALTER TABLE `projectassign`
  ADD PRIMARY KEY (`AssignmentID`),
  ADD KEY `ProjectID` (`ProjectID`),
  ADD KEY `AssigneeEmployeeID` (`AssigneeEmployeeID`);

--
-- Indexes for table `projectproposal`
--
ALTER TABLE `projectproposal`
  ADD PRIMARY KEY (`ProposalID`);

--
-- Indexes for table `projectupdate`
--
ALTER TABLE `projectupdate`
  ADD PRIMARY KEY (`UpdateID`),
  ADD KEY `ProjectID` (`ProjectID`),
  ADD KEY `EmployeeID` (`EmployeeID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `application`
--
ALTER TABLE `application`
  MODIFY `ApplicationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `ClientID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `EquipmentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipmentrental`
--
ALTER TABLE `equipmentrental`
  MODIFY `RentalID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `ProjectID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projectassign`
--
ALTER TABLE `projectassign`
  MODIFY `AssignmentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projectproposal`
--
ALTER TABLE `projectproposal`
  MODIFY `ProposalID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projectupdate`
--
ALTER TABLE `projectupdate`
  MODIFY `UpdateID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `application`
--
ALTER TABLE `application`
  ADD CONSTRAINT `application_ibfk_1` FOREIGN KEY (`ClientID`) REFERENCES `client` (`ClientID`);

--
-- Constraints for table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `client_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `equipmentrental`
--
ALTER TABLE `equipmentrental`
  ADD CONSTRAINT `equipmentrental_ibfk_1` FOREIGN KEY (`EquipmentID`) REFERENCES `equipment` (`EquipmentID`),
  ADD CONSTRAINT `equipmentrental_ibfk_2` FOREIGN KEY (`ApplicationID`) REFERENCES `application` (`ApplicationID`);

--
-- Constraints for table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `project_ibfk_1` FOREIGN KEY (`ApplicationID`) REFERENCES `application` (`ApplicationID`),
  ADD CONSTRAINT `project_ibfk_2` FOREIGN KEY (`ProposalID`) REFERENCES `projectproposal` (`ProposalID`);

--
-- Constraints for table `projectassign`
--
ALTER TABLE `projectassign`
  ADD CONSTRAINT `projectassign_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `project` (`ProjectID`),
  ADD CONSTRAINT `projectassign_ibfk_2` FOREIGN KEY (`AssigneeEmployeeID`) REFERENCES `employee` (`EmployeeID`);

--
-- Constraints for table `projectupdate`
--
ALTER TABLE `projectupdate`
  ADD CONSTRAINT `projectupdate_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `project` (`ProjectID`),
  ADD CONSTRAINT `projectupdate_ibfk_2` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
