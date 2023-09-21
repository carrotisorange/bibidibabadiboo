USE ecrash_v3;

-- CitationIssued

SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description ="Default SD" LIMIT 1);

SET @name = 'Citation_Issued', @note = 'Default SD - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "01", @description = "Yes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "No";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Pending";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


-- form_field - CitationIssued
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'CitationIssued' and path like 'citations%' ORDER BY form_field_common_id DESC LIMIT 1);

UPDATE `form_field` SET `name` = 'CitationIssued', `is_code_value_pair` = 1, `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'CitationIssued' AND `path` = 'citations/[a]' AND `form_system_id` = 1;
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Citations/[a]/CitationIssued', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Citations/[a]/CitationIssued', 1, @formFieldCommonId, 0, 0);

-- form_field - PhotographsTaken
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'photographsTaken' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'PhotographsTaken', 'incident'));

INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`,`is_code_value_pair`) VALUES ('PhotographsTaken', 'Incident', 1, @formFieldCommonId, 0, 0, 1);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/PhotographsTaken/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/PhotographsTaken/[a]', 1, @formFieldCommonId, 0, 0);

-- Photographs_Taken

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Photographs_Taken'  AND note ='Default AK - New');

UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

-- HI
SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description ="Default HI" LIMIT 1);

SET @name = 'Photographs_Taken', @note = 'Default HI - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "01", @description = "No";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Yes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));

-- VT
SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description ="Default VT" LIMIT 1);

SET @name = 'Photographs_Taken', @note = 'Default VT - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "Yes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "No";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));

-- SD
SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description ="Default SD" LIMIT 1);

SET @name = 'Photographs_Taken', @note = 'Default SD - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "01", @description = "Yes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "No";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Pending";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


-- Transported_To

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'transportedTo' and path like 'people%' );
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'TransportedTo', 'people/[a]'));

UPDATE `form_field` SET  `is_code_value_pair` = 1, `form_field_common_id` = @FormFieldCommonId WHERE  name = 'TransportedTo' and `path` = 'People/[a]' AND `form_system_id` = 1;
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/TransportedTo', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/TransportedTo', 1, @formFieldCommonId, 0, 0);


-- DE
SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'DE' LIMIT 1);
SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default DE' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);
SET @name = 'Transported_To', @note = 'Default DE - New', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
UPDATE form_code_list SET `is_multiselect` = 1 WHERE form_code_list_id = @formCodelistId;
-- HI
SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'HI' LIMIT 1);
SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default HI' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);
SET @name = 'Transported_To', @note = 'Default HI - New', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
UPDATE form_code_list SET `is_multiselect` = 1 WHERE form_code_list_id = @formCodelistId;
-- NJ
SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'NJ' LIMIT 1);
SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default NJ' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);
SET @name = 'Transported_To', @note = 'Default NJ - NEW', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
UPDATE form_code_list SET `is_multiselect` = 1 WHERE form_code_list_id = @formCodelistId;


-- Ejection
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'ejection' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'Ejection', 'people/[a]'));

UPDATE `form_field` SET `is_code_value_pair` = 1, `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Ejection' AND `path` = 'People/[a]' AND `form_system_id` = 1;
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'People/[a]/Ejection', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'People/[a]/Ejection', 1, @formFieldCommonId, 0, 0);

------- 

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default AK - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default AL - NEW');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default AR - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default AZ - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default CA - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default CO - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default CT - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default DC - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default DE - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default FL - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default GA - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default HI - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default IA - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default ID - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default IL - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default IN - NEW');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default KS - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default KY - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default LA - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default MA - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default MD - NEW');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default ME - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default MI - NEW');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default MO - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default MS - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default MT - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default NC - NEW');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default ND - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default NE - NEW');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default NH - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default NJ - NEW');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default NM - NEW');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default NV - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default NY - NEW');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default OH - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default OK - NEW');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default OR - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default PA - NEW');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default RI - NEW');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default SC - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default SD - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default TN - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default TX - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default UT - NEW');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default VA - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default VT - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default WA - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;

SET @formCodelistId = (SELECT form_code_list_id FROM form_code_list WHERE name='Ejection'  AND note ='Default WV - New');
UPDATE  `form_code_list`  SET `is_multiselect` = 1  WHERE form_code_list_id = @formCodelistId ;


-- form_field - Citation Details
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'CitationDetail' and path like 'citations%');

UPDATE `form_field` SET `is_code_value_pair` = 1, `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'CitationDetail' AND `path` = 'citations/[a]' AND `form_system_id` = 1;
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Citations/[a]/CitationDetail', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Citations/[a]/CitationDetail', 1, @formFieldCommonId, 0, 0);

-- form_code_list - AL
SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'AL' LIMIT 1);
SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default AL' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);
SET @name = 'Citation_Detail', @note = 'Default AL - New', @is_multiselect = null;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
UPDATE form_code_list SET `is_multiselect` = 1 WHERE form_code_list_id = @formCodelistId;

-- form_code_list - ND
SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'ND' LIMIT 1);
SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default ND' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);
SET @name = 'Citation_Detail', @note = 'Default ND - New', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
UPDATE form_code_list SET `is_multiselect` = 1 WHERE form_code_list_id = @formCodelistId;

-- form_code_list - WY
SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'WY' LIMIT 1);
SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default WY' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);
SET @name = 'Citation_Detail', @note = 'Default WY - New', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
UPDATE form_code_list SET `is_multiselect` = 1 WHERE form_code_list_id = @formCodelistId;


-- Posted_Statutory_SpeedLimit

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'postedStatutorySpeedLimit' and path like 'vehicles%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'PostedStatutorySpeedLimit', 'vehicles/[a]'));

UPDATE `form_field` SET  `is_code_value_pair` = 1, `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'PostedStatutorySpeedLimit' AND `path` = 'vehicles/[a]' AND `form_system_id` = 1;

INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Vehicles/[a]/PostedStatutorySpeedLimit', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Vehicles/[a]/PostedStatutorySpeedLimit', 1, @formFieldCommonId, 0, 0);

SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'CT' LIMIT 1);
SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default CT' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);
SET @name = 'Posted_Statutory_Speed_Limit', @note = 'Default CT - NEW', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
UPDATE form_code_list SET `name` = 'Posted_Statutory_SpeedLimit',`is_multiselect` = 1 WHERE form_code_list_id = @formCodelistId;


-- Loss_Cross_Street_Speed_Limit

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'lossCrossStreetSpeedLimit' and path = 'incident');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'LossCrossStreetSpeedLimit', 'incident'));

UPDATE `form_field` SET `is_code_value_pair` = 1 , `form_field_common_id` = @FormFieldCommonId  WHERE `name` = 'LossCrossStreetSpeedLimit' AND `path` = 'incident' AND `form_system_id` = 1;

INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/LossCrossStreetSpeedLimit/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/LossCrossStreetSpeedLimit/[a]', 1, @formFieldCommonId, 0, 0);

SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'PA' LIMIT 1);
SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default PA' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);
SET @name = 'Loss_Cross_Street_Speed_Limit', @note = 'Default PA - New', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
UPDATE form_code_list SET `is_multiselect` = 1 WHERE form_code_list_id = @formCodelistId;

-- CA

SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'CA' LIMIT 1);

SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default CA' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);

SET @name = 'Motor_Vehicle_Involved_With', @note = 'Default CA - New', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

UPDATE form_code_list SET `is_multiselect` = 1 WHERE form_code_list_id = @formCodelistId;

SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'motorVehicleInvolvedWith' and path = 'incident');

SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'MotorVehicleInvolvedWith', 'incident'));

UPDATE `form_field` SET `is_code_value_pair` = 1, `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'MotorVehicleInvolvedWith' AND `path` = 'incident' AND `form_system_id` = 1;
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Code', 'Incident/MotorVehicleInvolvedWith/[a]', 1, @formFieldCommonId, 0, 0);
INSERT INTO `form_field` (`name`, `path`, `form_system_id`, `form_field_common_id`, `is_enum`, `is_included_in_metadata`) VALUES ('Description', 'Incident/MotorVehicleInvolvedWith/[a]', 1, @formFieldCommonId, 0, 0);

-- Auto Extarction Accuracy Metrics 

ALTER TABLE `form_field` ADD COLUMN `is_critical` tinyint(1) NOT NULL AFTER `is_code_value_pair`;
ALTER TABLE `form_field` ADD COLUMN `is_major` tinyint(1) NOT NULL AFTER `is_critical`;
ALTER TABLE `form_field` ADD COLUMN `is_minor` tinyint(1) NOT NULL AFTER `is_major`;

-- Incident
SET @CriticalFields = 'caseIdentifier,stateReportNumber,crashDate,lossStateAbbr,reportTypeId,lossStreet,lossCrossStreet';

UPDATE form_field SET is_critical = 1 WHERE form_field_common_id IN (
	SELECT form_field_common_id from form_field_common WHERE FIND_IN_SET(name, @CriticalFields) AND path = 'incident'
) AND form_system_id = 1;

SET @MajorFields = 'incidentHitAndRun,crashCity';

UPDATE form_field SET is_major = 1 WHERE form_field_common_id IN (
	SELECT form_field_common_id from form_field_common WHERE FIND_IN_SET(name, @MajorFields) AND path = 'incident'
) AND form_system_id = 1;

SET @MinorFields = 'Latitude,longitude,gpsOther';

UPDATE form_field SET is_minor = 1 WHERE form_field_common_id IN (
	SELECT form_field_common_id from form_field_common WHERE FIND_IN_SET(name, @MinorFields) and path = 'incident'
) and form_system_id = 1;

-- People
SET @peopleCriticalFields = 'firstName,middleName,lastName,driversLicenseNumber,driversLicenseJurisdiction';

UPDATE form_field SET is_critical = 1 WHERE form_field_common_id IN (
SELECT form_field_common_id from form_field_common WHERE FIND_IN_SET(name, @peopleCriticalFields) AND path like 'people%'
) AND form_system_id = 1;

SET @peopleMajorFields = 'address,address2,state,city,zipCode,dateOfBirth,injuryStatus,personType,UnitNumber';

UPDATE form_field SET is_major = 1 WHERE form_field_common_id IN (
SELECT form_field_common_id from form_field_common WHERE FIND_IN_SET(name, @peopleMajorFields) AND path like 'people%'
) AND form_system_id = 1;

SET @peopleMinorFields = 'nameSuffix,homePhone';

UPDATE form_field SET is_minor = 1 WHERE form_field_common_id IN (
SELECT form_field_common_id from form_field_common WHERE FIND_IN_SET(name, @peopleMinorFields) AND path like 'people%'
) AND form_system_id = 1;


-- Vehicle
SET @vehicleCriticalFields = 'licensePlate,registrationState,vin,modelYear,make,model,VehicleTowed,AirBagDeployed,DamagedAreas';

UPDATE form_field SET is_critical = 1 WHERE form_field_common_id IN (
SELECT form_field_common_id from form_field_common WHERE FIND_IN_SET(name, @vehicleCriticalFields) AND path like 'vehicles%'
) AND form_system_id = 1;

SET @vehicleMajorFields = 'insuranceCompany,insurancePolicyNumber,insuranceExpirationDate';

UPDATE form_field SET is_major = 1 WHERE form_field_common_id IN (
SELECT form_field_common_id from form_field_common WHERE FIND_IN_SET(name, @vehicleMajorFields) AND path like 'vehicles%'
) AND form_system_id = 1;

SET @vehicleMinorFields = 'unitNumber';

UPDATE form_field SET is_minor = 1 WHERE form_field_common_id IN (
SELECT form_field_common_id from form_field_common WHERE FIND_IN_SET(name, @vehicleMinorFields) AND path like 'vehicles%'
) AND form_system_id = 1;


-- 16 Element

/* Update Mismatch */
-- SafetyEquipmentAvailableOrUsed
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'safetyEquipmentAvailableOrUsed' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'SafetyEquipmentAvailableOrUsed', 'People/[a]'));

UPDATE `form_field` SET `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'SafetyEquipmentAvailableOrUsed' AND `path` = 'People/[a]' AND `form_system_id` = 1;
UPDATE `form_field` SET `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Code' AND `path` = 'People/SafetyEquipmentAvailableOrUsed/[a]' AND `form_system_id` = 1;
UPDATE `form_field` SET `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Description' AND `path` = 'People/SafetyEquipmentAvailableOrUsed/[a]' AND `form_system_id` = 1;

-- AlcoholDrugTestGiven
SET @FormFieldCommonId = (select form_field_common_id from form_field_common where name = 'alcoholDrugTestGiven' and path like 'people%');
SET @FormFieldCommonId = (select ufn_get_form_field_common_id(@FormFieldCommonId, 'AlcoholDrugTestGiven', 'People/[a]'));

UPDATE `form_field` SET `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'AlcoholDrugTestGiven' AND `path` = 'People/[a]' AND `form_system_id` = 1;
UPDATE `form_field` SET `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Code' AND `path` = 'People/AlcoholDrugTestGiven/[a]' AND `form_system_id` = 1;
UPDATE `form_field` SET `form_field_common_id` = @FormFieldCommonId WHERE `name` = 'Description' AND `path` = 'People/AlcoholDrugTestGiven/[a]' AND `form_system_id` = 1;


SET @incidentMajorFields = 'DispatchTime,weatherCondition,RoadSurfaceCondition,lossCrossStreetSpeedLimit,firstHarmfulEvent,DirectionOfImpact,TrafficControlDeviceType,crashType,trafficControlTypeAtIntersection,unusualRoadCondition,MostHarmfulEvent,AccidentCondition,lightCondition,intersectionType,roadType,mannerCrashImpact,PhotographedBy,photographsBy,PhotographsByTemp,referenceMarkers,nextStreetDirection,nextStreetDistanceMeasure,nextStreetDistance,AccidentAtIntersection,Milepost,MilepostNextStreetDirection,MilepostNextStreetDistanceMeasure,MilepostNextStreetDistance,totalFatalInjuries,officerId,beat,precinct,totalNonfatalInjuries,MostHarmfulEventOtherDescription,unusualRoadConditionOtherDescription,motorVehicleInvolvedWithOtherObjectDescription,motorVehicleInvolvedWithFixedObjectDescription,motorVehicleInvolvedWithAnimalDescription,motorVehicleInvolvedWith,crashTypeDescription,AccidentAtIntersection,PhotographType,WeatherConditionOtherDescription,WeatherConditionOtherDescription2';

UPDATE form_field SET is_major = 1 WHERE form_field_common_id IN (
SELECT form_field_common_id from form_field_common WHERE FIND_IN_SET(name, @incidentMajorFields) and path like 'incident%'
) and form_system_id = 1 and name NOT IN ('Code', 'Description');


SET @peopleMajorFields = 'SeatingPosition,AlcoholDrugTestResult,MarijuanaUseSuspected,PassengerActionsAtTimeOfCrash,AlcoholDrugTestType,safetyEquipmentAvailableOrUsed,AlcoholDrugTestGiven,DrugTestStatus,PriorNonmotoristAction,AlcoholDrugUse,AlcoholTestStatus,ConditionAtTimeOfCrash,NonMotoristActionsAtTimeOfCrash,DriverActionsAtTimeOfCrash,DriverDistractedBy,DrugUseSuspected,AlcoholUseSuspected,drugTestType,alcoholTestType,safetyEquipmentHelmet,safetyEquipmentRestraint,ContributingCircumstancesPerson,transportedTo,ejection,AlcoholTestResult,PedestrianActionsAtTimeOfCrash,PedalcyclistActionsAtTimeOfCrash';

UPDATE form_field SET is_major = 1 WHERE form_field_common_id IN (
SELECT form_field_common_id from form_field_common WHERE FIND_IN_SET(name, @peopleMajorFields) and path LIKE 'people%'
) and form_system_id = 1 and name NOT IN ('Code', 'Description');

SET @vehicleMajorFields = 'ActionOnImpact,BodyTypeCategory,VehicleType,vehicleSpecialUse,DamageRating,RoadSurfaceCondition,TrafficControlDeviceType,TrafficwayDescription,VehicleManeuverActionPrior,ContributingCircumstancesVehicle,PostedStatutorySpeedLimit,DirectionOfTravelBeforeCrash,EventSequence,MostHarmfulEventForVehicle,UnitType,ICCMCNumber,OtherStateNumber2,OtherStateNumber,MotorCarrierIdDotNumber,MotorCarrierIDStateID,VehicleManeuverActionPriorOtherDescription,CommercialVehicle';

UPDATE form_field SET is_major = 1 WHERE form_field_common_id IN (
SELECT form_field_common_id from form_field_common WHERE FIND_IN_SET(name, @vehicleMajorFields) and path LIKE 'vehicles%'
) and form_system_id = 1 and name NOT IN ('Code', 'Description');

SET @incidentMajorFields = 'CitationDetail,ViolationCode,CitationType,CitationIssued,CitationNumber';

UPDATE form_field SET is_major = 1 WHERE form_field_common_id IN (
SELECT form_field_common_id from form_field_common WHERE FIND_IN_SET(name, @incidentMajorFields) and path LIKE 'citations%'
) and form_system_id = 1 and name NOT IN ('Code', 'Description');
