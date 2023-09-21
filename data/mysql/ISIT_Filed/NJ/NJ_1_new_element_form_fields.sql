USE ecrash_v3;

-- New element eCrash DB field: Alcohol_Drug_Test_Given

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'alcoholDrugTestGiven' and path like 'person%');

INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`,`is_code_value_pair`) VALUES ('AlcoholDrugTestGiven', 'People/[a]', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/AlcoholDrugTestGiven/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/AlcoholDrugTestGiven/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'safetyEquipmentAvailableOrUsed' and path like 'person%');

INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`,`is_code_value_pair`) VALUES ('SafetyEquipmentAvailableOrUsed', 'People/[a]', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/SafetyEquipmentAvailableOrUsed/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/SafetyEquipmentAvailableOrUsed/[a]', 1, @formFieldCommonId, 0, 0);
