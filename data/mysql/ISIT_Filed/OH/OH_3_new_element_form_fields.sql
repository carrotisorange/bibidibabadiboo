USE ecrash_v3;

-- New element eCrash DB field: Alcohol_Drug_Use
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'alcoholDrugUse' and path like 'people%');

SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'AlcoholDrugUse', 'People/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('AlcoholDrugUse', 'People/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/AlcoholDrugUse/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/AlcoholDrugUse/[a]', 1, @formFieldCommonId, 0, 0);

-- To drop newly[temporarily] created function
DROP FUNCTION IF EXISTS ufn_get_form_field_common_id;