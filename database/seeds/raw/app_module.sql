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


# Dump of table app_module
# ------------------------------------------------------------

LOCK TABLES `app_module` WRITE;
/*!40000 ALTER TABLE `app_module` DISABLE KEYS */;

INSERT INTO `app_module` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`, `status`, `isDeleted`)
VALUES
	(1,'User Manager','2019-10-06 09:00:00','2019-10-06 09:00:00',NULL,1,0),
	(2,'Usergroup Manager','2019-10-06 09:00:00','2019-10-06 09:00:00',NULL,1,0),
	(3,'Branch','2019-10-06 09:00:00','2019-10-06 09:00:00',NULL,1,0),
	(4,'Customer','2019-10-06 09:00:00','2019-10-06 09:00:00',NULL,1,0),
	(5,'Brand','2019-10-06 09:00:00','2019-10-06 09:00:00',NULL,1,0),
	(6,'Product','2019-10-06 09:00:00','2019-10-06 09:00:00',NULL,1,0),
	(7,'Banner','2019-10-06 09:00:00','2019-10-06 09:00:00',NULL,1,0),
	(8,'Rule','2019-10-06 09:00:00','2019-10-06 09:00:00',NULL,1,0),
	(9,'Division','2019-10-20 21:34:00','2019-10-20 21:34:00',NULL,1,0);

/*!40000 ALTER TABLE `app_module` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
