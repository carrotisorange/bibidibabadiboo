USE ecrash_v3;

SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'NY' LIMIT 1);

SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default NY' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);

SET @name = "First_Harmful_Event", @note = "Default NY - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND 
fcp.`code` = '30' AND `description` = 'Other Fixed Object* - NON-COLLISION');

UPDATE `form_code_pair` SET `description` = 'Other Fixed Object* - Non-Collision' WHERE `form_code_pair_id` = @formCodePairId;


SET @name = "Contributing_Circumstances_Vehicle", @note = "Default NY - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND 
fcp.`code` = "61" AND `description` = "Animal'S Action");

UPDATE `form_code_pair` SET `description` = "Animal's Action" WHERE `form_code_pair_id` = @formCodePairId;


SET @name = "Vehicle_Maneuver_Action_Prior", @note = "Default NY - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND 
fcp.`code` = '12' AND `description` = 'Changing Lances');

UPDATE `form_code_pair` SET `description` = 'Changing Lanes' WHERE `form_code_pair_id` = @formCodePairId;


