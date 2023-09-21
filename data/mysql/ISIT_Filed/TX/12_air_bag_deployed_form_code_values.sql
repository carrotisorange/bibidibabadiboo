SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default TX' LIMIT 1);

-- Form code list values for Air_Bag_Deployed
INSERT INTO `form_code_list` (`name`, `note`) VALUES ('Air_Bag_Deployed', 'AutoExtraction TX - New');
SET @formCodeListId = LAST_INSERT_ID();

INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodeListId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1","0");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2","1");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3","1");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4","1");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5","1");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("97","");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("99","");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);