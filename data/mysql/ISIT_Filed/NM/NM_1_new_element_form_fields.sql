
-- New element eCrash DB field: Incident/Crash_Type

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'crashType' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'CrashType', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('CrashType', 'Incident', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/CrashType/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/CrashType/[a]', 1, @formFieldCommonId, 0, 0);


-- New element eCrash DB field: Incident/Traffic_Control_Device_Type

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'trafficControlDeviceType' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'TrafficControlDeviceType', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('TrafficControlDeviceType', 'Incident', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/TrafficControlDeviceType/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/TrafficControlDeviceType/[a]', 1, @formFieldCommonId, 0, 0);


-- New element eCrash DB field: People/Alcohol_Drug_Test_Result

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'alcoholDrugTestResult' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'AlcoholDrugTestResult', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('AlcoholDrugTestResult', 'People/[a]', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/AlcoholDrugTestResult', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/AlcoholDrugTestResult', 1, @formFieldCommonId, 0, 0);


-- New element eCrash DB field: People/Seating_Position

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'seatingPosition' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'SeatingPosition', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('SeatingPosition', 'People/[a]', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/SeatingPosition', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/SeatingPosition', 1, @formFieldCommonId, 0, 0);


-- New element eCrash DB field: Vehicles/Body_Type_Category

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'bodyTypeCategory' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'BodyTypeCategory', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('BodyTypeCategory', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Vehicles/[a]/BodyTypeCategory', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Vehicles/[a]/BodyTypeCategory', 1, @formFieldCommonId, 0, 0);