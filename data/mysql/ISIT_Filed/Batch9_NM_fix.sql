USE ecrash_v3;
 
SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default NM' LIMIT 1);
SET @name = 'Alcohol_Drug_Use', @note = 'Default NM - NEW', @is_multiselect = 1;

SET @formCodeListId = (SELECT DISTINCT `fcl`.`form_code_list_id` FROM `form_code_list` `fcl` JOIN `form_code_list_group_map` `fclgm` USING(`form_code_list_id`) WHERE `fclgm`.`form_code_group_id` = @formCodeGroupId AND binary `fcl`.`name` = binary @name AND binary `fcl`.`note` = binary @note AND `fcl`.`is_multiselect` = @is_multiselect ORDER BY `fcl`.`form_code_list_id` DESC LIMIT 1);

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'Tested By Instrument for:' AND fcp.`description` = 'Tested By Instrument for:');

UPDATE `form_code_list_pair_map` SET `child_class_name` = 'Alcohol_Drug_Use-TestedByInstrument' WHERE `form_code_pair_id` = @formCodePairId and
form_code_list_id = @formCodelistId;