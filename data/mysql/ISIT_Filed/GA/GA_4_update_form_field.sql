USE ecrash_v3;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'drugTestStatus' and path like 'people%' LIMIT 1);
SET @FormFieldId = (select form_field_id from form_field where name = 'DrugTestStatus' and form_field_common_id = @FormFieldCommonId and form_system_id = 1 LIMIT 1);

UPDATE form_field SET is_code_value_pair = 1 WHERE form_field_id = @FormFieldId;
