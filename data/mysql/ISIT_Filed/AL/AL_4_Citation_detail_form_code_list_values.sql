USE ecrash_v3;


SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description ="Default AL" LIMIT 1);

-- Citation Detail Field Values
SET @name = 'Citation_Detail', @note = 'Default AL - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "99", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "No driver license";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Driving under the influence";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Driving under the influence of drugs";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Driving while revoked";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "8", @description = "Driving while suspended";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "24", @description = "Leaving the scene of an accident";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "27", @description = "Improper parking";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "28", @description = "Improper tag or expired tag";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "46", @description = "Violation of restrictions";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "57", @description = "Eluding police";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "66", @description = "Assault";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "71", @description = "Driving under the influence of alcohol and drugs";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "72", @description = "Driving under the influcence of any substance";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "75", @description = "Window tint";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "117", @description = "No tag";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "128", @description = "No registration in vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "131", @description = "No proof of inusrance";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "145", @description = "Driving a commercial vehicle without fist being licensed";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "148", @description = "Improper class or endorsements on license";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));



-- To drop newly[temporarily] created function
DROP FUNCTION IF EXISTS ufn_set_form_code_list;
DROP FUNCTION IF EXISTS ufn_set_form_code_pair;