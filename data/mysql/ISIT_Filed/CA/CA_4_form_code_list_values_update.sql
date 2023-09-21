USE ecrash_v3;
SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'CA' LIMIT 1);

SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default CA' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);


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
