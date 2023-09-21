USE ecrash_v3;

-- To remove the duplicate entries in production
-- SELECT * FROM form_code_list WHERE name = 'Weather_Condition' AND note = 'Default AZ - New'; 
SELECT @maxFormCodeListId := MAX(form_code_list_id), @minFormCodeListId := MIN(form_code_list_id) FROM form_code_list WHERE name = 'Weather_Condition' AND note = 'Default AZ - New';
DELETE FROM form_code_list WHERE form_code_list_id != @minFormCodeListId AND form_code_list_id = @maxFormCodeListId AND name = 'Weather_Condition' AND note = 'Default AZ - New';

-- SELECT * FROM form_code_list WHERE name = 'Road_Surface_Condition' AND note = 'Default AZ - New';
SELECT @maxFormCodeListId := MAX(form_code_list_id), @minFormCodeListId := MIN(form_code_list_id) FROM form_code_list WHERE name = 'Road_Surface_Condition' AND note = 'Default AZ - New';
DELETE FROM form_code_list WHERE form_code_list_id != @minFormCodeListId AND form_code_list_id = @maxFormCodeListId AND name = 'Road_Surface_Condition' AND note = 'Default AZ - New';


-- SELECT * FROM form_code_list WHERE name = 'Safety_Equipment_Restraint' AND note = 'Default AZ - New';
SELECT @maxFormCodeListId := MAX(form_code_list_id), @minFormCodeListId := MIN(form_code_list_id) FROM form_code_list WHERE name = 'Safety_Equipment_Restraint' AND note = 'Default AZ - New';
DELETE FROM form_code_list WHERE form_code_list_id != @maxFormCodeListId AND form_code_list_id = @minFormCodeListId AND name = 'Safety_Equipment_Restraint' AND note = 'Default AZ - New';

SELECT @maxFormCodeListId := MAX(form_code_list_id), @minFormCodeListId := MIN(form_code_list_id) FROM form_code_list WHERE name = 'Safety_Equipment_Restraint' AND note = 'Default AZ - New';
DELETE FROM form_code_list WHERE form_code_list_id != @maxFormCodeListId AND form_code_list_id = @minFormCodeListId AND name = 'Safety_Equipment_Restraint' AND note = 'Default AZ - New';


 -- SELECT * FROM form_code_list WHERE name = 'Contributing_Circumstances_Vehicle' AND note = 'Default MO - New';
SELECT @maxFormCodeListId := MAX(form_code_list_id), @minFormCodeListId := MIN(form_code_list_id) FROM form_code_list WHERE name = 'Contributing_Circumstances_Vehicle' AND note = 'Default MO - New';
DELETE FROM form_code_list WHERE form_code_list_id != @maxFormCodeListId AND form_code_list_id = @minFormCodeListId AND name = 'Contributing_Circumstances_Vehicle' AND note = 'Default MO - New';

-- -------------------------------------------------------------------------------------------------------------------------

SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'CA' LIMIT 1);

SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default CA' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);


SET @name = 'Vehicle_Maneuver_Action_Prior', @note = 'Default CA - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'K ' AND `description` = 'Parking Maneuver');
UPDATE `form_code_pair` SET  `code` = 'K' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'L '  AND `description` = 'ENTERING TRAFFIC');
UPDATE `form_code_pair` SET  `code` = 'L', description ='Entering Traffic' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'M '  AND `description` = 'Other Unsafe Turning');
UPDATE `form_code_pair` SET  `code` = 'M' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'N ' AND `description` = 'Xing Into Opposing Lane');
UPDATE `form_code_pair` SET  `code` = 'N' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'O ' AND `description` = 'Parked');
UPDATE `form_code_pair` SET  `code` = 'O' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'P '  AND `description` = 'Merging');
UPDATE `form_code_pair` SET  `code` = 'P' WHERE `form_code_pair_id` = @formCodePairId;


SET @name = 'Traffic_Control_Type_At_Intersection', @note = 'Default CA - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'A'  AND `description` = 'Controls Funcitoning');
UPDATE `form_code_pair` SET  `description` = 'Controls Functioning' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'B'  AND `description` = 'Controls Not Funcitoning');
UPDATE `form_code_pair` SET  `description` = 'Controls Not Functioning' WHERE `form_code_pair_id` = @formCodePairId;


SET @name = 'Safety_Equipment_Restraint', @note = 'Default CA - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'K'  AND `description` = 'Occpant Passive Restraint Not Used');
UPDATE `form_code_pair` SET  `description` = 'Occupant Passive Restraint Not Used' WHERE `form_code_pair_id` = @formCodePairId;