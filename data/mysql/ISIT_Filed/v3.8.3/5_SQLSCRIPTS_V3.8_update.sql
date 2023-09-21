USE ecrash_v3;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'ContributingCircumstancesPerson' and path like 'people%' );
UPDATE `form_field` SET  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'ContributingCircumstancesPerson' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'SafetyEquipmentRestraint' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'SafetyEquipmentRestraint' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'SafetyEquipmentHelmet' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'SafetyEquipmentHelmet' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'AlcoholTestType' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'AlcoholTestType' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'DrugTestType' and path like 'people%' );
UPDATE `form_field` SET  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'DrugTestType' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'AlcoholUseSuspected' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'AlcoholUseSuspected' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'DrugUseSuspected' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'DrugUseSuspected' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'DriverDistractedBy' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'DriverDistractedBy' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'DriverActionsAtTimeOfCrash' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'DriverActionsAtTimeOfCrash' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'NonMotoristActionsAtTimeOfCrash' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'NonMotoristActionsAtTimeOfCrash' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'ConditionAtTimeOfCrash' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'ConditionAtTimeOfCrash' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'AlcoholTestStatus' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'AlcoholTestStatus' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'AlcoholTestResult' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'AlcoholTestResult' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'AlcoholDrugUse' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'AlcoholDrugUse' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PriorNonmotoristAction' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'PriorNonmotoristAction' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'DrugTestStatus' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'DrugTestStatus' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'AlcoholDrugTestType' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'AlcoholDrugTestType' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PassengerActionsAtTimeOfCrash' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'PassengerActionsAtTimeOfCrash' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PedalcyclistActionsAtTimeOfCrash' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'PedalcyclistActionsAtTimeOfCrash' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PedestrianActionsAtTimeOfCrash' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'PedestrianActionsAtTimeOfCrash' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'MarijuanaUseSuspected' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'MarijuanaUseSuspected' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'AlcoholDrugTestResult' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'AlcoholDrugTestResult' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'SeatingPosition' and path like 'people%' );
UPDATE `form_field` SET   `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'SeatingPosition' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PartyId' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'PartyId' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PersonType' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'PersonType' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PersonTypeHidden' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'PersonTypeHidden' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'UnitNumber' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'UnitNumber' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'FirstName' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'FirstName' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'LastName' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'LastName' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'MiddleName' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'MiddleName' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'NameSuffix' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'NameSuffix' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'Address' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'Address' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'Address2' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'Address2' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'City' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'City' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'State' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'State' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'ZipCode' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'ZipCode' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'HomePhone' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'HomePhone' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'DateOfBirth' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'DateOfBirth' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'DriversLicenseNumber' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'DriversLicenseNumber' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'DriversLicenseJurisdiction' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'DriversLicenseJurisdiction' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'InjuryStatus' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0,  `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'InjuryStatus' and  `path` = 'People/[a]' AND `form_system_id` = 1;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'Sex' and path like 'people%' );
UPDATE `form_field` SET  `is_code_value_pair` = 0, `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'Sex' and  `path` = 'People/[a]' AND `form_system_id` = 1;

