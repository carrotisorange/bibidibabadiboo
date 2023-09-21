USE ecrash_v3;

SET @stateId = (SELECT `state_id` FROM state WHERE `name_abbr` = 'AK' LIMIT 1);

-- Form code list value mappings
SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default AK' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);

SET @name = 'Photographs_Taken', @note = 'Default AK - New', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "No";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Yes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Weather_Condition', @note = 'Default AK - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "No Additional Atmospheric conditions";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Clear";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Rain";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Sleet or Hail";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Fog, Smog, Smoke";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Severe Crosswinds";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Blowing Sand, Soil Dirt";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Cloudy";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Blowing Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Freezing Rain or Freezing Drizzle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "98", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Road_Surface_Condition', @note = 'Default AK - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "Non-Trafficway Area";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Dry";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Wet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Ice/Frost";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Sand";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Water (Standing, Moving)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Oil";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Slush";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Mud, Dirt, Gravel";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "98", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Safety_Equipment_Restraint', @note = 'Default AK - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "01", @description = "Shoulder Belt Only Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Lap Belt Only Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Shoulder and Lap Belt Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Child Restraint System - Forward Facing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Child Restraint System - Rear Facing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Child Restraint System Type Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Booster Seat";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Restraint Used - Type Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "Reflective Equipment/Clothing (Jacket, Backpack, etc.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Protective Clothing/Pads (Elbows, Knees, Shins, etc.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Lighting";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "DOT-Compliant Motorcycle Helmet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Helmet, Other than DOT - Compliant Motorcycle Helmet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Helmet, Unknown if DOT - Compliant Motorcycle Helmet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "No Helmet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "16", @description = "No Restraint System Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "17", @description = "No Safety Equipment Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "96", @description = "Other Restrain System";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other Safety Equipment";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "98", @description = "Unknown if Helmet Worn";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown if Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Ejection', @note = 'Default AK - New', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "Not Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Totally Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Partially Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Not Applicable";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown If Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Alcohol_Use_Suspected', @note = 'Default AK - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "No";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Yes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Drug_Use_Suspected', @note = 'Default AK - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "No";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Yes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Driver_Distracted_By', @note = 'Default AK - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "Not Distracted";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Manually Operating an Electronic Communication Device (Texting, Typing, Dialing)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Talking on Hand-Free Electronic Device";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Talking on Hand-Held Electronic Device";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Other Activity, Electronic Device (Navigation Device, DVD Player, etc.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Passenger";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Other Inside the Vehicle (Eating, Personal Hygiene, etc.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Outside the Vehicle (Includes Unspecified External Distractions)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Not Applicable";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown if Distracted";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Contributing_Circumstances_Person', @note = 'Default AK - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "No Contributing Action/Circumstance";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Rain Off Roadway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Failed to Yield Right-of-Way";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Ran Red Light";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Ran Stop Sign";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Failure to Obey Other Traffic Signs, Signals, Officer, etc.";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Failure to Signal Intentions";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Disregarded Other Road Markings";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Improper Turn";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "Improper Merge";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Improper Backing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Improper Passing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Passing with Insufficient Distance or Inadequate Visibility";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Failure to Yield to Overtaking Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Improper or Erratic Lane Changing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "Making Improper Entry to or Exit from Trafficway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "16", @description = "Wrong Side or Wrong Way";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "17", @description = "Followed Too Closely";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "18", @description = "Failed to Keep in Proper Lane";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "19", @description = "Operated Motor Vehicle in an Inattentive, Careless, Erratic, or Negligent Manner";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "20", @description = "Operated Motor Vehicle in a Reckless or Aggressive Manner";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "21", @description = "Swerved or Avoided Due to Wind, Slippery Surface, Motor Vehicle, Object, Non-Motorist in Roadway, etc.";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "22", @description = "Over-Correcting/Over-Steering";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "23", @description = "Reaction to or Failure to Take Drugs/Medication";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "24", @description = "Unlawful Driving on Sidewalk or Pathway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "25", @description = "Improper/Unsecure Load (with Passengers or Cargo)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "26", @description = "Towing or Pushing Improperly";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "27", @description = "Operating Without Required Equipment";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "28", @description = "Interfering with Driver";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "29", @description = "Construction/Maintenance/Utility Worker";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "30", @description = "Operator Inexperience";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "31", @description = "Pedestrian Error/Confusion";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "32", @description = "Emergency Services Personnel";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "33", @description = "Police or Law Enforcement Officer";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "34", @description = "Police Pursuing this Driver";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "35", @description = "Police Officer in Pursuit";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "36", @description = "Dart/Dash";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "37", @description = "In Roadway Improperly (Standing, Lying, Working, Playing)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "38", @description = "Disabled Vehicle Related (Working on, Pushing, Leaving/Approaching)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "39", @description = "Entering/Exiting Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "40", @description = "Improper Crossing of Roadway or Intersection (Jaywalking)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "41", @description = "Not Visible (Dark Clothing, No Lighting, etc.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "42", @description = "Set Vehicle in Motion";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "98", @description = "Other Contributing Action/Circumstance";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Prior_Nonmotorist_Action', @note = 'Default AK - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Waiting to Cross Roadway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Crossing Roadway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Jogging/Running";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Movement Along Roadway with Traffic (In or Adjacent to Travel Lane)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Movement Along Roadway Against Traffic (In or Adjacent to Travel Lane)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Movement on Sidewalk";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "In Roadway - Other (Working, Playing, Etc.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Adjacent to Roadway (e.g., Shoulder, Median)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "Working in Trafficway (Incident Response)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Entering/Exiting a Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Disabled Vehicle Related (Working on, Pushing, Leaving/Approaching)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Not Applicable";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "98", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Contributing_Circumstances_Vehicle', @note = 'Default AK - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Tires";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Brake System";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Steering";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Suspension";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Power Train";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Exhaust System";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Head Lights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Signal Lights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "Other Lights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Wipers";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Wheels";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Mirrors";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Windowns/Windshield";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Body, Doors";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "Truck Coupling/Trailer Hitch/Safety Chains";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "16", @description = "Safety Systems";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "98", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
