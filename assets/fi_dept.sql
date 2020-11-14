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
-- Table structure for table `fi_dept`
--
DROP TABLE fi_dept;
CREATE TABLE IF NOT EXISTS `fi_dept` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deptname` varchar(200) NOT NULL DEFAULT '',
  `dept_mgr` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `deptname` (`deptname`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=61 ;

--
-- Dumping data for table `fi_dept`
--

INSERT INTO `fi_dept` (`id`, `deptname`, `dept_mgr`) VALUES
(1, 'ACCOUNTS', ''),
(2, 'TESTING', ''),
(3, 'Admin', ''),
(4, 'ACE-INT', ''),
(5, 'DEV', 'Harsh Vardhan'),
(6, 'ACE-IND', ''),
(7, 'HR', ''),
(8, 'IT', ''),
(9, 'MDO', 'Murali Kadiramangalam'),
(10, 'IT-Web', 'Wendy M. McDonald'),
(22, 'SALES', ''),
(23, 'R&D', ''),
(24, 'AE', ''),
(25, 'Sales Admin', ''),
(26, 'India-Marketing', ''),
(28, 'Functional Managers', ''),
(29, 'Functional Manager', ''),
(30, '6403 Sales Gen Admin', ''),
(31, '1302 Facilities', ''),
(32, '6402 Sales Operations', ''),
(33, '1101 Accounting', ''),
(34, '4002 Technical Support', ''),
(35, '6601 Pre-Sales Support', ''),
(36, '6401 Sales - Non-Commissioned', ''),
(37, '6103 Channel Sales', ''),
(38, '6201 Sales - Commissioned', ''),
(39, '6501 Local Marketing - Sales', ''),
(40, '6203 License Compliance', ''),
(41, '6102 Sales Management', ''),
(42, '4001 Consulting', ''),
(43, '2002 FBU', ''),
(44, '2103 Platform Development Unit', ''),
(45, '2105 Meshing', ''),
(46, '2104 Design BU', ''),
(47, '1301 Information Technologies', ''),
(48, '5001 Corporate Marketing', ''),
(49, '1501 Human Resources', ''),
(50, '2107 Release Management', ''),
(51, '2006 MBU', ''),
(52, '4101 Training', ''),
(53, '4003 ACE Operations', ''),
(54, '5103 Partners', ''),
(55, '2311 SBU Digital Twin', ''),
(56, '1401 Legal & Contracts', ''),
(57, '3202 SCBU AE', ''),
(58, '2203 Semiconductor BU', ''),
(59, '3204 SCBU AE Loc Ind Tech Sup', ''),
(60, '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
