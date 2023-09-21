USE ecrash_v3;

-- Drop the function if already exists
DROP FUNCTION IF EXISTS `ufn_set_form_code_list`;

DELIMITER $$
CREATE FUNCTION `ufn_set_form_code_list`(
    _formCodeGroupId INT(10),
    _name VARCHAR(48),
    _note VARCHAR(64),
    _isMultiSelect TINYINT(1)
) RETURNS int(10)
BEGIN
    DECLARE formCodeListId INT DEFAULT NULL;
    
    INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES(_name, _note, _isMultiSelect);
    SET formCodeListId = LAST_INSERT_ID();
    
    INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (_formCodeGroupId, formCodeListId);
    
    RETURN (formCodeListId);
END$$
DELIMITER ;
