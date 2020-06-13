# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.26)
# Database: larascms_db
# Generation Time: 2019-10-04 04:10:07 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table sys_log_details
# ------------------------------------------------------------

LOCK TABLES `sys_log_details` WRITE;
/*!40000 ALTER TABLE `sys_log_details` DISABLE KEYS */;

INSERT INTO `sys_log_details` (`id`, `action`, `created_at`, `updated_at`)
VALUES
	(1,'Login','2019-10-04 09:00:00','2019-10-04 09:00:00'),
	(2,'Logout','2019-10-04 09:00:00','2019-10-04 09:00:00'),
	(3,'Update profile','2019-10-04 09:00:00','2019-10-04 09:00:00'),
	(4,'Add new user','2019-10-04 09:00:00','2019-10-04 09:00:00'),
	(5,'Edit user details','2019-10-04 09:00:00','2019-10-04 09:00:00'),
	(6,'Delete user','2019-10-04 09:00:00','2019-10-04 09:00:00'),
	(7,'Restore deleted user','2019-10-04 09:00:00','2019-10-04 09:00:00'),
	(8,'Change password','2019-10-04 09:00:00','2019-10-04 09:00:00'),
	(9, 'Add new division', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(10, 'Edit division', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(11, 'Delete division', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(12, 'Restore division', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(13, 'Add new branch', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(14, 'Edit branch', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(15, 'Delete branch', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(16, 'Restore branch', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(17, 'Add new rule', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(18, 'Edit rule', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(19, 'Delete rule', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(20, 'Restore rule', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(21, 'Add new usergroup', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(22, 'Edit usergroup', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(23, 'Delete usergroup', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(24, 'Restore usergroup', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(25, 'Add new brand', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(26, 'Edit brand', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(27, 'Delete brand', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(28, 'Restore brand', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(29, 'Add new language', '2019-11-23 21:27:38', '2019-11-23 21:27:38'),
	(30, 'Edit language', '2019-11-23 21:27:38', '2019-11-23 21:27:38');

/*!40000 ALTER TABLE `sys_log_details` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
