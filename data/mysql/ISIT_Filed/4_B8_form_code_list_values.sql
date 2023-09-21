USE ecrash_v3;
-- ID State
-- Insert new state group
INSERT INTO `form_code_group` (`description`) VALUES ('Default ID');
SET @formCodeGroupId = LAST_INSERT_ID();

SET @stateId = (SELECT state_id FROM state WHERE name_abbr = 'ID' LIMIT 1);

-- Idaho is mapped to default form code group, update the default value by new one.
UPDATE form_code_group_configuration fcgc
SET fcgc.form_code_group_id = @formCodeGroupId
WHERE state_id = @stateId AND form_template_id = 2;

-- Copy form code list from default form code group into Idaho
SET @defaultFormCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default' LIMIT 1);
INSERT INTO form_code_list_group_map (form_code_group_id, form_code_list_id)
SELECT @formCodeGroupId, fcl.form_code_list_id FROM form_code_list_group_map fclgm
JOIN form_code_list fcl ON fcl.form_code_list_id = fclgm.form_code_list_id
WHERE form_code_group_id = @defaultFormCodeGroupId;

-- Form code list value mappings

SET @name = 'Weather_Condition', @note = 'Default ID - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "Clear";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Cloudy";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Rain";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Sleet/Hail";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Fog";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Blowing Dust/Sand";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "8", @description = "Severe Cross Winds";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "A", @description = "Smoke/Smog";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "B", @description = "Blowing Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Road_Surface_Condition', @note = 'Default ID - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "Dry";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Wet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Slush";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Ice";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Mud/Dirt/Gravel";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Water (Standing/Moving)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Oil";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Stand";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Safety_Equipment_Restraint', @note = 'Default ID - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "0", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "1", @description = "Shoulder Belt Only";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Lap Belt Only";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Shoulder and Lap";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Helmet Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "N/A Non-Motorist";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Child Restraint System - Forward Facing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Child Restraint System - Rear Facing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Booster Seat";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "No Helmet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "U", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Ejection', @note = 'Default ID - New', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "Not Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Totally Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Partially Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "T", @description = "Thrown from Cycle/Animal";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Alcohol_Drug_Use', @note = 'Default ID - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "Neither Alcohol nor Drugs Detected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Yes, Alcohol";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Yes, Drugs";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Yes, Both";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Alcohol_Test_Type', @note = 'Default ID - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "None Given";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Test Refused";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Blood Test";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Urine Test";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Breath Test";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Field Test";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));

SET @name = 'Drug_Test_Type', @note = 'Default ID - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "None Given";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Test Refused";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Blood Test";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Urine Test";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Breath Test";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Field Test";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));

SET @name = 'Driver_Distracted_By', @note = 'Default ID - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "Electronic Communication Device (Cell, CB, Radio, etc.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Other Electronic Device (Navigation device, DVD player, IPODS)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Passenger";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Other Inside the Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Previous  Vehicle Crash/Ticketing Incident/Abandoned Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Other External Distraction Outside Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "N/A", @description = "Not Distracted";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Driver_Actions_At_Time_Of_Crash', @note = 'Default ID - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));


SET @code = "1", @description = "Going Straight";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Turning Right";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Right Turn On Red";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Turning Left";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Left Turn on Red";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "U-Turn";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Merging";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "8", @description = "Changing Lances";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Passing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Negotiating Curve";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Stopped in Traffic";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Slowing in Traffic";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Starting in Traffic";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "Parking";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "18", @description = "Backing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "20", @description = "Avoiding Obstacle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "21", @description = "Avoiding Vehicle, Pedestrian, Pedalcycle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "22", @description = "Pursuing Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "23", @description = "Fleeing Pursuit";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "24", @description = "Racking";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "25", @description = "Parked Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "26", @description = "Driverless Vehicle in Motion";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "64", @description = "Entering/Exiting Parked or Standing Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "65", @description = "Entering/Leaving Parking Lot, Driveway, Alley";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "30", @description = "Crossing at Intersection, Crosswalk";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "31", @description = "Crossing at Intersection, NO Crosswalk";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "35", @description = "Crossing at Mid-block, Crosswalk";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "36", @description = "Crossing at Mid-block, NO Crosswalk";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "40", @description = "Walk/Ride with Traffic in Bike Lane";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "41", @description = "Walk/Ride with Traffic NO Bike Lane";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "42", @description = "Walk/Ride Facing Traffic in Bike Lane";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "43", @description = "Walk/Ride Facing Traffic NO Bike Lane";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "44", @description = "Walk/Ride on Sidewalk";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "50", @description = "Standing On Roadway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "51", @description = "Playing ON Roadway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "52", @description = "Working ON Roadway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "60", @description = "Enter/Exit School Bus";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "70", @description = "Not ON Roadway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Contributing_Circumstances_Vehicle', @note = 'Default ID - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "0", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "1", @description = "Exceeded Posted Speed";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Speed Too Fast For Conditions";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Too Slow for Traffic";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Improper Overtaking";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Improper Lane Change";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Following Too Close";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Drove Left of Center";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "8", @description = "Overcorrected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Improper Backing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Improper Turn";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Failed to Signal";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Failed to Yield";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Failed to Obey Stop Sign";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "Failed to Obey Signal";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "21", @description = "Alcohol Impaired";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "22", @description = "Inattention";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "23", @description = "Vision Obstruction";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "24", @description = "Asleep, Drowsy, Fatigued";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "25", @description = "Sick";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "27", @description = "Physical Impairment";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "28", @description = "Improperly Parked";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "31", @description = "Previous Accident";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "32", @description = "Distracted IN or ON Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "34", @description = "Drug Impaired";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "35", @description = "Improper Use of Tum Lane";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "36", @description = "Animal(s) in Roadway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "37", @description = "Emotional - Depressed, Angry, Disturbed";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "38", @description = "Failed to maintain Lane";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "39", @description = "Foot Slipped Off or Caught On Pedal";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "40", @description = "Wrong Side or Wrong Way";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "16", @description = "Tire Defect";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "17", @description = "Wheel Defect";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "18", @description = "Light Defect";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "19", @description = "Other Vehicle Defect";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "41", @description = "Brakes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "42", @description = "Steering";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "43", @description = "Truck Coupling, Trailer Hitch, Safety Chains";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "44", @description = "Wipers";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


-- IL State
-- Form code list value mappings
SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description ="Default IL" LIMIT 1);

SET @name = 'Weather_Condition', @note = 'Default IL - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "Clear";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Rain";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Fog/smoke/haze";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Sleet/Hail";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Severe Cross Wind";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "8", @description = "Cloudy/Overcast";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Freezing Rain/Drizzle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Blowing Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Blowing Sand, Soil, Dirt";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));

SET @name = 'Road_Surface_Condition', @note = 'Default IL - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "Dry";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Wet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Snow or Slush";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Ice";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Sand, Mud, Dirt";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Safety_Equipment_Restraint', @note = 'Default IL - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "None present";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Shoulder and Lap Belt Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Safety Belt Not Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Helmet Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Helmet Not Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Child Restraint Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Child Restraint Used improperly";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "8", @description = "Child Restraint Not Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Usage unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Shoulder/Lap Belt Used Improperly";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Booster Seat";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Child Restraint - Forward Facing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Child Restraint - Rear Facing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Child Restraint - Type Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "Stretcher";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "16", @description = "Wheelchair";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "17", @description = "DOT Compliant Motorcycle Helmet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "18", @description = "Not DOT Compliant Motorcycle Helmet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "19", @description = "Bicycle Helmet (Pedalcyclist Involved only)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));

SET @name = 'Ejection', @note = 'Default IL - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Totally Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Partially Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Trapped/Extricated";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));

SET @name = 'Alcohol_Test_Status', @note = 'Default IL - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "995", @description = "Test Refused";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "996", @description = "Test Not Offered";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Test Performed Results Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Driver_Distracted_By', @note = 'Default IL - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "Cell Phone Handsfree";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Cell Phone Handheld";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Cell Phone - texting, email, etc";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Other Electronic Device (navigation, radio, etc.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Other - Inside Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Other - Outside Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Inattentive/Daydreaming";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Driver_Actions_At_Time_Of_Crash', @note = 'Default IL - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Failed to Yield";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Disregarded Control Devices";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Too Fast of Conditions";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Improper Turn";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Wrong Way/Side";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Followed Too closely";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "8", @description = "Improper Lane Change";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Improper Backing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Improper Passing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Improper Parking";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "License Restrictions";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Stopped School Bus";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Emergency Vehicle on Call";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "Evading Police Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "16", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "44", @description = "Texting";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "45", @description = "Cell Phone Use Other than Texting";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "46", @description = "Overcorrected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Non_Motorist_Actions_At_Time_Of_Crash', @note = 'Default IL - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "3", @description = "Turning Left";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Turning Right";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "20", @description = "Enter from Driver/Alley";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "50", @description = "No Action";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "51", @description = "Crossing - With Signal";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "52", @description = "Crossing - Against Signal";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "53", @description = "School Bus (Within 50ft.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "54", @description = "Parked Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "55", @description = "Not at Intersection";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "56", @description = "With Traffic";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "57", @description = "Against Traffic";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "58", @description = "To/From Disabled Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "59", @description = "Waiting for School Bus";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "60", @description = "Playing/Working on Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "61", @description = "Playing in Roadway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "62", @description = "Standing in Roadway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "63", @description = "Working in Roadway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "64", @description = "Other Action";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "65", @description = "Intoxicated Ped/Pedal";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown/NA";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Contributing_Circumstances_Vehicle', @note = 'Default IL - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Brakes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Steering";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Engine/Motor";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Suspension";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Tires";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Exhaust";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "8", @description = "Lights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Signals";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Windows";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Restraint System";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Wheels";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Trailer Coupling";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Cargo";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "Fuel System";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "16", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


-- IN State
-- Insert new state group
INSERT INTO `form_code_group` (`description`) VALUES ('Default IN');
SET @formCodeGroupId = LAST_INSERT_ID();


SET @stateId = (SELECT state_id FROM state WHERE name_abbr = 'IN' LIMIT 1);

-- Indiana is mapped to default form code group, update the default value by new one.
UPDATE form_code_group_configuration fcgc
SET fcgc.form_code_group_id = @formCodeGroupId
WHERE state_id = @stateId AND form_template_id = 2;


-- Copy form code list from default form code group into Indiana
SET @defaultFormCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default' LIMIT 1);
INSERT INTO form_code_list_group_map (form_code_group_id, form_code_list_id)
SELECT @formCodeGroupId, fcl.form_code_list_id FROM form_code_list_group_map fclgm
JOIN form_code_list fcl ON fcl.form_code_list_id = fclgm.form_code_list_id
WHERE form_code_group_id = @defaultFormCodeGroupId;


-- Form code list value mappings
SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default IN' LIMIT 1);

SET @name = "Weather_Condition", @note = "Default IN - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "Clear", @description = "Clear";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Cloudy", @description = "Cloudy";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Rain", @description = "Rain";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Snow", @description = "Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Sleet/Hail/Freeze Rain", @description = "Sleet/Hail/Freeze Rain";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Fog/Smoke/Smog", @description = "Fog/Smoke/Smog";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Severe Cross Wind", @description = "Severe Cross Wind";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Blowing Sand/Soil/Snow", @description = "Blowing Sand/Soil/Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Road_Surface_Condition", @note = "Default IN - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "Dry", @description = "Dry";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Wet", @description = "Wet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Muddy", @description = "Muddy";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Slush", @description = "Slush";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Snow/Ice", @description = "Snow/Ice";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Snow/Slush", @description = "Snow/Slush";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Ice", @description = "Ice";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Loose Material on Road", @description = "Loose Material on Road";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Water (standing or moving)", @description = "Water (standing or moving)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Other", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Safety_Equipment_Restraint", @note = "Default IN - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "No Restraint", @description = "No Restraint";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Lap Belt Only", @description = "Lap Belt Only";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Harness", @description = "Harness";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Child Restraint", @description = "Child Restraint";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Child Restraint (Missing from Driver set)", @description = "Child Restraint (Missing from Driver set)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Helmet", @description = "Helmet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Airbag", @description = "Airbag";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Airbag Deployed (No Restraint)", @description = "Airbag Deployed (No Restraint)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Airbag Deployed + Harness", @description = "Airbag Deployed + Harness";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Lap + Harness", @description = "Lap + Harness";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "No Helmet", @description = "No Helmet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Other", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Unknown", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Ejection", @note = "Default IN - NEW", @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "Not Ejected", @description = "Not Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Not Ejected or Trapped", @description = "Not Ejected or Trapped";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Partially Ejected", @description = "Partially Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Ejected", @description = "Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Trapped IN", @description = "Trapped IN";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Pinned Under", @description = "Pinned Under";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Unknown", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Alcohol_Drug_Test_Given", @note = "Default IN - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "None", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Alcohol", @description = "Alcohol";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Drug", @description = "Drug";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Alcohol and Drug", @description = "Alcohol and Drug";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Refused", @description = "Refused";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Non_Motorist_Actions_At_Time_Of_Crash", @note = "Default IN - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "On Designated Non-Motorist Lane", @description = "On Designated Non-Motorist Lane";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Not in Roadway", @description = "Not in Roadway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "On Shoulder", @description = "On Shoulder";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "On Roadway", @description = "On Roadway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "With Traffic", @description = "With Traffic";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Against Traffic", @description = "Against Traffic";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Crossing at Intersection", @description = "Crossing at Intersection";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Crossing Not at Intersection", @description = "Crossing Not at Intersection";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Moving", @description = "Moving";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Standing", @description = "Standing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Working", @description = "Working";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Getting In or Out of Vehicle", @description = "Getting In or Out of Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Getting Off or On School Bus", @description = "Getting Off or On School Bus";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Other", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Contributing_Circumstances_Person", @note = "Default IN - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "Alcoholic Beverages", @description = "Alcoholic Beverages";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Illegal Drugs", @description = "Illegal Drugs";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Prescription Drugs", @description = "Prescription Drugs";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Driver Asleep or Fatigued", @description = "Driver Asleep or Fatigued";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Driver Illness", @description = "Driver Illness";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Unsafe Speed", @description = "Unsafe Speed";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Failure to Yield", @description = "Failure to Yield";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Disregard Signal", @description = "Disregard Signal";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Left of Center", @description = "Left of Center";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Improper Passing", @description = "Improper Passing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Improper Turning", @description = "Improper Turning";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Improper Lane Change", @description = "Improper Lane Change";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Following too Closely", @description = "Following too Closely";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Unsafe Backing", @description = "Unsafe Backing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Overcorrecting", @description = "Overcorrecting";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Ran Off Road", @description = "Ran Off Road";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Wrong Way on One Way", @description = "Wrong Way on One Way";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Pedestrian's Actions", @description = "Pedestrian's Actions";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Passenger Distraction", @description = "Passenger Distraction";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Restriction Violation", @description = "Restriction Violation";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Jackknifing", @description = "Jackknifing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Cell Phone Usage", @description = "Cell Phone Usage";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Other Telematics", @description = "Other Telematics";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Driver Distracted", @description = "Driver Distracted";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Speed/Weather Conditions", @description = "Speed/Weather Conditions";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Unsafe Lane Movement", @description = "Unsafe Lane Movement";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Other", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "None", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Contributing_Circumstances_Vehicle", @note = "Default IN - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "Engine Failure or Defective", @description = "Engine Failure or Defective";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Accelerator Failure or Defective", @description = "Accelerator Failure or Defective";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Brake Failure or Defective", @description = "Brake Failure or Defective";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Tire Failure or Defective", @description = "Tire Failure or Defective";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Headlight(s) Defective or Not On", @description = "Headlight(s) Defective or Not On";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Other Lights Defective", @description = "Other Lights Defective";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Steering Failure", @description = "Steering Failure";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Window/Windshield Defective", @description = "Window/Windshield Defective";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Oversize/Overweight Load", @description = "Oversize/Overweight Load";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Insecured/Leaky Load", @description = "Insecured/Leaky Load";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Tow Hitch Failure", @description = "Tow Hitch Failure";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Other Lights Defective", @description = "Other Lights Defective";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "None", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


-- NC State
-- Form code list value mappings
SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default NC' LIMIT 1);

SET @name = "Weather_Condition", @note = "Default NC - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "Clear";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Cloudy";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Rain";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Fog, Smog, Smoke";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Sleet, Hail, Freezing Rain/Drizzle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Severe crosswinds";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "8", @description = "Blowing sand, Dirt, Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Road_Surface_Condition", @note = "Default NC - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "Dry";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Wet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Water (standing, moving)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Ice";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Slush";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Sand, Mud, Dirt, Gravel";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "8", @description = "Fuel, Oil";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Safety_Equipment_Restraint", @note = "Default NC - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "None Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Lap Belt Only";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Shoulder and Lap Belt";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Child Restraint";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Helmet (Motorcyclist or Non-Motorist)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Protective Pads";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Reflective Clothing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "8", @description = "Lighting";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Unable to Determine";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Ejection", @note = "Default NC - NEW", @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "1", @description = "Not Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Totally Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Partially Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Alcohol_Drug_Use", @note = "Default NC - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "0", @description = "No";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "1", @description = "Yes - Alcohol, Impairment Suspected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Yes - Alcohol, No Impairment Suspected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Yes - Other Drugs, Impairment Suspected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Yes - Other Drugs, No Impairment Suspected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Yes - Alcohol and Other Drugs, Impairment Suspected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Yes - Alcohol and Other Drugs, No Impairment Suspected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Contributing_Circumstances_Person", @note = "Default NC - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));


SET @code = "0", @description = "No Contributing Circumstances Indicated";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "1", @description = "Disregarded Yield Sign";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Disregarded Stop Sign";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Disregarded Other Traffic Signs";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Disregarded Traffic Signals";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Disregarded Road Markings";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Exceeded Authorized Speed Limit";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Exceeded Safe Speed for Conditions";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "8", @description = "Failure to Reduce Speed";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Improper Turn";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Right Turn on Red";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Crossed Centerline/Going Wrong Way";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Improper Lane change";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Use of Improper Lane";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Overcorrected/Oversteered";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "Passed Stopped School Bus";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "16", @description = "Passed on Hill";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "17", @description = "Passed on Curve";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "18", @description = "Other Improper Passing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "19", @description = "Failed to Yield Right of Way";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "20", @description = "Inattention";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "21", @description = "Improper Backing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "22", @description = "Improper Parking";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "23", @description = "Driver Distracted";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "24", @description = "Improper or No Signal";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "25", @description = "Followed Too Closely";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "26", @description = "Operated Vehicle in Erratic, Reckless, Careless, Negligent or Aggressive Manner";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "27", @description = "Swerved or Avoided Due to Wind, Slippery Surface, Vehicle, Object or Non-Motorist";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "28", @description = "Visibility Obscured";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "29", @description = "Operated Defective Equipment";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "30", @description = "Alcohol Use";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "31", @description = "Drug Use";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "32", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "33", @description = "Unable to Determine";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "34", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "35", @description = "Driver Distracted by Electronic Communication Device (cell phone, texting, etc.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "36", @description = "Driver Distracted by Other Electronic Device (Navigation Device, DVD Player, etc.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "37", @description = "Driver Distracted by Other Inside the Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "38", @description = "Driver Distracted by External Distraction (Outside the Vehicle)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Non_Motorist_Actions_At_Time_Of_Crash", @note = "Default NC - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "0", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "1", @description = "Coming from Behind Parked Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Darting";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Lying and/or Illegally in the Roadway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Failure to Yield Right of Way";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Not Visible (Dark Clothing, etc.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Inattentive (Talking, Eating, etc.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Failure to Obey Traffic Signs, Signals";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "8", @description = "Wrong Side of Road";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "9", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Contributing_Circumstances_Vehicle", @note = "Default NC - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "0", @description = "None Detected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "1", @description = "Brakes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "2", @description = "Headlights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "3", @description = "Rear Lights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "4", @description = "Steering";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "5", @description = "Tires";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "6", @description = "Other Defects";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "7", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


-- NV state update query
SET @stateId = (SELECT state_id FROM state WHERE name_abbr = 'NV' LIMIT 1);

SET @formCodeGroupId = (SELECT DISTINCT `fcgc`.`form_code_group_id` FROM `form_code_group_configuration` `fcgc` JOIN `form_code_group` `fcg` USING(`form_code_group_id`) WHERE `fcgc`.`state_id` = @stateId AND `fcgc`.`form_template_id` = 2 AND `fcg`.`description` = 'Default NV' ORDER BY `fcg`.`form_code_group_id` DESC LIMIT 1);

SET @name = 'Citation_Type', @note = 'Default NV - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @code = "NRS", @description = "NRS";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "CFR", @description = "CFR";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "CC/MC", @description = "CC/MC";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Pending", @description = "Pending";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


-- SD state
-- Form code list value mappings

SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description ="Default SD" LIMIT 1);

SET @name = 'Weather_Condition', @note = 'Default SD - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "01", @description = "Clear";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Cloudy";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Rain";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Sleet, Hail (freezing rain or";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Fog, Smog, Smoke";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Blowing Sand, Soil, Dirt";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Blowing Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "Severe Crosswinds";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other*";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Road_Surface_Condition', @note = 'Default SD - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "01", @description = "Dry";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Wet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Slush";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Ice";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Frost";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Water (standing, moving)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Sand, Mud, Dirt, Gravel";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "Oil";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));



SET @name = 'Safety_Equipment_Restraint', @note = 'Default SD - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "None Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Lap Belt Only Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Shoulder Harness Only Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Lap Belt and Shoulder Harness Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Helmet Only";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Eye Protection Only";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Helmet and Eye Protection";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Child/Youth Restraint System Used Properly";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Child/Youth Restraint System Used, Not Properly";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "Protective Pads Used (Non-Motorist Only)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Reflective Clothing (Non-Motorist Only)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Lighting (Non-Motorist Only)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other*";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Ejection', @note = 'Default SD - New', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "Not Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Ejected, Totally";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Ejected, Partially";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "96", @description = "Not Applicable (motorcycle, snowmobile, pedestrian, pedalcyclist, etc.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));



SET @name = 'Alcohol_Use_Suspected', @note = 'Default SD - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "None Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Alcohol Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown ";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));



SET @name = 'Drug_Use_Suspected', @note = 'Default SD - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "None Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Drugs Used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));



SET @name = 'Non_Motorist_Actions_At_Time_Of_Crash', @note = 'Default SD - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Improper Crossing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Darting";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Laying and/or Illegally in Roadway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Failure to Yield Right of way";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Not visible (Dark Clothing)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Distracted";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Failure to Obey Traffic Signs, Signals, or Officer";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Wrong Side of Road";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));



SET @name = 'Contributing_Circumstances_Person', @note = 'Default SD - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Failed to Yield to Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Failed to Yield to Pedestrian";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Disregarded Traffic Signs or Signals";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Exceeded Posted Speed Limit";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Driving Too Fast for Conditions";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Improper Turn";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Wrong Side or Wrong Way";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Improper Signal or Failure to Signal";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "Improper Lane Change";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Improper Passing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Improper Start from Parked Position";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Improper Parking";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Improper Backing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Followed Too Closely";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "Failure to Keep in Proper Lane";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "16", @description = "Running off Road";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "17", @description = "Swerving or Avoiding Due to Wind, Slippery Surface, vehicle, Object, Non-motorist, etc.";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "18", @description = "Over-correcting/Over-steering";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "19", @description = "Fatigued/Asleep";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "20", @description = "Drinking";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "21", @description = "Drugs – Medication";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "22", @description = "Drugs – Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "23", @description = "Illness (Heart attack, Stroke, etc.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "24", @description = "Physical Impairment";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "25", @description = "Illegally in Roadway";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "26", @description = "Cell Phone";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "27", @description = "Other Electronic Device (list in narrative)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "28", @description = "Distracted (list distraction in narrative)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other*";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Contributing_Circumstances_Vehicle', @note = 'Default SD - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Brakes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Steering";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Power Train";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Suspension";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Tires";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Exhaust";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Headlights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Signal Lights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "Tail Lights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Horn";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Windowns/Windshield";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Wheels";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Truck Coupling/Trailer Hitch/Safety Chains";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Cargo";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "Fuel System";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "16", @description = "Mirrors";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "17", @description = "Wipers";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "18", @description = "Body, doors, hood";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


-- UT State
-- Form code list value mappings
SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default UT' LIMIT 1);

SET @name = 'Photographs_Taken', @note = 'Default UT - New', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "Yes", @description = "Yes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "No", @description = "No";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Digital", @description = "Digital";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Film", @description = "Film";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Weather_Condition", @note = "Default UT - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "01", @description = "Clear";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Cloudy";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Rain";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Snowing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Blowing Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Sleet, Hail";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Fog, Smog";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Severe Crosswinds,";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "blowing Sand, Soil, Dirt";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Road_Surface_Condition", @note = "Default UT - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "01", @description = "Dry";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Wet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Slush";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Ice";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Water (standing, Moving)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Mud";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Sand, Dirt, Gravel";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "Oil";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));



SET @name = "Safety_Equipment_Restraint", @note = "Default UT - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Lap & Shoulder Belt";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Shoulder Belt Only";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Lap Belt Only";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Child Restraint - Forward Facing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Child Restraint - Rear Facing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Booster Seat";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Helmet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Helmet Plus Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Ejection", @note = "Default UT - NEW", @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "Not Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Totally Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Partially Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "96", @description = "Not Applicable (motorcycle, snowmobile, pedestrian, pedalcyclist etc.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));



SET @name = "Alcohol_Drug_Use", @note = "Default UT - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Alcohol";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Drugs";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Both";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));



SET @name = "Driver_Distracted_By", @note = "Default UT - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Cell Phone";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Radio/CD/DVD etc";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Other Electronic Device";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Passengers";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Texting";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "TV/Monitor";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Other Inside";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Other External";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Contributing_Circumstances_Person", @note = "Default UT - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Exceeded Posted Speed Limit";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Too Fast for Conditions";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Failed to Yield Right-of-Way";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Failed to Keep in Proper Lane";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Improper Lane Change";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Over-Correcting/Over-Steering";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Disregard Traffic Signs";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Disregard Traffic Signals";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "Disregard Road Markings";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Swerved or Evasive Action";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Followed too Closely";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Reckless/Aggressive";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Wrong Side/Wrong Way";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Improper Parking/Stopping";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "Ran Off Road";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "16", @description = "Improper Backing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "17", @description = "Improper Signal";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "18", @description = "Improper Passing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "19", @description = "Improper Turn";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "20", @description = "Hit and Run";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "21", @description = "street Racing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));



SET @name = "Non_Motorist_Actions_At_Time_Of_Crash", @note = "Default UT - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "96", @description = "Not Applicable";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "00", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Improper Crossing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Darting";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Wrong Side of Road";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Not Visible";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Inattentive";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Failure to Obey Traffic Signs, Signals, or Officer";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Failure to Yield Right-of-Way";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "In Roadway (standing, on knees, lying, etc.)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = "Contributing_Circumstances_Vehicle", @note = "Default UT - NEW", @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "00", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "01", @description = "Brakes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Steering";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Power Train";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Suspension";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Tires";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Exhaust";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Headlights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Signal Lights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "Tail Lights";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Horn";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Windows/Windshield";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Wheels";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Trailer Hitch";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Cargo";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "Fuel System";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "16", @description = "Mirrors";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "17", @description = "Wipers";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "18", @description = "Body, Doors, Hood";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "97", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Citation_Issued', @note = 'Default UT - New', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "Yes", @description = "Yes";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "No", @description = "No";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Pending", @description = "Pending";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Unknown", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


-- VI state
-- Form code list value mappings
INSERT INTO `form_code_group` (`description`) VALUES ('Default VI');
SET @formCodeGroupId = LAST_INSERT_ID();

SET @stateId = (SELECT state_id FROM state WHERE name_abbr = 'VI' LIMIT 1);

-- Virgin Island is mapped to default form code group, update the default value by new one.
SET @formCodeGroupConfiguration = (SELECT form_code_group_configuration_id FROM form_code_group_configuration WHERE state_id = @stateId AND form_template_id = 2 LIMIT 1);

-- SELECT @formCodeGroupConfiguration

-- if @formCodeGroupConfiguration has value then use Update query otherwise use insert query
-- UPDATE form_code_group_configuration fcgc SET fcgc.form_code_group_id = @formCodeGroupId WHERE state_id = @stateId AND form_template_id = 2;

INSERT INTO form_code_group_configuration  (state_id,form_template_id,form_code_group_id) VALUES (@stateId, 2,@formCodeGroupId); 

-- Virgin Island default form code list map
SET @defaultFormCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default' LIMIT 1);
INSERT INTO form_code_list_group_map (form_code_group_id, form_code_list_id)
SELECT @formCodeGroupId, fcl.form_code_list_id FROM form_code_list_group_map fclgm
JOIN form_code_list fcl ON fcl.form_code_list_id = fclgm.form_code_list_id
WHERE form_code_group_id = @defaultFormCodeGroupId;


-- Update State to dropdown
SET @name = 'States', @note = NULL, @is_multiselect = NULL;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @code ="YT", @description ="Yukon";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="VI", @description ="Virgin Islands";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));



-- YT state
-- Form code list value mappings
INSERT INTO `form_code_group` (`description`) VALUES ('Default YT');
SET @formCodeGroupId = LAST_INSERT_ID();

SET @stateId = (SELECT state_id FROM state WHERE name_abbr = 'YT' LIMIT 1);

-- Yukon is mapped to default form code group, update the default value by new one.
SET @formCodeGroupConfiguration = (SELECT form_code_group_configuration_id FROM form_code_group_configuration WHERE state_id = @stateId AND form_template_id = 2 LIMIT 1);
-- SELECT @formCodeGroupConfiguration

--  if @formCodeGroupConfiguration has value then use Update query otherwise use insert query
-- UPDATE form_code_group_configuration fcgc SET fcgc.form_code_group_id = @formCodeGroupId WHERE state_id = @stateId AND form_template_id = 2;

INSERT INTO form_code_group_configuration  (state_id,form_template_id,form_code_group_id) VALUES (@stateId, 2,@formCodeGroupId); 

-- Yukon default form code list map
SET @defaultFormCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default' LIMIT 1);
INSERT INTO form_code_list_group_map (form_code_group_id, form_code_list_id)
SELECT @formCodeGroupId, fcl.form_code_list_id FROM form_code_list_group_map fclgm
JOIN form_code_list fcl ON fcl.form_code_list_id = fclgm.form_code_list_id
WHERE form_code_group_id = @defaultFormCodeGroupId;


-- Update State to dropdown
SET @name = 'States', @note = NULL, @is_multiselect = NULL;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));
SET @code ="YT", @description ="Yukon";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code ="VI", @description ="Virgin Islands";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));