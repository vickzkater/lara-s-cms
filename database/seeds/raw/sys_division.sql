INSERT INTO `sys_divisions` (`id`, `name`, `description`, `status`, `ordinal`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(1, 'Lara-S-CMS', NULL, 1, 1, '2020-08-09 16:50:41', '2020-08-09 16:50:41', NULL);

INSERT INTO `sys_rules` (`id`, `module_id`, `name`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(null, 1, 'Add New', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL),
	(null, 1, 'Edit', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL),
	(null, 1, 'Delete', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL),
	(null, 1, 'Restore', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL),
	(null, 1, 'View List', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL),
	(null, 1, 'View Details', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL);
