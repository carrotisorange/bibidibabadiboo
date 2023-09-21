UPDATE form_code_list as fcl
    JOIN (SELECT fcl.form_code_list_id FROM form_code_group as fcg
    JOIN form_code_list_group_map as fclgm ON fclgm.form_code_group_id = fcg.form_code_group_id
    JOIN form_code_list as fcl ON fcl.form_code_list_id = fclgm.form_code_list_id
    WHERE fcg.description = 'Default TX' AND fcl.is_multiselect = 1
        AND fcl.name IN ('Ejection')
    ) as fcl1
    ON fcl.form_code_list_id = fcl1.form_code_list_id
 SET fcl.is_multiselect = NULL;

-- ** DBA Attention**
-- Some time the above update query will not execute due safe mode enabled, for that case use the following update query
/* SELECT GROUP_CONCAT(fcl.form_code_list_id) as form_code_list_id FROM form_code_group as fcg 
    JOIN form_code_list_group_map as fclgm ON fclgm.form_code_group_id = fcg.form_code_group_id
    JOIN form_code_list as fcl ON fcl.form_code_list_id = fclgm.form_code_list_id
    WHERE fcg.description = 'Default TX' AND fcl.is_multiselect = 1
        AND fcl.name IN ('Ejection');

-- Use the form_code_list_ids return in the above select query in the where clause of below update
UPDATE form_code_list SET is_multiselect = NULL
WHERE form_code_list_id IN(<form_code_list_id>)
*/