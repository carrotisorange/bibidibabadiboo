USE ecrash_v3;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Weather_Condition' AND note ='Default IA - New');
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '6' AND `description` = 'Sleet,Hail');
UPDATE `form_code_pair` SET `description` = 'Sleet, Hail' WHERE `form_code_pair_id` = @formCodePairId;


SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Weather_Condition' AND note ='Default IA - New');
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '10' AND `description` = 'Clowing sand, soil,dirt');
UPDATE `form_code_pair` SET `description` = 'Blowing sand, soil,dirt' WHERE `form_code_pair_id` = @formCodePairId;


SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Road_Surface_Condition' AND note ='Default IA - New');
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '6' AND `description` = 'mud, dirt');
UPDATE `form_code_pair` SET `description` = 'Mud, dirt' WHERE `form_code_pair_id` = @formCodePairId;


SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Contributing_Circumstances_Vehicle' AND note ='Default IA - New');
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '7' AND `description` = 'Windows/windshiel');
UPDATE `form_code_pair` SET `description` = 'Windows/windshield' WHERE `form_code_pair_id` = @formCodePairId;