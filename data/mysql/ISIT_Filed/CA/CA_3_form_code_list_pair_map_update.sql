USE ecrash_v3;


SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default CA' LIMIT 1);

SET @name = 'Weather_Condition', @note = 'Default CA - New', @is_multiselect = 1;

SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'E' AND fcp.`description`  = 'Fog/Visibility');

UPDATE `form_code_list_pair_map` SET `child_class_name` = 'Weather_Condition-E' WHERE `form_code_pair_id` = @formCodePairId and
form_code_list_id = @formCodelistId;


SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'F' AND fcp.`description`  = 'Other*');

UPDATE `form_code_list_pair_map` SET `child_class_name` = 'Weather_Condition-F' WHERE `form_code_pair_id` = @formCodePairId and
form_code_list_id = @formCodelistId;



SET @name = 'Motor_Vehicle_Involved_With', @note = 'Default CA - New', @is_multiselect = 0;

SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'H' AND fcp.`description`  = 'Animal:');

UPDATE `form_code_list_pair_map` SET `child_class_name` = 'Motor_Vehicle_Involved_With-H' WHERE `form_code_pair_id` = @formCodePairId and
form_code_list_id = @formCodelistId;



SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'I' AND fcp.`description`  = 'Fixed Object:');

UPDATE `form_code_list_pair_map` SET `child_class_name` = 'Motor_Vehicle_Involved_With-I' WHERE `form_code_pair_id` = @formCodePairId and
form_code_list_id = @formCodelistId;



SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'J' AND fcp.`description`  = 'Other Object:');

UPDATE `form_code_list_pair_map` SET `child_class_name` = 'Motor_Vehicle_Involved_With-J' WHERE `form_code_pair_id` = @formCodePairId and
form_code_list_id = @formCodelistId;




SET @name = 'Most_Harmful_Event', @note = 'Default CA - New', @is_multiselect = 1;

SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));


SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'B' AND fcp.`description`  = 'Other Improper Driving*:');

UPDATE `form_code_list_pair_map` SET `child_class_name` = 'Most_Harmful_Event-B' WHERE `form_code_pair_id` = @formCodePairId and
form_code_list_id = @formCodelistId;



SET @name = 'Vehicle_Maneuver_Action_Prior', @note = 'Default CA - New', @is_multiselect = 1;

SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'R' AND fcp.`description`  = 'Other*:');

UPDATE `form_code_list_pair_map` SET `child_class_name` = 'Vehicle_Maneuver_Action_Prior-R' WHERE `form_code_pair_id` = @formCodePairId and
form_code_list_id = @formCodelistId;




SET @name = 'Crash_Type', @note = 'Default CA - New', @is_multiselect = 1;

SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'H' AND fcp.`description`  = 'Other*:');

UPDATE `form_code_list_pair_map` SET `child_class_name` = 'Crash_Type-H' WHERE `form_code_pair_id` = @formCodePairId and
form_code_list_id = @formCodelistId;



SET @name = 'Unusual_Road_Condition', @note = 'Default CA - New', @is_multiselect = 1;

SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'G' AND fcp.`description`  = 'Other*:');

UPDATE `form_code_list_pair_map` SET `child_class_name` = 'Unusual_Road_Condition-G' WHERE `form_code_pair_id` = @formCodePairId and
form_code_list_id = @formCodelistId;

