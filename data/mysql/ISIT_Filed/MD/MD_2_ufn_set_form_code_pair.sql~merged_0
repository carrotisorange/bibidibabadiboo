USE ecrash_v3;

-- Drop the function if already exists
DROP FUNCTION IF EXISTS `ufn_set_form_code_pair`;

DELIMITER $$
CREATE FUNCTION `ufn_set_form_code_pair`(
    _formCodelistId INT(10),
    _code VARCHAR(128),
    _description VARCHAR(128)
) RETURNS int(10)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE formCodePairId INT DEFAULT NULL;
    DECLARE formCodeListPairMapId INT DEFAULT NULL;
    
    SET formCodePairId = (select `form_code_pair_id` from `form_code_pair` where binary `code` = binary _code AND binary `description` = binary _description order by `form_code_pair_id` DESC limit 1);
    
    IF formCodePairId IS NULL THEN
        INSERT INTO form_code_pair (`code`, `description`) VALUES (_code, _description);
        SET formCodePairId = LAST_INSERT_ID();
    END IF;
    
    SET formCodeListPairMapId = (select `form_code_pair_id` from `form_code_list_pair_map` where binary `form_code_list_id` = binary _formCodelistId AND binary `form_code_pair_id` = binary formCodePairId order by `form_code_pair_id` DESC limit 1);
    
    IF _formCodelistId IS NOT NULL AND formCodeListPairMapId IS NULL THEN
        INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (_formCodelistId, formCodePairId);
    END IF;
    
    RETURN (formCodePairId);
END$$
DELIMITER ;
