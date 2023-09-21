USE ecrash_v3;

SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default NM' LIMIT 1);

ALTER TABLE `ecrash_v3.1_uat`.`form_code_list_pair_map`ADD COLUMN `child_class_name` VARCHAR(128) NULL AFTER `form_code_pair_id`;

SET @name = 'Alcohol_Drug_Use', @note = 'Default NM - NEW', @is_multiselect = 1;

SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'Tested By Instrument' AND fcp.`description` = 'Tested By Instrument');

UPDATE `form_code_list_pair_map` SET `child_class_name` = 'Alcohol_Drug_Use-TestedByInstrument' WHERE `form_code_pair_id` = @formCodePairId and
form_code_list_id = @formCodelistId;


SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'Breath Test Administered' AND fcp.`description` = 'Breath Test Administered');

UPDATE `form_code_list_pair_map` SET `child_class_name` = 'Alcohol_Drug_Use-BreathTestAdministered' WHERE `form_code_pair_id` = @formCodePairId and
form_code_list_id = @formCodelistId;

