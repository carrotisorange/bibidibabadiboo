USE ecrash_v3;

-- To update the same length for code and description
ALTER TABLE `form_code_pair` MODIFY COLUMN `code` VARCHAR (128) NOT NULL;