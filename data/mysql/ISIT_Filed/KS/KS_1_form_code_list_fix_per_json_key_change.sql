USE ecrash_v3;

SET @stateId = (SELECT state_id FROM state WHERE name_abbr = 'KS' LIMIT 1);

SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default KS' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);


SET @name = 'Alcohol_Drug_Test_Type', @note = 'Default KS - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
UPDATE `form_code_list` SET `name` = 'Alcohol_Test_Type' WHERE `form_code_list_id` = @formCodelistId;