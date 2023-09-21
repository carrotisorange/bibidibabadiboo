-- New element eCrash DB field: Incident/manner_crash_impact

-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'mannerCrashImpact' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MannerCrashImpact', 'incident'));

INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('MannerCrashImpact', 'Incident', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/MannerCrashImpact/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/MannerCrashImpact/[a]', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Incident/Road_Type
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'roadType' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'RoadType', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('RoadType', 'Incident', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/RoadType/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/RoadType/[a]', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Incident/intersection_type
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'intersectionType' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'IntersectionType', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('IntersectionType', 'Incident', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/IntersectionType/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/IntersectionType/[a]', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Incident/light_condition 
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'lightCondition' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'LightCondition', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('LightCondition', 'Incident', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/LightCondition/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/LightCondition/[a]', 1, @formFieldCommonId, 0, 0);


-- New element eCrash DB field: People/alcohol_use_suspected
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'alcoholUseSuspected' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'AlcoholUseSuspected', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('AlcoholUseSuspected', 'People/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/AlcoholUseSuspected', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/AlcoholUseSuspected', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: People/drug_use_suspected
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'drugUseSuspected' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'DrugUseSuspected', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('DrugUseSuspected', 'People/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/DrugUseSuspected', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/DrugUseSuspected', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: People/driver_distracted_by
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'driverDistractedBy
' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'DriverDistractedBy', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('DriverDistractedBy', 'People/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/DriverDistractedBy', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/DriverDistractedBy', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: People/driver_actions_at_time_of_crash
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'driverActionsAtTimeOfCrash' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'DriverActionsAtTimeOfCrash', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('DriverActionsAtTimeOfCrash', 'People/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/DriverActionsAtTimeOfCrash', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/DriverActionsAtTimeOfCrash', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: People/non_motorist_actions_at_time_of_crash
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'nonMotoristActionsAtTimeOfCrash' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'NonMotoristActionsAtTimeOfCrash', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('NonMotoristActionsAtTimeOfCrash', 'People/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/NonMotoristActionsAtTimeOfCrash', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/NonMotoristActionsAtTimeOfCrash', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: People/condition_at_time_of_crash
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'conditionAtTimeOfCrash' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'ConditionAtTimeOfCrash', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('ConditionAtTimeOfCrash', 'People/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/ConditionAtTimeOfCrash', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/ConditionAtTimeOfCrash', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: People/alcohol_test_status
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'alcoholTestStatus' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'AlcoholTestStatus', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('AlcoholTestStatus', 'People/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/AlcoholTestStatus', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/AlcoholTestStatus', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: People/alcohol_test_result
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'alcoholTestResult' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'AlcoholTestResult', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('AlcoholTestResult', 'People/[a]', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: People/Sex
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'sex' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Sex', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Sex', 'People/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/Sex', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/Sex', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Vehicles/Posted_Satutory_Speed_Limit
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'postedStatutorySpeedLimit' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'PostedStatutorySpeedLimit', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('PostedStatutorySpeedLimit', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Vehicles/unit_type
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'unitType' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'UnitType', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('UnitType', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Vehicles/[a]/UnitType', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Vehicles/[a]/UnitType', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Vehicles/most_harmful_event_v
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'mostHarmfulEventForVehicle' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MostHarmfulEventForVehicle', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('MostHarmfulEventForVehicle', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Vehicles/[a]/MostHarmfulEventForVehicle', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Vehicles/[a]/MostHarmfulEventForVehicle', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Vehicles/event_sequence
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'eventSequence' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'EventSequence', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('EventSequence', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Vehicles/[a]/EventSequence', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Vehicles/[a]/EventSequence', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Vehicles/Commercial_Vehicle
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'commercialvehicle' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'CommercialVehicle', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('CommercialVehicle', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Vehicles/vehicle_maneuver_action_prior
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'vehicleManeuverActionPrior' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'VehicleManeuverActionPrior', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('VehicleManeuverActionPrior', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Vehicles/[a]/VehicleManeuverActionPrior', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Vehicles/[a]/VehicleManeuverActionPrior', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Vehicles/trafficway_description
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'trafficwayDescription' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'TrafficwayDescription', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('TrafficwayDescription', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Vehicles/[a]/TrafficwayDescription', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Vehicles/[a]/TrafficwayDescription', 1, @formFieldCommonId, 0, 0);

-- New element eCrash DB field: Vehicles/traffic_control_device_type
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'trafficControlDeviceType' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'TrafficControlDeviceType', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('TrafficControlDeviceType', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Vehicles/[a]/TrafficControlDeviceType', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Vehicles/[a]/TrafficControlDeviceType', 1, @formFieldCommonId, 0, 0);


-- New element eCrash DB field: Citations/Citation_Number1
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'citationNumber' and path like 'citations%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'CitationNumber', 'citations/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('CitationNumber', 'Citations/[a]', 1, @formFieldCommonId, 0, 0);

-- To drop newly[temporarily] created function
DROP FUNCTION IF EXISTS ufn_get_form_field_common_id;