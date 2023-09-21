USE ecrash_v3;

-- ND
-- Update form code list multiselect field for ND.

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name = 'Alcohol_Drug_Use' and note = 'Default ND - New' and is_multiselect = 0 LIMIT 1);
UPDATE form_code_list SET is_multiselect = 1 WHERE form_code_list_id = @formCodelistId;