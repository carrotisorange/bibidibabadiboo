USE ecrash_v3;


-- New element eCrash DB field: Pedalcyclist_Actions_At_Time_Of_Crash
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PedalcyclistActionsAtTimeOfCrash' 
and path like 'people%');

SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'PedalcyclistActionsAtTimeOfCrash', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_code_value_pair`) VALUES ('PedalcyclistActionsAtTimeOfCrash', 'People/[a]', 1, @formFieldCommonId, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('Code', 'People/PedalcyclistActionsAtTimeOfCrash/[a]', 1, @formFieldCommonId);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('Description', 'People/PedalcyclistActionsAtTimeOfCrash/[a]', 1, @formFieldCommonId);

-- New element eCrash DB field: Pedestrian_Actions_At_Time_Of_Crash
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PedestrianActionsAtTimeOfCrash' 
and path like 'people%');

SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'PedestrianActionsAtTimeOfCrash', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_code_value_pair`) VALUES ('PedestrianActionsAtTimeOfCrash', 'People/[a]', 1, @formFieldCommonId, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('Code', 'People/PedestrianActionsAtTimeOfCrash/[a]', 1, @formFieldCommonId);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('Description', 'People/PedestrianActionsAtTimeOfCrash/[a]', 1, @formFieldCommonId);

-- To drop newly[temporarily] created function
DROP FUNCTION IF EXISTS ufn_get_form_field_common_id;
