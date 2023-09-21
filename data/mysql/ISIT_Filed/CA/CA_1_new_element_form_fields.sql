USE ecrash_v3;


-- New element eCrash DB field: Incident/Accident_Condition

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'accidentCondition' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'AccidentCondition', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('AccidentCondition', 'Incident', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/AccidentCondition/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/AccidentCondition/[a]', 1, @formFieldCommonId, 0, 0);


-- New element eCrash DB field: Incident/Total_Nonfatal_Injuries
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'TotalNonfatalInjuries' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'TotalNonfatalInjuries', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('TotalNonfatalInjuries', 'Incident', 1, @FormFieldCommonId);

-- New element eCrash DB field: Incident/Precinct
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'Precinct' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Precinct', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('Precinct', 'Incident', 1, @FormFieldCommonId);


-- New element eCrash DB field: Incident/Beat
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'Beat' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Beat', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('Beat', 'Incident', 1, @FormFieldCommonId);


-- New element eCrash DB field: Incident/OfficerID
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'OfficerID' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'OfficerID', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('OfficerID', 'Incident', 1, @FormFieldCommonId);


-- New element eCrash DB field: Incident/Total_Fatal_Injuries
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'TotalFatalInjuries' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'TotalFatalInjuries', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('TotalFatalInjuries', 'Incident', 1, @FormFieldCommonId);


-- New element eCrash DB field: Incident/Milepost_Next_Street_Distance
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'MilepostNextStreetDistance' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MilepostNextStreetDistance', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('MilepostNextStreetDistance', 'Incident', 1, @FormFieldCommonId);



-- New element eCrash DB field: Incident/Milepost_Next_Street_Distance_Measure
-- To get form field common id

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'milepostNextStreetDistanceMeasure' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MilepostNextStreetDistanceMeasure', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('MilepostNextStreetDistanceMeasure', 'Incident', 1, @FormFieldCommonId);



-- New element eCrash DB field: Incident/Milepost_Next_Street_Direction
-- To get form field common id

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'milepostNextStreetDirection' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MilepostNextStreetDirection', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('MilepostNextStreetDirection', 'Incident', 1, @FormFieldCommonId);



-- New element eCrash DB field: Incident/Milepost
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'milepost' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Milepost', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('Milepost', 'Incident', 1, @FormFieldCommonId);



-- New element eCrash DB field: Incident/AccidentAtIntersection
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'accidentAtIntersection' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'AccidentAtIntersection', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('AccidentAtIntersection', 'Incident', 1, @FormFieldCommonId);


-- New element eCrash DB field: Incident/NextStreetDistance
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'nextStreetDistance' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'NextStreetDistance', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('NextStreetDistance', 'Incident', 1, @FormFieldCommonId);


-- New element eCrash DB field: Incident/Next_Street_Distance_Measure
-- To get form field common id

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'nextStreetDistanceMeasure' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'NextStreetDistanceMeasure', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('NextStreetDistanceMeasure', 'Incident', 1, @FormFieldCommonId);



-- New element eCrash DB field: Incident/Next_Street_Direction
-- To get form field common id


SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'nextStreetDirection' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'NextStreetDirection', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('NextStreetDirection', 'Incident', 1, @FormFieldCommonId);



-- New element eCrash DB field: Incident/Reference_Markers
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'referenceMarkers' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'ReferenceMarkers', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('ReferenceMarkers', 'Incident', 1, @FormFieldCommonId);


-- New element eCrash DB field: Incident/Most_Harmful_Event

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'MostHarmfulEvent' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MostHarmfulEvent', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('MostHarmfulEvent', 'Incident', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/MostHarmfulEvent/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/MostHarmfulEvent/[a]', 1, @formFieldCommonId, 0, 0);


-- New element eCrash DB field: Incident/Most_Harmful_Event_Other_Description
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'mostHarmfulEventOtherDescription' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MostHarmfulEventOtherDescription', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('MostHarmfulEventOtherDescription', 'Incident', 1, @FormFieldCommonId);


-- New element eCrash DB field: Incident/Weather_Other_Description
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'weatherConditionOtherDescription' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'WeatherConditionOtherDescription', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('WeatherConditionOtherDescription', 'Incident', 1, @FormFieldCommonId);



-- New element eCrash DB field: Incident/Weather_Other_Description2
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'weatherConditionOtherDescription2' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'WeatherConditionOtherDescription2', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('WeatherConditionOtherDescription2', 'Incident', 1, @FormFieldCommonId);



-- New element eCrash DB field: Incident/Unusual_Road_Condition

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'unusualRoadCondition' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'UnusualRoadCondition', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('UnusualRoadCondition', 'Incident', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/UnusualRoadCondition/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/UnusualRoadCondition/[a]', 1, @formFieldCommonId, 0, 0);


-- New element eCrash DB field: Incident/Unusual_Road_Condition_Other_Description
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'unusualRoadConditionOtherDescription' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'UnusualRoadConditionOtherDescription', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('UnusualRoadConditionOtherDescription', 'Incident', 1, @FormFieldCommonId);



-- New element eCrash DB field: Incident/Traffic_Control_Type_At_Intersection

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'trafficControlTypeAtIntersection' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'TrafficControlTypeAtIntersection', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('TrafficControlTypeAtIntersection', 'Incident', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/TrafficControlTypeAtIntersection/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/TrafficControlTypeAtIntersection/[a]', 1, @formFieldCommonId, 0, 0);


-- New element eCrash DB field: Incident/Crash_Type_Description
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'crashTypeDescription' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'CrashTypeDescription', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('CrashTypeDescription', 'Incident', 1, @FormFieldCommonId);


-- New element eCrash DB field: Incident/Motor_Vehicle_Involved_With

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'motorVehicleInvolvedWith' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MotorVehicleInvolvedWith', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('MotorVehicleInvolvedWith', 'Incident', 1, @FormFieldCommonId);




-- New element eCrash DB field: Incident/Motor_Vehicle_Involved_With_Animal_Description
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'motorVehicleInvolvedWithAnimalDescription' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MotorVehicleInvolvedWithAnimalDescription', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('MotorVehicleInvolvedWithAnimalDescription', 'Incident', 1, @FormFieldCommonId);



-- New element eCrash DB field: Incident/Motor_Vehicle_Involved_With_Fixed_Object_Description
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'motorVehicleInvolvedWithFixedObjectDescription' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MotorVehicleInvolvedWithFixedObjectDescription', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('MotorVehicleInvolvedWithFixedObjectDescription', 'Incident', 1, @FormFieldCommonId);




-- New element eCrash DB field: Incident/Motor_Vehicle_Involved_With_Other_Object_Description
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'motorVehicleInvolvedWithOtherObjectDescription' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MotorVehicleInvolvedWithOtherObjectDescription', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('MotorVehicleInvolvedWithOtherObjectDescription', 'Incident', 1, @FormFieldCommonId);



-- New element eCrash DB field: Vehicles/Body_Type_Category

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'damageRating' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'DamageRating', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('DamageRating', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Vehicles/[a]/DamageRating', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Vehicles/[a]/DamageRating', 1, @formFieldCommonId, 0, 0);


-- New element eCrash DB field: Vehicles/Vehicle_Special_Use

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'vehicleSpecialUse' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'vehicleSpecialUse', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('vehicleSpecialUse', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Vehicles/[a]/vehicleSpecialUse', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Vehicles/[a]/vehicleSpecialUse', 1, @formFieldCommonId, 0, 0);



-- New element eCrash DB field: Incident/Vehicle_Maneuver_Action_Prior_Other_Description
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'vehicleManeuverActionPriorOtherDescription' and path like 'vehicles%');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'VehicleManeuverActionPriorOtherDescription', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('VehicleManeuverActionPriorOtherDescription', 'Incident', 1, @FormFieldCommonId);


-- New element eCrash DB field: Incident/Motor_Carrier_ID_State_ID
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'motorCarrierIDStateID' and path like 'vehicles%');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MotorCarrierIDStateID', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('MotorCarrierIDStateID', 'Incident', 1, @FormFieldCommonId);


-- New element eCrash DB field: Incident/Motor_Carrier_ID_DOT_Number
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'motorCarrierIdDotNumber' and path like 'vehicles%');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MotorCarrierIdDotNumber', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('MotorCarrierIdDotNumber', 'Incident', 1, @FormFieldCommonId);



-- New element eCrash DB field: Incident/Other_State_Number
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'otherStateNumber' and path like 'vehicles%');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'OtherStateNumber', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('OtherStateNumber', 'Incident', 1, @FormFieldCommonId);


-- New element eCrash DB field: Incident/Other_State_Number2
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'otherStateNumber2' and path like 'vehicles%');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'OtherStateNumber2', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('OtherStateNumber2', 'Incident', 1, @FormFieldCommonId);


-- New element eCrash DB field: Incident/ICCMC_Number
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'iCCMCNumber' and path like 'vehicles%');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'ICCMCNumber', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('ICCMCNumber', 'Incident', 1, @FormFieldCommonId);


-- New element eCrash DB field: Vehicles/Direction_Of_Travel_Before_Crash

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'directionOfTravelBeforeCrash' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'DirectionOfTravelBeforeCrash', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('DirectionOfTravelBeforeCrash', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Vehicles/[a]/DirectionOfTravelBeforeCrash', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Vehicles/[a]/DirectionOfTravelBeforeCrash', 1, @formFieldCommonId, 0, 0);


-- New element eCrash DB field: Vehicles/Vehicle_Type

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'vehicleType' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'VehicleType', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`, `is_code_value_pair`) VALUES ('VehicleType', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Vehicles/[a]/VehicleType', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Vehicles/[a]/VehicleType', 1, @formFieldCommonId, 0, 0);


