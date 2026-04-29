-- Community schema for G5 Codex.
-- Replace `g5_` with the configured G5_TABLE_PREFIX before applying when needed.

CREATE TABLE IF NOT EXISTS `g5_community_config` (
  `config_key` varchar(100) NOT NULL DEFAULT '',
  `config_value` text NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`config_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `g5_community_board` (
  `board_id` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `read_level` tinyint(4) NOT NULL DEFAULT '1',
  `write_level` tinyint(4) NOT NULL DEFAULT '2',
  `comment_level` tinyint(4) NOT NULL DEFAULT '2',
  `list_order` int(11) NOT NULL DEFAULT '0',
  `use_category` tinyint(1) NOT NULL DEFAULT '0',
  `use_latest` tinyint(1) NOT NULL DEFAULT '1',
  `use_comment` tinyint(1) NOT NULL DEFAULT '1',
  `use_mail_post` tinyint(1) NOT NULL DEFAULT '1',
  `use_mail_comment` tinyint(1) NOT NULL DEFAULT '1',
  `mail_admin` tinyint(1) NOT NULL DEFAULT '0',
  `upload_file_count` int(11) NOT NULL DEFAULT '0',
  `upload_file_size` int(11) NOT NULL DEFAULT '0',
  `upload_extensions` varchar(255) NOT NULL DEFAULT '',
  `use_point` tinyint(1) NOT NULL DEFAULT '0',
  `point_write` int(11) NOT NULL DEFAULT '0',
  `point_comment` int(11) NOT NULL DEFAULT '0',
  `point_read` int(11) NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`board_id`),
  KEY `idx_status_order` (`status`, `list_order`),
  KEY `idx_latest` (`use_latest`, `status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `g5_community_board_category` (
  `category_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `board_id` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `list_order` int(11) NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `uq_board_category_name` (`board_id`, `name`),
  KEY `idx_board_order` (`board_id`, `status`, `list_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `g5_community_post` (
  `post_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `board_id` varchar(50) NOT NULL DEFAULT '',
  `category_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `mb_id` varchar(20) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` mediumtext NOT NULL,
  `content_format` varchar(20) NOT NULL DEFAULT 'html',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `is_notice` tinyint(1) NOT NULL DEFAULT '0',
  `notice_order` int(11) NOT NULL DEFAULT '0',
  `notice_started_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `notice_ended_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_secret` tinyint(1) NOT NULL DEFAULT '0',
  `comment_count` int(11) NOT NULL DEFAULT '0',
  `view_count` int(11) NOT NULL DEFAULT '0',
  `attachment_count` int(11) NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'published',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_activity_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`post_id`),
  KEY `idx_board_list` (`board_id`, `status`, `last_activity_at`, `post_id`),
  KEY `idx_board_notice` (`board_id`, `status`, `is_notice`, `notice_order`, `notice_started_at`, `notice_ended_at`, `post_id`),
  KEY `idx_board_category` (`board_id`, `category_id`, `status`, `last_activity_at`, `post_id`),
  KEY `idx_board_created` (`board_id`, `status`, `created_at`, `post_id`),
  KEY `idx_status_created` (`status`, `created_at`, `post_id`),
  KEY `idx_admin_created` (`created_at`, `post_id`),
  KEY `idx_author` (`mb_id`, `created_at`),
  KEY `idx_updated` (`updated_at`, `post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `g5_community_comment` (
  `comment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `mb_id` varchar(20) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'published',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`comment_id`),
  KEY `idx_post_list` (`post_id`, `status`, `comment_id`),
  KEY `idx_status_comment` (`status`, `comment_id`),
  KEY `idx_author` (`mb_id`, `created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `g5_community_latest_index` (
  `latest_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(20) NOT NULL DEFAULT 'board',
  `board_id` varchar(50) NOT NULL DEFAULT '',
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `mb_id` varchar(20) NOT NULL DEFAULT '',
  `comment_count` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_activity_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`latest_id`),
  UNIQUE KEY `uq_scope_post` (`scope`, `board_id`, `post_id`),
  KEY `idx_scope_latest` (`scope`, `board_id`, `last_activity_at`, `post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `g5_community_point_wallet` (
  `mb_id` varchar(20) NOT NULL DEFAULT '',
  `balance` int(11) NOT NULL DEFAULT '0',
  `earned_total` int(11) NOT NULL DEFAULT '0',
  `spent_total` int(11) NOT NULL DEFAULT '0',
  `expired_total` int(11) NOT NULL DEFAULT '0',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`mb_id`),
  KEY `idx_balance` (`balance`, `mb_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `g5_community_point_ledger` (
  `ledger_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mb_id` varchar(20) NOT NULL DEFAULT '',
  `amount` int(11) NOT NULL DEFAULT '0',
  `balance_after` int(11) NOT NULL DEFAULT '0',
  `reason` varchar(50) NOT NULL DEFAULT '',
  `target_type` varchar(50) NOT NULL DEFAULT '',
  `target_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `expires_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` varchar(20) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ledger_id`),
  KEY `idx_member_ledger` (`mb_id`, `ledger_id`),
  KEY `idx_target` (`target_type`, `target_id`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `g5_community_point_available` (
  `available_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mb_id` varchar(20) NOT NULL DEFAULT '',
  `source_ledger_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `amount_total` int(11) NOT NULL DEFAULT '0',
  `amount_remaining` int(11) NOT NULL DEFAULT '0',
  `expires_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`available_id`),
  KEY `idx_member_available` (`mb_id`, `expires_at`, `available_id`),
  KEY `idx_available_expiry` (`expires_at`, `available_id`),
  KEY `idx_source_ledger` (`source_ledger_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `g5_community_attachment` (
  `attachment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `storage` varchar(20) NOT NULL DEFAULT 'local',
  `path` varchar(255) NOT NULL DEFAULT '',
  `original_name` varchar(255) NOT NULL DEFAULT '',
  `mime_type` varchar(100) NOT NULL DEFAULT '',
  `file_size` bigint(20) unsigned NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`attachment_id`),
  KEY `idx_post_status` (`post_id`, `status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `g5_community_scrap` (
  `scrap_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mb_id` varchar(20) NOT NULL DEFAULT '',
  `board_id` varchar(50) NOT NULL DEFAULT '',
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`scrap_id`),
  UNIQUE KEY `uq_member_post` (`mb_id`, `post_id`),
  KEY `idx_member_created` (`mb_id`, `created_at`, `scrap_id`),
  KEY `idx_post` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
