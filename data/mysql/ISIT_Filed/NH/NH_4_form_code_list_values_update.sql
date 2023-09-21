USE ecrash_v3;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Condition_At_Time_Of_Crash' AND note ='Default NH - New');
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '2' AND `description` = 'Had Been Dirking');
UPDATE `form_code_pair` SET `description` = 'Had Been Drinking' WHERE `form_code_pair_id` = @formCodePairId;