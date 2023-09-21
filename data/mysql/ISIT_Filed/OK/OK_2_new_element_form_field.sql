USE ecrash_v3;

-- New element eCrash DB field: Alcohol_Drug_Test_Type
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'AlcoholDrugTestType' and path like 'people%');

SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'AlcoholDrugTestType', 'people/[a]'));


INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`,`is_code_value_pair`) VALUES ('AlcoholDrugTestType', 'People/[a]', 1, @formFieldCommonId, 0, 0,1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/AlcoholDrugTestType/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/AlcoholDrugTestType/[a]', 1, @formFieldCommonId, 0, 0);
