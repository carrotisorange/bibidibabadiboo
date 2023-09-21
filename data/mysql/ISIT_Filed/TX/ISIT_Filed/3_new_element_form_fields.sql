-- New element eCrash DB field: Dispatch_Time
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'dispatchedTime' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Dispatch_Time', 'incident'));

-- form_field name should be same as form_code_list to be added
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Dispatch_Time', 'incident', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: weather_condition
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'weatherCondition' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Weather_Condition', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Weather_Condition', 'incident', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: road_condition
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'roadCondition' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Road_Condition', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Road_Condition', 'incident', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Loss_Street_Speed_Limit
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'lossStreetSpeedLimit' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Loss_Street_Speed_Limit', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Loss_Street_Speed_Limit', 'incident', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: contributing_circucmstance_v
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'contributingCircumstancesV' and path like 'vehicle%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Contributing_Circumstances_Vehicle', 'vehicle/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Contributing_Circumstances_Vehicle', 'vehicle/[a]', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: contributing_circucmstance_v
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'contributingCircumstances' and path like 'person%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Contributing_Circumstances_Person', 'person/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Contributing_Circumstances_Person', 'person/[a]', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Safety_Equipment_Restraint1
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'safetyEquipmentRestraint' and path like 'person%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Safety_Equipment_Restraint', 'person/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Safety_Equipment_Restraint', 'person/[a]', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Safety_Equipment_Helmet
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'safetyEquipmentHelmet' and path like 'person%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Safety_Equipment_Helmet', 'person/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Safety_Equipment_Helmet', 'person/[a]', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Ejection
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'ejection' and path like 'person%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Ejection', 'person/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Ejection', 'person/[a]', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Transported_To
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'transportedTo' and path like 'person%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Transported_To', 'person/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Transported_To', 'person/[a]', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: alcohol_test_type
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'alcoholTestType' and path like 'person%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Alcohol_Test_Type', 'person/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Alcohol_Test_Type', 'person/[a]', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: drug_test_type
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'drugTestType' and path like 'person%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Drug_Test_Type', 'person/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Drug_Test_Type', 'person/[a]', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Citation_Detail1
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'citationDetail1' and path like 'citation%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Citation_Detail', 'citations/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Citation_Detail', 'citations/[a]', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Violation_Code1
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'violationCode1' and path like 'citation%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Violation_Code', 'citations/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Violation_Code', 'citations/[a]', 1, @formFieldCommonId, 0, 0);

DROP FUNCTION IF EXISTS ufn_get_form_field_common_id;