USE ecrash_v3;

-- AB

SET @stateId = (SELECT state_id FROM state WHERE name_abbr = 'AB' LIMIT 1);

INSERT INTO form_code_group (description) values ("Default AB");
SET @formCodeGroupId = LAST_INSERT_ID();


-- Query cross check the configuration before update below query
-- SELECT * FROM form_code_group_configuration fcgc WHERE state_id = @stateId AND agency_id IS NULL AND form_template_id = 2;
-- Alberta is mapped to default form code group, update the default value by new one.


INSERT INTO form_code_group_configuration (state_id, form_template_id, form_code_group_id) values (@stateId, 2, @formCodeGroupId);

-- Copy form code list from default form code group into Alberta

SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default AB' LIMIT 1);
SET @defaultFormCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default' LIMIT 1);

INSERT INTO form_code_list_group_map (form_code_group_id, form_code_list_id)
SELECT @formCodeGroupId, fcl.form_code_list_id FROM form_code_list_group_map fclgm
JOIN form_code_list fcl ON fcl.form_code_list_id = fclgm.form_code_list_id
WHERE form_code_group_id = @defaultFormCodeGroupId;

-- Update State to dropdown
SET @name = 'States', @note = NULL, @is_multiselect = NULL;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @code ="AB", @description ="Alberta";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


-- MB
SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'MB' LIMIT 1);

INSERT INTO form_code_group (description) values ("Default MB");
SET @formCodeGroupId = LAST_INSERT_ID();

INSERT INTO form_code_group_configuration (state_id, form_template_id, form_code_group_id) values (@stateId, 2, @formCodeGroupId);

-- Copy form code list from default form code group into Manitoba
SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default MB' LIMIT 1);
SET @defaultFormCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default' LIMIT 1);

INSERT INTO form_code_list_group_map (form_code_group_id, form_code_list_id)
SELECT @formCodeGroupId, fcl.form_code_list_id FROM form_code_list_group_map fclgm
JOIN form_code_list fcl ON fcl.form_code_list_id = fclgm.form_code_list_id
WHERE form_code_group_id = @defaultFormCodeGroupId;

-- Update State to dropdown
SET @name = 'States', @note = NULL, @is_multiselect = NULL;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @code ="MB", @description ="Manitoba";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


-- NL

SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'NL' LIMIT 1);

INSERT INTO form_code_group (description) values ("Default NL");
SET @formCodeGroupId = LAST_INSERT_ID();

-- NL is mapped to default form code group, update the default value by new one.

INSERT INTO form_code_group_configuration (state_id, form_template_id, form_code_group_id) values (@stateId, 2, @formCodeGroupId);

-- Copy form code list from default form code group into NL
SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default NL' LIMIT 1);
SET @defaultFormCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default' LIMIT 1);

INSERT INTO form_code_list_group_map (form_code_group_id, form_code_list_id)
SELECT @formCodeGroupId, fcl.form_code_list_id FROM form_code_list_group_map fclgm
JOIN form_code_list fcl ON fcl.form_code_list_id = fclgm.form_code_list_id
WHERE form_code_group_id = @defaultFormCodeGroupId;

-- Update State to dropdown
SET @name = 'States', @note = NULL, @is_multiselect = NULL;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @code ="NL", @description ="Newfoundland and Labrador";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description)); 

-- NT

SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'NT' LIMIT 1);

INSERT INTO form_code_group (description) values ("Default NT");
SET @formCodeGroupId = LAST_INSERT_ID();

SET @stateId = (SELECT state_id FROM state WHERE name_abbr = 'NT' LIMIT 1);

-- NT is mapped to default form code group, update the default value by new one.

INSERT INTO form_code_group_configuration (state_id, form_template_id, form_code_group_id) values (@stateId, 2, @formCodeGroupId);

-- Copy form code list from default form code group into NT
SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default NT' LIMIT 1);
SET @defaultFormCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default' LIMIT 1);

INSERT INTO form_code_list_group_map (form_code_group_id, form_code_list_id)
SELECT @formCodeGroupId, fcl.form_code_list_id FROM form_code_list_group_map fclgm
JOIN form_code_list fcl ON fcl.form_code_list_id = fclgm.form_code_list_id
WHERE form_code_group_id = @defaultFormCodeGroupId;


-- Update State to dropdown
SET @name = 'States', @note = NULL, @is_multiselect = NULL;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @code ="NT", @description ="Northwest Territories";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));

-- QC

SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'QC' LIMIT 1);

INSERT INTO form_code_group (description) values ("Default QC");
SET @formCodeGroupId = LAST_INSERT_ID();

-- QC is mapped to default form code group, update the default value by new one.
INSERT INTO form_code_group_configuration (state_id, form_template_id, form_code_group_id) values (@stateId, 2, @formCodeGroupId);

-- Copy form code list from default form code group into QC
SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default QC' LIMIT 1);
SET @defaultFormCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default' LIMIT 1);

INSERT INTO form_code_list_group_map (form_code_group_id, form_code_list_id)
SELECT @formCodeGroupId, fcl.form_code_list_id FROM form_code_list_group_map fclgm
JOIN form_code_list fcl ON fcl.form_code_list_id = fclgm.form_code_list_id
WHERE form_code_group_id = @defaultFormCodeGroupId;

-- Update State to dropdown
SET @name = 'States', @note = NULL, @is_multiselect = NULL;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @code ="QC", @description ="Quebec";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


-- SK

SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'SK' LIMIT 1);

INSERT INTO form_code_group (description) values ("Default SK");
SET @formCodeGroupId = LAST_INSERT_ID();

-- SK is mapped to default form code group, update the default value by new one.

INSERT INTO form_code_group_configuration (state_id, form_template_id, form_code_group_id) values (@stateId, 2, @formCodeGroupId);

-- Copy form code list from default form code group into SK
SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default SK' LIMIT 1);
SET @defaultFormCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default' LIMIT 1);

INSERT INTO form_code_list_group_map (form_code_group_id, form_code_list_id)
SELECT @formCodeGroupId, fcl.form_code_list_id FROM form_code_list_group_map fclgm
JOIN form_code_list fcl ON fcl.form_code_list_id = fclgm.form_code_list_id
WHERE form_code_group_id = @defaultFormCodeGroupId;


-- Update State to dropdown
SET @name = 'States', @note = NULL, @is_multiselect = NULL;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @code ="SK", @description ="Saskatchewan";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));