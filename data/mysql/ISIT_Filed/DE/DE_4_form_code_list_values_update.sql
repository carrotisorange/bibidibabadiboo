USE ecrash_v3;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Transported_To' AND note ='Default DE - NEW');
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "15" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);
UPDATE `form_code_pair` SET `description` = "Veterans' Hospital" WHERE form_code_pair_id = @formCodePairId;
