USE ecrash_v3;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Posted_Statutory_Speed_Limit'  AND 
 note ='Default CT - NEW');

SET @code = "88", @description = "Not Applicable";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));