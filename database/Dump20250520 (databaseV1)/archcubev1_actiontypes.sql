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
-- Table structure for table `actiontypes`
--

DROP TABLE IF EXISTS `actiontypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actiontypes` (
  `actionTypeId` int NOT NULL AUTO_INCREMENT,
  `actionName` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`actionTypeId`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actiontypes`
--

LOCK TABLES `actiontypes` WRITE;
/*!40000 ALTER TABLE `actiontypes` DISABLE KEYS */;
INSERT INTO `actiontypes` VALUES (1,'Login','User signs into the system'),(2,'Logout','User signs out of the system'),(3,'Create Employee','New employee record created'),(4,'Update Employee','Employee profile modified'),(5,'Delete Employee','Employee record deleted'),(6,'Create Payroll','Payroll period generated'),(7,'Update Payroll','Edits to salary, deductions, etc.'),(8,'Approve Payroll','Payroll is approved for release'),(9,'Generate Payslip','Payslip generated for employees'),(10,'Edit Attendance','Manual adjustment to attendance data'),(11,'Add Contribution','New government contribution added'),(12,'Update Contribution','Modified contribution values'),(13,'Delete Contribution','Removed a contribution entry'),(14,'Change Settings','System configuration updated'),(15,'Password Reset','User password was reset'),(16,'Create Admin User','New admin or HR user created'),(17,'Restore Record','Restored a deleted or archived record'),(18,'Print Report','Summary report printed'),(19,'Download Report','Logs or payroll reports downloaded');
/*!40000 ALTER TABLE `actiontypes` ENABLE KEYS */;
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
