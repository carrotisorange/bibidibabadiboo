USE ecrash_v3;

-- To maintain the code/description pair field even if it's text field not multiselect
ALTER TABLE `form_field` ADD `is_code_value_pair` TINYINT(1) DEFAULT 0;

-- Select statement will return 28 rows
SELECT name FROM form_field WHERE `form_system_id` = 1 AND `name` IN ('ViolationCode','ContributingCircumstancesVehicle','NonMotoristActionsatTimeofCrash','DriverActionsatTimeOfCrash','DriverDistractedBy','DrugUseSuspected','AlcoholUseSuspected','SafetyEquipmentRestraint','RoadSurfaceCondition','WeatherCondition','AlcoholDrugUse','SafetyEquipmentHelmet','ContributingCircumstancesPerson','AlcoholTestStatus','LightCondition','TrafficControlDeviceType','TrafficwayDescription','ConditionAtTimeOfCrash','VehicleManeuverActionPrior','IntersectionType','RoadType','EventSequence','MostHarmfulEventforVehicle','UnitType','MannerCrashImpact','AlcoholTestType','DrugTestType');


UPDATE `form_field` SET `is_code_value_pair` = '1' WHERE `form_system_id` = 1 AND `name` IN ('ViolationCode','ContributingCircumstancesVehicle','NonMotoristActionsatTimeofCrash','DriverActionsatTimeOfCrash','DriverDistractedBy','DrugUseSuspected','AlcoholUseSuspected','SafetyEquipmentRestraint','RoadSurfaceCondition','WeatherCondition','AlcoholDrugUse','SafetyEquipmentHelmet','ContributingCircumstancesPerson','AlcoholTestStatus','LightCondition','TrafficControlDeviceType','TrafficwayDescription','ConditionAtTimeOfCrash','VehicleManeuverActionPrior','IntersectionType','RoadType','EventSequence','MostHarmfulEventforVehicle','UnitType','MannerCrashImpact','AlcoholTestType','DrugTestType');