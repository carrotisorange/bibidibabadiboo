USE ecrash_v3;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Contributing_Circumstances_Vehicle' AND is_multiselect = 1 AND
 note ='Default MO - NEW');

SET @code = "None", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));