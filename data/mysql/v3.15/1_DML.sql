USE ecrash_v3;

DROP TABLE IF EXISTS `qc_remark`;
CREATE TABLE qc_remark(
id int unsigned NOT NULL PRIMARY key AUTO_INCREMENT,
report_id  int unsigned NOT NULL,
form_id int unsigned NOT NULL,
state_id int unsigned,
field_name VARCHAR(200) NOT NULL,
criticality enum('critical' , 'minor' , 'major' , 'no_issue') not null,
pass_value VARCHAR(200),
remark_value VARCHAR(200),
report_entry_date_created date,
created_by int(10) NOT NULL,
created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
CONSTRAINT `fk_qc_remark_report_id` FOREIGN KEY (`report_id`) REFERENCES `report` (`report_id`),
CONSTRAINT `fk_qc_remark_form_id` FOREIGN KEY (`form_id`) REFERENCES `form` (`form_id`),
CONSTRAINT `fk_qc_remark_state_id` FOREIGN KEY (`state_id`) REFERENCES `state` (`state_id`)

) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `qc_report_queue`;
CREATE TABLE `qc_report_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `report_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_qc_report_queue_report_id` FOREIGN KEY (`report_id`) REFERENCES `report` (`report_id`),
  CONSTRAINT `fk_qc_report_queue_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
