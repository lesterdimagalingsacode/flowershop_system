-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: flowershop
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
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `model` varchar(50) DEFAULT NULL,
  `model_id` int(10) unsigned DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_audit_user` (`user_id`),
  KEY `idx_audit_action` (`action`),
  KEY `idx_audit_model` (`model`,`model_id`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (19,1,1,1,'2026-05-07 12:02:59','2026-05-07 12:02:59'),(25,9,1,3,'2026-05-08 09:01:28','2026-05-08 09:01:59'),(26,9,2,3,'2026-05-08 09:02:30','2026-05-08 09:02:30'),(43,10,6,1,'2026-05-10 10:05:25','2026-05-10 10:05:25'),(45,11,5,1,'2026-05-10 12:54:09','2026-05-10 12:54:09'),(78,2,7,1,'2026-05-11 12:03:44','2026-05-11 12:03:44');
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Bouquets','bouquets','Hand-arranged fresh flower bouquets',1,'2026-04-29 01:30:44','2026-04-29 01:30:44',NULL),(2,'Roses','roses','Classic and premium roses',1,'2026-04-29 01:30:44','2026-04-29 01:30:44',NULL),(3,'Sunflowers','sunflowers','Bright and cheerful sunflowers',1,'2026-04-29 01:30:44','2026-04-29 01:30:44',NULL),(4,'Orchids','orchids','Elegant exotic orchids',1,'2026-04-29 01:30:44','2026-04-29 01:30:44',NULL),(5,'Mixed Arrangements','mixed','Creative mixed flower arrangements',1,'2026-04-29 01:30:44','2026-04-29 01:30:44',NULL),(6,'Seasonal','seasonal','Flowers based on the current season',1,'2026-04-29 01:30:44','2026-04-29 01:30:44',NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `email` varchar(150) NOT NULL DEFAULT '',
  `attempted_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ip` (`ip_address`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order` (`order_id`),
  KEY `idx_order_items_product` (`product_id`),
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,1,1,599.00,599.00,'2026-05-04 03:15:09'),(2,1,5,1,399.00,399.00,'2026-05-04 03:15:09'),(3,1,6,1,899.00,899.00,'2026-05-04 03:15:09'),(4,2,1,1,599.00,599.00,'2026-05-04 03:16:16'),(5,2,2,1,499.00,499.00,'2026-05-04 03:16:16'),(6,3,1,1,599.00,599.00,'2026-05-04 11:13:18'),(7,4,7,1,649.00,649.00,'2026-05-04 11:31:17'),(8,4,8,1,549.00,549.00,'2026-05-04 11:31:17'),(9,5,1,1,599.00,599.00,'2026-05-04 11:34:47'),(10,6,1,1,599.00,599.00,'2026-05-04 11:43:00'),(11,6,7,1,649.00,649.00,'2026-05-04 11:43:00'),(12,7,7,1,649.00,649.00,'2026-05-06 19:08:16'),(13,8,3,1,799.00,799.00,'2026-05-07 12:44:47'),(14,8,1,1,599.00,599.00,'2026-05-07 12:44:47'),(15,9,1,3,599.00,1797.00,'2026-05-07 12:50:31'),(16,10,5,1,399.00,399.00,'2026-05-08 07:03:49'),(17,11,7,1,649.00,649.00,'2026-05-08 08:53:17'),(18,12,5,1,399.00,399.00,'2026-05-09 12:11:29'),(19,13,1,1,599.00,599.00,'2026-05-09 13:27:02'),(20,14,3,1,799.00,799.00,'2026-05-09 13:28:34'),(21,15,8,1,549.00,549.00,'2026-05-09 13:37:25'),(22,16,6,1,899.00,899.00,'2026-05-09 13:40:06'),(23,17,2,1,499.00,499.00,'2026-05-09 13:47:42'),(24,18,2,1,499.00,499.00,'2026-05-09 13:57:18'),(25,19,2,1,499.00,499.00,'2026-05-09 14:29:51'),(26,20,7,1,649.00,649.00,'2026-05-09 15:00:56'),(27,21,7,1,649.00,649.00,'2026-05-09 19:21:47'),(28,22,2,1,499.00,499.00,'2026-05-09 19:35:13'),(29,23,2,1,499.00,499.00,'2026-05-09 20:10:03'),(30,24,2,1,499.00,499.00,'2026-05-10 07:53:11'),(31,25,2,1,499.00,499.00,'2026-05-10 08:43:45'),(32,26,8,1,549.00,549.00,'2026-05-10 12:50:42'),(33,26,1,1,599.00,599.00,'2026-05-10 12:50:42'),(34,27,8,1,549.00,549.00,'2026-05-10 12:55:02'),(35,28,8,1,549.00,549.00,'2026-05-10 18:30:51'),(36,29,2,1,499.00,499.00,'2026-05-10 18:52:00'),(37,30,6,1,899.00,899.00,'2026-05-10 18:59:03'),(38,31,2,1,499.00,499.00,'2026-05-10 20:02:44'),(39,32,2,1,499.00,499.00,'2026-05-10 20:05:14'),(40,33,1,1,599.00,599.00,'2026-05-10 20:06:01'),(41,34,1,1,599.00,599.00,'2026-05-10 20:08:04'),(42,35,1,1,599.00,599.00,'2026-05-11 06:51:26'),(43,36,1,1,599.00,599.00,'2026-05-11 06:54:12'),(44,37,1,1,599.00,599.00,'2026-05-11 07:02:12'),(45,38,1,1,599.00,599.00,'2026-05-11 07:03:20'),(46,39,1,1,599.00,599.00,'2026-05-11 07:10:58'),(47,40,1,1,599.00,599.00,'2026-05-11 07:13:49'),(48,41,2,1,499.00,499.00,'2026-05-11 07:19:32'),(49,42,2,1,499.00,499.00,'2026-05-11 07:29:12'),(50,43,2,1,499.00,499.00,'2026-05-11 07:36:07'),(51,44,2,1,499.00,499.00,'2026-05-11 07:38:23'),(52,45,2,1,499.00,499.00,'2026-05-11 07:43:20'),(53,46,7,1,649.00,649.00,'2026-05-11 07:57:28'),(54,47,7,1,649.00,649.00,'2026-05-11 08:11:52'),(55,48,7,1,649.00,649.00,'2026-05-11 08:52:20'),(56,49,7,1,649.00,649.00,'2026-05-11 08:53:18'),(57,50,7,1,649.00,649.00,'2026-05-11 08:58:18'),(58,51,6,1,899.00,899.00,'2026-05-11 09:04:45'),(59,52,7,1,649.00,649.00,'2026-05-11 09:10:40'),(60,53,7,1,649.00,649.00,'2026-05-11 09:14:09'),(61,54,7,1,649.00,649.00,'2026-05-11 09:46:01'),(62,55,7,1,649.00,649.00,'2026-05-11 09:53:56'),(63,56,5,1,399.00,399.00,'2026-05-11 13:30:45'),(64,57,6,1,899.00,899.00,'2026-05-11 13:33:02');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_status_history`
--

DROP TABLE IF EXISTS `order_status_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_status_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `changed_by` int(10) unsigned DEFAULT NULL,
  `from_status` varchar(20) DEFAULT NULL,
  `to_status` varchar(20) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_osh_user` (`changed_by`),
  KEY `idx_osh_order` (`order_id`),
  CONSTRAINT `fk_osh_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_osh_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_status_history`
--

LOCK TABLES `order_status_history` WRITE;
/*!40000 ALTER TABLE `order_status_history` DISABLE KEYS */;
INSERT INTO `order_status_history` VALUES (1,1,3,'pending','pending','Order placed by customer','2026-05-04 03:15:09'),(2,2,3,'pending','pending','Order placed by customer','2026-05-04 03:16:16'),(3,3,2,'pending','pending','Order placed by customer','2026-05-04 11:13:18'),(4,4,4,'pending','pending','Order placed by customer','2026-05-04 11:31:17'),(5,5,5,'pending','pending','Order placed by customer','2026-05-04 11:34:47'),(6,6,3,'pending','pending','Order placed by customer','2026-05-04 11:43:00'),(7,4,4,'pending','cancelled','Cancelled by customer','2026-05-04 11:50:27'),(8,6,3,'pending','cancelled','Cancelled by customer','2026-05-04 11:54:47'),(9,2,3,'pending','cancelled','Cancelled by customer','2026-05-04 11:54:59'),(10,1,3,'pending','cancelled','Cancelled by customer','2026-05-04 11:55:06'),(11,7,2,'pending','pending','Order placed by customer','2026-05-06 19:08:16'),(12,7,1,'pending','confirmed',NULL,'2026-05-07 12:29:04'),(13,8,2,'pending','pending','Order placed by customer','2026-05-07 12:44:47'),(14,8,1,'pending','cancelled',NULL,'2026-05-07 12:47:44'),(15,9,2,'pending','pending','Order placed by customer','2026-05-07 12:50:31'),(16,9,1,'pending','confirmed',NULL,'2026-05-07 15:41:41'),(17,9,1,'confirmed','processing',NULL,'2026-05-07 15:45:28'),(18,9,1,'processing','ready',NULL,'2026-05-07 15:50:08'),(19,9,1,'ready','delivered',NULL,'2026-05-07 15:50:34'),(20,10,3,'pending','pending','Order placed by customer','2026-05-08 07:03:49'),(21,10,1,'pending','confirmed',NULL,'2026-05-08 07:04:10'),(22,10,1,'confirmed','processing',NULL,'2026-05-08 07:04:15'),(23,10,1,'processing','ready',NULL,'2026-05-08 07:04:19'),(24,10,1,'ready','delivered',NULL,'2026-05-08 07:04:22'),(25,11,9,'pending','pending','Order placed by customer','2026-05-08 08:53:17'),(26,11,1,'pending','confirmed',NULL,'2026-05-08 08:58:10'),(27,11,1,'confirmed','processing',NULL,'2026-05-08 08:58:22'),(28,11,1,'processing','ready',NULL,'2026-05-08 08:58:32'),(29,11,1,'ready','delivered',NULL,'2026-05-08 08:59:00'),(30,12,2,'pending','pending','Order placed by customer','2026-05-09 12:11:29'),(31,13,2,'pending','pending','Order placed by customer','2026-05-09 13:27:03'),(32,14,2,'pending','pending','Order placed by customer','2026-05-09 13:28:34'),(33,15,2,'pending','pending','Order placed by customer','2026-05-09 13:37:25'),(34,16,2,'pending','pending','Order placed by customer','2026-05-09 13:40:06'),(35,17,2,'pending','pending','Order placed by customer','2026-05-09 13:47:42'),(36,18,2,'pending','pending','Order placed by customer','2026-05-09 13:57:19'),(38,18,2,'confirmed','confirmed','Payment confirmed via PayMongo','2026-05-09 14:05:04'),(39,19,2,'pending','pending','Order placed by customer','2026-05-09 14:29:51'),(40,20,2,'pending','pending','Order placed by customer','2026-05-09 15:00:56'),(41,20,2,'pending','confirmed','Payment confirmed via PayMongo (qrph)','2026-05-09 15:01:34'),(42,21,2,'pending','pending','Order placed by customer','2026-05-09 19:21:47'),(43,21,2,'pending','confirmed','Payment confirmed via PayMongo (card)','2026-05-09 19:25:41'),(44,22,2,'pending','pending','Order placed by customer','2026-05-09 19:35:13'),(45,23,2,'pending','pending','Order placed by customer','2026-05-09 20:10:03'),(46,24,10,'pending','pending','Order placed by customer','2026-05-10 07:53:11'),(47,25,10,'pending','pending','Order placed by customer','2026-05-10 08:43:45'),(48,25,1,'pending','confirmed',NULL,'2026-05-10 08:55:22'),(49,26,3,'pending','pending','Order placed by customer','2026-05-10 12:50:42'),(50,27,11,'pending','pending','Order placed by customer','2026-05-10 12:55:02'),(51,28,2,'pending','pending','Order placed by customer','2026-05-10 18:30:51'),(52,28,2,'pending','confirmed','Payment confirmed via PayMongo (card)','2026-05-10 18:34:04'),(53,28,1,'confirmed','processing',NULL,'2026-05-10 18:34:50'),(54,28,1,'processing','ready',NULL,'2026-05-10 18:35:10'),(55,28,1,'ready','delivered',NULL,'2026-05-10 18:35:18'),(56,29,2,'pending','pending','Order placed by customer','2026-05-10 18:52:02'),(57,29,2,'pending','confirmed','Payment confirmed via PayMongo (card)','2026-05-10 18:55:06'),(58,30,2,'pending','pending','Order placed by customer','2026-05-10 18:59:03'),(59,30,2,'pending','confirmed','Payment confirmed via PayMongo (card)','2026-05-10 20:01:37'),(60,31,2,'pending','pending','Order placed by customer','2026-05-10 20:02:44'),(61,32,2,'pending','pending','Order placed by customer','2026-05-10 20:05:15'),(62,33,2,'pending','pending','Order placed by customer','2026-05-10 20:06:01'),(63,34,2,'pending','pending','Order placed by customer','2026-05-10 20:08:07'),(64,34,2,'pending','confirmed','Payment confirmed via PayMongo (qrph)','2026-05-10 20:13:47'),(65,35,2,'pending','pending','Order placed by customer','2026-05-11 06:51:26'),(66,36,2,'pending','pending','Order placed by customer','2026-05-11 06:54:12'),(67,37,2,'pending','pending','Order placed by customer','2026-05-11 07:02:12'),(68,37,2,'pending','cancelled','Cancelled by customer','2026-05-11 07:02:30'),(69,38,2,'pending','pending','Order placed by customer','2026-05-11 07:03:20'),(70,38,2,'pending','cancelled','Cancelled by customer','2026-05-11 07:09:57'),(71,39,2,'pending','pending','Order placed by customer','2026-05-11 07:10:58'),(72,39,2,'pending','cancelled','Cancelled by customer','2026-05-11 07:12:22'),(73,40,2,'pending','pending','Order placed by customer','2026-05-11 07:13:49'),(74,41,2,'pending','pending','Order placed by customer','2026-05-11 07:19:32'),(75,41,2,'pending','cancelled','Cancelled by customer','2026-05-11 07:27:55'),(76,42,2,'pending','pending','Order placed by customer','2026-05-11 07:29:12'),(77,43,2,'pending','pending','Order placed by customer','2026-05-11 07:36:07'),(78,44,2,'pending','pending','Order placed by customer','2026-05-11 07:38:24'),(79,45,2,'pending','pending','Order placed by customer','2026-05-11 07:43:20'),(80,46,2,'pending','pending','Order placed by customer','2026-05-11 07:57:28'),(81,47,2,'pending','pending','Order placed by customer','2026-05-11 08:11:52'),(82,48,2,'pending','pending','Order placed by customer','2026-05-11 08:52:20'),(83,49,2,'pending','pending','Order placed by customer','2026-05-11 08:53:18'),(84,50,2,'pending','pending','Order placed by customer','2026-05-11 08:58:18'),(85,50,2,'pending','confirmed','Payment confirmed via PayMongo Payment Intent (card)','2026-05-11 08:58:21'),(86,51,2,'pending','pending','Order placed by customer','2026-05-11 09:04:45'),(87,52,2,'pending','pending','Order placed by customer','2026-05-11 09:10:40'),(88,53,2,'pending','pending','Order placed by customer','2026-05-11 09:14:09'),(89,54,2,'pending','pending','Order placed by customer','2026-05-11 09:46:01'),(90,55,12,'pending','pending','Order placed by customer','2026-05-11 09:53:57'),(91,55,12,'pending','confirmed','Payment confirmed via PayMongo Payment Intent (card)','2026-05-11 09:54:01'),(92,55,1,'confirmed','processing',NULL,'2026-05-11 09:55:04'),(93,55,1,'processing','ready',NULL,'2026-05-11 09:55:26'),(94,55,1,'ready','delivered',NULL,'2026-05-11 09:55:39'),(95,54,2,'pending','confirmed','Payment confirmed via PayMongo Payment Intent (card)','2026-05-11 12:35:07'),(96,54,1,'confirmed','processing',NULL,'2026-05-11 12:47:26'),(97,54,1,'processing','ready',NULL,'2026-05-11 12:47:50'),(98,56,2,'pending','pending','Order placed by customer','2026-05-11 13:30:45'),(99,56,2,'pending','confirmed','Payment confirmed via PayMongo Payment Intent (card)','2026-05-11 13:30:49'),(100,57,2,'pending','pending','Order placed by customer','2026-05-11 13:33:02'),(101,57,1,'pending','confirmed',NULL,'2026-05-11 13:33:27'),(102,57,1,'confirmed','processing',NULL,'2026-05-11 13:33:38'),(103,57,1,'processing','ready',NULL,'2026-05-11 13:33:50'),(104,57,1,'ready','delivered',NULL,'2026-05-11 13:34:07');
/*!40000 ALTER TABLE `order_status_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `order_number` varchar(20) NOT NULL,
  `status` enum('pending','confirmed','processing','ready','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cod','online') NOT NULL DEFAULT 'cod',
  `delivery_address` text NOT NULL,
  `notes` text DEFAULT NULL,
  `promo_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `idx_orders_user` (`user_id`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_number` (`order_number`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,3,'PS-DAB7DD-20260504','cancelled',1897.00,0.00,1897.00,'cod','baler','qwerty',NULL,0.00,'2026-05-04 03:15:09','2026-05-04 11:55:05',NULL),(2,3,'PS-058D4F-20260504','cancelled',1098.00,0.00,1098.00,'cod','Ket saan','Qwerty',NULL,0.00,'2026-05-04 03:16:16','2026-05-04 11:54:59',NULL),(3,2,'PS-E210A6-20260504','pending',599.00,0.00,599.00,'cod','kET SAAN LAANG PO HEHEHE','PAKIINGATAN, HINDI NA NGA AKO ININGATAN GAGANYANIN PA',NULL,0.00,'2026-05-04 11:13:18','2026-05-04 11:13:18',NULL),(4,4,'PS-55115C-20260504','cancelled',1198.00,0.00,1198.00,'cod','Purok 3 Barangay Dilaguidi, Dilasag, Aurora','',NULL,0.00,'2026-05-04 11:31:17','2026-05-04 11:50:27',NULL),(5,5,'PS-73F1CC-20260504','pending',599.00,0.00,599.00,'cod','baler','helli',NULL,0.00,'2026-05-04 11:34:47','2026-05-04 11:34:47',NULL),(6,3,'PS-431F37-20260504','cancelled',1248.00,0.00,1248.00,'cod','dito sa tabi tabi','',NULL,0.00,'2026-05-04 11:43:00','2026-05-04 11:54:47',NULL),(7,2,'PS-0C6A31-20260506','confirmed',649.00,0.00,649.00,'cod','tang ina ket saan','wala',NULL,0.00,'2026-05-06 19:08:16','2026-05-07 12:29:04',NULL),(8,2,'PS-F41F9E-20260507','cancelled',1398.00,0.00,1398.00,'cod','Purok 4, Sabang, Baler, Aurora, Region III (Central Luzon)','wala po',NULL,0.00,'2026-05-07 12:44:47','2026-05-07 12:47:44',NULL),(9,2,'PS-77DC8B-20260507','delivered',1797.00,0.00,1797.00,'cod','143, Pingit, Baler, Aurora, Region III (Central Luzon)','hihi',NULL,0.00,'2026-05-07 12:50:31','2026-05-07 15:50:34',NULL),(10,3,'PS-5548F2-20260508','delivered',399.00,0.00,399.00,'cod','bhb, Reserva, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-08 07:03:49','2026-05-08 07:04:22',NULL),(11,9,'PS-DE35DF-20260508','delivered',649.00,0.00,649.00,'cod','Purok 4, Zabali, Baler, Aurora, Region III (Central Luzon)','hehe',NULL,0.00,'2026-05-08 08:53:17','2026-05-08 08:59:00',NULL),(12,2,'PS-19313D-20260509','pending',399.00,0.00,399.00,'cod','Purok 4, Zabali, Baler, Aurora, Region III (Central Luzon)','WAHAHAHAHAHA',NULL,0.00,'2026-05-09 12:11:29','2026-05-09 12:11:29',NULL),(13,2,'PS-6D60C0-20260509','pending',599.00,0.00,599.00,'cod','Purok 4, Zabali, Baler, Aurora, Region III (Central Luzon)','mweheheheheheheheheheh',NULL,0.00,'2026-05-09 13:27:02','2026-05-09 13:27:03',NULL),(14,2,'PS-23A6B7-20260509','pending',799.00,0.00,799.00,'cod','Purok 4, Zabali, Baler, Aurora, Region III (Central Luzon)','wala',NULL,0.00,'2026-05-09 13:28:34','2026-05-09 13:28:34',NULL),(15,2,'PS-5CE11D-20260509','pending',549.00,0.00,549.00,'cod','Purok 4, Suclayin, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-09 13:37:25','2026-05-09 13:37:25',NULL),(16,2,'PS-6A1D0C-20260509','pending',899.00,0.00,899.00,'cod','Purok 4, Sabang, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-09 13:40:06','2026-05-09 13:40:06',NULL),(17,2,'PS-ECDD1F-20260509','pending',499.00,0.00,499.00,'cod','Purok 4, Sabang, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-09 13:47:42','2026-05-09 13:47:42',NULL),(18,2,'PS-EF05F5-20260509','confirmed',499.00,0.00,499.00,'online','Purok 4, Sabang, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-09 13:57:18','2026-05-09 14:05:04',NULL),(19,2,'PS-FE1108-20260509','pending',499.00,0.00,499.00,'cod','Purok 4, Zabali, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-09 14:29:51','2026-05-09 14:29:51',NULL),(20,2,'PS-8A8ED2-20260509','confirmed',649.00,0.00,649.00,'online','Purok 4, Obligacion, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-09 15:00:56','2026-05-09 15:01:34',NULL),(21,2,'PS-BA1838-20260509','confirmed',649.00,0.00,649.00,'online','Purok 4, Cabituculan West, Maria Aurora, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-09 19:21:47','2026-05-09 19:25:41',NULL),(22,2,'PS-1E5755-20260509','pending',499.00,0.00,499.00,'online','Purok 4, Zabali, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-09 19:35:13','2026-05-09 19:35:13',NULL),(23,2,'PS-BD138F-20260509','pending',499.00,0.00,499.00,'online','Purok 4, Abuleg, Dinalungan, Aurora, Region III (Central Luzon)','hehehe',NULL,0.00,'2026-05-09 20:10:03','2026-05-09 20:10:03',NULL),(24,10,'PS-7A0BD0-20260510','pending',499.00,0.00,499.00,'cod','Purok 4, Sabang, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-10 07:53:11','2026-05-10 07:53:11',NULL),(25,10,'PS-164D39-20260510','confirmed',499.00,0.00,499.00,'cod','Purok 4, Suclayin, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-10 08:43:45','2026-05-10 08:55:22',NULL),(26,3,'PS-2C5860-20260510','pending',1148.00,0.00,1148.00,'cod','bengbeng, Zabali, Baler, Aurora, Region III (Central Luzon)','8:50',NULL,0.00,'2026-05-10 12:50:42','2026-05-10 12:50:42',NULL),(27,11,'PS-639CB5-20260510','pending',549.00,0.00,549.00,'online','123, Zabali, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-10 12:55:02','2026-05-10 12:55:02',NULL),(28,2,'PS-BAC7D4-20260510','delivered',549.00,0.00,549.00,'online','Purok 4, Sabang, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-10 18:30:51','2026-05-10 18:35:18',NULL),(29,2,'PS-FF07A8-20260510','confirmed',499.00,0.00,499.00,'online','Purok 4, Calabuanan, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-10 18:52:00','2026-05-10 18:55:06',NULL),(30,2,'PS-6F12DD-20260510','confirmed',899.00,0.00,899.00,'online','Purok 4, Buhangin, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-10 18:59:02','2026-05-10 20:01:37',NULL),(31,2,'PS-41C34C-20260510','pending',499.00,0.00,499.00,'online','Purok 4, Obligacion, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-10 20:02:44','2026-05-10 20:02:44',NULL),(32,2,'PS-AD6E80-20260510','pending',499.00,0.00,499.00,'cod','Purok 4, Pingit, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-10 20:05:14','2026-05-10 20:05:15',NULL),(33,2,'PS-959FFE-20260510','pending',599.00,0.00,599.00,'online','Purok 4, Calabuanan, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-10 20:06:01','2026-05-10 20:06:01',NULL),(34,2,'PS-3BD4C1-20260510','confirmed',599.00,0.00,599.00,'online','Purok 4, Suclayin, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-10 20:08:03','2026-05-10 20:13:47',NULL),(35,2,'PS-E8118E-20260511','pending',599.00,0.00,599.00,'online','Purok 4, Calabuanan, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 06:51:26','2026-05-11 06:51:26',NULL),(36,2,'PS-459AB8-20260511','pending',599.00,0.00,599.00,'online','Purok 4, Suclayin, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 06:54:12','2026-05-11 06:54:12',NULL),(37,2,'PS-43C0DD-20260511','cancelled',599.00,0.00,599.00,'online','Purok 4, Sabang, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 07:02:12','2026-05-11 07:02:30',NULL),(38,2,'PS-80C010-20260511','cancelled',599.00,0.00,599.00,'online','Purok 4, Zabali, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 07:03:20','2026-05-11 07:09:57',NULL),(39,2,'PS-230B55-20260511','cancelled',599.00,0.00,599.00,'online','Purok 4, Reserva, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 07:10:58','2026-05-11 07:12:22',NULL),(40,2,'PS-DBDC81-20260511','pending',599.00,0.00,599.00,'online','Purok 4, Barangay II (Pob.), Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 07:13:49','2026-05-11 07:13:49',NULL),(41,2,'PS-4C092B-20260511','cancelled',499.00,0.00,499.00,'online','Purok 4, Calabuanan, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 07:19:32','2026-05-11 07:27:54',NULL),(42,2,'PS-8794DC-20260511','pending',499.00,0.00,499.00,'online','Purok 4, Pingit, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 07:29:12','2026-05-11 07:29:12',NULL),(43,2,'PS-7821C4-20260511','pending',499.00,0.00,499.00,'online','Purok 4, Barangay V (Pob.), Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 07:36:07','2026-05-11 07:36:07',NULL),(44,2,'PS-FEE670-20260511','pending',499.00,0.00,499.00,'online','Purok 4, Sabang, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 07:38:23','2026-05-11 07:38:24',NULL),(45,2,'PS-81421F-20260511','pending',499.00,0.00,499.00,'online','Purok 4, Buhangin, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 07:43:20','2026-05-11 07:43:20',NULL),(46,2,'PS-8B4BBE-20260511','pending',649.00,0.00,649.00,'online','Purok 4, Calabuanan, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 07:57:28','2026-05-11 07:57:28',NULL),(47,2,'PS-86D0AC-20260511','pending',649.00,0.00,649.00,'online','Purok 4, Obligacion, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 08:11:52','2026-05-11 08:11:52',NULL),(48,2,'PS-3ED8BE-20260511','pending',649.00,0.00,649.00,'online','Purok 4, Calabuanan, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 08:52:19','2026-05-11 08:52:20',NULL),(49,2,'PS-E0F625-20260511','pending',649.00,0.00,649.00,'online','Purok 4, Calabuanan, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 08:53:18','2026-05-11 08:53:18',NULL),(50,2,'PS-A38C98-20260511','confirmed',649.00,0.00,649.00,'online','Purok 4, Barangay III (Pob.), Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 08:58:18','2026-05-11 08:58:21',NULL),(51,2,'PS-D217FF-20260511','pending',899.00,0.00,899.00,'online','Purok 4, Buhangin, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 09:04:45','2026-05-11 09:04:45',NULL),(52,2,'PS-048335-20260511','pending',649.00,0.00,649.00,'online','Purok 4, Sabang, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 09:10:40','2026-05-11 09:10:40',NULL),(53,2,'PS-192089-20260511','pending',649.00,0.00,649.00,'online','Purok 4, Barangay I (Pob.), Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 09:14:09','2026-05-11 09:14:09',NULL),(54,2,'PS-9C2BBB-20260511','ready',649.00,0.00,649.00,'online','Purok 4, Buhangin, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 09:46:01','2026-05-11 12:47:50',NULL),(55,12,'PS-4F16E4-20260511','delivered',649.00,0.00,649.00,'online','1234, Barangay II (Pob.), Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 09:53:56','2026-05-11 09:55:39',NULL),(56,2,'PS-5006A4-20260511','confirmed',399.00,0.00,399.00,'online','Purok 4, Barangay V (Pob.), Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 13:30:45','2026-05-11 13:30:49',NULL),(57,2,'PS-E04CD9-20260511','delivered',899.00,0.00,899.00,'cod','Purok 4, Obligacion, Baler, Aurora, Region III (Central Luzon)','',NULL,0.00,'2026-05-11 13:33:02','2026-05-11 13:34:07',NULL);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `paymongo_payment_id` varchar(100) DEFAULT NULL,
  `paymongo_link_id` varchar(100) DEFAULT NULL,
  `idempotency_key` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'PHP',
  `status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idempotency_key` (`idempotency_key`),
  KEY `idx_payments_order` (`order_id`),
  KEY `idx_payments_status` (`status`),
  KEY `idx_payments_paymongo` (`paymongo_payment_id`),
  CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,18,'pay_BRTtzUZ4rDHc7oR5GhueG8MS','cs_04dcba8edc7467cc6843b9bf','order_18_1778335040',499.00,'PHP','paid','card','2026-05-09 08:05:04','2026-05-09 13:57:20','2026-05-09 14:05:04'),(2,20,'pay_k89L449g44tm5rY7VoYaED6j','cs_6d90f9da8f9a658a5b42935a','order_20_1778338858',649.00,'PHP','paid','qrph','2026-05-09 09:01:34','2026-05-09 15:00:58','2026-05-09 15:01:34'),(3,21,'pay_2sTuuRxXimw7NuwGajTtRjYF','cs_03c5ec6a7cbacf2c8aecaf6a','order_21_1778354511',649.00,'PHP','paid','card','2026-05-09 13:25:41','2026-05-09 19:21:56','2026-05-09 19:25:41'),(4,22,NULL,'cs_43df9e0150f0bfc0cbadf6e9','order_22_1778355327',499.00,'PHP','pending','online',NULL,'2026-05-09 19:35:32','2026-05-09 19:35:32'),(5,23,NULL,'cs_89251dc9e8acb0f1bf2f03ea','order_23_1778357406',499.00,'PHP','pending','online',NULL,'2026-05-09 20:10:14','2026-05-09 20:10:14'),(6,27,NULL,'cs_c647c69660b9ad510607528f','order_27_1778417704',549.00,'PHP','pending','online',NULL,'2026-05-10 12:55:05','2026-05-10 12:55:05'),(7,28,'pay_yqcTnemauAmCnBEqBUZkaQYG','cs_70e588a06b140bd7ff598661','order_28_1778437852',549.00,'PHP','paid','card','2026-05-10 12:34:04','2026-05-10 18:30:53','2026-05-10 18:34:04'),(8,29,'pay_EsLJN3K6fRfhbpY4hcSbbM3v','cs_2725897c2caa8dfa2240ed15','order_29_1778439123',499.00,'PHP','paid','card','2026-05-10 12:55:06','2026-05-10 18:52:04','2026-05-10 18:55:06'),(9,30,NULL,'cs_2e0d1ad5c34e0c1996d01802','order_30_1778439543',899.00,'PHP','pending','online',NULL,'2026-05-10 18:59:04','2026-05-10 18:59:04'),(10,30,'pay_FthfYCNfk3PaNXV847py45NJ','cs_be437065dd2784cfab69b9f2','order_30_1778441510',899.00,'PHP','paid','card','2026-05-10 14:01:37','2026-05-10 19:31:52','2026-05-10 20:01:37'),(11,34,'pay_MxBXJys5VQyXWsdxh8gwwNYT','cs_a9b2654362b23cde5532d9f6','order_34_1778443689',599.00,'PHP','paid','qrph','2026-05-10 14:13:47','2026-05-10 20:08:10','2026-05-10 20:13:47'),(12,36,NULL,'cs_9f61754c6defaef672bb0701','order_36_1778482468',599.00,'PHP','pending','online',NULL,'2026-05-11 06:54:28','2026-05-11 06:54:28'),(13,40,NULL,'cs_213bab1b006db17241227e9c','order_40_1778483908',599.00,'PHP','pending','online',NULL,'2026-05-11 07:18:28','2026-05-11 07:18:28'),(14,50,'pay_92LXkjepgu5WTYWVLfHTGYfJ','pi_EqExh6FZobcNapHqwoAchRJG','pi_50_1778489901',649.00,'PHP','paid','card','2026-05-11 02:58:21','2026-05-11 08:58:21','2026-05-11 08:58:21'),(15,51,NULL,'pi_bwkuABbYcnpdE3vww27kNwtU','pi_51_1778490287',899.00,'PHP','pending','online',NULL,'2026-05-11 09:04:47','2026-05-11 09:04:47'),(16,52,NULL,'pi_Jt8k79zP7Fxg6bCH8uqnt3D2','pi_52_1778490644',649.00,'PHP','pending','online',NULL,'2026-05-11 09:10:44','2026-05-11 09:10:44'),(17,53,NULL,'pi_AYMzgJRNQXtHKTbjLkySW4GU','pi_53_1778490852',649.00,'PHP','pending','online',NULL,'2026-05-11 09:14:12','2026-05-11 09:14:12'),(18,54,NULL,'pi_yYxwNq4yoLqRLW8oqcTrWRQQ','pi_54_1778492764',649.00,'PHP','pending','online',NULL,'2026-05-11 09:46:04','2026-05-11 09:46:04'),(19,55,'pay_tZ5pRW7UzpX1hrpDSprCaRn6','pi_3Kxvq9pD2bbQ5whpVPmJZbWR','pi_55_1778493241',649.00,'PHP','paid','card','2026-05-11 03:54:01','2026-05-11 09:54:01','2026-05-11 09:54:01'),(20,54,'pay_NEE5Z5rQzUFmC7EsQ2PLxjxa','pi_NWUE8vJGFNTTWkpStci1b1Vi','pi_54_1778502907',649.00,'PHP','paid','card','2026-05-11 06:35:07','2026-05-11 12:35:07','2026-05-11 12:35:07'),(21,56,'pay_G4zaiaQ6UBGpyTRhLGXxVDqq','pi_agzB5b3um8QHqY8bBivCdRrJ','pi_56_1778506249',399.00,'PHP','paid','card','2026-05-11 07:30:49','2026-05-11 13:30:49','2026-05-11 13:30:49');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(10) unsigned NOT NULL DEFAULT 0,
  `low_stock_alert` int(10) unsigned NOT NULL DEFAULT 5,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_products_category` (`category_id`),
  KEY `idx_products_active` (`is_active`),
  KEY `idx_products_stock` (`stock`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'Classic Red Bouquet','classic-red-bouquet','A timeless red rose bouquet perfect for any occasion.',599.00,0,5,'product_69fe3e95b0a747.71652969.jpg',1,'2026-04-29 01:30:44','2026-05-11 07:13:49',NULL),(2,1,'Pink Dream Bouquet','pink-dream-bouquet','Soft pink flowers arranged beautifully.',499.00,0,5,'product_69fe3feac67298.34797714.jpg',1,'2026-04-29 01:30:44','2026-05-11 07:43:20',NULL),(3,2,'Premium Red Roses','premium-red-roses','12 stems of premium long-stem red roses.',799.00,28,5,'product_69fe407a52ed05.53493549.jpg',1,'2026-04-29 01:30:44','2026-05-09 13:28:34',NULL),(4,2,'White Rose Elegance','white-rose-elegance','Pure white roses symbolizing innocence and love.',699.00,25,5,'product_69fe410159c021.24458857.png',1,'2026-04-29 01:30:44','2026-05-08 20:09:38',NULL),(5,3,'Sunflower Sunshine','sunflower-sunshine','Bright sunflowers to brighten up any room.',399.00,36,10,'product_69fe43dcb0bce9.62170648.webp',1,'2026-04-29 01:30:44','2026-05-11 13:30:45',NULL),(6,4,'Purple Orchid Vase','purple-orchid-vase','Elegant purple orchids in a beautiful vase.',899.00,5,3,'product_69fe444ad3b3e6.23318272.png',1,'2026-04-29 01:30:44','2026-05-11 13:33:02',NULL),(7,5,'Rainbow Mix Arrangement','rainbow-mix','A colorful mix of seasonal flowers.',649.00,3,5,'product_69fe44acb4d491.95153514.png',1,'2026-04-29 01:30:44','2026-05-11 09:53:57',NULL),(8,6,'Seasonal Special','seasonal-special','Best flowers of the season handpicked for you.',549.00,17,5,'product_69fe453c44c6e3.55476189.png',1,'2026-04-29 01:30:44','2026-05-10 18:30:51',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promo_codes`
--

DROP TABLE IF EXISTS `promo_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promo_codes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `type` enum('percent','fixed') NOT NULL DEFAULT 'percent',
  `value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `min_order` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_uses` int(10) unsigned DEFAULT NULL COMMENT 'NULL = unlimited',
  `uses` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_promo_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promo_codes`
--

LOCK TABLES `promo_codes` WRITE;
/*!40000 ALTER TABLE `promo_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `promo_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `verification_token` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','customer') NOT NULL DEFAULT 'customer',
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin',NULL,'Admin','petalsouladmin@gmail.com',NULL,NULL,'$2y$12$4oQ46LiGhzAQFfrmg75AJuFKmnomqgBWv3g48HwR5NyW9sBQb8WP2','admin',NULL,NULL,1,'2026-04-29 01:30:44','2026-05-10 07:22:02',NULL),(2,'Kim Lester',NULL,'Lumibao','kimlesterlumibao@gmail.com','2026-05-07 18:01:25',NULL,'$2y$12$C7qYtIB58E3gr.InIKZ2NOOaJ9O26u5H88sMYv5zl0B9JsccYrmB6','customer',NULL,NULL,1,'2026-05-01 12:09:53','2026-05-07 18:01:25',NULL),(3,'Ace',NULL,'Santos','ace@gmail.com',NULL,NULL,'$2y$12$8HrdU/zauEyBrwGe9OO8FO0u/Ju2PlEQUig.n3yQNDVDDC.ouOTH.','customer','09123456789',NULL,1,'2026-05-04 03:14:18','2026-05-04 03:14:18',NULL),(4,'Aira','Cinco','Noveno','airanoveno123@gmail.com',NULL,NULL,'$2y$12$knD3nNYVbtp5CmnqlnCZiuuVH8R7MfyAVMr9/yNTGQub2nYCnGvl6','customer',NULL,NULL,1,'2026-05-04 11:26:10','2026-05-04 11:26:10',NULL),(5,'Karyl',NULL,'Salamera','ferinakarylsalamera@gmail.com',NULL,NULL,'$2y$12$L6Fu7QH3SgxRP/1nYQqUM.nnMq5r72hGtLiHjZp2rO8varkT2LeHq','customer','0643894386464',NULL,1,'2026-05-04 11:33:14','2026-05-04 11:33:14',NULL),(6,'Jam','Angara','Villar','jamvillar@gmail.com',NULL,NULL,'$2y$12$pez44xyAFxwHshsxnqLd8eALm98m8e85iJ0ss3GrMJhuxf3snbn8C','customer',NULL,NULL,1,'2026-05-04 11:44:43','2026-05-04 11:44:43',NULL),(7,'Mhar','June','Barcelo','marjune0605@gmail.com',NULL,NULL,'$2y$12$IGx42rlN7Vl/Tu7yvkYYau3BRVxRx1feiaCPLmwEgnrsOY5rRotGu','customer',NULL,NULL,1,'2026-05-04 12:36:42','2026-05-04 12:36:42',NULL),(8,'Lester',NULL,'Flores','kimmyrut102@gmail.com','2026-05-07 17:58:19',NULL,'$2y$12$POMVAExn2l88HcDYV62pNe4CslxH6JTirhZhIVEfGlE1qbtsn/TkW','customer',NULL,NULL,1,'2026-05-07 17:21:25','2026-05-07 17:58:19',NULL),(9,'Aira',NULL,'Noveno','airanoveno14@gmail.com','2026-05-10 12:44:07',NULL,'$2y$12$sNl2V9Ai2.x9zcWx1IP0MOkT8IEHcTUahGa7kEzzv5cBCpbhAQnLC','customer',NULL,NULL,1,'2026-05-08 08:50:56','2026-05-10 12:44:07',NULL),(10,'Kratos',NULL,'De Zeus','lumibaokimlester@gmail.com','2026-05-10 07:46:13',NULL,'$2y$12$j5JymTG77f9WRHZr2kREiuwduzdgFugjdgi9.6SkdF3k0k6TUqTe2','customer',NULL,NULL,1,'2026-05-10 07:41:24','2026-05-10 07:46:13',NULL),(11,'Jamm','Angara','Villar','jamvillar28@gmail.com',NULL,'5fa94bf1a6d5ff9cbc674f1ea9109b60b2cf0b76dad3a2f7668b2aa352c6b49e','$2y$12$M1u7OKEYK7wyqvGrl0niKu7Z6vk7JyOK1C7xt29uSUJ9byo5MBT9S','customer',NULL,NULL,1,'2026-05-10 12:53:35','2026-05-10 12:53:35',NULL),(12,'jean','almirol','de guzman','rhoandeguzman1@gmail.com',NULL,'39c8508142ffceecfd14d69ec28031e67fdf03ab72bb5138b3c74b97acc140fd','$2y$12$lzOur4GscilP/UbG5xYcS.r52jPSgJaHPCxo9TkQqMDEB8CKbMzv2','customer',NULL,NULL,1,'2026-05-11 09:52:09','2026-05-11 09:52:09',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'flowershop'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-12  3:16:31
