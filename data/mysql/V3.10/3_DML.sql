USE ecrash_v3;


-- FL

-- Update AlcoholTestResult's  code value pair value 
UPDATE `form_field` SET `is_code_value_pair` = 0  WHERE `name` = 'AlcoholTestResult' AND `path` = 'People/[a]' AND `form_system_id` = 1;

-- NU

SET @stateId = (SELECT state_id FROM state WHERE name_abbr = 'NU' LIMIT 1);

INSERT INTO form_code_group (description) values ("Default NU");
SET @formCodeGroupId = LAST_INSERT_ID();


-- Query cross check the configuration before update below query
-- SELECT * FROM form_code_group_configuration fcgc WHERE state_id = @stateId AND agency_id IS NULL AND form_template_id = 2;
-- Nunavut is mapped to default form code group, update the default value by new one.


INSERT INTO form_code_group_configuration (state_id, form_template_id, form_code_group_id) values (@stateId, 2, @formCodeGroupId);

-- Copy form code list from default form code group into Nunavut

SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default NU' LIMIT 1);
SET @defaultFormCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default' LIMIT 1);

INSERT INTO form_code_list_group_map (form_code_group_id, form_code_list_id)
SELECT @formCodeGroupId, fcl.form_code_list_id FROM form_code_list_group_map fclgm
JOIN form_code_list fcl ON fcl.form_code_list_id = fclgm.form_code_list_id
WHERE form_code_group_id = @defaultFormCodeGroupId;

-- Update State to dropdown
SET @name = 'States', @note = NULL, @is_multiselect = NULL;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @code ="NU", @description ="Nunavut";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
