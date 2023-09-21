USE ecrash_v3;

-- AL
-- Update form code list multiselect field for AL.

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name = 'Ejection' and note = 'Default AL - NEW' and is_multiselect = 0 LIMIT 1);
UPDATE form_code_list SET is_multiselect = 1 WHERE form_code_list_id = @formCodelistId;

-- AL End

-- AK
-- Update form code list multiselect field for AK.

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name = 'Ejection' and note = 'Default AK - New' and is_multiselect = 0 LIMIT 1);
UPDATE  form_code_list SET is_multiselect = 1 WHERE form_code_list_id = @formCodelistId;

-- AK End

-- PA
-- Delete duplicate form code list field.
SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name = 'Loss_Cross_Street_Speed_Limit' and note = 'Default PA - New' and is_multiselect = 1 LIMIT 1);
DELETE FROM form_code_list WHERE form_code_list_id = @formCodelistId;

-- Update form code list multiselect field for PA.
SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name = 'Loss_Cross_Street_Speed_Limit' and note = 'Default PA - NEW' and is_multiselect = 0 LIMIT 1);
UPDATE form_code_list SET is_multiselect = 1 WHERE form_code_list_id = @formCodelistId;

-- PA End




