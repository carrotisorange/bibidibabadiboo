USE ecrash_v3;

-- New element eCrash DB field: Incident/Photograph_Type
-- To get form field common id
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'PhotographType' and path = 'incident');

-- Insert / Retrieve the existing the form field common id
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'PhotographType', 'incident'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('PhotographType', 'Incident', 1, @FormFieldCommonId);