-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.27-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.5.0.6680
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for loot
CREATE DATABASE IF NOT EXISTS `loot` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;
USE `loot`;

-- Dumping structure for table loot.activity
CREATE TABLE IF NOT EXISTS `activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player` varchar(20) NOT NULL,
  `package` varchar(20) NOT NULL,
  `points` int(11) NOT NULL,
  `stamp` varchar(15) NOT NULL,
  `IP` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `player` (`player`)
) ENGINE=InnoDB AUTO_INCREMENT=240 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table loot.config
CREATE TABLE IF NOT EXISTS `config` (
  `name` varchar(255) NOT NULL,
  `value` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table loot.images
CREATE TABLE IF NOT EXISTS `images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `public` varchar(255) NOT NULL,
  `private` varchar(255) NOT NULL,
  `tags` varchar(255) NOT NULL,
  `height` int(10) NOT NULL,
  `width` int(10) NOT NULL,
  `ext` varchar(5) NOT NULL,
  `hits` int(10) NOT NULL,
  `ip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table loot.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player` varchar(20) NOT NULL,
  `text` varchar(255) NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT 0,
  `createdate` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table loot.packages
CREATE TABLE IF NOT EXISTS `packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `GUID` varchar(20) NOT NULL,
  `hunt` varchar(20) NOT NULL,
  `found` int(11) NOT NULL DEFAULT 0,
  `worth` int(11) NOT NULL DEFAULT 0,
  `expiry` date NOT NULL,
  `owner` varchar(28) DEFAULT NULL,
  `mayor` varchar(28) DEFAULT NULL,
  `tag` varchar(140) DEFAULT NULL,
  `img` varchar(32) DEFAULT NULL,
  `hint` varchar(140) DEFAULT NULL,
  `IP` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `GUID` (`GUID`),
  KEY `campaign` (`hunt`)
) ENGINE=InnoDB AUTO_INCREMENT=321 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table loot.players
CREATE TABLE IF NOT EXISTS `players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(32) NOT NULL,
  `display` varchar(28) NOT NULL,
  `email` varchar(140) DEFAULT NULL,
  `pass` varchar(64) NOT NULL,
  `tag` varchar(140) DEFAULT NULL,
  `img` varchar(32) DEFAULT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `found` int(11) NOT NULL DEFAULT 0,
  `lotto` date DEFAULT NULL,
  `IP` varchar(50) DEFAULT NULL,
  `reset` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `display` (`display`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table loot.trophies
CREATE TABLE IF NOT EXISTS `trophies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player` varchar(20) NOT NULL,
  `trophy` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `createdate` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
