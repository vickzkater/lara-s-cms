# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.26)
# Database: kjv_db
# Generation Time: 2019-10-20 14:35:28 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table sys_modules
# ------------------------------------------------------------

-- LOCK TABLES `sys_modules` WRITE;
/*!40000 ALTER TABLE `sys_modules` DISABLE KEYS */;

INSERT INTO `sys_modules` (`id`, `name`, `status`, `created_at`, `updated_at`)
VALUES
	(1,'Division',1,'2019-10-06 09:00:00','2019-10-06 09:00:00'),
	(2,'Branch',1,'2019-10-06 09:00:00','2019-10-06 09:00:00'),
	(3,'Rule',1,'2019-10-06 09:00:00','2019-10-06 09:00:00'),
	(4,'Usergroup',1,'2019-10-06 09:00:00','2019-10-06 09:00:00'),
	(5,'User',1,'2019-10-06 09:00:00','2019-10-06 09:00:00'),
	(6,'Language',1,'2019-10-26 16:36:00','2019-10-26 16:36:00'),
	(7,'Dictionary',1,'2019-10-26 16:36:00','2019-10-26 16:36:00'),
	(8,'System Log',1,'2019-11-14 20:54:00','2019-11-14 20:54:00'),
	(9,'Banner',1,'2019-10-06 09:00:00','2019-10-06 09:00:00'),
	(10,'Customer',1,'2019-10-06 09:00:00','2019-10-06 09:00:00'),
	(11,'Brand',1,'2019-10-06 09:00:00','2019-10-06 09:00:00'),
	(12,'Topic',1,'2019-12-29 11:04:00','2019-12-29 11:04:00'),
	(13,'Article',1,'2019-12-29 11:04:00','2019-12-29 11:04:00');

/*!40000 ALTER TABLE `sys_modules` ENABLE KEYS */;
-- UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
