USE ecrash_v3;

-- New element eCrash DB field: weather_condition
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'drugTestStatus' and path like 'people%');

SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'DrugTestStatus', 'People/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('DrugTestStatus', 'People/[a]', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/DrugTestStatus/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/DrugTestStatus/[a]', 1, @formFieldCommonId, 0, 0);

-- To drop newly[temporarily] created function
DROP FUNCTION IF EXISTS ufn_get_form_field_common_id;