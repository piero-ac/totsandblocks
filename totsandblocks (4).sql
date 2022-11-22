-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 22, 2022 at 03:07 PM
-- Server version: 5.7.34
-- PHP Version: 7.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `totsandblocks`
--

-- --------------------------------------------------------

--
-- Table structure for table `Category`
--

CREATE TABLE `Category` (
  `categoryID` int(11) NOT NULL,
  `categoryName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Category`
--

INSERT INTO `Category` (`categoryID`, `categoryName`) VALUES
(1, 'Kitchen'),
(2, 'Office'),
(3, 'Classroom'),
(4, 'Food'),
(5, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `Item`
--

CREATE TABLE `Item` (
  `itemCode` varchar(255) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `itemCategory` int(11) NOT NULL,
  `itemDescription` varchar(255) DEFAULT NULL,
  `addedBy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Item`
--

INSERT INTO `Item` (`itemCode`, `itemName`, `itemCategory`, `itemDescription`, `addedBy`) VALUES
('0622-90', 'Sterilite Dish Drying Rack', 1, 'Sterilite Dish Rack with Self Draining Base, Black', 1),
('1293', 'Bounty Paper Plates', 5, 'Goodwill', 1),
('527', 'Bazic Construction', 2, '16 Sheet 18\"x12\" Assorted Colors, 2-Pack', 1),
('81029', 'Expo Low Odor Dry Erase Marker Set', 2, 'Chisel Tips, Fashion Colors, 4 Count', 1),
('calcti30', 'Calculator TI-30', 2, 'Used for teaching math.', 1),
('cocacola24', 'Coca-Cola Soda Soft Drink', 4, '12 fl oz, 24 Pack', 1),
('cray', 'Crayons', 3, '24-pack', 1),
('xyz', 'Mackeral', 2, 'White Printer Paper', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Location`
--

CREATE TABLE `Location` (
  `locationID` int(11) NOT NULL,
  `locationName` varchar(20) NOT NULL,
  `locationAddress` varchar(255) NOT NULL,
  `locationPhone` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Location`
--

INSERT INTO `Location` (`locationID`, `locationName`, `locationAddress`, `locationPhone`) VALUES
(1, 'Academy', '2500 Route 9, South St, Old Bridge, NJ 08857', '7326798866'),
(2, 'Preschool', '2 Worth Pl, Old Bridge, NJ 08857', '7326790088');

-- --------------------------------------------------------

--
-- Table structure for table `Quantity`
--

CREATE TABLE `Quantity` (
  `itemCode` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `locationID` int(11) NOT NULL,
  `addedBy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Quantity`
--

INSERT INTO `Quantity` (`itemCode`, `quantity`, `locationID`, `addedBy`) VALUES
('0622-90', 29, 1, 1),
('1293', 3, 1, 1),
('1293', 23, 2, 1),
('527', 1, 1, 1),
('527', 11, 2, 1),
('81029', 7, 1, 1),
('81029', 13, 2, 1),
('cocacola24', 3, 1, 1),
('xyz', 2, 1, 1),
('xyz', 5, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `userID` int(11) NOT NULL,
  `fName` varchar(255) NOT NULL,
  `lName` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `sex` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`userID`, `fName`, `lName`, `position`, `username`, `password`, `sex`) VALUES
(1, 'Piero', 'Coronado', 'Director', 'pcoronado', 'test123', 'M');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Category`
--
ALTER TABLE `Category`
  ADD PRIMARY KEY (`categoryID`);

--
-- Indexes for table `Item`
--
ALTER TABLE `Item`
  ADD PRIMARY KEY (`itemCode`),
  ADD KEY `itemCategory` (`itemCategory`),
  ADD KEY `addedBy` (`addedBy`);

--
-- Indexes for table `Location`
--
ALTER TABLE `Location`
  ADD PRIMARY KEY (`locationID`);

--
-- Indexes for table `Quantity`
--
ALTER TABLE `Quantity`
  ADD PRIMARY KEY (`itemCode`,`locationID`) USING BTREE,
  ADD KEY `locationID` (`locationID`),
  ADD KEY `addedBy` (`addedBy`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Category`
--
ALTER TABLE `Category`
  MODIFY `categoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Location`
--
ALTER TABLE `Location`
  MODIFY `locationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Item`
--
ALTER TABLE `Item`
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`itemCategory`) REFERENCES `Category` (`categoryID`),
  ADD CONSTRAINT `item_ibfk_2` FOREIGN KEY (`addedBy`) REFERENCES `Users` (`userID`);

--
-- Constraints for table `Quantity`
--
ALTER TABLE `Quantity`
  ADD CONSTRAINT `quantity_ibfk_1` FOREIGN KEY (`itemCode`) REFERENCES `Item` (`itemCode`),
  ADD CONSTRAINT `quantity_ibfk_2` FOREIGN KEY (`locationID`) REFERENCES `Location` (`locationID`),
  ADD CONSTRAINT `quantity_ibfk_3` FOREIGN KEY (`addedBy`) REFERENCES `Users` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
