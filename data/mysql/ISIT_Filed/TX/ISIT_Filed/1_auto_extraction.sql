CREATE TABLE `auto_extraction_data` (
  `auto_extraction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `report_id` int(10) unsigned NOT NULL,
  `entry_stage_id` int(10) unsigned NOT NULL,
  `entry_data` blob NOT NULL,
  PRIMARY KEY (`auto_extraction_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `auto_extraction_image_process` (
  `auto_extraction_image_process_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `report_id` int(10) unsigned NOT NULL,
  `state_code` varchar(2) COLLATE utf8_bin DEFAULT NULL,
  `hash_key` char(64) COLLATE utf8_bin NOT NULL,
  `work_type_id` int(10) unsigned DEFAULT NULL,
  `api_processed` tinyint(1) DEFAULT '0',
  `sent_to_ml` tinyint(1) NOT NULL DEFAULT '0',
  `ml_response` tinyint(1) NOT NULL DEFAULT '0',
  `error_desc` text COLLATE utf8_bin,
  `date_created` timestamp NULL DEFAULT NULL,
  `date_updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`auto_extraction_image_process_id`),
  UNIQUE KEY `report_id_UNIQUE` (`report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
