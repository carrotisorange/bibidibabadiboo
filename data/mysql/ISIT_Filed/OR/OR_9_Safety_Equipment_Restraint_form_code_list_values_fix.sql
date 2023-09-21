USE ecrash_v3;


SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Safety_Equipment_Restraint' AND is_multiselect = 1 AND note ='Default OR - New');

-- Updating first letter for the first word to upper case for abag
SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "Lap/Shoulder" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

UPDATE  `form_code_pair`  SET `code` = 'Lap/Shldr',`description` = 'Lap/Shldr'   WHERE form_code_pair_id = @formCodePairId ;


SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "abag-deplyd" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

UPDATE  `form_code_pair`  SET `code` = 'A/Bag-deplyd',`description` = 'A/Bag-deplyd'   WHERE form_code_pair_id = @formCodePairId ;



SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "Abag-not dp" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

UPDATE  `form_code_pair`  SET `code` = 'A/Bag-not dp',`description` = 'A/Bag-not dp'   WHERE form_code_pair_id = @formCodePairId ;

