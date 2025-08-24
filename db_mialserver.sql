-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.10.0.7000
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
CREATE DATABASE IF NOT EXISTS `db_mailserver` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `db_mailserver`;

-- Dumping structure for table db_mailserver.attachments
CREATE TABLE IF NOT EXISTS `attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_mailserver.attachments: ~4 rows (approximately)
INSERT INTO `attachments` (`id`, `email_id`, `file_name`, `file_path`, `file_size`, `file_type`) VALUES
	(3, 2, 'mail.html', '6854e4e8ec97e_mail.html', 64469, 'text/html'),
	(4, 2, '40030440.pdf', '6854e4e8ecb5a_40030440.pdf', 313872, 'application/pdf'),
	(5, 3, 'mail.html', '685ccc191ec6c_mail.html', 64469, 'text/html'),
	(6, 3, 'local_unit.zip', '685ccc53455a4_local_unit.zip', 12952350, 'application/x-zip-compressed');

-- Dumping structure for table db_mailserver.emails
CREATE TABLE IF NOT EXISTS `emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` varchar(36) DEFAULT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) DEFAULT NULL,
  `to_recipients` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`to_recipients`)),
  `to_ids_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`to_ids_json`)),
  `subject` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `cc_recipients` text DEFAULT NULL,
  `cc_ids_json` text DEFAULT NULL,
  `folder` enum('inbox','sent','drafts','trash') NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `conversation_id` (`conversation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_mailserver.emails: ~0 rows (approximately)
INSERT INTO `emails` (`id`, `conversation_id`, `sender_id`, `recipient_id`, `to_recipients`, `to_ids_json`, `subject`, `content`, `cc_recipients`, `cc_ids_json`, `folder`, `is_read`, `created_at`) VALUES
	(2, 'affec13c-c778-4bdf-a563-fa2557457536', 2, NULL, NULL, '["3","1"]', 'terstr', 'sasaf', NULL, '["1","3"]', 'sent', 0, '2025-06-20 04:38:24'),
	(3, NULL, 1, NULL, NULL, '["2"]', '', '', NULL, '[]', 'drafts', 0, '2025-06-26 04:27:05');

-- Dumping structure for table db_mailserver.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_mailserver.users: ~3 rows (approximately)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `token`, `created_at`) VALUES
	(1, 'Krishna Bahadur Raut', 'rautkrishna575@gmail.com', '$2y$10$GhdOy74ZK9dmAYI722KV4eadWC2NfsJ.rokRlbFFJcNRpTvxMsssu', NULL, '2025-06-19 11:51:00'),
	(2, 'saurav karki', 'skarki@gmail.com', '$2y$10$Y0kQ1b8WmkbBenkxfjZw3enpvKaV2mZjOnPZGTAm65BDDatxzRDkO', 'b720a4a6cca5832de68234e3e151d63defc579d0794058fc7c7c00163b6d9984', '2025-06-19 12:53:29'),
	(3, 'niraj adhikari', 'niraj@gmail.com', '$2y$10$xkszukaqhoepr2S8IWKdN.nSkZIiyH44niI.tU8nHV7Tv0sORNYWS', '514a7bce7ef8712a2ee34ed6f12ceecbd8eaff4d0b39d9240872a13a038b9522', '2025-06-19 12:57:51');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
