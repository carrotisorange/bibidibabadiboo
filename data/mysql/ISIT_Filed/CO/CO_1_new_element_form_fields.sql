USE ecrash_v3;

-- New element eCrash DB field: Marijuana_Use_Suspected

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'marijuanaUseSuspected' and path like 'person%');

INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`,`is_code_value_pair`) VALUES ('MarijuanaUseSuspected', 'People/[a]', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/MarijuanaUseSuspected/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/MarijuanaUseSuspected/[a]', 1, @formFieldCommonId, 0, 0);