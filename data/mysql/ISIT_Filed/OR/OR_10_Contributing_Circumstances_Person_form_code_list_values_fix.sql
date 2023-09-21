USE ecrash_v3;


SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Contributing_Circumstances_Person' AND is_multiselect = 1 AND note ='Default OR - New');

-- Updating first letter for the first word to upper case 
SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`description` = "improper passing" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

UPDATE  `form_code_pair`  SET   `description` = 'Improper passing'  WHERE form_code_pair_id = @formCodePairId ;
