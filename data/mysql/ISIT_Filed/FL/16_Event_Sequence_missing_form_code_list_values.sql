-- Missing values for event sequence
SET @formCodeListId = (SELECT form_code_list_id FROM form_code_list WHERE name ='Event_Sequence' ORDER BY form_code_list_id DESC LIMIT 1);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("40", "Equipment Failure (blown tire, brake failure, etc.)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("41", "Separation of Units");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("42", "Ran Off Roadway, Right");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("43", "Ran Off Roadway, Left");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("44", "Cross Median");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("45", "Cross Centerline");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("46", "Downhill Runaway");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId, @formCodePairId);