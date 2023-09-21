USE ecrash_v3;


SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Non_Motorist_Actions_At_Time_Of_Crash' AND is_multiselect = 1 AND note ='Default OR - New');

SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "Disgreard Traffic Sign" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

-- Change the spelling for Disgreard into Disregard
UPDATE  `form_code_pair`  SET `code` = 'Disregard Traffic Sign' , `description` = 'Disregard Traffic Sign'  WHERE form_code_pair_id = @formCodePairId ;

