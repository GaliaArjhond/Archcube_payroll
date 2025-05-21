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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actiontypes`
--

LOCK TABLES `actiontypes` WRITE;
/*!40000 ALTER TABLE `actiontypes` DISABLE KEYS */;
INSERT INTO `actiontypes` VALUES (1,'Login','User signs into the system'),(2,'Logout','User signs out of the system'),(3,'Create Employee','New employee record created'),(4,'Update Employee','Employee profile modified'),(5,'Delete Employee','Employee record deleted'),(6,'Create Payroll','Payroll period generated'),(7,'Update Payroll','Edits to salary, deductions, etc.'),(8,'Approve Payroll','Payroll is approved for release'),(9,'Generate Payslip','Payslip generated for employees'),(10,'Edit Attendance','Manual adjustment to attendance data'),(11,'Add Contribution','New government contribution added'),(12,'Update Contribution','Modified contribution values'),(13,'Delete Contribution','Removed a contribution entry'),(14,'Change Settings','System configuration updated'),(15,'Password Reset','User password was reset'),(16,'Create Admin User','New admin or HR user created'),(17,'Restore Record','Restored a deleted or archived record'),(18,'Print Report','Summary report printed'),(19,'Download Report','Logs or payroll reports downloaded'),(20,'RFID Check-In','Employee checked in using RFID'),(21,'RFID Check-Out','Employee checked out using RFID'),(22,'Schedule Updated','An existing schedule was modified.'),(23,'Schedule Created','A new schedule was added to the system.'),(24,'Schedule Delete','An existing schedule was deleted.');
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
  `timeIn` time DEFAULT NULL,
  `timeOut` time DEFAULT NULL,
  `status` enum('On Time','Late','Absent') DEFAULT 'Absent',
  `remarks` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`attendanceId`),
  UNIQUE KEY `employeeId` (`employeeId`,`attendanceDate`),
  CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employeeId`) REFERENCES `employees` (`employeeId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
INSERT INTO `attendance` VALUES (1,2,'2025-05-21','20:28:43','20:28:47','On Time',NULL),(2,1,'2025-05-21','20:29:56','20:30:00','Late',NULL),(4,4,'2025-05-21','20:45:20','20:45:20','On Time',NULL);
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
-- Table structure for table `deletedemployeeslog`
--

DROP TABLE IF EXISTS `deletedemployeeslog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deletedemployeeslog` (
  `logId` int NOT NULL AUTO_INCREMENT,
  `employeeId` int DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `deletedAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `deletedByUserId` int DEFAULT NULL,
  PRIMARY KEY (`logId`),
  KEY `deletedByUserId` (`deletedByUserId`),
  CONSTRAINT `deletedemployeeslog_ibfk_1` FOREIGN KEY (`deletedByUserId`) REFERENCES `users` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deletedemployeeslog`
--

LOCK TABLES `deletedemployeeslog` WRITE;
/*!40000 ALTER TABLE `deletedemployeeslog` DISABLE KEYS */;
INSERT INTO `deletedemployeeslog` VALUES (1,1,'employee1','james@gmail.com','2025-05-21 11:53:57',8),(2,5,'john jones','johnjones123@gmail.com','2025-05-21 12:04:10',8),(3,5,'john jones','johnjones123@gmail.com','2025-05-21 12:07:47',8),(4,5,'john jones','johnjones123@gmail.com','2025-05-21 12:08:35',8),(5,5,'john jones','johnjones123@gmail.com','2025-05-21 12:31:22',8),(6,5,'john jones','johnjones123@gmail.com','2025-05-21 12:32:40',8),(7,5,'john jones','johnjones123@gmail.com','2025-05-21 12:39:34',8),(8,5,'john jones','johnjones123@gmail.com','2025-05-21 12:40:22',8),(9,5,'john jones','johnjones123@gmail.com','2025-05-21 12:40:58',8),(10,5,'john jones','johnjones123@gmail.com','2025-05-21 12:45:28',8),(11,5,'john jones','johnjones123@gmail.com','2025-05-21 12:45:34',8),(12,5,'john jones','johnjones123@gmail.com','2025-05-21 12:47:08',8),(13,3,'james','james@gmail.com','2025-05-21 23:27:08',8);
/*!40000 ALTER TABLE `deletedemployeeslog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_schedules`
--

DROP TABLE IF EXISTS `employee_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_schedules` (
  `employeeScheduleId` int NOT NULL AUTO_INCREMENT,
  `employeeId` int NOT NULL,
  `templateId` int NOT NULL,
  `assignedAt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`employeeScheduleId`),
  KEY `employeeId` (`employeeId`),
  KEY `templateId` (`templateId`),
  CONSTRAINT `employee_schedules_ibfk_1` FOREIGN KEY (`employeeId`) REFERENCES `employees` (`employeeId`) ON DELETE CASCADE,
  CONSTRAINT `employee_schedules_ibfk_2` FOREIGN KEY (`templateId`) REFERENCES `schedule_templates` (`templateId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_schedules`
--

LOCK TABLES `employee_schedules` WRITE;
/*!40000 ALTER TABLE `employee_schedules` DISABLE KEYS */;
INSERT INTO `employee_schedules` VALUES (1,1,1,'2025-05-21 20:10:12');
/*!40000 ALTER TABLE `employee_schedules` ENABLE KEYS */;
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
  `rfidCodeId` int NOT NULL,
  `name` varchar(100) NOT NULL,
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
  UNIQUE KEY `rfidCodeId` (`rfidCodeId`),
  UNIQUE KEY `email` (`email`),
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
  CONSTRAINT `employees_ibfk_5` FOREIGN KEY (`payrollTypeId`) REFERENCES `payrolltype` (`PayrollTypeId`),
  CONSTRAINT `employees_ibfk_6` FOREIGN KEY (`rfidCodeId`) REFERENCES `rfid_cards` (`rfidCodeId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'uploads/default.png',1,'mary jane','maryjane@gmail.com','09123456789','456 bahay','2000-04-12','user',2,'2025-05-21 00:00:00',1000000.00,2,1,1,2,'2025-05-21 15:25:23','2025-05-21 15:25:23'),(2,'uploads/default.png',2,'john jones','johnjones123@gmail.com','09121231289','123 bahay','1997-06-25','user',1,'2025-05-21 00:00:00',100000.00,1,2,3,3,'2025-05-21 15:27:56','2025-05-21 15:27:56'),(4,'uploads/emp_682db02e3ffd19.19811068.jpg',4,'arjhond galia','galia@gmail.com','09198354487','457 bahay','2003-10-20','user',1,'2025-05-21 00:00:00',100000.00,1,1,1,2,'2025-05-21 18:51:26','2025-05-21 18:51:26');
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
  CONSTRAINT `govtcontributions_ibfk_1` FOREIGN KEY (`employeeId`) REFERENCES `employees` (`employeeId`) ON DELETE CASCADE,
  CONSTRAINT `govtcontributions_ibfk_2` FOREIGN KEY (`contributionTypeId`) REFERENCES `govtcontributiontypes` (`contributionTypeId`),
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
  `dueDay` int DEFAULT '15',
  `frequency` enum('Monthly','Quarterly') DEFAULT 'Monthly',
  `isActive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`contributionTypeId`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `govtcontributiontypes`
--

LOCK TABLES `govtcontributiontypes` WRITE;
/*!40000 ALTER TABLE `govtcontributiontypes` DISABLE KEYS */;
INSERT INTO `govtcontributiontypes` VALUES (1,'SSS',15,'Monthly',1),(2,'Pag-IBIG',10,'Monthly',1),(3,'PhilHealth',15,'Monthly',1),(4,'BIR Withholding Tax',10,'Monthly',1),(5,'Quarterly Income Tax',30,'Quarterly',1);
/*!40000 ALTER TABLE `govtcontributiontypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `notificationId` int NOT NULL AUTO_INCREMENT,
  `employeeId` int DEFAULT NULL,
  `message` varchar(255) NOT NULL,
  `isRead` tinyint(1) DEFAULT '0',
  `notifyDate` datetime NOT NULL,
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `visibleTo` enum('admin','employee','both') DEFAULT 'admin',
  PRIMARY KEY (`notificationId`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:58:52','2025-05-21 22:58:52','admin'),(2,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:58:58','2025-05-21 22:58:58','admin'),(3,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:58:59','2025-05-21 22:58:59','admin'),(4,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:58:59','2025-05-21 22:58:59','admin'),(5,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:58:59','2025-05-21 22:58:59','admin'),(6,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:59:00','2025-05-21 22:59:00','admin'),(7,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:59:00','2025-05-21 22:59:00','admin'),(8,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:59:00','2025-05-21 22:59:00','admin'),(9,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:59:00','2025-05-21 22:59:00','admin'),(10,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:59:04','2025-05-21 22:59:04','admin'),(11,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:59:05','2025-05-21 22:59:05','admin'),(12,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:59:06','2025-05-21 22:59:06','admin'),(13,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:59:06','2025-05-21 22:59:06','admin'),(14,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:59:06','2025-05-21 22:59:06','admin'),(15,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:59:07','2025-05-21 22:59:07','admin'),(16,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:59:07','2025-05-21 22:59:07','admin'),(17,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 22:59:11','2025-05-21 22:59:11','admin'),(18,NULL,'Reminder: Payroll is due tomorrow!',1,'2025-05-21 23:00:49','2025-05-21 23:00:49','admin');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
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
-- Table structure for table `rfid_cards`
--

DROP TABLE IF EXISTS `rfid_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rfid_cards` (
  `rfidCodeId` int NOT NULL AUTO_INCREMENT,
  `rfidCode` varchar(50) NOT NULL,
  `status` enum('available','assigned','lost','inactive') DEFAULT 'available',
  PRIMARY KEY (`rfidCodeId`),
  UNIQUE KEY `rfidCode` (`rfidCode`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rfid_cards`
--

LOCK TABLES `rfid_cards` WRITE;
/*!40000 ALTER TABLE `rfid_cards` DISABLE KEYS */;
INSERT INTO `rfid_cards` VALUES (1,'0005608566','assigned'),(2,'0005922138','assigned'),(3,'0005632512','assigned'),(4,'0005770880','assigned'),(5,'0005820525','available'),(6,'0005753669','available'),(7,'0005636616','available'),(8,'0005903860','available'),(9,'0005861616','available'),(10,'0005891557','available');
/*!40000 ALTER TABLE `rfid_cards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule_templates`
--

DROP TABLE IF EXISTS `schedule_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule_templates` (
  `templateId` int NOT NULL AUTO_INCREMENT,
  `templateName` varchar(100) NOT NULL,
  `workDays` set('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `timeIn` time NOT NULL,
  `timeOut` time NOT NULL,
  `breakStart` time DEFAULT NULL,
  `breakEnd` time DEFAULT NULL,
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`templateId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule_templates`
--

LOCK TABLES `schedule_templates` WRITE;
/*!40000 ALTER TABLE `schedule_templates` DISABLE KEYS */;
INSERT INTO `schedule_templates` VALUES (1,'Standard Weekday Shift','Monday,Tuesday,Wednesday,Thursday,Friday','09:00:00','17:00:00',NULL,NULL,'2025-05-21 20:10:06','2025-05-21 20:10:06');
/*!40000 ALTER TABLE `schedule_templates` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `systemlogs`
--

LOCK TABLES `systemlogs` WRITE;
/*!40000 ALTER TABLE `systemlogs` DISABLE KEYS */;
INSERT INTO `systemlogs` VALUES (1,8,16,'2025-05-20 18:40:33'),(2,10,16,'2025-05-20 18:42:25'),(3,10,16,'2025-05-20 18:42:25'),(4,8,1,NULL),(5,8,1,NULL),(6,8,1,NULL),(7,8,1,NULL),(8,8,1,NULL),(9,8,1,NULL),(10,8,1,'2025-05-21 10:19:33'),(11,8,2,'2025-05-21 10:24:40'),(12,8,1,'2025-05-21 10:24:43'),(13,8,2,'2025-05-21 11:53:57'),(14,8,5,'2025-05-21 12:45:28'),(15,8,5,'2025-05-21 12:45:34'),(16,8,5,'2025-05-21 12:47:08'),(17,8,5,'2025-05-21 12:47:08'),(18,8,1,'2025-05-21 12:49:10'),(19,8,3,'2025-05-21 12:51:02'),(20,8,1,'2025-05-21 13:47:57'),(21,8,3,'2025-05-21 15:27:56'),(22,8,20,'2025-05-21 15:32:17'),(23,8,21,'2025-05-21 15:32:17'),(24,8,3,'2025-05-21 15:36:32'),(25,8,20,'2025-05-21 15:37:00'),(26,8,21,'2025-05-21 15:37:00'),(27,8,2,'2025-05-21 18:48:41'),(28,8,1,'2025-05-21 18:48:44'),(29,8,3,'2025-05-21 18:51:26'),(31,8,21,'2025-05-21 18:58:29'),(32,8,20,'2025-05-21 20:28:43'),(33,8,21,'2025-05-21 20:28:47'),(34,8,20,'2025-05-21 20:29:56'),(35,8,21,'2025-05-21 20:30:00'),(36,8,20,'2025-05-21 20:44:56'),(37,8,21,'2025-05-21 20:44:56'),(38,8,20,'2025-05-21 20:45:20'),(39,8,21,'2025-05-21 20:45:20'),(40,8,1,'2025-05-21 21:05:57'),(41,8,5,'2025-05-21 23:27:08'),(42,8,5,'2025-05-21 23:27:08'),(43,8,1,'2025-05-21 23:42:34'),(44,8,2,'2025-05-21 23:43:59'),(45,8,1,'2025-05-21 23:52:37'),(46,8,2,'2025-05-21 23:52:40'),(47,8,1,'2025-05-21 23:53:20'),(48,8,2,'2025-05-21 23:53:23');
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

-- Dump completed on 2025-05-21 23:54:43
