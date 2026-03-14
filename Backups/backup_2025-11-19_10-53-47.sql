mysqldump: [Warning] Using a password on the command line interface can be insecure.
-- MySQL dump 10.13  Distrib 8.3.0, for Win64 (x86_64)
--
-- Host: localhost    Database: carwash_db
-- ------------------------------------------------------
-- Server version	8.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `details` text COLLATE utf8mb4_general_ci,
  `ip_address` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
INSERT INTO `activity_log` VALUES (0,1,'Administrator','Login Success','User logged in successfully','::1','2025-11-19 09:56:10'),(0,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-19 09:56:11'),(0,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-19 09:56:34'),(0,1,'Administrator','Update Service','Updated service ID 63: customer=\'FRED\', vehicle=\'UAL 967Q\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:02:25'),(0,1,'Administrator','Update Service','Updated service ID 62: customer=\'MARVIN\', vehicle=\'UBB133N\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:02:30'),(0,1,'Administrator','Update Service','Updated service ID 61: customer=\'TIMOTHY\', vehicle=\'UBB133N\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:02:37'),(0,1,'Administrator','Update Service','Updated service ID 60: customer=\'MAVIN\', vehicle=\'UA 232BV\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:02:43'),(0,1,'Administrator','Update Service','Updated service ID 59: customer=\'MAVIN\', vehicle=\'UAL 967Q\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:02:47'),(0,1,'Administrator','Update Service','Updated service ID 58: customer=\'TIMOTHY\', vehicle=\'UBH 600P\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:02:52'),(0,1,'Administrator','Update Service','Updated service ID 56: customer=\'FRED\', vehicle=\'UBH 600P\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:03:05'),(0,1,'Administrator','Update Service','Updated service ID 57: customer=\'TIMOTHY\', vehicle=\'UBH 600P\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:03:16'),(0,1,'Administrator','Update Service','Updated service ID 55: customer=\'MAVIN\', vehicle=\'UA 789H\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:04:29'),(0,1,'Administrator','Update Service','Updated service ID 54: customer=\'MAVIN\', vehicle=\'UA 789H\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:04:35'),(0,1,'Administrator','Update Service','Updated service ID 11: customer=\'Mulabbi\', vehicle=\'UA 789H\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:05:09'),(0,1,'Administrator','Update Service','Updated service ID 12: customer=\'Kato Frank\', vehicle=\'UBL 835S\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:05:20'),(0,1,'Administrator','Update Service','Updated service ID 13: customer=\'Misairi\', vehicle=\'UBH 600P\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:05:28'),(0,1,'Administrator','Update Service','Updated service ID 15: customer=\'TIMOTHY\', vehicle=\'UBB133N\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:05:36'),(0,1,'Administrator','Update Service','Updated service ID 53: customer=\'MAVIN\', vehicle=\'UA 232BV\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:05:52'),(0,1,'Administrator','Update Service','Updated service ID 14: customer=\'Crepper\', vehicle=\'UA 232BV\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:06:05'),(0,1,'Administrator','Update Service','Updated service ID 16: customer=\'MAVIN\', vehicle=\'UAL 967Q\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:06:14'),(0,1,'Administrator','Update Service','Updated service ID 17: customer=\'TIMOTHY\', vehicle=\'UA 983CF\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:06:24'),(0,1,'Administrator','Update Service','Updated service ID 18: customer=\'Timothy\', vehicle=\'UBR 9072\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:06:35'),(0,1,'Administrator','Update Service','Updated service ID 19: customer=\'FRED\', vehicle=\'UBJ 903C\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:06:44'),(0,1,'Administrator','Update Service','Updated service ID 20: customer=\'MAVIN\', vehicle=\'UBB133N\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:06:55'),(0,1,'Administrator','Update Service','Updated service ID 21: customer=\'MAVIN\', vehicle=\'UBH 600P\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:07:04'),(0,1,'Administrator','Update Service','Updated service ID 22: customer=\'TIMOTHY\', vehicle=\'UBN 787G\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:07:14'),(0,1,'Administrator','Update Service','Updated service ID 23: customer=\'TIMOTHY\', vehicle=\'UA 509AX\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:07:23'),(0,1,'Administrator','Update Service','Updated service ID 26: customer=\'MAVIN\', vehicle=\'UAU 088Y\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:07:30'),(0,1,'Administrator','Update Service','Updated service ID 27: customer=\'MAVIN\', vehicle=\'UBE737J\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:07:43'),(0,1,'Administrator','Failed Update Service','Failed to update service ID 53. Details: No rows affected (access denied or ID not found)','::1','2025-11-19 10:07:55'),(0,1,'Administrator','Update Service','Updated service ID 28: customer=\'TIMOTHY\', vehicle=\'UBM 971U\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:08:03'),(0,1,'Administrator','Update Service','Updated service ID 29: customer=\'mavin\', vehicle=\'UA 678J\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:08:12'),(0,1,'Administrator','Update Service','Updated service ID 30: customer=\'Timothy\', vehicle=\'UBR 9072\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:08:21'),(0,1,'Administrator','Update Service','Updated service ID 31: customer=\'Timothy\', vehicle=\'UBM442H\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:08:29'),(0,1,'Administrator','Update Service','Updated service ID 32: customer=\'mavin\', vehicle=\'UBJ 903C\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:08:37'),(0,1,'Administrator','Update Service','Updated service ID 33: customer=\'Timothy\', vehicle=\'UBJ 903C\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:08:50'),(0,1,'Administrator','Update Service','Updated service ID 34: customer=\'mavin\', vehicle=\'UBR 9072\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:09:00'),(0,1,'Administrator','Update Service','Updated service ID 35: customer=\'mavin\', vehicle=\'UBJ 903C\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:09:09'),(0,1,'Administrator','Update Service','Updated service ID 36: customer=\'MAVIN\', vehicle=\'UFG55\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:09:21'),(0,1,'Administrator','Update Service','Updated service ID 37: customer=\'MAVIN\', vehicle=\'UA 232BV\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:09:40'),(0,1,'Administrator','Update Service','Updated service ID 38: customer=\'TIMOTHY\', vehicle=\'UBB133N\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:09:49'),(0,1,'Administrator','Update Service','Updated service ID 39: customer=\'TIMOTHY\', vehicle=\'UBB133N\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:09:58'),(0,1,'Administrator','Update Service','Updated service ID 43: customer=\'MAVIN\', vehicle=\'UAL 967Q\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:10:07'),(0,1,'Administrator','Update Service','Updated service ID 44: customer=\'MAVIN\', vehicle=\'UAL 110Q\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:10:16'),(0,1,'Administrator','Update Service','Updated service ID 45: customer=\'MAVIN\', vehicle=\'UBE 658S\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:10:24'),(0,1,'Administrator','Update Service','Updated service ID 47: customer=\'TIMOTHY\', vehicle=\'UAL 967Q\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:10:32'),(0,1,'Administrator','Update Service','Updated service ID 48: customer=\'TIMOTHY\', vehicle=\'UBL 835S\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:10:42'),(0,1,'Administrator','Update Service','Updated service ID 46: customer=\'TIMOTHY\', vehicle=\'UBH 600P\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:10:51'),(0,1,'Administrator','Update Service','Updated service ID 49: customer=\'TIMOTHY\', vehicle=\'UAL 967Q\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:11:04'),(0,1,'Administrator','Update Service','Updated service ID 50: customer=\'MAVIN\', vehicle=\'UA 232BV\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:11:14'),(0,1,'Administrator','Update Service','Updated service ID 51: customer=\'MAVIN\', vehicle=\'UBH 600P\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:11:21'),(0,1,'Administrator','Update Service','Updated service ID 52: customer=\'MAVIN\', vehicle=\'UAL 967Q\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:11:29'),(0,1,'Administrator','Update Service','Updated service ID 63: customer=\'FRED\', vehicle=\'UAL 967Q\', service=\'Full Wash\', price=5000, payment_status=\'nopay\'','::1','2025-11-19 10:21:24'),(0,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-19 10:24:27'),(0,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-19 10:24:41'),(0,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-19 10:32:33'),(0,1,'Administrator','Database Backup Success','Backup saved to C:\\wamp64\\www\\Car-Wash11/backups/backup_2025-11-19_10-32-38.sql','::1','2025-11-19 10:32:39'),(0,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-19 10:34:16'),(0,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-19 10:35:23'),(0,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-19 10:36:17'),(0,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-19 10:36:20'),(0,6,'Kevin','Login Success','User logged in successfully','192.168.1.78','2025-11-19 10:36:39'),(0,6,'Kevin','Dashboard Access','Staff dashboard accessed','192.168.1.78','2025-11-19 10:36:39'),(0,6,'Kevin','Dashboard Access','Staff dashboard accessed','192.168.1.78','2025-11-19 10:36:59'),(0,6,'Kevin','Dashboard Access','Staff dashboard accessed','192.168.1.78','2025-11-19 10:37:18'),(0,6,'Kevin','Dashboard Access','Staff dashboard accessed','192.168.1.78','2025-11-19 10:37:46'),(0,1,'Administrator','Update Service','Updated service ID 63: customer=\'FRED\', vehicle=\'UAL 967Q\', service=\'Full Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-19 10:39:13'),(0,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-19 10:40:12'),(0,6,'Kevin','Add Service','Added service for TIMOTHY, Vehicle: UBB133N, Type: TX, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:41:39'),(0,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-19 10:42:09'),(0,6,'Kevin','Add Service','Added service for TIMOTHY, Vehicle: UA 789H, Type: probox, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:42:10'),(0,1,'Administrator','Database Backup Success','Backup saved to C:\\wamp64\\www\\Car-Wash/backups/backup_2025-11-19_10-42-18.sql','::1','2025-11-19 10:42:18'),(0,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-19 10:43:00'),(0,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-19 10:43:13'),(0,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-19 10:43:19'),(0,6,'Kevin','Add Service','Added service for FRED, Vehicle: UBH 600P, Type: BIKE, Service: Engine Wash, Price: 3000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:43:24'),(0,1,'Administrator','Logout','User logged out','::1','2025-11-19 10:43:47'),(0,6,'Kevin','Add Service','Added service for FRED, Vehicle: UA 232BV, Type: WISH, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:43:47'),(0,6,'Kevin','Login Success','User logged in successfully','::1','2025-11-19 10:43:56'),(0,6,'Kevin','Dashboard Access','Staff dashboard accessed','::1','2025-11-19 10:43:56'),(0,6,'Kevin','Add Service','Added service for TIMOTHY, Vehicle: UBL 835S, Type: RAV 4, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:44:18'),(0,6,'Kevin','Add Service','Added service for MAVIN, Vehicle: UBH 600P, Type: TX, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:45:12'),(0,6,'Kevin','Add Service','Added service for TIMOTHY, Vehicle: UAL 967Q, Type: NOAH, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:45:45'),(0,6,'Kevin','Add Service','Added service for TIMOTHY, Vehicle: UBL 835S, Type: SUBARU, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:46:11'),(0,6,'Unknown','Generate Service PDF','Generated service PDF with filters: Search=\'ubl\', From=\'\', To=\'\', Limit=\'10\', Total Records: 0','::1','2025-11-19 10:46:20'),(0,6,'Kevin','Add Service','Added service for MAVIN, Vehicle: UA 789H, Type: RANGE ROVER, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:46:49'),(0,6,'Kevin','Add Service','Added service for FRED, Vehicle: UAL 967Q, Type: HARRIER, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:47:14'),(0,6,'Kevin','Add Service','Added service for TIMOTHY, Vehicle: UBH 600P, Type: HARRIER, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:47:37'),(0,6,'Kevin','Add Service','Added service for FRED, Vehicle: UBL 835S, Type: LEGACY, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:48:39'),(0,6,'Kevin','Add Service','Added service for FRED, Vehicle: UA 789H, Type: PROBOX, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:49:11'),(0,6,'Kevin','Add Service','Added service for TIMOTHY, Vehicle: UBH 600P, Type: HILUX, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:49:40'),(0,6,'Kevin','Add Service','Added service for FRED, Vehicle: UBL 835S, Type: FILDER, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:50:13'),(0,6,'Administrator','Generate Service PDF','Generated service PDF with filters: Search=\'ubl\', From=\'\', To=\'\', Limit=\'10\', Total Records: 6','::1','2025-11-19 10:50:14'),(0,6,'Administrator','Generate Service PDF','Generated service PDF with filters: Search=\'ubl\', From=\'\', To=\'\', Limit=\'10\', Total Records: 6','::1','2025-11-19 10:50:33'),(0,6,'Administrator','Generate Service PDF','Generated service PDF with filters: Search=\'ubl\', From=\'\', To=\'\', Limit=\'10\', Total Records: 6','::1','2025-11-19 10:51:39'),(0,6,'Administrator','Generate Service PDF','Generated service PDF with filters: Search=\'nop\', From=\'\', To=\'\', Limit=\'10\', Total Records: 0','::1','2025-11-19 10:52:03'),(0,6,'Administrator','Generate Service PDF','Generated service PDF with filters: Search=\'pre\', From=\'\', To=\'\', Limit=\'25\', Total Records: 2','::1','2025-11-19 10:52:17'),(0,6,'Kevin','Add Service','Added service for TIMOTHY, Vehicle: UAL 967Q, Type: HARRIER, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:52:21'),(0,6,'Kevin','Dashboard Access','Admin dashboard accessed','::1','2025-11-19 10:52:49'),(0,6,'Kevin','Add Service','Added service for FRED, Vehicle: UBH 600P, Type: PROBOX, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:52:57'),(0,6,'Kevin','Add Service','Added service for FRED, Vehicle: UBL 835S, Type: WISH, Service: Full Wash, Price: 5000 UGX, Payment: paid','192.168.1.78','2025-11-19 10:53:26'),(0,6,'Kevin','Dashboard Access','Admin dashboard accessed','::1','2025-11-19 10:53:27');
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `expense_name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `added_by` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_types`
--

DROP TABLE IF EXISTS `service_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_types`
--

LOCK TABLES `service_types` WRITE;
/*!40000 ALTER TABLE `service_types` DISABLE KEYS */;
INSERT INTO `service_types` VALUES (1,'Exterior Wash',5000.00),(2,'Interior Cleaning',5000.00),(3,'Full Wash',5000.00),(4,'Engine Wash',3000.00);
/*!40000 ALTER TABLE `service_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `vehicle_number` varchar(50) DEFAULT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `service_type` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('nopay','paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'nopay',
  `added_by` varchar(100) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (11,'Mulabbi','0700667769','UA 789H','ALIX','Full Wash',5000.00,'paid','Kevin','2025-11-06 17:01:40'),(12,'Kato Frank','0772668813','UBL 835S','Harrier','Full Wash',5000.00,'paid','Kevin','2025-11-06 17:02:42'),(13,'Misairi','0700667769','UBH 600P','Land Cruiser','Full Wash',5000.00,'paid','Kevin','2025-11-06 17:03:36'),(14,'Crepper','0753332233','UA 232BV','HILUX','Full Wash',5000.00,'paid','Kevin','2025-11-07 09:40:48'),(15,'TIMOTHY','0765345122','UBB133N','WISH','Full Wash',5000.00,'paid','Kevin','2025-11-07 11:57:22'),(16,'MAVIN','0772668813','UAL 967Q','IPSUM','Full Wash',5000.00,'paid','Kevin','2025-11-07 14:35:04'),(17,'TIMOTHY','0753332233','UA 983CF','HARRIER','Full Wash',5000.00,'paid','Kevin','2025-11-07 14:36:27'),(18,'Timothy','0703414971','UBR 9072','HULIX','Full Wash',5000.00,'paid','Kevin','2025-11-07 17:45:15'),(19,'FRED','0703414971','UBJ 903C','HARRIER','Full Wash',5000.00,'paid','Kevin','2025-11-07 17:45:48'),(20,'MAVIN','0753332233','UBB133N','WISH','Full Wash',5000.00,'paid','Kevin','2025-11-08 08:14:34'),(21,'MAVIN','0700667769','UBH 600P','FILDER','Full Wash',5000.00,'paid','Kevin','2025-11-08 08:16:16'),(22,'TIMOTHY','0753332233','UBN 787G','NOAH','Full Wash',5000.00,'paid','Kevin','2025-11-08 08:32:59'),(23,'TIMOTHY','0765345122','UA 509AX','SUBARU','Full Wash',5000.00,'paid','Kevin','2025-11-08 09:47:48'),(26,'MAVIN','0772668813','UAU 088Y','TX','Full Wash',5000.00,'paid','Kevin','2025-11-08 10:19:18'),(27,'MAVIN','0700667769','UBE737J','HILUX','Full Wash',5000.00,'paid','Kevin','2025-11-08 11:19:18'),(28,'TIMOTHY','0753332233','UBM 971U','FILDER','Full Wash',5000.00,'paid','Kevin','2025-11-08 13:53:16'),(29,'mavin','0703414971','UA 678J','HARRIER','Full Wash',5000.00,'paid','Kevin','2025-11-08 14:47:38'),(30,'Timothy','0703414971','UBR 9072','AELIX','Full Wash',5000.00,'paid','Kevin','2025-11-09 14:35:15'),(31,'Timothy','0703414971','UBM442H','FILDER','Full Wash',5000.00,'paid','Kevin','2025-11-09 14:35:35'),(32,'mavin','0703414971','UBJ 903C','NOAH','Full Wash',5000.00,'paid','Kevin','2025-11-09 14:36:06'),(33,'Timothy','0703414971','UBJ 903C','V8','Full Wash',5000.00,'paid','Kevin','2025-11-09 14:36:26'),(34,'mavin','0703414971','UBR 9072','LAND CRUSER','Full Wash',5000.00,'paid','Kevin','2025-11-09 14:36:55'),(35,'mavin','0703414971','UBJ 903C','HULIX','Full Wash',5000.00,'paid','Kevin','2025-11-09 14:37:09'),(36,'MAVIN','0772668813','UFG55','NISSAN','Full Wash',5000.00,'paid','Kevin','2025-11-09 14:37:29'),(37,'MAVIN','0753332233','UA 232BV','RAUM','Full Wash',5000.00,'paid','Kevin','2025-11-10 17:16:03'),(38,'TIMOTHY','0700667769','UBB133N','NOAH','Full Wash',5000.00,'paid','Kevin','2025-11-10 17:16:23'),(39,'TIMOTHY','0772668813','UBB133N','HARRIER','Full Wash',5000.00,'paid','Kevin','2025-11-10 17:16:44'),(43,'MAVIN','0700667769','UAL 967Q','IST','Full Wash',5000.00,'paid','Kevin','2025-11-11 09:02:18'),(44,'MAVIN','0753332233','UAL 110Q','VITZ','Full Wash',5000.00,'paid','Kevin','2025-11-11 09:04:31'),(45,'MAVIN','0772668813','UBE 658S','PREIMO','Full Wash',5000.00,'paid','Kevin','2025-11-11 09:05:02'),(46,'TIMOTHY','0700667769','UBH 600P','EXTREL','Full Wash',5000.00,'paid','Kevin','2025-11-11 17:17:43'),(47,'TIMOTHY','0753332233','UAL 967Q','NISSAN','Full Wash',5000.00,'paid','Kevin','2025-11-11 17:18:04'),(48,'TIMOTHY','0753332233','UBL 835S','PREIMO','Full Wash',5000.00,'paid','Kevin','2025-11-12 17:36:09'),(49,'TIMOTHY','0753332233','UAL 967Q','NISSAN','Full Wash',5000.00,'paid','Kevin','2025-11-12 17:36:53'),(50,'MAVIN','0700667769','UA 232BV','RAUM','Full Wash',5000.00,'paid','Kevin','2025-11-12 17:37:21'),(51,'MAVIN','0753332233','UBH 600P','Land Cruiser','Full Wash',5000.00,'paid','Kevin','2025-11-12 17:37:46'),(52,'MAVIN','0753332233','UAL 967Q','VANGAN','Full Wash',5000.00,'paid','Kevin','2025-11-12 17:38:22'),(53,'MAVIN','0772668813','UA 232BV','SUBARU','Full Wash',5000.00,'paid','Kevin','2025-11-12 17:38:59'),(54,'MAVIN','0753332233','UA 789H','STARET','Full Wash',5000.00,'paid','Kevin','2025-11-14 06:10:32'),(55,'MAVIN','0700667769','UA 789H','ISUZU','Full Wash',5000.00,'paid','Kevin','2025-11-14 06:11:00'),(56,'FRED','0772668813','UBH 600P','BENZ','Full Wash',5000.00,'paid','Kevin','2025-11-14 06:11:36'),(57,'TIMOTHY','0700667769','UBH 600P','KULGER','Full Wash',5000.00,'paid','Kevin','2025-11-14 06:12:06'),(58,'TIMOTHY','0753332233','UBH 600P','HARRIER','Full Wash',5000.00,'paid','Kevin','2025-11-14 06:12:40'),(59,'MAVIN','0700667769','UAL 967Q','GOLF','Full Wash',5000.00,'paid','Kevin','2025-11-14 06:13:05'),(60,'MAVIN','0772668813','UA 232BV','SENITA','Full Wash',5000.00,'paid','Kevin','2025-11-14 06:13:36'),(61,'TIMOTHY','0765345122','UBB133N','SENITA','Full Wash',5000.00,'paid','Kevin','2025-11-14 06:14:10'),(62,'MARVIN','0753332233','UBB133N','HARRIER','Full Wash',5000.00,'paid','Kevin','2025-11-14 06:14:50'),(63,'FRED','0700667769','UAL 967Q','TX','Full Wash',5000.00,'paid','Kevin','2025-11-14 06:15:30'),(64,'TIMOTHY','0700667769','UBB133N','TX','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:41:39'),(65,'TIMOTHY','0700667769','UA 789H','probox','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:42:10'),(66,'FRED','0753332233','UBH 600P','BIKE','Engine Wash',3000.00,'paid','Kevin','2025-11-19 10:43:24'),(67,'FRED','0772668813','UA 232BV','WISH','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:43:47'),(68,'TIMOTHY','0753332233','UBL 835S','RAV 4','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:44:18'),(69,'MAVIN','0772668813','UBH 600P','TX','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:45:12'),(70,'TIMOTHY','0753332233','UAL 967Q','NOAH','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:45:45'),(71,'TIMOTHY','0700667769','UBL 835S','SUBARU','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:46:11'),(72,'MAVIN','0753332233','UA 789H','RANGE ROVER','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:46:48'),(73,'FRED','0765345122','UAL 967Q','HARRIER','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:47:14'),(74,'TIMOTHY','0753332233','UBH 600P','HARRIER','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:47:36'),(75,'FRED','0700667769','UBL 835S','LEGACY','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:48:39'),(76,'FRED','0772668813','UA 789H','PROBOX','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:49:11'),(77,'TIMOTHY','0700667769','UBH 600P','HILUX','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:49:40'),(78,'FRED','0765345122','UBL 835S','FILDER','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:50:13'),(79,'TIMOTHY','0753332233','UAL 967Q','HARRIER','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:52:21'),(80,'FRED','0765345122','UBH 600P','PROBOX','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:52:57'),(81,'FRED','0772668813','UBL 835S','WISH','Full Wash',5000.00,'paid','Kevin','2025-11-19 10:53:26');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','$2y$10$h/Ui2etNwHSrlP8fFY/KcufJ7E.ciBvIqYRhSZMAqHb4zV6tOubbO','admin'),(6,'Kevin','$2y$10$O391mb7TEHrGffYek1LNEumgedusZto1d3WC6m5Un1ipoHbhkGnnm','staff'),(7,'Frank','$2y$10$nMqP8OTrhpVMl4Ja84OdzO3IGV5H6bhsPjFxKGT2sQobanvWqFBFG','admin'),(8,'DaPEST','$2y$10$yf3E0O1BNSczxfxWylfd9u4arivO5pmfAEpFxYFHs5KxOw77nWxii','admin');
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

-- Dump completed on 2025-11-19 13:53:47
