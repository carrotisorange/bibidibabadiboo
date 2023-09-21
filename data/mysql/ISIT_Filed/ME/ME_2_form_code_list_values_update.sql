USE ecrash_v3;

SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'ME' LIMIT 1);

SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default ME' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);


SET @name = 'Non_Motorist_Actions_At_Time_Of_Crash', @note = 'Default ME - New', @is_multiselect = 1;
SET @formCodeListId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '9' AND `description` = 'Not Visible(Dark Clothing, No Lighting)');
UPDATE `form_code_pair` SET `description` = 'Not Visible (Dark Clothing, No Lighting)' WHERE `form_code_pair_id` = @formCodePairId;
