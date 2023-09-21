USE ecrash_v3;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'CitationType' and path like 'citations%');

INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Citations/[a]/Citation_Type', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Citations/[a]/Citation_Type', 1, @formFieldCommonId, 0, 0);


SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'marijuanaUseSuspected' and path like 'person%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MarijuanaUseSuspected', 'people/[a]'));
UPDATE form_field set form_field_common_id = @FormFieldCommonId where name = 'MarijuanaUseSuspected';
UPDATE form_field set form_field_common_id = @FormFieldCommonId where name = 'Code' and path like 'People/MarijuanaUseSuspected%';
UPDATE form_field set form_field_common_id = @FormFieldCommonId where name = 'Description' and path like 'People/MarijuanaUseSuspected%';


SET @stateId = (SELECT state_id FROM state WHERE name_abbr = 'NV' LIMIT 1);

SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default NV' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);

SET @name = 'Citation_Type', @note = 'Default NV - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @code = "NRS", @description = "NRS";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "CFR", @description = "CFR";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "CC/MC", @description = "CC/MC";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Pending", @description = "Pending";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));