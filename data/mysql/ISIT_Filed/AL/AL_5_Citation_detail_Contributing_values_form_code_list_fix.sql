USE ecrash_v3;


SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Citation_Detail' AND is_multiselect = 1 AND note ='Default AL - New');

-- Set Multiselect as Null
UPDATE  `form_code_list`  SET is_multiselect =NULL  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Citation_Detail' AND is_multiselect IS NULL AND note ='Default AL - New');

SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "145" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

-- Update misspelled Value
UPDATE `form_code_pair` SET `description` = 'Driving a commercial vehicle without first being licensed' WHERE form_code_pair_id = @formCodePairId AND `description` = 'Driving a commercial vehicle without fist being licensed';


SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "131" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

-- Update misspelled Value
UPDATE `form_code_pair` SET `description` = 'No proof of insurance' WHERE form_code_pair_id = @formCodePairId AND `description` = 'No proof of inusrance';


-- Contributing Circumstances Person 
SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Contributing_Circumstances_Person' AND is_multiselect = 1 AND note ='Default AL - New');

SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "99" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

UPDATE `form_code_pair` SET `description` = 'Unknown' WHERE form_code_pair_id = @formCodePairId AND `description` = 'Unknownn';
