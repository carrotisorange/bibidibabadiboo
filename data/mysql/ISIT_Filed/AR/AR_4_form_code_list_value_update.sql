USE ecrash_v3;

SET @stateId = (SELECT state_id FROM state WHERE name_abbr = 'AR' LIMIT 1);

SET @formCodelistId = (SELECT DISTINCT fcl.form_code_list_id FROM form_code_list fcl
    JOIN form_code_list_group_map fclgm USING(form_code_list_id)
    JOIN form_code_group_configuration fcgc USING(form_code_group_id)
    WHERE fcgc.state_id = @stateId and fcgc.form_template_id = 2
    AND fcl.name = 'Safety_Equipment_Helmet' AND fcl.note = 'Default AR - New');

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = '102' AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);
UPDATE `form_code_pair` SET `code` = '101' WHERE `form_code_pair_id` = @formCodePairId AND `description` = 'Non-DOT-compliant motorcycle helmet worn';
SET @code ="102", @description ="Helmet worn, unknown if DOT-compliant";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @formCodelistId = (SELECT DISTINCT fcl.form_code_list_id FROM form_code_list fcl
    JOIN form_code_list_group_map fclgm USING(form_code_list_id)
    JOIN form_code_group_configuration fcgc USING(form_code_group_id)
    WHERE fcgc.state_id = @stateId and fcgc.form_template_id = 2
    AND fcl.name = 'Driver_Actions_At_Time_Of_Crash' AND fcl.note = 'Default AR - New');

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`code` = '500' AND fcp.`form_code_pair_id` = fclpm.`form_code_pair_id`);
UPDATE `form_code_pair` SET `description` = 'Reckless operation' WHERE `form_code_pair_id` = @formCodePairId AND `description` = 'Recklee operation';


SET @formCodelistId = (SELECT DISTINCT fcl.form_code_list_id FROM form_code_list fcl
    JOIN form_code_list_group_map fclgm USING(form_code_list_id)
    JOIN form_code_group_configuration fcgc USING(form_code_group_id)
    WHERE fcgc.state_id = @stateId and fcgc.form_template_id = 2
    AND fcl.name = 'Ejection' AND fcl.note = 'Default AR - New');

-- To remove all ejection form code pair values
DELETE FROM form_code_list_pair_map WHERE `form_code_list_id` = @formCodeListId;
-- Ejection form code value pairs
SET @code ="000", @description ="Not Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="100", @description ="Ejected, partially";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="101", @description ="Ejected, totally";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="970", @description ="Not Applicable";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="999", @description ="Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @formCodelistId = (SELECT DISTINCT fcl.form_code_list_id FROM form_code_list fcl
    JOIN form_code_list_group_map fclgm USING(form_code_list_id)
    JOIN form_code_group_configuration fcgc USING(form_code_group_id)
    WHERE fcgc.state_id = @stateId and fcgc.form_template_id = 2
    AND fcl.name = 'Non_Motorist_Actions_At_Time_Of_Crash' AND fcl.note = 'Default AR - New');

SET @formCodePairId = (select fclpm.form_code_pair_id from form_code_list_pair_map fclpm INNER JOIN `form_code_pair` fcp ON 
(fclpm.`form_code_list_id` = @formCodeListId) WHERE fcp.`form_code_pair_id` = fclpm.`form_code_pair_id` AND fcp.`code` = '104' AND `description` = 'Disabled vehicle related (working on , pushing, leaving, approaching)');
UPDATE `form_code_pair` SET `description` = 'Disabled vehicle related (working on, pushing, leaving, approaching)' WHERE `form_code_pair_id` = @formCodePairId;


SET @formCodelistId = (SELECT DISTINCT fcl.form_code_list_id FROM form_code_list fcl
    JOIN form_code_list_group_map fclgm USING(form_code_list_id)
    JOIN form_code_group_configuration fcgc USING(form_code_group_id)
    WHERE fcgc.state_id = @stateId and fcgc.form_template_id = 2
    AND fcl.name = 'Contributing_Circumstances_Vehicle' AND fcl.note = 'Default AR - New');
-- To remove all Contributing_Circumstances_Vehicle form code pair values
DELETE FROM form_code_list_pair_map WHERE `form_code_list_id` = @formCodeListId;
-- Contributing_Circumstances_Vehicle form code value pairs
SET @code ="000", @description ="None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="100", @description ="Brake";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="101", @description ="Exhaust System";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="102", @description ="Body or Doors";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="103", @description ="Steering";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="104", @description ="Power train";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="105", @description ="Suspensions";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="106", @description ="Tire";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="107", @description ="Wheels";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="108", @description ="Headlights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="109", @description ="Tail Lights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="110", @description ="Turn Signals";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="111", @description ="Windows or Windshield";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="112", @description ="Mirrors";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="113", @description ="wipers";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="114", @description ="Truck coupling, trailer hitch or safety chains";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="115", @description ="Fuel System";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="116", @description ="Cruise Control";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="198", @description ="Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="999", @description ="Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="0", @description ="None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="1", @description ="Too Fast for Conditions";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="2", @description ="Failure to Yield";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="3", @description ="Driving Without Lights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="4", @description ="Failure to Dim Headlights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="5", @description ="Disregard Stop Sign";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="6", @description ="Disregard Yield Sign";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="7", @description ="Disregard Traffic Signal";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="8", @description ="Wrong Side of Road";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="9", @description ="Wrong Way/One Way Traffic";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="10", @description ="Following Too Close";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="11", @description ="Improper Right Turn";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="12", @description ="Improper Left Turn";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="13", @description ="Improper Lane Change";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="14", @description ="Improper Passing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="15", @description ="Prohibited U Turn";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="16", @description ="Defective Lights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="17", @description ="Defective Brakes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="18", @description ="Other Defective Equipment";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="19", @description ="Improper Backing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="20", @description ="Failure or Improper Signal";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="21", @description ="Disregard Officer/Flagman";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="22", @description ="Cutting In";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="23", @description ="Impeding Traffic";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="24", @description ="Improperly Parked";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="25", @description ="Crowded Off Road";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="26", @description ="Alcohol";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="27", @description ="Drugs";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="28", @description ="Careless/Prohibited Driving";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="29", @description ="Crossing Median";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="98", @description ="Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="99", @description ="Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
