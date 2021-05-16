CREATE TABLE IF NOT EXISTS `#__template_overrides` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `template` varchar(50) NOT NULL DEFAULT '',
  `hash_id` varchar(255) NOT NULL DEFAULT '',
  `extension_id` int DEFAULT 0,
  `state` tinyint NOT NULL DEFAULT 0,
  `action` varchar(50) NOT NULL DEFAULT '',
  `client_id` tinyint unsigned NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `modified_date` datetime,
  PRIMARY KEY (`id`),
  KEY `idx_template` (`template`),
  KEY `idx_extension_id` (`extension_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES
(0, 'plg_installer_override', 'plugin', 'override', 'installer', 0, 1, 1, 1, '', '', '', 0, '0000-00-00 00:00:00', 4, 0),
(0, 'plg_quickicon_overridecheck', 'plugin', 'overridecheck', 'quickicon', 0, 1, 1, 1, '', '', '', 0, '0000-00-00 00:00:00', 0, 0);
