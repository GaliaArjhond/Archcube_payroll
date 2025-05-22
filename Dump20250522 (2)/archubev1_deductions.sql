-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: archubev1
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
-- Table structure for table `deductions`
--

DROP TABLE IF EXISTS `deductions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deductions` (
  `deductionId` int NOT NULL AUTO_INCREMENT,
  `employeeId` int NOT NULL,
  `payrollPeriodId` int NOT NULL,
  `deductionTypeId` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `notes` text,
  `advanceId` int DEFAULT NULL,
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`deductionId`),
  KEY `employeeId` (`employeeId`),
  KEY `payrollPeriodId` (`payrollPeriodId`),
  KEY `deductionTypeId` (`deductionTypeId`),
  KEY `advanceId` (`advanceId`),
  CONSTRAINT `deductions_ibfk_1` FOREIGN KEY (`employeeId`) REFERENCES `employees` (`employeeId`),
  CONSTRAINT `deductions_ibfk_2` FOREIGN KEY (`payrollPeriodId`) REFERENCES `payrollperiod` (`payrollPeriodId`),
  CONSTRAINT `deductions_ibfk_3` FOREIGN KEY (`deductionTypeId`) REFERENCES `deductiontypes` (`deductionTypeId`),
  CONSTRAINT `deductions_ibfk_4` FOREIGN KEY (`advanceId`) REFERENCES `advancepayments` (`advanceId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deductions`
--

LOCK TABLES `deductions` WRITE;
/*!40000 ALTER TABLE `deductions` DISABLE KEYS */;
/*!40000 ALTER TABLE `deductions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-22 12:14:59
