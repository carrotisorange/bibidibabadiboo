CREATE TABLE `state_configuration` (
  `state_configuration_id` int(11) NOT NULL AUTO_INCREMENT,
  `state_id` int(11) NOT NULL,
  `auto_extraction` int(11) DEFAULT NULL,
  `work_type_id_list` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`state_configuration_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE report_entry_data ADD COLUMN new_app TINYINT(1) NOT NULL DEFAULT 0;