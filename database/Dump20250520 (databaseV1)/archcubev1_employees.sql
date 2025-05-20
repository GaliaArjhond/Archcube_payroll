-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: archcubev1
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `employeeId` int NOT NULL AUTO_INCREMENT,
  `profileImage` varchar(255) DEFAULT 'default.png',
  `rfidCode` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phoneNumber` varchar(20) DEFAULT NULL,
  `address` text NOT NULL,
  `birthDate` date NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `genderId` int NOT NULL,
  `hiredDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `basicSalary` decimal(10,2) DEFAULT NULL,
  `civilStatusId` int NOT NULL,
  `positionId` int NOT NULL,
  `empStatusId` int NOT NULL,
  `payrollTypeId` int NOT NULL,
  `createAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`employeeId`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `rfidCode` (`rfidCode`),
  UNIQUE KEY `phoneNumber` (`phoneNumber`),
  KEY `genderId` (`genderId`),
  KEY `civilStatusId` (`civilStatusId`),
  KEY `positionId` (`positionId`),
  KEY `empStatusId` (`empStatusId`),
  KEY `payrollTypeId` (`payrollTypeId`),
  CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`genderId`) REFERENCES `gendertypes` (`genderId`),
  CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`civilStatusId`) REFERENCES `civilstatus` (`civilStatusId`),
  CONSTRAINT `employees_ibfk_3` FOREIGN KEY (`positionId`) REFERENCES `position` (`positionId`),
  CONSTRAINT `employees_ibfk_4` FOREIGN KEY (`empStatusId`) REFERENCES `empstatus` (`empStatusId`),
  CONSTRAINT `employees_ibfk_5` FOREIGN KEY (`payrollTypeId`) REFERENCES `payrolltype` (`PayrollTypeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-20 19:43:34
