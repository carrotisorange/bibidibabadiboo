USE ecrash_v3;


SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Alcohol_Drug_Test_Type' AND note ='Default KS - New');
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = '01' AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);
UPDATE `form_code_pair` SET `description` = 'Evidential Test (breath, blood, etc)' WHERE `form_code_pair_id` = @formCodePairId AND `description` = 'Evedential Test (breath, blood, etc)';

