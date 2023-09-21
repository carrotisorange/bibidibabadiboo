USE ecrash_v3;

SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'SD' LIMIT 1);

SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default SD' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);

SET @name = 'Contributing_Circumstances_Person', @note = 'Default SD - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '07' AND `description` = 'Wrong side or Wrong Way');
UPDATE `form_code_pair` SET `description` = 'Wrong Side or Wrong Way' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '15' AND `description` = 'Failure to keep in Proper Lane');
UPDATE `form_code_pair` SET `description` = 'Failure to Keep in Proper Lane' WHERE `form_code_pair_id` = @formCodePairId;
