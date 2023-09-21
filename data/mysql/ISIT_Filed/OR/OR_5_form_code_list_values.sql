USE ecrash_v3;


SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description ="Default OR" LIMIT 1);

SET @name = 'Road_Surface_Condition', @note = 'Default OR - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "Dry", @description = "Dry";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Wet", @description = "Wet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Snow/slush", @description = "Snow/slush";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Icy", @description = "Icy";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Muddy", @description = "Muddy";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Debris", @description = "Debris";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Ruts/Holes/Bumps", @description = "Ruts/Holes/Bumps";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Worn/Polished", @description = "Worn/Polished";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Low/Soft Shoulder", @description = "Low/Soft Shoulder";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Other (explain)", @description = "Other (explain)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Weather_Condition', @note = 'Default OR - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "Clear", @description = "Clear";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Cloudy (Overcast)", @description = "Cloudy (Overcast)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Rain", @description = "Rain";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Snow", @description = "Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Sleet/Hail/Etc.", @description = "Sleet/Hail/Etc.";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Fog/Smog", @description = "Fog/Smog";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Smoke", @description = "Smoke";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Blowing Sand/Dirt", @description = "Blowing Sand/Dirt";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Severe Crosswind", @description = "Severe Crosswind";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Other/Unknown", @description = "Other/Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Safety_Equipment_Restraint', @note = 'Default OR - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "No Eqp used", @description = "No Eqp used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Lap Only", @description = "Lap Only";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Lap/Shoulder", @description = "Lap/Shoulder";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Child Rst-prp", @description = "Child Rst-prp";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "A/Bag-deplyd", @description = "A/Bag-deplyd";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "None instld", @description = "None instld";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Unknown", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Shldr Only", @description = "Shldr Only";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Helmet", @description = "Helmet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Child Rst - impr", @description = "Child Rst - impr";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Abag-not dp", @description = "Abag-not dp";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Ejection', @note = 'Default OR - New', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "Y", @description = "Yes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "N", @description = "No";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "P", @description = "Probable";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Alcohol_Drug_Use', @note = 'Default OR - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "None", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Under infl-drugs", @description = "Under infl-drugs";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Under infl-alcohol", @description = "Under infl-alcohol";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Under infl - meds", @description = "Under infl - meds";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Under infl - marijuana", @description = "Under infl - marijuana";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Unknown", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Contributing_Circumstances_Person', @note = 'Default OR - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "None", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Cell Phone Use", @description = "Cell Phone Use";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Obstructed View", @description = "Obstructed View";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Failed to yield row", @description = "Failed to yield row";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Disrgrd traf sign", @description = "Disrgrd traf sign";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Too fast for cond", @description = "Too fast for cond";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Made improper turn", @description = "Made improper turn";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Wrong side/way", @description = "Wrong side/way";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Follow too closely", @description = "Follow too closely";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Improper lane change", @description = "Improper lane change";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Improper backing", @description = "Improper backing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Improper passing", @description = "Improper passing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Improper signal", @description = "Improper signal";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Improper parking", @description = "Improper parking";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Fatigue/drowsy", @description = "Fatigue/drowsy";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Ill", @description = "Ill";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Blackout", @description = "Blackout";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Inattentive", @description = "Inattentive";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Distracted", @description = "Distracted";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Unknown", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Improp Restr Eqp Use", @description = "Improp Restr Eqp Use";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Other (explain)", @description = "Other (explain)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Contributing_Circumstances_Vehicle', @note = 'Default OR - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "None", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Brakes", @description = "Brakes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Steering", @description = "Steering";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Power plant", @description = "Power plant";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Suspension", @description = "Suspension";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Tires", @description = "Tires";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Exhaust", @description = "Exhaust";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Lights", @description = "Lights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Signals", @description = "Signals";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Windows/windshield", @description = "Windows/windshield";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Restraint System", @description = "Restraint System";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Wheels", @description = "Wheels";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "coupling", @description = "coupling";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Cargo", @description = "Cargo";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Other", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Non_Motorist_Actions_At_Time_Of_Crash', @note = 'Default OR - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "None", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Failed to yield ROW", @description = "Failed to yield ROW";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Disgreard Traffic Sign", @description = "Disgreard Traffic Sign";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Illegally In Road", @description = "Illegally In Road";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Equipment Violation", @description = "Equipment Violation";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Clothing Not Visible", @description = "Clothing Not Visible";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Under Infl- Drugs", @description = "Under Infl- Drugs";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Under Inful - Alcohol", @description = "Under Inful - Alcohol";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Inattentive", @description = "Inattentive";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Distracted", @description = "Distracted";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Cell Phone", @description = "Cell Phone";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Unknown", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Other (explain)", @description = "Other (explain)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Passenger_Actions_At_Time_Of_Crash', @note = 'Default OR - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "None", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Interfered w/Driver", @description = "Interfered w/Driver";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Under Infl - Drugs", @description = "Under Infl - Drugs";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Under Infl - Alcohol", @description = "Under Infl - Alcohol";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Unknown", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Improper Restr Equ Use", @description = "Improper Restr Equ Use";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Other (explain)", @description = "Other (explain)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


-- To drop newly[temporarily] created function
DROP FUNCTION IF EXISTS ufn_set_form_code_list;
DROP FUNCTION IF EXISTS ufn_set_form_code_pair;