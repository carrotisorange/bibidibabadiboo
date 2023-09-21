USE ecrash_v3;

SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default OH' LIMIT 1);
SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='injuryStatus' AND is_multiselect IS NULL AND note ='Default OH');

-- Below select statement will return the default injuryStatus for Default OH. This should be removed from form_code_list_group_map table.
-- SELECT * FROM form_code_list_group_map WHERE form_code_group_id = @formCodeGroupId AND form_code_list_id = @formCodelistId;

DELETE FROM form_code_list_group_map WHERE form_code_group_id = @formCodeGroupId AND form_code_list_id = @formCodelistId;