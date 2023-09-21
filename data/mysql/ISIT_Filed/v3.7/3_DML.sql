USE ecrash_v3;

SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'CA' LIMIT 1);

SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default CA' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);

SET @name = 'Crash_Type', @note = 'Default CA - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'H' AND `description` = 'Other:*');
UPDATE `form_code_pair` SET  `description` = 'Other*:' WHERE `form_code_pair_id` = @formCodePairId;

UPDATE `form_code_list_pair_map` SET `child_class_name` = 'Crash_Type-H' WHERE `form_code_pair_id` = @formCodePairId and form_code_list_id = @formCodelistId;


SET @name = 'Condition_At_Time_Of_Crash', @note = 'Default CA - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'B' AND `description` = 'Had- Under the Influence');
UPDATE `form_code_pair` SET  `description` = 'HBD - Under the Influence' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'C' AND `description` = 'Had- Not Under the Influence*');
UPDATE `form_code_pair` SET  `description` = 'HBD - Not Under the Influence*' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'D' AND `description` = 'Had - Impairment Unknown*');
UPDATE `form_code_pair` SET  `description` = 'HBD - Impairment Unknown*' WHERE `form_code_pair_id` = @formCodePairId;


SET @name = 'Safety_Equipment_Restraint', @note = 'Default CA - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'P' AND `description` = 'NOT REQUIRED');
UPDATE `form_code_pair` SET  `description` = 'Not Required' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'Q' AND `description` = 'CHILD RESTRAINT IN VEHICLE USED');
UPDATE `form_code_pair` SET  `description` = 'Child Restraint In Vehicle Used' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'R' AND `description` = 'CHILD RESTRAINT IN VEHICLE NOT USED');
UPDATE `form_code_pair` SET  `description` = 'Child Restraint In Vehicle Not Used' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'S' AND `description` = 'CHILD RESTRAINT IN VEHICLE USE UNKNOWN');
UPDATE `form_code_pair` SET  `description` = 'Child Restraint In Vehicle Use Unknown' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'T' AND `description` = 'CHILD RESTRAINT IN VEHICLE IMPROPER USE');
UPDATE `form_code_pair` SET  `description` = 'Child Restraint In Vehicle Improper Use' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'U' AND `description` = 'CHILD RESTRAINT NONE IN VEHICLE');
UPDATE `form_code_pair` SET  `description` = 'Child Restraint None In Vehicle' WHERE `form_code_pair_id` = @formCodePairId;


SET @name = 'Vehicle_Maneuver_Action_Prior', @note = 'Default CA - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'D' AND `description` = 'Marking Right Turn');
UPDATE `form_code_pair` SET  `description` = 'Making Right Turn' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'E' AND `description` = 'Marking Left Turn');
UPDATE `form_code_pair` SET  `description` = 'Making Left Turn' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'F' AND `description` = 'Marking U Turn');
UPDATE `form_code_pair` SET  `description` = 'Making U Turn' WHERE `form_code_pair_id` = @formCodePairId;

-----------------------FL--------------------
SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'FL' LIMIT 1);

SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default FL' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);


SET @name = 'Weather_Condition', @note = 'Default FL - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '4' AND `description` = 'Fog, Smog,Smoke');
UPDATE `form_code_pair` SET  `description` = 'Fog, Smog, Smoke' WHERE `form_code_pair_id` = @formCodePairId;


SET @name = 'Manner_Crash_Impact', @note = 'Default FL - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '4' AND `description` = 'Sidesweipe, same direction');
UPDATE `form_code_pair` SET  `description` = 'Sideswipe, same direction' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '5' AND `description` = 'Sidesweipe, opposite direction');
UPDATE `form_code_pair` SET  `description` = 'Sideswipe, opposite direction' WHERE `form_code_pair_id` = @formCodePairId;


SET @name = 'Unit_Type', @note = 'Default FL - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '20' AND `description` = 'Medium/Heavy Trucks (more than 10,000 lbs (4.536 k');
UPDATE `form_code_pair` SET  `description` = 'Medium/Heavy Trucks (more than 10,000 lbs (4.536 k))' WHERE `form_code_pair_id` = @formCodePairId;


SET @name = 'Vehicle_Maneuver_Action_Prior', @note = 'Default FL - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '6' AND `description` = 'Changing Lances');
UPDATE `form_code_pair` SET  `description` = 'Changing Lanes' WHERE `form_code_pair_id` = @formCodePairId;


SET @name = 'Condition_At_Time_Of_Crash', @note = 'Default FL - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '77' AND `description` = 'other, explain in narrative');
UPDATE `form_code_pair` SET  `description` = 'Other, explain in narrative' WHERE `form_code_pair_id` = @formCodePairId;


-- CA New field
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PhotographsByTemp' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'PhotographsByTemp', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('PhotographsByTemp', 'Incident', 1, @formFieldCommonId, 0, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PhotographsBy' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'PhotographsBy', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('PhotographsBy', 'Incident', 1, @formFieldCommonId, 0, 0, 0);