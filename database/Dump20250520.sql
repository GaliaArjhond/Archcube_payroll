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

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance` (
  `attendanceId` int NOT NULL AUTO_INCREMENT,
  `employeeId` int NOT NULL,
  `attendanceDate` date NOT NULL,
  `checkIn` time NOT NULL,
  `checkOut` time NOT NULL,
  `status` enum('Present','Absent','Late','On Leave','Half Day') DEFAULT 'Present',
  `createAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`attendanceId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `civilstatus`
--

DROP TABLE IF EXISTS `civilstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `civilstatus` (
  `civilStatusId` int NOT NULL AUTO_INCREMENT,
  `civilStatusName` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`civilStatusId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `civilstatus`
--

LOCK TABLES `civilstatus` WRITE;
/*!40000 ALTER TABLE `civilstatus` DISABLE KEYS */;
INSERT INTO `civilstatus` VALUES (1,'Single'),(2,'Married'),(3,'Divorced'),(4,'Widowed');
/*!40000 ALTER TABLE `civilstatus` ENABLE KEYS */;
UNLOCK TABLES;

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

--
-- Table structure for table `empstatus`
--

DROP TABLE IF EXISTS `empstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empstatus` (
  `empStatusId` int NOT NULL AUTO_INCREMENT,
  `empStatusName` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`empStatusId`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empstatus`
--

LOCK TABLES `empstatus` WRITE;
/*!40000 ALTER TABLE `empstatus` DISABLE KEYS */;
INSERT INTO `empstatus` VALUES (1,'Full-Time'),(2,'Part-Time'),(3,'Contractual'),(4,'Probationary'),(5,'Intern'),(6,'Terminated');
/*!40000 ALTER TABLE `empstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gendertypes`
--

DROP TABLE IF EXISTS `gendertypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gendertypes` (
  `genderId` int NOT NULL AUTO_INCREMENT,
  `genderName` varchar(50) NOT NULL,
  PRIMARY KEY (`genderId`),
  UNIQUE KEY `genderName` (`genderName`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gendertypes`
--

LOCK TABLES `gendertypes` WRITE;
/*!40000 ALTER TABLE `gendertypes` DISABLE KEYS */;
INSERT INTO `gendertypes` VALUES (2,'Female'),(1,'Male'),(3,'Non-binary'),(5,'Others'),(4,'Prefer not to say');
/*!40000 ALTER TABLE `gendertypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `govtcontributions`
--

DROP TABLE IF EXISTS `govtcontributions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `govtcontributions` (
  `govtContributionsID` int NOT NULL AUTO_INCREMENT,
  `employeeId` int NOT NULL,
  `contributionTypeId` int NOT NULL,
  `contributionNumber` varchar(50) DEFAULT NULL,
  `contributionAmount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`govtContributionsID`),
  UNIQUE KEY `employeeId` (`employeeId`,`contributionTypeId`),
  KEY `contributionTypeId` (`contributionTypeId`),
  CONSTRAINT `govtcontributions_ibfk_1` FOREIGN KEY (`contributionTypeId`) REFERENCES `govtcontributiontypes` (`contributionTypeId`),
  CONSTRAINT `govtcontributions_chk_1` CHECK ((`contributionAmount` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `govtcontributions`
--

LOCK TABLES `govtcontributions` WRITE;
/*!40000 ALTER TABLE `govtcontributions` DISABLE KEYS */;
/*!40000 ALTER TABLE `govtcontributions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `govtcontributiontypes`
--

DROP TABLE IF EXISTS `govtcontributiontypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `govtcontributiontypes` (
  `contributionTypeId` int NOT NULL AUTO_INCREMENT,
  `contributionTypeName` varchar(100) NOT NULL,
  PRIMARY KEY (`contributionTypeId`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `govtcontributiontypes`
--

LOCK TABLES `govtcontributiontypes` WRITE;
/*!40000 ALTER TABLE `govtcontributiontypes` DISABLE KEYS */;
INSERT INTO `govtcontributiontypes` VALUES (1,'SSS'),(2,'PhilHealth'),(3,'Pag-IBIG'),(4,'TIN'),(5,'Withholding Tax');
/*!40000 ALTER TABLE `govtcontributiontypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payrolltype`
--

DROP TABLE IF EXISTS `payrolltype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payrolltype` (
  `PayrollTypeId` int NOT NULL AUTO_INCREMENT,
  `PayrollTypeName` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`PayrollTypeId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payrolltype`
--

LOCK TABLES `payrolltype` WRITE;
/*!40000 ALTER TABLE `payrolltype` DISABLE KEYS */;
INSERT INTO `payrolltype` VALUES (1,'Monthly'),(2,'Semi-Monthly'),(3,'Weekly'),(4,'Daily');
/*!40000 ALTER TABLE `payrolltype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `position`
--

DROP TABLE IF EXISTS `position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `position` (
  `positionId` int NOT NULL AUTO_INCREMENT,
  `positionName` varchar(100) DEFAULT NULL,
  `baseSalary` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`positionId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `position`
--

LOCK TABLES `position` WRITE;
/*!40000 ALTER TABLE `position` DISABLE KEYS */;
INSERT INTO `position` VALUES (1,'Architect',35000.00),(2,'Engineer',40000.00),(3,'Foreman',30000.00),(4,'Laborer',20000.00);
/*!40000 ALTER TABLE `position` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `secques`
--

DROP TABLE IF EXISTS `secques`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `secques` (
  `secQuesId` int NOT NULL AUTO_INCREMENT,
  `secQuesName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`secQuesId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `secques`
--

LOCK TABLES `secques` WRITE;
/*!40000 ALTER TABLE `secques` DISABLE KEYS */;
INSERT INTO `secques` VALUES (1,'What is your mother’s maiden name?'),(2,'What was your first pet’s name?'),(3,'What is the name of your first school?'),(4,'What is your favorite book?');
/*!40000 ALTER TABLE `secques` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `systemlogs`
--

DROP TABLE IF EXISTS `systemlogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `systemlogs` (
  `logId` int NOT NULL AUTO_INCREMENT,
  `userId` int NOT NULL,
  `actionTypeId` int NOT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`logId`),
  KEY `userId` (`userId`),
  KEY `actionTypeId` (`actionTypeId`),
  CONSTRAINT `systemlogs_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`),
  CONSTRAINT `systemlogs_ibfk_2` FOREIGN KEY (`actionTypeId`) REFERENCES `actiontypes` (`actionTypeId`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `systemlogs`
--

LOCK TABLES `systemlogs` WRITE;
/*!40000 ALTER TABLE `systemlogs` DISABLE KEYS */;
INSERT INTO `systemlogs` VALUES (1,8,16,'2025-05-20 18:40:33'),(2,10,16,'2025-05-20 18:42:25'),(3,10,16,'2025-05-20 18:42:25'),(4,8,1,NULL),(5,8,1,NULL),(6,8,1,NULL),(7,8,1,NULL),(8,8,1,NULL),(9,8,1,NULL);
/*!40000 ALTER TABLE `systemlogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `userId` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'admin',
  `secQuesId` int DEFAULT NULL,
  `secQuesAnswer` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `username` (`username`),
  KEY `secQuesId` (`secQuesId`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`secQuesId`) REFERENCES `secques` (`secQuesId`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (8,'admin1','$2y$10$NBmbNcWb/NZRLICdFu4npejeRRlX7KNUKpZoj1V.587cb.WhekHUy','admin',1,'$2y$10$OvGfJ6xdcgfB1txASEHTHeGQ.oYgmoRJ6bct9xXtvAlDcWBD255YG','2025-05-20 10:40:33'),(10,'admin3','$2y$10$N5E6W4OCy/6mg8znP00slOH.XP.O1v3fs7luhfLbyw3pn62n8JpVy','admin',1,'$2y$10$tu.elPeaW5gLpLQoC.DyQu9IHDV6b.FMJpoMG56aU84aa.239z0Iy','2025-05-20 10:42:25');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-20 10:30:19
