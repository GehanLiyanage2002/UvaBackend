CREATE DATABASE  IF NOT EXISTS `uva_pms` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `uva_pms`;
-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: uva_pms
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` time DEFAULT NULL,
  `meet_link` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'Event Title UP','2025-08-20','00:00:00','https://example.com.up','this is a test UP','2025-08-16 05:35:58','2025-08-20 07:05:18'),(2,'Event Title','2025-08-26','12:00:00',NULL,NULL,'2025-08-16 05:36:10','2025-08-20 06:45:48'),(3,'Event Title update','2025-08-16','12:00:00',NULL,NULL,'2025-08-16 06:59:33','2025-08-20 07:02:07'),(4,'Tets event','2025-09-06',NULL,'http://localhost:5173/manager/calendar','This is a test description','2025-08-20 06:50:53','2025-08-20 06:50:53'),(8,'Omnis vitae tempora','2025-08-06','13:06:00','Excepturi placeat v','Similique nisi eiusm','2025-08-20 09:15:35','2025-08-20 09:15:35'),(9,'New','2025-08-22','10:00:00',NULL,NULL,'2025-08-22 10:17:02','2025-08-22 10:17:17');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notices`
--

DROP TABLE IF EXISTS `notices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `priority` enum('normal','high','urgent') DEFAULT 'normal',
  `status` enum('active','inactive','archived') DEFAULT 'active',
  `expires_at` datetime DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_priority` (`priority`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notices`
--

LOCK TABLES `notices` WRITE;
/*!40000 ALTER TABLE `notices` DISABLE KEYS */;
INSERT INTO `notices` VALUES (1,'Updated Notice','This is notice content','urgent','active','2024-12-31 23:59:59',1,1,'2025-08-14 06:18:56','2025-08-23 11:36:27'),(3,'Important Notice','This is notice content','high','active','2025-08-23 17:09:00',0,1,'2025-08-14 06:26:45','2025-08-23 11:39:16'),(4,'Important Notice','This is notice content','high','active','0000-00-00 00:00:00',1,1,'2025-08-14 06:28:24','2025-08-14 06:28:24'),(5,'Important Notice','This is notice content','high','active','0000-00-00 00:00:00',1,1,'2025-08-14 06:28:29','2025-08-14 06:28:29'),(6,'Reprehenderit magni','Et est natus ut dol hfhfhfdhfh','high','inactive','2015-02-19 22:19:00',1,1,'2025-08-23 11:39:25','2025-08-23 11:39:37');
/*!40000 ALTER TABLE `notices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_members`
--

DROP TABLE IF EXISTS `project_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_user` (`project_id`,`user_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_assigned_by` (`assigned_by`),
  CONSTRAINT `fk_project_members_assigned_by` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_project_members_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_project_members_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_members`
--

LOCK TABLES `project_members` WRITE;
/*!40000 ALTER TABLE `project_members` DISABLE KEYS */;
INSERT INTO `project_members` VALUES (5,4,2,'2025-08-12 10:06:38',4),(6,6,2,'2025-08-13 06:58:20',4),(7,3,5,'2025-08-15 05:26:27',4),(8,7,5,'2025-08-15 05:32:34',4),(9,6,5,'2025-08-15 06:40:53',4),(10,8,2,'2025-08-20 04:26:43',4),(12,8,5,'2025-08-20 04:40:09',4),(13,7,2,'2025-08-20 04:42:21',4);
/*!40000 ALTER TABLE `project_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_supervisors`
--

DROP TABLE IF EXISTS `project_supervisors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_supervisors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_supervisor` (`project_id`,`supervisor_id`),
  KEY `idx_project_supervisors_project` (`project_id`),
  KEY `idx_project_supervisors_supervisor` (`supervisor_id`),
  KEY `idx_project_supervisors_assigned_by` (`assigned_by`),
  CONSTRAINT `project_supervisors_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_supervisors_ibfk_2` FOREIGN KEY (`supervisor_id`) REFERENCES `supervisors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_supervisors_ibfk_3` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_supervisors`
--

LOCK TABLES `project_supervisors` WRITE;
/*!40000 ALTER TABLE `project_supervisors` DISABLE KEYS */;
INSERT INTO `project_supervisors` VALUES (1,6,3,1,'2025-08-14 03:49:35'),(6,5,3,1,'2025-08-14 03:59:03'),(8,3,1,1,'2025-08-23 11:09:33'),(9,7,3,1,'2025-08-23 11:16:56'),(10,4,3,1,'2025-08-23 11:21:29'),(13,8,4,1,'2025-08-23 11:48:02');
/*!40000 ALTER TABLE `project_supervisors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `manager_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `manager_id` (`manager_id`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,'test01','fsgesg',1,'2025-08-10 06:01:57',NULL,NULL),(3,'test new','test description updated new',4,'2025-08-12 08:42:04','2025-01-01','2025-08-23'),(4,'test new','test description updated new',4,'2025-08-12 08:53:25','2025-08-10','2025-08-13'),(5,'New Project','Project description here',4,'2025-08-12 10:10:19','2025-08-12','0000-00-00'),(6,'New Project','Project description here',4,'2025-08-13 06:57:51','2025-08-12','2025-10-10'),(7,'My Pro','test description updated new',4,'2025-08-14 04:02:05','2025-08-12','2025-08-23'),(8,'New Completed','This is a new project',4,'2025-08-20 03:07:02','2025-08-20','2025-08-22');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `url` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES (3,8,'http://localhost/uwu_pms_backend-main/uploads/pdf/project_8_New_Project_2025-08-20_09-36-07.pdf','2025-08-20 07:36:08'),(4,7,'http://localhost/uwu_pms_backend-main/uploads/pdf/project_7_My_Pro_2025-08-20_09-36-33.pdf','2025-08-20 07:36:34');
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supervisors`
--

DROP TABLE IF EXISTS `supervisors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supervisors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `type` enum('supervisor','co-supervisor') NOT NULL,
  `faculty_name` varchar(255) NOT NULL,
  `department_name` varchar(255) NOT NULL,
  `about` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_supervisors_type` (`type`),
  KEY `idx_supervisors_faculty` (`faculty_name`),
  KEY `idx_supervisors_department` (`department_name`),
  KEY `idx_supervisors_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supervisors`
--

LOCK TABLES `supervisors` WRITE;
/*!40000 ALTER TABLE `supervisors` DISABLE KEYS */;
INSERT INTO `supervisors` VALUES (1,'Updated Name','updated@example.com','123456789','supervisor','Engineering','IT','Description','2025-08-13 11:34:19','2025-08-13 11:35:42'),(3,'John Doe updated','john2@example.com.updated','1234567890','supervisor','Engineering updated','IT updated','Description updated','2025-08-14 03:48:33','2025-08-23 11:47:25'),(4,'Ipsa natus sunt ull','joxituzy@mailinator.com','Fugit rerum qui vol','supervisor','Omnis minus molestia','Modi quis placeat e','Praesentium est cons','2025-08-23 11:47:48','2025-08-23 11:47:48');
/*!40000 ALTER TABLE `supervisors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_assignments`
--

DROP TABLE IF EXISTS `task_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_task_user` (`task_id`,`user_id`),
  KEY `idx_task_id` (`task_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_assigned_by` (`assigned_by`),
  CONSTRAINT `fk_task_assignments_assigned_by` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_task_assignments_task` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_task_assignments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_assignments`
--

LOCK TABLES `task_assignments` WRITE;
/*!40000 ALTER TABLE `task_assignments` DISABLE KEYS */;
INSERT INTO `task_assignments` VALUES (2,4,2,4,'2025-08-13 09:47:46'),(3,4,5,4,'2025-08-15 06:40:58'),(4,8,2,4,'2025-08-20 04:53:16'),(5,8,5,4,'2025-08-20 04:53:24'),(10,9,2,4,'2025-08-20 05:12:17'),(13,11,2,4,'2025-08-20 11:46:18'),(14,10,5,4,'2025-08-22 07:00:45'),(15,5,2,4,'2025-08-22 08:46:40'),(16,7,5,4,'2025-08-22 10:13:24');
/*!40000 ALTER TABLE `task_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `status` enum('todo','in_progress','testing','done') DEFAULT 'todo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
INSERT INTO `tasks` VALUES (1,3,'Updated Task','in_progress','2025-08-13 06:36:13'),(3,3,'New Task','todo','2025-08-13 06:49:22'),(4,6,'New Task','todo','2025-08-13 06:59:52'),(5,7,'New Task UP','in_progress','2025-08-20 03:39:57'),(6,7,'New Task 2','done','2025-08-20 03:40:08'),(7,7,'New Task 3 Updated','done','2025-08-20 03:40:40'),(8,8,'New Task 1 updated','done','2025-08-20 04:46:23'),(9,8,'Final Task','in_progress','2025-08-20 04:47:49'),(10,8,'Old Task','in_progress','2025-08-20 04:48:44'),(11,8,'asfsfsfa','in_progress','2025-08-20 06:22:06');
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('member','manager') NOT NULL DEFAULT 'member',
  `academic_year` varchar(20) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Don Gehan Chamikara Liyanage','iit22002@std.uwu.ac.lk','$2y$10$PygaX5TrtCIS72qfyz8w.OoPISyCi4qaORiUq2dLkt1VjLCaEald6','manager',NULL,NULL,NULL,'2025-08-08 15:55:04',NULL),(2,'Pubudu Gunawardhana','iit22010@std.uwu.ac.lk','$2y$10$oqBn/FBlw71EYWwFpRRqsubJL7bgA6/NTx/S2M5IcKsPyQTA2/ZD.','member',NULL,NULL,NULL,'2025-08-08 16:41:58',NULL),(3,'Manula Chandupa','iit22011@std.uwu.ac.lk','$2y$10$DuwSt6EYvCt5RS6xFJK90uVO6kgNQfbhawv1WF3r3D/Fz4ZGRJQPm','manager',NULL,NULL,NULL,'2025-08-08 16:46:27',NULL),(4,'Manager 01','udara@std.uwu.ac.lk','$2y$10$8OI43fhbQNiWgZmq.xJzeukNLM4cIzh/xwIwLhaSz5tTp.n721xke','manager','2nd Year','This is a sample bio update','http://localhost/uwu_pms_backend-main/uploads/profile_images/profile_4_1755850409_68a826a9e845b.jpg','2025-08-11 10:14:55','2025-08-22 08:13:30'),(5,'Member 01','mem1@std.uwu.ac.lk','$2y$10$8iEhUlUikg2pdP.nNYaZ0u//xiOSvySq9kMiyhy8G0TWC7AJZfWmu','member','2nd Year','This is a sample','http://localhost/uwu_pms_backend-main/uploads/profile_images/profile_5_1755850467_68a826e3f06d1.jpg','2025-08-15 04:41:49','2025-08-22 08:14:28');
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

-- Dump completed on 2025-08-23 17:29:37
