USE ecrash_v3;

SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'NM' LIMIT 1);

SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default NM' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);

SET @name = "Contributing_Circumstances_Vehicle", @note = "Default NM - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'Avoid no contact vehicle' AND `description` = 'Avoid no contact vehicle');
UPDATE `form_code_pair` SET `code` = 'Avoid no contact - vehicle', `description` = 'Avoid no contact - vehicle' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'Failed to yield right of way' AND `description` = 'Failed to yield right of way');
UPDATE `form_code_pair` SET `code` = 'Failed to yield right-of-way', `description` = 'Failed to yield right-of-way' WHERE `form_code_pair_id` = @formCodePairId;

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'Under the influence of Drugs or Medication' AND `description` = 'Under the influence of Drugs or Medication');
UPDATE `form_code_pair` SET `code` = 'Under the influence of drugs or medication', `description` = 'Under the influence of drugs or medication' WHERE `form_code_pair_id` = @formCodePairId;



SET @name = "Crash_Type", @note = "Default NM - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'Collision w/Motor Vehicle' AND `description` = 'Collision w/Motor Vehicle');
UPDATE `form_code_pair` SET `code` = 'Collision w/ Motor Vehicle', `description` = 'Collision w/ Motor Vehicle' WHERE `form_code_pair_id` = @formCodePairId;


SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'Collision w/Person' AND `description` = 'Collision w/Person');
UPDATE `form_code_pair` SET `code` = 'Collision w/ Person', `description` = 'Collision w/ Person' WHERE `form_code_pair_id` = @formCodePairId;


SET @name = "Body_Type_Category", @note = "Default NM - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'T2' AND `description` = 'Single Unit Truck (2-axle)');

UPDATE `form_code_pair` SET `description` = 'Single Unit Truck (2-axle and GVWR more than 10,000 lbs)' WHERE `form_code_pair_id` = @formCodePairId;


SET @name = "Condition_At_Time_Of_Crash", @note = "Default NM - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));


SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'Illness' AND `description` = 'Illness');

UPDATE `form_code_pair` SET `code` = 'Illness, Fainted', `description` = 'Illness, Fainted' WHERE `form_code_pair_id` = @formCodePairId;


SET @name = "Trafficway_Description", @note = "Default NM - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'One Way' AND `description` = 'One Way');

UPDATE `form_code_pair` SET `code` = 'One-Way', `description` = 'One-Way' WHERE `form_code_pair_id` = @formCodePairId;



SET @name = "Driver_Actions_At_Time_Of_Crash", @note = "Default NM - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = 'Changing Lances' AND `description` = 'Changing Lances');

UPDATE `form_code_pair` SET `code` = 'Changing Lanes', `description` = 'Changing Lanes' WHERE `form_code_pair_id` = @formCodePairId;















