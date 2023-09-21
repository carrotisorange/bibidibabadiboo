USE ecrash_v3;


DROP TABLE IF EXISTS `report_tat_status`;
CREATE TABLE `report_tat_status` (
  `report_tat_status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `report_id` int(10) unsigned NOT NULL,
  `tat_hours` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`report_tat_status_id`),
  UNIQUE KEY `report_id` (`report_id`),
  CONSTRAINT `report_id` FOREIGN KEY (`report_id`) REFERENCES `report` (`report_id`) ON DELETE CASCADE ON UPDATE CASCADE 
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;




ALTER TABLE `work_type` 
ADD COLUMN `tat_hours` TINYINT(10) DEFAULT NULL AFTER `has_coversheet` ;

UPDATE `work_type` SET `tat_hours` = 12 WHERE `name_internal` = 'ecrash' AND `work_type_id` = 1;
UPDATE `work_type` SET `tat_hours` = 24 WHERE `name_internal` = 'cru-goforward' AND `work_type_id` = 3;