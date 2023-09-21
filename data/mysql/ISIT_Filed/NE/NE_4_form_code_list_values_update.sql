USE ecrash_v3;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Contributing_Circumstances_Person' AND note ='Default NE - NEW');
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "1" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);
UPDATE `form_code_pair` SET `code` = '01' WHERE form_code_pair_id = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "2" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);
UPDATE `form_code_pair` SET `code` = '02' WHERE form_code_pair_id = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "3" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);
UPDATE `form_code_pair` SET `code` = '03' WHERE form_code_pair_id = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "4" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);
UPDATE `form_code_pair` SET `code` = '04' WHERE form_code_pair_id = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "5" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);
UPDATE `form_code_pair` SET `code` = '05' WHERE form_code_pair_id = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "6" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);
UPDATE `form_code_pair` SET `code` = '06' WHERE form_code_pair_id = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "7" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);
UPDATE `form_code_pair` SET `code` = '07' WHERE form_code_pair_id = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "8" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);
UPDATE `form_code_pair` SET `code` = '08' WHERE form_code_pair_id = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "9" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);
UPDATE `form_code_pair` SET `code` = '09' WHERE form_code_pair_id = @formCodePairId;


SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Non_Motorist_Actions_At_Time_Of_Crash' AND note ='Default NE - NEW');
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '01' AND `description` = 'Entering or crossing sepcified location');
UPDATE `form_code_pair` SET `description` = 'Entering or crossing specified location' WHERE `form_code_pair_id` = @formCodePairId;