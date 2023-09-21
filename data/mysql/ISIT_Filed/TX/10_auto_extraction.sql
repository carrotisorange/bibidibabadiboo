DROP TABLE IF EXISTS `auto_extraction_data`;
CREATE TABLE `auto_extraction_data` (
  `auto_extraction_data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `report_id` int(10) unsigned NOT NULL,
  `entry_stage_id` int(10) unsigned NOT NULL,
  `original_entry_data` blob NOT NULL,
  `entry_data` blob NOT NULL,
  PRIMARY KEY (`auto_extraction_data_id`),
  KEY `idx_auto_extraction_data_reportid` (`report_id`),
  CONSTRAINT `fk_auto_extraction_data_reportid` FOREIGN KEY (`report_id`) REFERENCES `report` (`report_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `auto_extraction_image_process`;
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
  UNIQUE KEY `idx_auto_extraction_image_process_reportid` (`report_id`),
  KEY `idx_auto_extraction_image_process_worktypeid` (`work_type_id`),
  CONSTRAINT `fk_auto_extraction_image_process_reportid` FOREIGN KEY (`report_id`) REFERENCES `report` (`report_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_auto_extraction_image_process_worktypeid` FOREIGN KEY (`work_type_id`) REFERENCES `work_type` (`work_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Autoextraction Data
-- ALTER TABLE `auto_extraction_data` ADD COLUMN `original_entry_data` BLOB NOT NULL BEFORE `entry_data`;

ALTER TABLE report ADD COLUMN is_auto_keyed TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE report ADD COLUMN is_auto_extracted TINYINT(1) NOT NULL DEFAULT 0;

-- No need to execute in UA, QC and PROD
-- ALTER TABLE `report_entry` DROP COLUMN `is_auto_keyed`;
-- ALTER TABLE `report_entry` DROP COLUMN `is_auto_extracted`;