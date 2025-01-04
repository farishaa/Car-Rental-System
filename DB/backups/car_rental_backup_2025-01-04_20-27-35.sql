-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: car_rental
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
-- Table structure for table `adminauditlogs`
--

DROP TABLE IF EXISTS `adminauditlogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adminauditlogs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `action_details` text NOT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `adminauditlogs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adminauditlogs`
--

LOCK TABLES `adminauditlogs` WRITE;
/*!40000 ALTER TABLE `adminauditlogs` DISABLE KEYS */;
INSERT INTO `adminauditlogs` VALUES (1,2,'Login','Admin logged in successfully.','2025-01-04 19:13:02'),(2,2,'Delete Car','Deleted car: Proton Saga (ID: 5)','2025-01-04 19:13:36'),(3,3,'Unsuccessful Login','Invalid password attempt for admin email: peter@gmail.com','2025-01-04 19:14:18'),(4,5,'Login','Admin logged in successfully.','2025-01-04 19:16:00'),(5,8,'Unsuccessful Login','Invalid password attempt for admin email: kai@gmail.com','2025-01-04 19:16:25'),(6,5,'Login','Admin logged in successfully.','2025-01-04 19:25:47');
/*!40000 ALTER TABLE `adminauditlogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auditlogs`
--

DROP TABLE IF EXISTS `auditlogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditlogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `auditlogs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auditlogs`
--

LOCK TABLES `auditlogs` WRITE;
/*!40000 ALTER TABLE `auditlogs` DISABLE KEYS */;
INSERT INTO `auditlogs` VALUES (1,1,'Booking Created','User ID: 1 booked car \'Perodua Ativa\' (Model: 2023) from 2025-01-07 to 2025-01-09 for RM 150.00','2025-01-04 19:05:46'),(2,4,'Booking Created','User ID: 4 booked car \'Perodua Alza\' (Model: 2024) from 2025-01-03 to 2025-01-04 for RM 70.00','2025-01-04 19:03:34'),(3,4,'Booking Created','User ID: 4 booked car \'Proton Perdana\' (Model: 2021) from 2025-01-06 to 2025-01-07 for RM 50.00','2025-01-04 19:06:38'),(4,6,'Booking Created','User ID: 6 booked car \'Honda City\' (Model: 2010) from 2025-01-09 to 2025-01-14 for RM 425.00','2025-01-04 19:09:25'),(5,7,'Booking Created','User ID: 7 booked car \'Perodua Axia\' (Model: 2020) from 2025-01-07 to 2025-01-10 for RM 180.00','2025-01-04 19:10:12');
/*!40000 ALTER TABLE `auditlogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cars`
--

DROP TABLE IF EXISTS `cars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cars` (
  `car_id` int(11) NOT NULL AUTO_INCREMENT,
  `car_name` varchar(50) NOT NULL,
  `car_model` varchar(50) NOT NULL,
  `rental_price` decimal(10,2) NOT NULL,
  `availability` enum('available','booked') DEFAULT 'available',
  PRIMARY KEY (`car_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cars`
--

LOCK TABLES `cars` WRITE;
/*!40000 ALTER TABLE `cars` DISABLE KEYS */;
INSERT INTO `cars` VALUES (1,'Perodua Myvi','2021',50.00,'booked'),(2,'Perodua Axia','2020',60.00,'booked'),(3,'Toyota Yaris','2019',45.00,'available'),(4,'Perodua Alza','2024',70.00,'booked'),(6,'Proton Perdana','2021',50.00,'booked'),(7,'Honda City','2010',85.00,'booked'),(8,'Honda CRV','2024',100.00,'available'),(9,'Perodua Ativa','2023',75.00,'booked'),(10,'Proton X-70','2024',75.00,'available');
/*!40000 ALTER TABLE `cars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rental`
--

DROP TABLE IF EXISTS `rental`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rental` (
  `rent_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `duration` int(11) NOT NULL,
  PRIMARY KEY (`rent_id`),
  KEY `user_id` (`user_id`),
  KEY `car_id` (`car_id`),
  CONSTRAINT `rental_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `rental_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `cars` (`car_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rental`
--

LOCK TABLES `rental` WRITE;
/*!40000 ALTER TABLE `rental` DISABLE KEYS */;
INSERT INTO `rental` VALUES (1,4,4,'2025-01-03','2025-01-04',70.00,1),(2,1,9,'2025-01-07','2025-01-09',150.00,2),(3,4,6,'2025-01-06','2025-01-07',50.00,1),(4,6,7,'2025-01-09','2025-01-14',425.00,5),(5,7,2,'2025-01-07','2025-01-10',180.00,3);
/*!40000 ALTER TABLE `rental` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `psswd` varchar(255) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `role` enum('customer','admin','dba') NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'John','$2y$10$k91V75kd4D4x5az5ZqBezuh0hMmVObS8p6T7Xc.GNr1BuRfDO1b0O','john@gmail.com','customer'),(2,'Bob','$2y$10$477Nn6NzM/yQ0rhQ7L.Al.t5ebfrIkURzjzkTYO001p1KFojt/Kuy','bob@gmail.com','admin'),(3,'Peter','$2y$10$.pQIYSoEr65idzLtFXnNiOBwVszduo/ARAmYc/IbbJxd.ft/kovtW','peter@gmail.com','admin'),(4,'Charlie','$2y$10$nVaJZPgY0Y1VaQyuQEK1muyn81HWe9ws5xAJyAbEEUMyWbiZKZslK','charlie@gmail.com','customer'),(5,'Dani','$2y$10$AQKYmQMc8/VL9WlVLFA5k.C3KAIEis.aNVTJWUvV12FRmOtEBllDy','dani@gmail.com','admin'),(6,'Emily','$2y$10$YXyn/dwJRrm48ZsbRljNxO9ilovHH2vvDCFa1Ed7w5GmmBfOMtBTq','emily@gmail.com','customer'),(7,'ayu','$2y$10$NBgWXbTXyApCUSb1grd2nOB/lOq21KeXluWNspNhWxRdol2sjb4Ta','ayu@gmail.com','customer'),(8,'kai','$2y$10$UR.3DqKEcyPSawmfX32t/uz1s2AGnoj.BWV3O3n3C8bZPEsTXROiq','kai@gmail.com','admin'),(9,'ren','$2y$10$acTrAj7Gu0aPlg5riZtEeOjhlZEZZtksSYAshh2wn1g6jOKAYZZze','ren@gmail.com','customer'),(10,'dba','$2y$10$4wDnU0nKyCzen4tK8eknmuKGMrx2enbaDetey2fzrAKCEP77XIpJ2','dba@gmail.com','dba');
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

-- Dump completed on 2025-01-05  3:27:36
