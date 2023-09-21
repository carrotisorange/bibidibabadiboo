USE ecrash_v3;

ALTER TABLE `auto_extraction_image_process` ADD COLUMN `is_handwritten` tinyint(1) NOT NULL AFTER `error_desc`;

ALTER TABLE `auto_extraction_image_process` ADD INDEX `idx_auto_extraction_image_process_apiprocessed` (`api_processed`);
ALTER TABLE `auto_extraction_image_process` ADD INDEX `idx_auto_extraction_image_process_senttoml` (`sent_to_ml`);
ALTER TABLE `auto_extraction_image_process` ADD INDEX `idx_auto_extraction_image_process_mlresponse` (`ml_response`);
ALTER TABLE `auto_extraction_image_process` ADD INDEX `idx_auto_extraction_image_process_ishandwritten` (`is_handwritten`);


ALTER TABLE `state_configuration` MODIFY COLUMN `auto_extraction` tinyint(1) DEFAULT 0;
ALTER TABLE `state_configuration` ADD INDEX `idx_stateconfiguration_autoextraction`(`auto_extraction`);
ALTER TABLE `state_configuration` ADD INDEX `idx_stateconfiguration_worktypeidlist`(`work_type_id_list`);
ALTER TABLE `state_configuration` MODIFY COLUMN `state_id` int(10) unsigned NOT NULL;
ALTER TABLE `state_configuration` ADD CONSTRAINT `fk_stateconfiguration_stateid` FOREIGN KEY (`state_id`) REFERENCES `state`(`state_id`);