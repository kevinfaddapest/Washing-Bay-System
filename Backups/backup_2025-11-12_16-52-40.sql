-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: carwash_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
INSERT INTO `activity_log` VALUES (1,1,'Administrator','Login','User logged in successfully','::1','2025-11-12 14:01:45'),(2,1,'Administrator','Add Expense','Added expense: Fuel, Category: Utilities, Amount: 5000 UGX, Date: 2025-11-12','::1','2025-11-12 14:02:09'),(3,1,'Administrator','Add Service','Added service for Kanyago, Vehicle: TZK 453T, Type: Truck, Service: Full Wash, Price: 15000 UGX, Payment: paid','::1','2025-11-12 14:04:19'),(4,1,'Administrator','Add User','Added new user: Mannith with role: staff','::1','2025-11-12 14:07:05'),(5,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-12 14:12:17'),(6,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-12 14:16:34'),(7,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-12 14:16:37'),(8,1,'Administrator','Add Expense','Added expense: Salary, Category: Payment, Amount: 20000 UGX, Date: 2025-11-12','::1','2025-11-12 14:34:15'),(9,1,'Administrator','Delete Expense','Deleted expense with ID: 4','::1','2025-11-12 14:35:10'),(10,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-12 14:35:37'),(11,1,'Administrator','Delete Service','Deleted service with ID: 7. Service details: {\"id\":7,\"customer_name\":\"Kanyago\",\"contact\":\"0700998877\",\"vehicle_number\":\"TZK 453T\",\"vehicle_type\":\"Truck\",\"service_type\":\"Full Wash\",\"price\":\"15000.00\",\"payment_status\":\"paid\",\"added_by\":\"Administrator\",\"date\":\"2025-11-12 17:04:19\"}','::1','2025-11-12 14:35:52'),(12,1,'Administrator','Failed Delete Service','Failed to delete service with ID: 7. Details: No rows affected (access denied or ID not found)','::1','2025-11-12 14:35:59'),(13,1,'Administrator','Delete Expense','Deleted expense: {\"id\":3,\"expense_name\":\"Fuel\",\"category\":\"Utilities\",\"amount\":\"5000.00\",\"added_by\":\"Administrator\",\"date\":\"2025-11-12\",\"created_at\":\"2025-11-12 17:02:09\"}','::1','2025-11-12 14:38:31'),(14,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-12 14:43:23'),(15,1,'Administrator','Delete Service','Deleted service with ID: 2. Service details: {\"id\":2,\"customer_name\":\"Melchy\",\"contact\":\"0700667755\",\"vehicle_number\":\"UA 200B\",\"vehicle_type\":\"Harrier\",\"service_type\":\"Engine Wash\",\"price\":\"5000.00\",\"payment_status\":\"paid\",\"added_by\":\"Administrator\",\"date\":\"2025-11-12 14:26:23\"}','::1','2025-11-12 14:43:31'),(16,1,'Administrator','Delete Service','Deleted service with ID: 3. Service details: {\"id\":3,\"customer_name\":\"Delly\",\"contact\":\"0700774554\",\"vehicle_number\":\"UBP 354K\",\"vehicle_type\":\"Lorry\",\"service_type\":\"Full Wash\",\"price\":\"15000.00\",\"payment_status\":\"paid\",\"added_by\":\"Administrator\",\"date\":\"2025-11-12 14:28:08\"}','::1','2025-11-12 14:43:50'),(17,1,'Administrator','Delete Service','Deleted service with ID: 1. Service details: {\"id\":1,\"customer_name\":\"Hero\",\"contact\":\"070033224\",\"vehicle_number\":\"UBH 600F\",\"vehicle_type\":\"Premio\",\"service_type\":\"Exterior Wash\",\"price\":\"10000.00\",\"payment_status\":\"paid\",\"added_by\":\"Administrator\",\"date\":\"2025-11-12 14:25:03\"}','::1','2025-11-12 14:46:13'),(18,1,'Administrator','Add Service','Added service for Kakiga, Vehicle: Vitz, Type: UBH 455C, Service: Engine Wash, Price: 5000 UGX, Payment: unpaid','::1','2025-11-12 14:55:58'),(19,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-12 14:56:02'),(20,1,'Administrator','Update Service','Updated service ID 8: customer=\'Kevin\', vehicle=\'Vitz\', service=\'Engine Wash\', price=5000, payment_status=\'Paid\'','::1','2025-11-12 14:56:55'),(21,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-12 15:02:57'),(22,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-12 15:13:42'),(23,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-12 15:15:00'),(24,1,'Administrator','Add Service','Added service for Derrick, Vehicle: UDF 450V, Type: Lorry, Service: Full Wash, Price: 15000 UGX, Payment: paid','::1','2025-11-12 15:27:26'),(25,1,'Administrator','Delete Service','Deleted service with ID: 4. Service details: {\"id\":4,\"customer_name\":\"Mannith\",\"contact\":\"0700443322\",\"vehicle_number\":\"UA 857M\",\"vehicle_type\":\"Ipsum\",\"service_type\":\"Full Wash\",\"price\":\"15000.00\",\"payment_status\":\"paid\",\"added_by\":\"Administrator\",\"date\":\"2025-11-12 14:28:53\"}','::1','2025-11-12 15:27:37'),(26,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-12 15:32:23'),(27,1,'Administrator','Update Expense','Updated expense ID: 2 to expense_name=\'Machine Repair\', category=\'Repairs\', amount=10000, date=\'2025-11-12\'','::1','2025-11-12 15:33:33'),(28,1,'Administrator','Delete Expense','Deleted expense record: {\"id\":2,\"expense_name\":\"Machine Repair\",\"category\":\"Repairs\",\"amount\":\"10000.00\",\"added_by\":\"Administrator\",\"date\":\"2025-11-12\",\"created_at\":\"2025-11-12 14:35:31\"}','::1','2025-11-12 15:33:54'),(29,1,'Administrator','Add Expense','Added expense: Fuel, Category: Utilities, Amount: 20000 UGX, Date: 2025-11-12','::1','2025-11-12 15:34:26'),(30,1,'Administrator','Add Expense','Added expense: Salary, Category: Wages, Amount: 15000 UGX, Date: 2025-11-12','::1','2025-11-12 15:34:49'),(31,1,'Administrator','Delete Expense','Deleted expense: {\"id\":6,\"expense_name\":\"Salary\",\"category\":\"Wages\",\"amount\":\"15000.00\",\"added_by\":\"Administrator\",\"date\":\"2025-11-12\",\"created_at\":\"2025-11-12 18:34:49\"}','::1','2025-11-12 15:34:55'),(32,1,'Administrator','Add Expense','Added expense: Water, Category: Bill, Amount: 30000 UGX, Date: 2025-11-12','::1','2025-11-12 15:36:38'),(33,1,'Administrator','Delete Expense','Deleted expense record: {\"id\":7,\"expense_name\":\"Water\",\"category\":\"Bill\",\"amount\":\"30000.00\",\"added_by\":\"Administrator\",\"date\":\"2025-11-12\",\"created_at\":\"2025-11-12 18:36:38\"}','::1','2025-11-12 15:36:45'),(34,1,'Administrator','Delete Expense','Deleted expense: {\"id\":1,\"expense_name\":\"Water Bill\",\"category\":\"Utilities\",\"amount\":\"25000.00\",\"added_by\":\"Administrator\",\"date\":\"2025-11-12\",\"created_at\":\"2025-11-12 14:33:18\"}','::1','2025-11-12 15:39:20'),(35,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-12 15:39:31'),(36,1,'Administrator','Update Service','Updated service ID 5: customer=\'Christine\', vehicle=\'UA 867V\', service=\'Full Wash\', price=15000, payment_status=\'Unpaid\'','::1','2025-11-12 15:39:41'),(37,1,'Administrator','Delete Service','Deleted service with ID: 5. Service details: {\"id\":5,\"customer_name\":\"Christine\",\"contact\":\"0700998866\",\"vehicle_number\":\"UA 867V\",\"vehicle_type\":\"Raum\",\"service_type\":\"Full Wash\",\"price\":\"15000.00\",\"payment_status\":\"unpaid\",\"added_by\":\"Da Pest\",\"date\":\"2025-11-12 14:50:32\"}','::1','2025-11-12 15:39:52'),(38,1,'Administrator','Delete Expense','Deleted expense: {\"id\":5,\"expense_name\":\"Fuel\",\"category\":\"Utilities\",\"amount\":\"20000.00\",\"added_by\":\"Administrator\",\"date\":\"2025-11-12\",\"created_at\":\"2025-11-12 18:34:26\"}','::1','2025-11-12 15:40:42'),(39,1,'Administrator','Add Expense','Added expense: Water Bill, Category: Utilities, Amount: 23000 UGX, Date: 2025-11-12','::1','2025-11-12 15:41:11'),(40,1,'Administrator','Update Expense','Updated expense ID: 8 to expense_name=\'Water Billling\', category=\'Utilities\', amount=23000, date=\'2025-11-12\'','::1','2025-11-12 15:41:21'),(41,1,'Administrator','Delete Expense','Deleted expense: {\"id\":8,\"expense_name\":\"Water Billling\",\"category\":\"Utilities\",\"amount\":\"23000.00\",\"added_by\":\"Administrator\",\"date\":\"2025-11-12\",\"created_at\":\"2025-11-12 18:41:11\"}','::1','2025-11-12 15:46:20'),(42,1,'Administrator','Add Expense','Added expense: Water Bill, Category: Utilities, Amount: 20000 UGX, Date: 2025-11-12','::1','2025-11-12 15:46:54'),(43,1,'Administrator','Add Expense','Added expense: Salary, Category: Wages, Amount: 15000 UGX, Date: 2025-11-12','::1','2025-11-12 15:47:12'),(44,1,'Administrator','Add Expense','Added expense: Machine Repair, Category: Maintenance, Amount: 50000 UGX, Date: 2025-11-12','::1','2025-11-12 15:47:32'),(45,1,'Administrator','Delete Expense','Deleted expense: {\"id\":11,\"expense_name\":\"Machine Repair\",\"category\":\"Maintenance\",\"amount\":\"50000.00\",\"added_by\":\"Administrator\",\"date\":\"2025-11-12\",\"created_at\":\"2025-11-12 18:47:32\"}','::1','2025-11-12 15:47:36'),(46,1,'Administrator','Add Expense','Added expense: Machine Repair, Category: Maintenance, Amount: 25000 UGX, Date: 2025-11-12','::1','2025-11-12 15:51:33'),(47,1,'Administrator','Delete Expense','Deleted expense: {\"id\":12,\"expense_name\":\"Machine Repair\",\"category\":\"Maintenance\",\"amount\":\"25000.00\",\"added_by\":\"Administrator\",\"date\":\"2025-11-12\",\"created_at\":\"2025-11-12 18:51:33\"}','::1','2025-11-12 15:51:46'),(48,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-12 15:51:50'),(49,1,'Administrator','Delete Service','Deleted service: {\"id\":6,\"customer_name\":\"Monisha\",\"contact\":\"0700553442\",\"vehicle_number\":\"UAK 352Q\",\"vehicle_type\":\"Pick Up\",\"service_type\":\"Engine Wash\",\"price\":\"5000.00\",\"payment_status\":\"paid\",\"added_by\":\"Da Pest\",\"date\":\"2025-11-12 15:19:07\"}','::1','2025-11-12 15:52:02'),(50,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-12 15:52:12'),(51,1,'Administrator','Dashboard Access','Admin dashboard accessed','::1','2025-11-12 15:52:35');
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expenses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `expense_name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `added_by` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
INSERT INTO `expenses` VALUES (9,'Water Bill','Utilities',20000.00,'Administrator','2025-11-12','2025-11-12 15:46:54'),(10,'Salary','Wages',15000.00,'Administrator','2025-11-12','2025-11-12 15:47:12');
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_types`
--

DROP TABLE IF EXISTS `service_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_types`
--

LOCK TABLES `service_types` WRITE;
/*!40000 ALTER TABLE `service_types` DISABLE KEYS */;
INSERT INTO `service_types` VALUES (1,'Exterior Wash',10000.00),(2,'Interior Cleaning',10000.00),(3,'Full Wash',15000.00),(4,'Engine Wash',5000.00);
/*!40000 ALTER TABLE `service_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `vehicle_number` varchar(50) DEFAULT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `service_type` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('paid','unpaid') DEFAULT 'unpaid',
  `added_by` varchar(100) DEFAULT NULL,
  `date` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (8,'Kevin','0700055552','Vitz','UBH 455C','Engine Wash',5000.00,'paid','Administrator','2025-11-12 14:55:58'),(9,'Derrick','0700332244','UDF 450V','Lorry','Full Wash',15000.00,'paid','Administrator','2025-11-12 15:27:26');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','$2y$10$Hc.ZhRxpRE6uJcJVddvA6uaVZ8VEk9AP3dOrTpdOhMFWx2fKBit/W','admin'),(2,'Da Pest','$2y$10$zJUJVP6yoNaiz57k1/nhmu6y1ydEVX1DRv7aI0Ws3j/K/.GCWopE6','staff'),(3,'Mannith','$2y$10$oavMYTTFtC8hfw/WSPmaJe8zICWtDOojK3eOEo7EW6RTWDasLA4Vy','staff');
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

-- Dump completed on 2025-11-12 18:52:43
