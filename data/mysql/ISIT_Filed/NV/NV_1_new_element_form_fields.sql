USE ecrash_v3;


-- New element eCrash DB field: Citation_Type
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'citationType' and path like 'citation%');

SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Citation_Type', 'citations/[a]'));
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Citation_Type', 'Citations/[a]', 1, @formFieldCommonId, 0, 0);