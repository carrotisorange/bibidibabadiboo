USE ecrash_v3;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Contributing_Circumstances_Person' AND is_multiselect = 1 AND note ='Default OK - New');


-- Updating first letter for the first word to upper case for other
SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "37" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

UPDATE  `form_code_pair`  SET `description` = 'Other'   WHERE form_code_pair_id = @formCodePairId ;


-- Updating first letter for the first word to upper case for failed
SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "42" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

UPDATE  `form_code_pair`  SET `description` = 'Failed to stop for school bus'  WHERE form_code_pair_id = @formCodePairId ;


-- Updating  Distraced to Distracted 
SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "70" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

UPDATE `form_code_pair`  SET `description` = 'Inattention -  Distracted by passenger in vehicle'  WHERE form_code_pair_id = @formCodePairId ;


-- Updating  Contributing_Circumstances_Person values  to Contributing_Circumstances_Vehicle 
SET @newformCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Contributing_Circumstances_Vehicle' AND is_multiselect = 1 AND note ='Default OK - New');

update form_code_list_pair_map set  form_code_list_id = @newformCodelistId where form_code_list_id = @formCodelistId; 

-- Delete Contributing_Circumstances_Person for OK state
DELETE FROM form_code_list where form_code_list_id = @formCodelistId; 

