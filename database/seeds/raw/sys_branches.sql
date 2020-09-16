INSERT INTO `sys_branches` (`id`, `division_id`, `name`, `location`, `gmaps`, `phone`, `status`, `ordinal`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(1, 1, 'Headquarter', NULL, NULL, NULL, 1, 1, '2020-08-09 16:50:59', '2020-08-09 16:50:59', NULL);

INSERT INTO `sys_rules` (`id`, `module_id`, `name`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(null, 2, 'Add New', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL),
	(null, 2, 'Edit', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL),
	(null, 2, 'Delete', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL),
	(null, 2, 'Restore', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL),
	(null, 2, 'View List', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL),
	(null, 2, 'View Details', NULL, 1, '2020-08-09 16:51:34', '2020-08-09 16:51:34', NULL);
