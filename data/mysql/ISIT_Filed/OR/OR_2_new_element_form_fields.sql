USE ecrash_v3;


-- New element eCrash DB field: Passenger_Actions_At_Time_Of_Crash
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PassengerActionsAtTimeOfCrash' 
and path like 'people%');

SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'PassengerActionsAtTimeOfCrash', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_code_value_pair`) VALUES ('PassengerActionsAtTimeOfCrash', 'People/[a]', 1, @formFieldCommonId, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('Code', 'People/PassengerActionsAtTimeOfCrash/[a]', 1, @formFieldCommonId);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('Description', 'People/PassengerActionsAtTimeOfCrash/[a]', 1, @formFieldCommonId);

-- To drop newly[temporarily] created function
DROP FUNCTION IF EXISTS ufn_get_form_field_common_id;
