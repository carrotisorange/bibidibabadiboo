-- New element eCrash DB field: Incident/Direction_Of_Impact

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'directionOfImpact' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'DirectionOfImpact', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('DirectionOfImpact', 'Incident', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/DirectionOfImpact/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/DirectionOfImpact/[a]', 1, @formFieldCommonId, 0, 0);


-- New element eCrash DB field: Incident/First_Harmful_Event

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'firstHarmfulEvent' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'FirstHarmfulEvent', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('FirstHarmfulEvent', 'Incident', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/FirstHarmfulEvent/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/FirstHarmfulEvent/[a]', 1, @formFieldCommonId, 0, 0);


-- New element eCrash DB field: Vehicles/Action_On_Impact

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'actionOnImpact' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'ActionOnImpact', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('ActionOnImpact', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Vehicles/[a]/ActionOnImpact', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Vehicles/[a]/ActionOnImpact', 1, @formFieldCommonId, 0, 0);


