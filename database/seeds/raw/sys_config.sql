-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.36-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             11.0.0.5919
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping data for table larascms_db.sys_config: ~1 rows (approximately)
/*!40000 ALTER TABLE `sys_config` DISABLE KEYS */;
INSERT INTO `sys_config` (`id`, `app_name`, `app_backend`, `app_url_site`, `app_url_main`, `app_url_api`, `app_version`, `app_favicon_type`, `app_favicon`, `app_logo`, `app_logo_image`, `help`, `powered`, `powered_url`, `meta_keywords`, `meta_title`, `meta_description`, `meta_author`, `created_at`, `updated_at`) VALUES
	(1, 'Lara-S-CMS', 'MODEL', 'http://localhost/lara-s-cms/public/', 'http://localhost/lara-s-cms/public/', NULL, '1.0', 'ico', 'favicon.ico', 'laptop', 'uploads/config/logo-square.png', 'Content Management System for Website Lara-S-CMS', 'KINIDI Tech', 'https://kiniditech.com', 'kiniditech,kinidi tech,kinidi,laravel,larascms,lara-s-cms,php,skeleton,cms,content management system,dashboard,admin,website', 'Lara-S-CMS is a PHP Laravel Skeleton', 'Lara-S-CMS is a PHP Laravel Skeleton for Content Management System/Admin Dashboard (within/without website) using Bootstrap 4 Admin Dashboard Template Gentelella', 'KINIDI Tech', '2020-06-22 22:08:07', '2020-06-28 13:33:26');
/*!40000 ALTER TABLE `sys_config` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
