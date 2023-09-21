USE ecrash_v3;

SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'AR' LIMIT 1);

SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default AR' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);


SET @name = 'Ejection', @note = 'Default AR - New', @is_multiselect = 0;
SET @formCodeListId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

-- To append missing Ejection form code value pairs
SET @code ="0", @description ="Not ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="1", @description ="Totally Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="2", @description ="Partially Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="99", @description ="Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));