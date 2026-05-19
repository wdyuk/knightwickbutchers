CREATE TABLE IF NOT EXISTS `migration_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `checksum` char(64) NOT NULL,
  `applied_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `migration_history_filename_unique` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `homepage_popup` (
  `id` int(10) unsigned NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `content` mediumtext,
  `button_text` varchar(255) DEFAULT NULL,
  `button_url` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `homepage_popup` (`id`, `enabled`, `title`, `content`, `button_text`, `button_url`, `updated_at`)
SELECT 1, 0, '', '', '', '', NOW()
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `homepage_popup` WHERE `id` = 1
);
