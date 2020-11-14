-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 14, 2018 at 04:45 PM
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
-- Table structure for table `fi_ou`
--
DROP TABLE fi_ou;
CREATE TABLE IF NOT EXISTS `fi_ou` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ou_short_name` varchar(50) DEFAULT NULL,
  `ou_long_string` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;

--
-- Dumping data for table `fi_ou`
--

INSERT INTO `fi_ou` (`id`, `ou_short_name`, `ou_long_string`) VALUES
(17, 'Bangalore - Apache', 'OU=Standard,OU=Users,OU=Bangalore_Apache,OU=RG - India,DC=win,DC=ansys,DC=com'),
(16, 'Bangalore', 'OU=Standard,OU=Users,OU=Bangalore,OU=RG - India,DC=win,DC=ansys,DC=com'),
(15, 'Pune - Sales', 'OU=Standard,OU=Users,OU=Pune-Sales,OU=RG - India,DC=win,DC=ansys,DC=com'),
(11, 'Pune', 'OU=Standard,OU=Users,OU=Pune,OU=RG - India,DC=win,DC=ansys,DC=com'),
(10, 'Gothenburg', 'OU=Standard,OU=Users,OU=Gothenburg,OU=RG - Europe,DC=win,DC=ansys,DC=com'),
(9, 'Villeurbanne', 'OU=Standard,OU=Users,OU=Villeurbanne,OU=RG - Europe,DC=win,DC=ansys,DC=com'),
(8, 'HongKong', 'OU=Standard,OU=Users,OU=HongKong,OU=RG - Asia,DC=win,DC=ansys,DC=com'),
(7, 'Evanston', 'OU=Standard,OU=Users,OU=Evanston,OU=RG - North America,DC=win,DC=ansys,DC=com'),
(6, 'Canonsburg', 'OU=Standard,OU=Users,OU=Lebanon,OU=RG - North America,DC=win,DC=ansys,DC=com'),
(5, 'Lebanon', 'OU=Standard,OU=Users,OU=Lebanon,OU=RG - North America,DC=win,DC=ansys,DC=com'),
(4, 'Telecommuters', 'OU=Standard,OU=Users,OU=Telecommuters,OU=RG - North America,DC=win,DC=ansys,DC=com'),
(3, 'Milton', 'OU=Standard,OU=Users,OU=Milton,OU=RG - Europe,DC=win,DC=ansys,DC=com'),
(2, 'Waterloo', 'OU=Standard,OU=Users,OU=Waterloo,OU=RG - North America,DC=win,DC=ansys,DC=com'),
(1, 'Canonsburg-IT', 'OU=IT,OU=Users,OU=Canonsburg,OU=RG - North America,DC=win,DC=ansys,DC=com'),
(18, 'Telecommuters', 'OU=Standard,OU=Users,OU=Telecommuters,OU=RG - India,DC=win,DC=ansys,DC=com'),
(19, 'Noida Apache', 'OU=Standard,OU=Users,OU=Noida_Apache,OU=RG - India,DC=win,DC=ansys,DC=com'),
(20, 'Hyderabad', 'OU=Standard,OU=Users,OU=Hyderabad,OU=RG - India,DC=win,DC=ansys,DC=com'),
(21, 'Noida', 'OU=Standard,OU=Users,OU=Noida,OU=RG - India,DC=win,DC=ansys,DC=com'),
(22, 'Functional Manager', 'OU=Standard,OU=Users,OU=Functional Manager,OU=RG - India,DC=win,DC=ansys,DC=com'),
(23, 'Systems Business Unit', 'OU=Standard,OU=Users,OU=Systems Business Unit,OU=RG - India,DC=win,DC=ansys,DC=com'),
(24, 'World Wide Sales', 'OU=Standard,OU=Users,OU=World Wide Sales,OU=RG - India,DC=win,DC=ansys,DC=com'),
(25, 'ANSYS Customer Excellence', 'OU=Standard,OU=Users,OU=ANSYS Customer Excellence,OU=RG - India,DC=win,DC=ansys,DC=com'),
(26, 'Finance', 'OU=Standard,OU=Users,OU=Finance,OU=RG - India,DC=win,DC=ansys,DC=com'),
(27, 'Fluids Business Unit', 'OU=Standard,OU=Users,OU=Fluids Business Unit,OU=RG - India,DC=win,DC=ansys,DC=com'),
(28, 'Platform Development Unit', 'OU=Standard,OU=Users,OU=Platform Development Unit,OU=RG - India,DC=win,DC=ansys,DC=com'),
(29, 'Meshing Development Unit', 'OU=Standard,OU=Users,OU=Meshing Development Unit,OU=RG - India,DC=win,DC=ansys,DC=com'),
(30, 'Design Business Unit', 'OU=Standard,OU=Users,OU=Design Business Unit,OU=RG - India,DC=win,DC=ansys,DC=com'),
(31, 'Global Marketing', 'OU=Standard,OU=Users,OU=Global Marketing,OU=RG - India,DC=win,DC=ansys,DC=com'),
(32, 'Legal', 'OU=Standard,OU=Users,OU=Legal,OU=RG - India,DC=win,DC=ansys,DC=com'),
(33, 'Human Resources', 'OU=Standard,OU=Users,OU=Human Resources,OU=RG - India,DC=win,DC=ansys,DC=com'),
(34, 'Release Management', 'OU=Standard,OU=Users,OU=Release Management,OU=RG - India,DC=win,DC=ansys,DC=com'),
(35, 'Mechanical Business Unit', 'OU=Standard,OU=Users,OU=Mechanical Business Unit,OU=RG - India,DC=win,DC=ansys,DC=com'),
(36, 'Semiconductor BU', 'OU=Standard,OU=Users,OU=Semiconductor BU,OU=RG - India,DC=win,DC=ansys,DC=com');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
