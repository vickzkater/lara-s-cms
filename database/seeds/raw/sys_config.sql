INSERT INTO `sys_config` (`id`, `app_name`, `app_url_site`, `app_url_main`, `app_version`, `app_favicon_type`, `app_favicon`, `app_logo`, `app_logo_image`, `help`, `powered`, `powered_url`, `meta_keywords`, `meta_title`, `meta_description`, `meta_author`, `created_at`, `updated_at`) VALUES
	(1, 'Lara-S-CMS', 'http://localhost/lara-s-cms/public/', 'http://localhost/lara-s-cms/public/', '1.0', 'ico', 'favicon.ico', 'laptop', 'uploads/config/logo-square.png', 'Content Management System for Website Lara-S-CMS', 'KINIDI Tech', 'https://kiniditech.com', 'kiniditech,kinidi tech,kinidi,laravel,larascms,lara-s-cms,php,skeleton,cms,content management system,dashboard,admin,website', 'Lara-S-CMS - a PHP Laravel Skeleton', 'Lara-S-CMS is a PHP Laravel Skeleton for Content Management System/Admin Dashboard (within/without website)', 'KINIDI Tech', '2020-06-22 22:08:07', '2020-06-28 13:33:26');

INSERT INTO `sys_modules` (`id`, `name`, `status`, `created_at`, `updated_at`)
VALUES (null,'Config',1,'2020-06-29 09:00:00','2020-06-29 09:00:00');

INSERT INTO `sys_rules` (`id`, `module_id`, `name`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(null, 10, 'Update', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL);