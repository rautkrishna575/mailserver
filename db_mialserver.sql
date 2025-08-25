-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.20-commercial - MySQL Enterprise Server - Commercial
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for db_mailserver
CREATE DATABASE IF NOT EXISTS `db_mailserver` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_mailserver`;

-- Dumping structure for table db_mailserver.attachments
CREATE TABLE IF NOT EXISTS `attachments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email_id` int NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_size` int NOT NULL,
  `file_type` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_mailserver.attachments: ~7 rows (approximately)
INSERT INTO `attachments` (`id`, `email_id`, `file_name`, `file_path`, `file_size`, `file_type`) VALUES
	(3, 2, 'mail.html', '6854e4e8ec97e_mail.html', 64469, 'text/html'),
	(4, 2, '40030440.pdf', '6854e4e8ecb5a_40030440.pdf', 313872, 'application/pdf'),
	(10, 4, 'mailserver.zip', '68aa9aa7b0693_mailserver.zip', 15633920, 'application/x-zip-compressed'),
	(11, 4, 'liscense.jpeg', '68aa9ac746a19_liscense.jpeg', 136045, 'image/jpeg'),
	(12, 4, 'blue book1.jpeg', '68aaa692c1bc5_blue book1.jpeg', 128308, 'image/jpeg'),
	(14, 6, 'liscense.jpeg', '68aaa7091c0b7_liscense.jpeg', 136045, 'image/jpeg'),
	(15, 19, 'Trace.jpg', '68ac308711b8b_Trace.jpg', 417729, 'image/jpeg');

-- Dumping structure for table db_mailserver.emails
CREATE TABLE IF NOT EXISTS `emails` (
  `id` int NOT NULL AUTO_INCREMENT,
  `conversation_id` varchar(36) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sender_id` int NOT NULL,
  `recipient_id` int DEFAULT NULL,
  `to_recipients` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `to_ids_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `subject` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_general_ci,
  `cc_recipients` text COLLATE utf8mb4_general_ci,
  `cc_ids_json` text COLLATE utf8mb4_general_ci,
  `folder` enum('inbox','sent','drafts','trash') COLLATE utf8mb4_general_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `conversation_id` (`conversation_id`),
  CONSTRAINT `emails_chk_1` CHECK (json_valid(`to_recipients`)),
  CONSTRAINT `emails_chk_2` CHECK (json_valid(`to_ids_json`))
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_mailserver.emails: ~12 rows (approximately)
INSERT INTO `emails` (`id`, `conversation_id`, `sender_id`, `recipient_id`, `to_recipients`, `to_ids_json`, `subject`, `content`, `cc_recipients`, `cc_ids_json`, `folder`, `is_read`, `created_at`) VALUES
	(2, 'affec13c-c778-4bdf-a563-fa2557457536', 2, NULL, NULL, '["3","1"]', 'terstr', 'sasaf', NULL, '["1","3"]', 'sent', 0, '2025-06-20 04:38:24'),
	(4, 'cb1f0d09-b427-4c5b-844f-8b5eab71d392', 1, NULL, NULL, '["2"]', 'Exam-III', 'safdsfsa', NULL, '[]', 'sent', 0, '2025-08-24 05:44:45'),
	(6, '0c84c6ce-c931-47d3-ab87-60b3496916fd', 1, NULL, NULL, '["3"]', 'sdasad', 'asdsadsda', NULL, '[]', 'sent', 0, '2025-08-24 05:45:48'),
	(7, '687ea9b0-3182-4999-8439-0b90c197818e', 1, NULL, NULL, '["3"]', 'asdasd', 'sad', NULL, '[]', 'sent', 0, '2025-08-24 05:46:58'),
	(9, 'e949b221-b0cf-4a67-a8ee-d71d4b77dbb4', 1, NULL, NULL, '["3","2"]', 'Exam-III', 'test', NULL, '[]', 'sent', 0, '2025-08-24 05:52:45'),
	(11, '6f3b7a7f-d381-447f-94a0-040c0f707487', 1, NULL, NULL, '["3"]', 'sadasd', 'sadasdas', NULL, '[]', 'sent', 0, '2025-08-24 05:55:38'),
	(13, '6de3ba49-379d-4d39-a28b-d9ed9f09c5bb', 1, NULL, NULL, '["2"]', 'dasdasd', 'sadsad', NULL, '[]', 'sent', 0, '2025-08-24 05:57:44'),
	(14, 'e6ce10fd-71fc-44b3-804d-18cae9179fa0', 1, NULL, NULL, '["2"]', 'dasdasd', 'sadsad', NULL, '[]', 'sent', 0, '2025-08-24 05:57:49'),
	(15, 'ff142e69-7ee3-4bcb-b530-34e74d8a0452', 1, NULL, NULL, '["2"]', 'dasdasd', 'sadsad', NULL, '[]', 'trash', 0, '2025-08-24 05:59:02'),
	(16, '8f751695-3e90-4cc0-a2eb-6efbd9c8c832', 1, NULL, NULL, '["3"]', 'Exam-III', 'asdasdsad', NULL, '[]', 'trash', 0, '2025-08-24 06:01:27'),
	(18, '03002b52-3fe8-4ae9-996b-b2a04a3cd79d', 1, NULL, NULL, '["3"]', 'Fwd: terstr', '---------- Forwarded message ---------\r\n\r\n        \r\nFrom: saurav karki &amp;amp;lt;skarki@gmail.com&amp;amp;gt;\r\n\r\n        \r\nDate: Jun 20, 2025, 10:23 AM\r\n\r\n        \r\nSubject: terstr\r\n\r\n        \r\nTo: Me\r\n\r\n\r\n    sasaf', NULL, '[]', 'sent', 0, '2025-08-24 09:17:16'),
	(19, 'e94b6213-dbe8-42a8-a4aa-cdcd0d73fedf', 4, NULL, NULL, '["1"]', 'test', 'test', NULL, '[]', 'trash', 0, '2025-08-25 09:44:39');

-- Dumping structure for table db_mailserver.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_otp` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_otp_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_mailserver.users: ~4 rows (approximately)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `token`, `reset_otp`, `reset_otp_expiry`, `created_at`) VALUES
	(1, 'Krishna Bahadur Raut', 'rautkrishna575@gmail.com', '$2y$10$Tut2HDXbJhO9dpyG5Swk1ehxsJQXncFtfcSonC86URg58BsQbNmjK', NULL, NULL, NULL, '2025-06-19 11:51:00'),
	(2, 'saurav karki', 'skarki@gmail.com', '$2y$10$Y0kQ1b8WmkbBenkxfjZw3enpvKaV2mZjOnPZGTAm65BDDatxzRDkO', 'b720a4a6cca5832de68234e3e151d63defc579d0794058fc7c7c00163b6d9984', NULL, NULL, '2025-06-19 12:53:29'),
	(3, 'niraj adhikari', 'niraj@gmail.com', '$2y$10$xkszukaqhoepr2S8IWKdN.nSkZIiyH44niI.tU8nHV7Tv0sORNYWS', '514a7bce7ef8712a2ee34ed6f12ceecbd8eaff4d0b39d9240872a13a038b9522', NULL, NULL, '2025-06-19 12:57:51'),
	(4, 'sanjay', 'sanjayshrestha511@gmail.com', '$2y$10$yS8ahhZw1oc8cmdTWV9xg.kAq8z900Gjyz0BF2XyqYIPG7cozQW1.', NULL, NULL, NULL, '2025-08-25 09:43:19');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
