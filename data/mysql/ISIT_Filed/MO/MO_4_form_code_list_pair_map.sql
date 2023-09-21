USE ecrash_v3;

-- To remove unwanted option Dry from Weather condition.
SET @formCodeListId = (SELECT `form_code_list_id` FROM `form_code_list` WHERE `name` = "Weather_Condition" AND `note` = "Default MO - New");

SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "Dry" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

DELETE FROM form_code_list_pair_map WHERE form_code_pair_id = @formCodePairId AND form_code_list_id = @formCodeListId;