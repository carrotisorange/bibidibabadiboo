USE ecrash_v3;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Contributing_Circumstances_Person' AND is_multiselect = 1 AND
 note ='Default MI - NEW');

SET @code = "16", @description = "Careless Driving";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));