SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'Latitude' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Latitude', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Latitude', 'Incident', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'DispatchTime' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'DispatchTime', 'incident'));
UPDATE `form_field` SET `name` = 'DispatchTime', `path` = 'Incident', `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Dispatch_Time' AND `path` = 'incident' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'weatherCondition' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'weatherCondition', 'incident'));
UPDATE `form_field` SET `name` = 'WeatherCondition', `path` = 'Incident', `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Weather_Condition' AND `path` = 'incident' AND `form_system_id` = 1;
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/WeatherCondition/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/WeatherCondition/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'RoadSurfaceCondition' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'RoadSurfaceCondition', 'incident'));
UPDATE `form_field` SET `name` = 'RoadSurfaceCondition', `path` = 'Incident', `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Road_Condition' AND `path` = 'incident' AND `form_system_id` = 1;
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/RoadSurfaceCondition/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/RoadSurfaceCondition/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'LossCrossStreetSpeedLimit' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'LossCrossStreetSpeedLimit', 'incident'));
UPDATE `form_field` SET `name` = 'LossCrossStreetSpeedLimit', `path` = 'Incident', `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Loss_Street_Speed_Limit' AND `path` = 'incident' AND `form_system_id` = 1;

-- People
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PartyId' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'PartyId', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('PartyId', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PersonType' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'PersonType', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('PersonType', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PersonTypeHidden' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'PersonTypeHidden', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('PersonTypeHidden', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'UnitNumber' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'UnitNumber', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('UnitNumber', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'FirstName' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'FirstName', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('FirstName', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'LastName' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'LastName', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('LastName', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'MiddleName' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MiddleName', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('MiddleName', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'NameSuffix' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'NameSuffix', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('NameSuffix', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'Address' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Address', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Address', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'Address2' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Address2', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Address2', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'City' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'City', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('City', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'State' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'State', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('State', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'ZipCode' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'ZipCode', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('ZipCode', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'HomePhone' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'HomePhone', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('HomePhone', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'DateOfBirth' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'DateOfBirth', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('DateOfBirth', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'DriversLicenseNumber' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'DriversLicenseNumber', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('DriversLicenseNumber', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'DriversLicenseJurisdiction' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'DriversLicenseJurisdiction', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('DriversLicenseJurisdiction', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'InjuryStatus' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'InjuryStatus', 'people/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('InjuryStatus', 'People/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'SafetyEquipmentRestraint' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'SafetyEquipmentRestraint', 'people/[a]'));
UPDATE `form_field` SET `name` = 'SafetyEquipmentRestraint', `path` = 'People/[a]', `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Safety_Equipment_Restraint' AND `path` = 'person/[a]' AND `form_system_id` = 1;
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/SafetyEquipmentRestraint', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/SafetyEquipmentRestraint', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'SafetyEquipmentHelmet' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'SafetyEquipmentHelmet', 'people/[a]'));
UPDATE `form_field` SET `name` = 'SafetyEquipmentHelmet', `path` = 'People/[a]', `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Safety_Equipment_Helmet' AND `path` = 'person/[a]' AND `form_system_id` = 1;
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/SafetyEquipmentHelmet', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/SafetyEquipmentHelmet', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'Ejection' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Ejection', 'people/[a]'));
UPDATE `form_field` SET `name` = 'Ejection', `path` = 'People/[a]', `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Ejection' AND `path` = 'person/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'TransportedTo' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'TransportedTo', 'people/[a]'));
UPDATE `form_field` SET `name` = 'TransportedTo', `path` = 'People/[a]', `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Transported_To' AND `path` = 'person/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'AlcoholTestType' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'AlcoholTestType', 'people/[a]'));
UPDATE `form_field` SET `name` = 'AlcoholTestType', `path` = 'People/[a]', `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Alcohol_Test_Type' AND `path` = 'person/[a]' AND `form_system_id` = 1;
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/AlcoholTestType', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/AlcoholTestType', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'DrugTestType' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'DrugTestType', 'people/[a]'));
UPDATE `form_field` SET `name` = 'DrugTestType', `path` = 'People/[a]', `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Drug_Test_Type' AND `path` = 'person/[a]' AND `form_system_id` = 1;
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/DrugTestType', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/DrugTestType', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'ContributingCircumstancesPerson' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'ContributingCircumstancesPerson', 'people/[a]'));
UPDATE `form_field` SET `name` = 'ContributingCircumstancesPerson', `path` = 'People/[a]', `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Contributing_Circumstances_Person' AND `path` = 'person/[a]' AND `form_system_id` = 1;
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/ContributingCircumstancesPerson', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/ContributingCircumstancesPerson', 1, @formFieldCommonId, 0, 0);

-- Vehicle
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'VinValidationVinStatus' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'VinValidationVinStatus', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('VinValidationVinStatus', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'UnitNumber' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'UnitNumber', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('UnitNumber', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'LicensePlate' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'LicensePlate', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('LicensePlate', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'RegistrationState' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'RegistrationState', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('RegistrationState', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'VIN' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'VIN', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('VIN', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'VehicleTowed' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'VehicleTowed', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('VehicleTowed', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'ModelYear' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'ModelYear', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('ModelYear', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'ModelYearOriginal' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'ModelYearOriginal', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('ModelYearOriginal', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'Make' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Make', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Make', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'MakeOriginal' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MakeOriginal', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('MakeOriginal', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'Model' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Model', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Model', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'ModelOriginal' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'ModelOriginal', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('ModelOriginal', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'InsuranceCompany' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'InsuranceCompany', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('InsuranceCompany', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'InsurancePolicyNumber' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'InsurancePolicyNumber', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('InsurancePolicyNumber', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'InsuranceExpirationDate' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'InsuranceExpirationDate', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('InsuranceExpirationDate', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'DamagedAreas' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'DamagedAreas', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('DamagedAreas', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'AirBagDeployed' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'AirBagDeployed', 'vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('AirBagDeployed', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'ContributingCircumstancesVehicle' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'ContributingCircumstancesVehicle', 'vehicles/[a]'));
UPDATE `form_field` SET `name` = 'ContributingCircumstancesVehicle', `path` = 'Vehicles/[a]', `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Contributing_Circumstances_Vehicle' AND `path` = 'vehicle/[a]' AND `form_system_id` = 1;
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Vehicles/[a]/ContributingCircumstancesVehicle', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Vehicles/[a]/ContributingCircumstancesVehicle', 1, @formFieldCommonId, 0, 0);

-- Citations
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'CitationType' and path like 'citations%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'CitationType', 'citations/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('CitationType', 'Citations/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'CitationIssued' and path like 'citations%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'CitationIssued', 'citations/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('CitationIssued', 'Citations/[a]', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'CitationDetail' and path like 'citations%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'CitationDetail', 'citations/[a]'));
UPDATE `form_field` SET `name` = 'CitationDetail', `path` = 'Citations/[a]', `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Citation_Detail' AND `path` = 'citations/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'ViolationCode' and path like 'citations%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'ViolationCode', 'citations/[a]'));
UPDATE `form_field` SET `name` = 'ViolationCode', `path` = 'Citations/[a]', `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Violation_Code' AND `path` = 'citations/[a]' AND `form_system_id` = 1;
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Citations/[a]/ViolationCode', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Citations/[a]/ViolationCode', 1, @formFieldCommonId, 0, 0);

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PartyId' and path like 'citations%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'PartyId', 'citations/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('PartyId', 'Citations/[a]', 1, @formFieldCommonId, 0, 0);

-- To drop newly[temporarily] created function
DROP FUNCTION IF EXISTS ufn_get_form_field_common_id;