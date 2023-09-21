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
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE formCodeListId INT DEFAULT NULL;
    
    SET formCodeListId = (SELECT DISTINCT `fcl`.`form_code_list_id` FROM `form_code_list` `fcl` JOIN `form_code_list_group_map` `fclgm` USING(`form_code_list_id`) WHERE `fclgm`.`form_code_group_id` = _formCodeGroupId AND binary `fcl`.`name` = binary _name AND binary `fcl`.`note` = binary _note AND `fcl`.`is_multiselect` = _isMultiSelect ORDER BY `fcl`.`form_code_list_id` DESC LIMIT 1);
    
    IF formCodeListId IS NULL THEN
        INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES(_name, _note, _isMultiSelect);
        SET formCodeListId = LAST_INSERT_ID();
        
        INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (_formCodeGroupId, formCodeListId);
    END IF;
    
    RETURN (formCodeListId);
END$$
DELIMITER ;
