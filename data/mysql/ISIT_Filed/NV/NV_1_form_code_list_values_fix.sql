USE ecrash_v3;

SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'NV' LIMIT 1);

-- Form code list value mappings
SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default NV' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);

SET @name = 'Citation_Type', @note = 'Default NV - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

UPDATE `form_code_list` SET `is_multiselect` = 0 WHERE `form_code_list_id` = @formCodelistId;

-- select * from form_field where name = 'Citation_Type' AND path like 'Citations/%';
DELETE FROM form_field where name = 'Citation_Type' AND path like 'Citations/%';
-- select * from form_field where name = 'Code' AND path like '%Citation_Type';
DELETE FROM form_field where name = 'Code' AND path like '%Citation_Type';
-- select * from form_field where name = 'Description' AND path like '%Citation_Type';
DELETE FROM form_field where name = 'Description' AND path like '%Citation_Type';