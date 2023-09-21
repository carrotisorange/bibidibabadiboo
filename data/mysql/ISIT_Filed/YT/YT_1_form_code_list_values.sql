USE ecrash_v3;

-- Form code list value mappings
INSERT INTO `form_code_group` (`description`) VALUES ('Default YT');
SET @formCodeGroupId = LAST_INSERT_ID();

SET @stateId = (SELECT state_id FROM state WHERE name_abbr = 'YT' LIMIT 1);

-- Yukon is mapped to default form code group, update the default value by new one.
SET @formCodeGroupConfiguration = (SELECT form_code_group_configuration_id FROM form_code_group_configuration WHERE state_id = @stateId AND form_template_id = 2 LIMIT 1);
-- SELECT @formCodeGroupConfiguration

--  if @formCodeGroupConfiguration has value then use Update query otherwise use insert query
-- UPDATE form_code_group_configuration fcgc SET fcgc.form_code_group_id = @formCodeGroupId WHERE state_id = @stateId AND form_template_id = 2;

INSERT INTO form_code_group_configuration  (state_id,form_template_id,form_code_group_id) VALUES (@stateId, 2,@formCodeGroupId); 

-- Yukon default form code list map
SET @defaultFormCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default' LIMIT 1);
INSERT INTO form_code_list_group_map (form_code_group_id, form_code_list_id)
SELECT @formCodeGroupId, fcl.form_code_list_id FROM form_code_list_group_map fclgm
JOIN form_code_list fcl ON fcl.form_code_list_id = fclgm.form_code_list_id
WHERE form_code_group_id = @defaultFormCodeGroupId;


-- Update State to dropdown
SET @name = 'States', @note = NULL, @is_multiselect = NULL;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @code ="YT", @description ="Yukon";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="VI", @description ="Virgin Islands";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));