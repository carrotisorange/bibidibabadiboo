CREATE TABLE `auto_extraction_accuracy` (
    `auto_extraction_accuracy_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user_id` int(10) unsigned NOT NULL,
    `report_id` int(10) unsigned NOT NULL,
    `accuracy_details` blob,
    PRIMARY KEY (`auto_extraction_accuracy_id`),
    KEY `idx_auto_extraction_accuracy_reportid` (`report_id`),
    KEY `idx_auto_extraction_accuracy_userid` (`user_id`),
    KEY `idx_auto_extraction_accuracy_datecreated` (`date_created`),
    CONSTRAINT `fk_auto_extraction_accuracy_reportid` FOREIGN KEY (`report_id`) REFERENCES `report` (`report_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_auto_extraction_accuracy_userid` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;