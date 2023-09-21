USE ecrash_v3;

-- Insert new state group
INSERT INTO `form_code_group` (`description`) VALUES ('Default KY');
SET @formCodeGroupId = LAST_INSERT_ID();

SET @stateId = (SELECT state_id FROM state WHERE name_abbr = 'KY' LIMIT 1);

-- Kentucky is mapped to default form code group, update the default value by new one.
UPDATE form_code_group_configuration fcgc
SET fcgc.form_code_group_id = @formCodeGroupId
WHERE state_id = @stateId AND form_template_id = 2;


-- Copy form code list from default form code group into Kentucky
SET @defaultFormCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default' LIMIT 1);
INSERT INTO form_code_list_group_map (form_code_group_id, form_code_list_id)
SELECT @formCodeGroupId, fcl.form_code_list_id FROM form_code_list_group_map fclgm
JOIN form_code_list fcl ON fcl.form_code_list_id = fclgm.form_code_list_id
WHERE form_code_group_id = @defaultFormCodeGroupId;


SET @name = 'Weather_Condition', @note = 'Default KY - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "01", @description = "Blowing Sand/Soil/Dirt/Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Clear";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Cloudy";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Fog/Smog/Smoke";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Fog with Rain";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Raining";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Severe Crosswinds";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Sleet/Hail/Freezing rain";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "Snowing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Fog";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Smog/Smoke";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Road_Surface_Condition', @note = 'Default KY - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "01", @description = "Dry";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Ice";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Sand-Mud-Dirt-Oil-Gravel";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Snow/Slush";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Wet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Water (Standing or Moving)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Flooded";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Gravel";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "Sand";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Oil";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Mud-Dirt";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Safety_Equipment_Restraint', @note = 'Default KY - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "Shoulder/Lap Belt";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Installed/Not in Use";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Lap Belt Only";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Shoulder Belt Only";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Child Safety Seat";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Helmet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Helmet Not used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "8", @description = "Other passive Restraint";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Not Installed";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Child Booster Seat";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Child Restraint System - Forward Facing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Child Restraint System - Rear Facing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "80", @description = "Air Bag-Kars";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Ejection', @note = 'Default KY - New', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "Not Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Partially Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Totally Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Alcohol_Use_Suspected', @note = 'Default KY - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "Yes", @description = "Yes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "No", @description = "No";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Contributing_Circumstances_Person', @note = 'Default KY - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "Alcohol Involvement";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Cell Phone";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Disregard Traffic Control";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Distraction";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Drug Involvement";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Emotional";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Exceeded State Speed Limit";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "8", @description = "Failed to Yield Right of Way";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Fatigue";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Fell Asleep";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Following Too Close";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Improper Backing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Improper Passing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Inattention";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "Lost Consciousness/Fainted";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "16", @description = "Medication";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "17", @description = "Misjudge Clearance";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "18", @description = "Not Under Proper Control";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "19", @description = "Overcorrecting/Oversteering";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "20", @description = "Physical Disability";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "21", @description = "Sick";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "22", @description = "Too Fast for Conditions";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "23", @description = "Turning Improperly";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "24", @description = "Weaving in Traffic";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "None Detected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Contributing_Circumstances_Vehicle', @note = 'Default KY - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "Brakes Defective";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Headlights Defective";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Load Securement";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Other Lighting Defective";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Oversized Load on Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Overweight";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Steering Failure";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "8", @description = "Tire Failure";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Tow Hitch Defective/Unit Separation";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Body Doors";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Tires";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Windows, Windshield";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Mirrors";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Wipers";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "None Detected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
