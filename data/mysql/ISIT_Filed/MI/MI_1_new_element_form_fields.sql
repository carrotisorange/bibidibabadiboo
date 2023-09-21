USE ecrash_v3;

-- New element eCrash DB field: PriorNonmotoristAction

INSERT INTO `form_field_common` (`name`, `path`) VALUES ('PriorNonmotoristAction', 'people/[a]');
SET @form_field_common_id = LAST_INSERT_ID();

INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`,`is_code_value_pair`) VALUES ('PriorNonmotoristAction', 'People/[a]', '1', @form_field_common_id,1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('Code', 'People/[a]/PriorNonmotoristAction', '1', @form_field_common_id);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`) VALUES ('Description', 'People/[a]/PriorNonmotoristAction', '1', @form_field_common_id);