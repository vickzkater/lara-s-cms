INSERT INTO `sys_languages` (`id`, `name`, `alias`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'English', 'EN', 1, '2020-06-07 13:59:23', '2020-06-07 13:59:23'),
	(2, 'Indonesia', 'ID', 1, '2020-06-07 13:59:42', '2020-06-13 10:23:08');

INSERT INTO `sys_rules` (`id`, `module_id`, `name`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(null, 6, 'Add New', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL),
	(null, 6, 'Edit', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL),
	(null, 6, 'View List', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL),
	(null, 6, 'View Details', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL);