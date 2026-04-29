CREATE TABLE IF NOT EXISTS `g5_site_page` (
  `page_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `content` mediumtext NOT NULL,
  `content_format` varchar(20) NOT NULL DEFAULT 'html',
  `access_level` tinyint(4) NOT NULL DEFAULT '1',
  `show_pc` tinyint(1) NOT NULL DEFAULT '1',
  `show_mobile` tinyint(1) NOT NULL DEFAULT '1',
  `list_order` int(11) NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`page_id`),
  UNIQUE KEY `uq_slug` (`slug`),
  KEY `idx_status_order` (`status`, `list_order`, `page_id`),
  KEY `idx_access_device` (`status`, `access_level`, `show_pc`, `show_mobile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
