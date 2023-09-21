USE ecrash_v3;

-- Change Spelling from MC/Bike Eye ShieldOnly to MC/Bike Eye Shield Only
SET @formCodeListId = (SELECT `form_code_list_id` FROM `form_code_list` WHERE `name` = "Safety_Equipment_Restraint" AND `note` = "Default MD - NEW");

SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "22" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

UPDATE  `form_code_pair`  SET  `description` = 'MC/Bike Eye Shield Only'  WHERE form_code_pair_id = @formCodePairId ;

