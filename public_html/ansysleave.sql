-- MySQL dump 10.13  Distrib 5.5.34, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: ansysleave
-- ------------------------------------------------------
-- Server version	5.5.34-0ubuntu0.13.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `fi_compoff`
--

DROP TABLE IF EXISTS `fi_compoff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fi_compoff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `work_date` date DEFAULT NULL,
  `compoff_date` date DEFAULT NULL,
  `applied` date DEFAULT NULL,
  `comments` text,
  `status` enum('Approved','Pending','Cancelled') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `emp_id` (`emp_id`,`work_date`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fi_compoff`
--

LOCK TABLES `fi_compoff` WRITE;
/*!40000 ALTER TABLE `fi_compoff` DISABLE KEYS */;
INSERT INTO `fi_compoff` VALUES (8,1,0,'2014-04-19','0000-00-00','2014-04-18',NULL,'Pending'),(9,5,0,'2014-04-06','0000-00-00','2014-04-18',NULL,'Pending');
/*!40000 ALTER TABLE `fi_compoff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fi_dept`
--

DROP TABLE IF EXISTS `fi_dept`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fi_dept` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deptname` varchar(200) NOT NULL DEFAULT '',
  `dept_mgr` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fi_dept`
--

LOCK TABLES `fi_dept` WRITE;
/*!40000 ALTER TABLE `fi_dept` DISABLE KEYS */;
INSERT INTO `fi_dept` VALUES (1,'ACCTS','Preet Inderjeet Walia'),(2,'QAD','A.R. Srikrishnan'),(3,'ADMN','Ashutosh Kotkar'),(4,'INTS&S','Harshad Karve'),(5,'DEV','Harsh Vardhan'),(6,'INDS&S','Amit Agarwal'),(7,'HR',''),(8,'IT',''),(9,'MDO','Murali Kadiramangalam'),(10,'IT-Web','Wendy M. McDonald'),(15,'HR','XHR'),(21,'Testing','');
/*!40000 ALTER TABLE `fi_dept` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fi_emp_list`
--

DROP TABLE IF EXISTS `fi_emp_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fi_emp_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empno` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `dept` int(11) DEFAULT NULL,
  `manager` int(11) DEFAULT NULL,
  `cname` varchar(100) DEFAULT NULL,
  `location` int(11) DEFAULT NULL,
  `ou` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `password` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empno` (`empno`),
  UNIQUE KEY `empno_2` (`empno`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fi_emp_list`
--

LOCK TABLES `fi_emp_list` WRITE;
/*!40000 ALTER TABLE `fi_emp_list` DISABLE KEYS */;
INSERT INTO `fi_emp_list` VALUES (1,1,'admin',7,1,'Megha Vaze',1,1,1,'548caebe47a13915d3373a31b3250ead'),(4,4,'admin1',1,1,'Admin1',1,1,1,'548caebe47a13915d3373a31b3250ead'),(5,2,'skk',2,1,'S. Kulkarni',3,3,1,'1b73ee474e2be61e323d78b46a2fa043'),(6,3,'ppp',3,1,'PPP',6,2,1,'5a9ec3578af700da86d1bed843ecfdba'),(7,101,'Employee1',1,1,'Employee1',6,3,1,NULL),(9,102,'Employee2',2,1,'Employee2',8,3,1,NULL),(11,103,'EMployee3',4,1,'Employee3',2,3,1,NULL),(13,104,'Employee4',7,1,'Employee4',6,3,1,NULL),(14,105,'Employee5',3,1,'Employee5',2,1,1,'ded2354cedeb603f0636677cb1bec750'),(23,106,'Shraddha',5,0,'Shraddha Kulkarni',1,3,1,NULL);
/*!40000 ALTER TABLE `fi_emp_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fi_employee_leave_buckets`
--

DROP TABLE IF EXISTS `fi_employee_leave_buckets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fi_employee_leave_buckets` (
  `employee_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `leave_type_id` int(11) DEFAULT NULL,
  `maximum` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_id` (`employee_id`,`year`,`leave_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fi_employee_leave_buckets`
--

LOCK TABLES `fi_employee_leave_buckets` WRITE;
/*!40000 ALTER TABLE `fi_employee_leave_buckets` DISABLE KEYS */;
INSERT INTO `fi_employee_leave_buckets` VALUES (1,2013,5,10,1),(1,2013,6,6,2),(1,2013,7,6,3),(23,2014,5,8,14),(23,2014,6,15,15),(23,2014,7,10,16);
/*!40000 ALTER TABLE `fi_employee_leave_buckets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fi_holidays`
--

DROP TABLE IF EXISTS `fi_holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fi_holidays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eventname` varchar(200) NOT NULL DEFAULT '',
  `eventdate` date NOT NULL DEFAULT '0000-00-00',
  `eventcomments` text NOT NULL,
  `location` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fi_holidays`
--

LOCK TABLES `fi_holidays` WRITE;
/*!40000 ALTER TABLE `fi_holidays` DISABLE KEYS */;
INSERT INTO `fi_holidays` VALUES (23,'Aambedkar Jayanti','2014-04-14','Holiday',1),(24,'Good Friday','2014-04-18','Holiday',2),(27,'Ansys Anniversary','2014-04-21','',0);
/*!40000 ALTER TABLE `fi_holidays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fi_leave`
--

DROP TABLE IF EXISTS `fi_leave`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fi_leave` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) DEFAULT NULL,
  `applied` datetime DEFAULT NULL,
  `manager` int(11) DEFAULT NULL,
  `leave_type` int(11) DEFAULT NULL,
  `from_dt` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `reason` text,
  `manager_comment` text,
  `status` enum('Approved','Pending','Cancelled') DEFAULT NULL,
  `leave_days` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fi_leave`
--

LOCK TABLES `fi_leave` WRITE;
/*!40000 ALTER TABLE `fi_leave` DISABLE KEYS */;
INSERT INTO `fi_leave` VALUES (2,1,'2014-04-07 15:34:43',1,5,'2014-04-09','2014-04-11','Going to trip','','Approved',3),(3,1,'2014-04-08 15:16:52',1,5,'2014-04-16','2014-04-18','test','','Approved',3),(4,1,'2014-04-08 15:48:07',1,6,'2014-04-24','2014-04-25','rere','','Approved',2),(8,1,'2014-04-08 16:50:18',1,5,'2014-04-16','2014-04-18','ghgh','','Cancelled',3),(13,1,'2014-04-08 16:57:01',1,5,'2014-04-16','2014-04-18','ghgh','','Approved',3),(12,1,'2014-04-08 16:56:25',1,5,'2014-04-16','2014-04-18','ghgh','','Approved',3),(14,1,'2014-04-08 16:58:38',1,5,'2014-04-16','2014-04-18','ghgh','','Approved',3),(22,1,'2014-04-12 15:40:55',1,5,'2014-04-12','2014-04-15','sasa','','Approved',2),(23,1,'2014-04-12 15:44:45',1,6,'2014-04-12','2014-04-15','','','Approved',2),(24,7,'2014-04-16 09:33:33',1,7,'2014-04-24','2014-04-25','trt','','Pending',2),(25,5,'2014-04-16 09:53:43',1,6,'2014-04-17','2014-04-18','','','Approved',2),(26,5,'2014-04-16 11:14:26',1,6,'2014-04-16','2014-04-17','','','Approved',2),(27,5,'2014-04-16 11:17:51',1,7,'2014-04-23','2014-04-24','','','Approved',2),(28,1,'2014-04-17 16:17:54',1,6,'2014-04-24','2014-04-26','','','Approved',2),(29,1,'2014-04-17 16:20:12',1,6,'2014-04-22','2014-04-23','','','Approved',2),(30,1,'2014-04-17 16:21:07',1,6,'2014-04-29','2014-04-30','','','Approved',2),(31,1,'2014-04-17 16:23:32',1,5,'2014-05-15','2014-05-20','','','Approved',4),(32,1,'2014-04-17 16:24:22',1,7,'2014-04-15','2014-04-30','','','Approved',12),(33,1,'2014-04-17 16:25:03',1,7,'2014-05-13','2014-05-21','','','Approved',7),(43,14,'2014-04-17 16:58:14',1,5,'2014-04-17','2014-04-22','','','Pending',2),(55,5,'2014-04-17 23:51:22',1,6,'2014-04-14','2014-04-15','','','Cancelled',2),(54,14,'2014-04-17 23:08:07',1,5,'2014-04-17','2014-04-22','','','Cancelled',3),(53,14,'2014-04-17 17:12:53',1,5,'2014-04-17','2014-04-22','','','Cancelled',3),(56,5,'2014-04-18 10:41:26',1,5,'2014-04-21','2014-04-22','','','Cancelled',2),(57,5,'2014-04-18 10:41:39',1,5,'2014-04-17','2014-04-22','','','Cancelled',4),(58,5,'2014-04-18 10:44:09',1,6,'2014-05-07','2014-05-08','','','Cancelled',2),(59,1,'2014-04-20 00:41:12',1,8,'2014-05-24','2014-05-24','Maternity','','Cancelled',0),(60,1,'2014-04-20 00:42:15',1,8,'2014-05-23','2014-06-27','','','Pending',26);
/*!40000 ALTER TABLE `fi_leave` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fi_leave_buckets`
--

DROP TABLE IF EXISTS `fi_leave_buckets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fi_leave_buckets` (
  `leave_type_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `maximum` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fi_leave_buckets`
--

LOCK TABLES `fi_leave_buckets` WRITE;
/*!40000 ALTER TABLE `fi_leave_buckets` DISABLE KEYS */;
INSERT INTO `fi_leave_buckets` VALUES (12,2014,15),(6,2014,10),(7,2014,15);
/*!40000 ALTER TABLE `fi_leave_buckets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fi_leave_carry_forwards`
--

DROP TABLE IF EXISTS `fi_leave_carry_forwards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fi_leave_carry_forwards` (
  `emp_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `leave_type` int(11) DEFAULT NULL,
  `no_of_leaves` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fi_leave_carry_forwards`
--

LOCK TABLES `fi_leave_carry_forwards` WRITE;
/*!40000 ALTER TABLE `fi_leave_carry_forwards` DISABLE KEYS */;
INSERT INTO `fi_leave_carry_forwards` VALUES (1,2014,5,6),(4,2014,5,2),(1,2014,7,4),(1,2014,7,4);
/*!40000 ALTER TABLE `fi_leave_carry_forwards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fi_leave_types`
--

DROP TABLE IF EXISTS `fi_leave_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fi_leave_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typename` varchar(200) NOT NULL DEFAULT '',
  `comments` text NOT NULL,
  `status` varchar(20) DEFAULT 'Y',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fi_leave_types`
--

LOCK TABLES `fi_leave_types` WRITE;
/*!40000 ALTER TABLE `fi_leave_types` DISABLE KEYS */;
INSERT INTO `fi_leave_types` VALUES (6,'Casual','Allowed up to 10 Days','Y'),(7,'Sick','Allowed up to 15 Days','Y'),(12,'Earned','','Y'),(8,'Maternity','Maternity leave','Y');
/*!40000 ALTER TABLE `fi_leave_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fi_office_locations`
--

DROP TABLE IF EXISTS `fi_office_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fi_office_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fi_office_locations`
--

LOCK TABLES `fi_office_locations` WRITE;
/*!40000 ALTER TABLE `fi_office_locations` DISABLE KEYS */;
INSERT INTO `fi_office_locations` VALUES (1,'Pune'),(2,'Bangalore'),(6,'Hyderabad'),(7,'Jaipur'),(11,'Delhi');
/*!40000 ALTER TABLE `fi_office_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fi_ou`
--

DROP TABLE IF EXISTS `fi_ou`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fi_ou` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ou_short_name` varchar(50) DEFAULT NULL,
  `ou_long_string` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fi_ou`
--

LOCK TABLES `fi_ou` WRITE;
/*!40000 ALTER TABLE `fi_ou` DISABLE KEYS */;
INSERT INTO `fi_ou` VALUES (1,'Canonsburg-IT','OU=IT,OU=Users,OU=Canonsburg,OU=RG - North America,DC=win,DC=ansys,DC=com'),(2,'Waterloo','OU=Standard,OU=Users,OU=Waterloo,OU=RG - North America,DC=win,DC=ansys,DC=com'),(3,'Milton','OU=Standard,OU=Users,OU=Milton,OU=RG - Europe,DC=win,DC=ansys,DC=com'),(4,'Telecommuters','OU=Standard,OU=Users,OU=Telecommuters,OU=RG - North America,DC=win,DC=ansys,DC=com'),(5,'Lebanon','OU=Standard,OU=Users,OU=Lebanon,OU=RG - North America,DC=win,DC=ansys,DC=com'),(6,'Canonsburg','OU=Standard,OU=Users,OU=Lebanon,OU=RG - North America,DC=win,DC=ansys,DC=com'),(7,'Evanston','OU=Standard,OU=Users,OU=Evanston,OU=RG - North America,DC=win,DC=ansys,DC=com'),(8,'HongKong','OU=Standard,OU=Users,OU=HongKong,OU=RG - Asia,DC=win,DC=ansys,DC=com'),(9,'Villeurbanne','OU=Standard,OU=Users,OU=Villeurbanne,OU=RG - Europe,DC=win,DC=ansys,DC=com'),(10,'Gothenburg','OU=Standard,OU=Users,OU=Gothenburg,OU=RG - Europe,DC=win,DC=ansys,DC=com'),(11,'Pune','OU=Standard,OU=Users,OU=Pune,OU=RG - India,DC=win,DC=ansys,DC=com');
/*!40000 ALTER TABLE `fi_ou` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paintings`
--

DROP TABLE IF EXISTS `paintings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paintings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `keyword` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paintings`
--

LOCK TABLES `paintings` WRITE;
/*!40000 ALTER TABLE `paintings` DISABLE KEYS */;
INSERT INTO `paintings` VALUES (1,'skk','hunter');
/*!40000 ALTER TABLE `paintings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-04-20 14:25:50
