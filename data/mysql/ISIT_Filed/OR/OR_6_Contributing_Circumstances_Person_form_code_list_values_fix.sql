USE ecrash_v3;


SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Contributing_Circumstances_Person' AND is_multiselect = 1 AND note ='Default OR - New');

SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "Failed to yield ROW" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

UPDATE  `form_code_pair`  SET `code` = 'Failed to yield row' , `description` = 'Failed to yield row'  WHERE form_code_pair_id = @formCodePairId ;



SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "improper backing" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

UPDATE  `form_code_pair`  SET `code` = 'Improper backing' , `description` = 'Improper backing'  WHERE form_code_pair_id = @formCodePairId ;


SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "improper passing" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

UPDATE  `form_code_pair`  SET `code` = 'Improper passing' , `description` = 'Improper passing'  WHERE form_code_pair_id = @formCodePairId ;


SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "improper signal" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

UPDATE  `form_code_pair`  SET `code` = 'Improper signal' , `description` = 'Improper signal'  WHERE form_code_pair_id = @formCodePairId ;


SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "improper parking" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

UPDATE  `form_code_pair`  SET `code` = 'Improper parking' , `description` = 'Improper parking'  WHERE form_code_pair_id = @formCodePairId ;


SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "inattentive" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

UPDATE  `form_code_pair`  SET `code` = 'Inattentive' , `description` = 'Inattentive'  WHERE form_code_pair_id = @formCodePairId ;



SET @formCodePairId = (select  fclpm.form_code_pair_id from form_code_list_pair_map  fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = "Imprp Restr Eqp Use" AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);

UPDATE  `form_code_pair`  SET `code` = 'Improp Restr Eqp Use' , `description` = 'Improp Restr Eqp Use'  WHERE form_code_pair_id = @formCodePairId ;


