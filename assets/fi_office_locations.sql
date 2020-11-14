-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 14, 2018 at 04:44 PM
-- Server version: 5.5.49-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ansysleave`
--

-- --------------------------------------------------------

--
-- Table structure for table `fi_office_locations`
--
DROP TABLE fi_office_locations;
CREATE TABLE IF NOT EXISTS `fi_office_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `fi_office_locations`
--

INSERT INTO `fi_office_locations` (`id`, `location`) VALUES
(1, 'Pune'),
(2, 'Bangalore'),
(6, 'Hyderabad'),
(12, 'Bangalore - Apache'),
(11, 'Noida'),
(13, 'Noida - Apache'),
(14, 'Kolkata'),
(15, 'Chennai'),
(16, 'Pune - Sales'),
(17, 'Functional Manager'),
(18, 'Systems Business Unit'),
(19, 'World Wide Sales'),
(20, 'Finance'),
(21, 'ANSYS Customer Excellence'),
(22, 'Bengaluru');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
