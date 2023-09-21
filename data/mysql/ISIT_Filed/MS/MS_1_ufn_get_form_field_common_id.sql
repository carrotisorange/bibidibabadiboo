USE ecrash_v3;

-- Drop the function if already exists
DROP FUNCTION IF EXISTS ufn_get_form_field_common_id;

DELIMITER $$
CREATE FUNCTION ufn_get_form_field_common_id(
    FormFieldCommonId INT,
    name VARCHAR(255),
    path VARCHAR(255)
)
RETURNS int
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE returnFormFieldCommonId INT;

    IF FormFieldCommonId IS NOT NULL THEN
        SET returnFormFieldCommonId = FormFieldCommonId;
    ELSEIF FormFieldCommonId IS NULL THEN
        INSERT INTO form_field_common (`name`, `path`) VALUES (name, path);
        SET returnFormFieldCommonId = LAST_INSERT_ID();
    END IF;
    
    RETURN (returnFormFieldCommonId);
END $$

DELIMITER ;