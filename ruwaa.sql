-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 01, 2025 at 03:42 PM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ruwaa`
--

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `ClientID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Phone Number` varchar(100) NOT NULL,
  `role` enum('Client','MakeupArtist') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`ClientID`, `Name`, `Email`, `Password`, `Phone Number`, `role`) VALUES
(2832, 'hessah', 'hessah@gmail.com', '$2y$10$zNCJ2887UASaaR3Tp0jZu.d5jF.eWiNnjAd0lxucXiawPsgF2tmpC', '09876543456789', 'Client');

-- --------------------------------------------------------

--
-- Table structure for table `makeup artist profile`
--

CREATE TABLE `makeup artist profile` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `whatsapp` varchar(100) NOT NULL,
  `instagram` varchar(100) NOT NULL,
  `services` json NOT NULL,
  `work_images` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `makeup atrist`
--

CREATE TABLE `makeup atrist` (
  `ArtistID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `PhoneNumber` varchar(100) NOT NULL,
  `Description` text NOT NULL,
  `Services` enum('Bridal Makeup','Evening Makeup') NOT NULL,
  `InstagramAccount` varchar(50) NOT NULL,
  `Profile` varchar(255) NOT NULL,
  `work` json NOT NULL,
  `price` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `makeup atrist`
--

INSERT INTO `makeup atrist` (`ArtistID`, `Name`, `Email`, `Password`, `PhoneNumber`, `Description`, `Services`, `InstagramAccount`, `Profile`, `work`, `price`) VALUES
(1, 'Wafa Alharbi', 'wafa@example.com', 'wafa1234', 'https://wa.me/966532892021', 'Professional makeup artist', 'Evening Makeup', 'https://instagram.com/wafa_artist11', 'wafa.jpg', '[\"images/w1.jpg\", \"images/w2.jpg\", \"images/w3.jpg\"]', 300),
(2, 'Ghzlan', 'Ghzlan@example.com', 'Ghzlan1234', 'https://wa.me/966502596924', 'Expert in natural and glam looks', 'Bridal Makeup', 'https://instagram.com/glambyghzlan', 'images/g.jpg', '[\"images/g1.jpg\", \"images/g2.jpg\", \"images/g3.jpg\"]', 450),
(3, 'Eman Makeup', 'Eman@example.com', 'Eman1234', 'https://wa.me/966599778821', 'Certified bridal makeup specialist', 'Bridal Makeup', 'https://instagram.com/eman.makeup.artist.1', 'images/e.jpg', '[\"images/e1.jpg\", \"images/e2.jpg\", \"images/e3.jpg\"]', 500);

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `ReservationID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Time` time NOT NULL,
  `Status` enum('Cancelled','Pending','Completed') NOT NULL,
  `Service` enum('Bridal Makeup','Evening Makeup') NOT NULL,
  `ClientID` int(11) NOT NULL,
  `ArtistID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`ClientID`);

--
-- Indexes for table `makeup artist profile`
--
ALTER TABLE `makeup artist profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `makeup atrist`
--
ALTER TABLE `makeup atrist`
  ADD PRIMARY KEY (`ArtistID`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`ReservationID`),
  ADD KEY `ClientID` (`ClientID`),
  ADD KEY `ArtistID` (`ArtistID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`ArtistID`) REFERENCES `makeup atrist` (`ArtistID`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`ClientID`) REFERENCES `client` (`ClientID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
