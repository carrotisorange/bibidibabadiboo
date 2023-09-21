USE ecrash_v3;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Road_Surface_Condition' AND note ='Default VA - New');
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '3' AND `description` = 'Wet');
UPDATE `form_code_pair` SET `code` = '2' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '4' AND `description` = 'Snowy');
UPDATE `form_code_pair` SET `code` = '3' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '5' AND `description` = 'Icy');
UPDATE `form_code_pair` SET `code` = '4' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '6' AND `description` = 'Muddy');
UPDATE `form_code_pair` SET `code` = '5' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '7' AND `description` = 'Oil/Other Fluids');
UPDATE `form_code_pair` SET `code` = '6' WHERE `form_code_pair_id` = @formCodePairId;

SET @code = "7", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Driver_Actions_At_Time_Of_Crash' AND note ='Default VA - New');
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '14' AND `description` = 'improper turn - wide right turn');
UPDATE `form_code_pair` SET `description` = 'Improper turn - wide right turn' WHERE `form_code_pair_id` = @formCodePairId;


SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Safety_Equipment_Restraint' AND note ='Default VA - New');
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '4' AND `description` = 'Schild restraint');
UPDATE `form_code_pair` SET `description` = 'Child restraint' WHERE `form_code_pair_id` = @formCodePairId;
