USE ecrash_v3;

-- New element eCrash DB field: road_condition
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'RoadSurfaceCondition' and path like 'Vehicles%');

SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'RoadSurfaceCondition', 'Vehicles/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('RoadSurfaceCondition', 'Vehicles/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Vehicles/RoadSurfaceCondition/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Vehicles/RoadSurfaceCondition/[a]', 1, @formFieldCommonId, 0, 0);