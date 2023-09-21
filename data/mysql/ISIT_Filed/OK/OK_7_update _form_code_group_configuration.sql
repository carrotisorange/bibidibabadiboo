USE ecrash_v3;

SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default OK' LIMIT 1);

SET @stateId = (SELECT state_id FROM state WHERE name_abbr = 'OK' LIMIT 1);

-- Oklahoma is mapped to default form code group, update the default value by new one without Agency based.
UPDATE form_code_group_configuration fcgc  
SET fcgc.form_code_group_id = @FormCodeGroupId
WHERE state_id = @stateId AND form_template_id = 2;

