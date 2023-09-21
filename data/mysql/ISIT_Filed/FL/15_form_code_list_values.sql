-- Form code list value mappings
SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default FL' LIMIT 1);

-- eCrash DB field: weather_condition
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Weather_Condition", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Clear");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Cloudy");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Rain");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "Fog, Smog,Smoke");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5", "Sleet/Hail/Freeze Rain");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6", "Blowing Sand, Soil, Dirt");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7", "Severe Crosswinds");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("77", "Other (Narrative may contain clarifying information)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);


-- eCrash DB field: road_condition
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Road_Surface_Condition", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Dry");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Wet");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "Ice/Frost");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5", "Oil");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6", "Mud, Dirt, Gravel");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7", "Sand");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("8", "Water (standing/moving)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("77", "Other Explain In Narrative");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("88", "Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: contributing_circumstances_v
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Contributing_Circumstances_Vehicle", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "None");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Brakes");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Tires");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "Lights (head, signal, tail)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6", "Steering");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7", "Wipers");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("9", "Exhaust System");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("10", "Body, Doors");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("11", "Power Train");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("12", "Suspension");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("13", "Wheels");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("14", "Windows/Windshield");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("15", "Mirrors");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("16", "Truck Coupling/Trailer Hitch/Safety Chains");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("77", "Other, Explain in Narrative");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("88", "Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: Person_Type
INSERT INTO `form_code_list` (`name`, `note`) VALUES ("Person_Type", "Default FL - New");
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Driver");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Non Motor");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Passenger");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: safety_equipment_restraint
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Safety_Equipment_Restraint", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Not Applicable");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "None Used - Motor Vehicle Occupant");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Shoulder and Lap belt used");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "Shoulder Belt Only Used");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5", "Lap Belt Only Used");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6", "Restraint Used - Typed Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7", "Child Restraint System - Forward Facing");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("8", "Child Restraint System - Rear Facing");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("9", "Booster Seat");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("10", "Child Restraint Type Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("77", "Other (Narrative may contain clarifying information)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: Safety_Equipment_Helmet
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Safety_Equipment_Helmet", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "DOT Compliant Motorcycle Helmet");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Other Helmet");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "No Helmet");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: Ejection
INSERT INTO `form_code_list` (`name`, `note`) VALUES ("Ejection", "Default FL - New");
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Not Ejected");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Ejected, Totally");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Ejected, Partially");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "Not Applicable");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("88", "Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: alcohol_use_suspected
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Alcohol_Use_Suspected", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "No");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Yes");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("88", "Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: drug_use_suspected
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Drug_Use_Suspected", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "No");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Yes");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("88", "Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: driver_distracted_by
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Driver_Distracted_By", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Not Distracted");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Electronic Communication Devices (cell phone, etc.)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Other Electronic Device (navigation device, DVD Player)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "Other Inside the Vehicle (explain in Narrative)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5", "External Distraction (outside the Vehicle, explain in Narrative)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6", "Texting");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7", "Inattentive");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("88", "Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: driver_actions_at_time_of_crash
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Driver_Actions_At_Time_Of_Crash", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "No Contributing Action");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Operated MV in Careless or Negligent Manner");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Failed to Yield  Right-of-way");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "Improper Backing");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6", "Improper Turn");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("10", "Followed Too Closely");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("11", "Ran Red Light");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("12", "Drove Too Fast For Conditions");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("13", "Ran Stop Sign");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("15", "Improper Passing");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("17", "Exceeded Posted Speed");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("21", "Wrong Side of Wrong Way");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("25", "Failed to Keep in Proper Lane");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("26", "Ran off Roadway");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("27", "Disregarded other Traffic Sign");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("28", "Disregarded other Road Markings");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("29", "Over-Correcting/Oversteering");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("30", "Swerved or Avoided: Due to Wind, Slippery Surface, MV, Object, Non-Motorist in Roadway, etc.");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("31", "Operated MV in Erratic Reckless or Aggressive Manner");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("77", "Other Contributing Action");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: non_motorist_actions_at_time_of_crash
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Non_Motorist_Actions_At_Time_Of_Crash", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "No Improper Action");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Dark/Dash");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Failure to Yield Right of Way");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "Failure to Obey Traffic Signs, signals, or Officer");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5", "In Roadway Improperly (standing, lying, working, playing)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6", "Disabled Vehicle Related (working on, pushing, leaving/approaching)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7", "Entering/Exited Parked/Standing Vehicle");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("8", "Inattentive (talking, eating, etc.)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("9", "Not Visible (dark clothing, no lighting, etc.)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("10", "Improper Turn/Merge");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("11", "Improper Passing");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("12", "Wrong Way Riding or Walking");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("77", "Other, Explain in Narrative");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("88", "Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: manner_crash_impact
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Manner_Crash_Impact", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Front to Rear");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Front to Front");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Angle");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "Sidesweipe, same direction");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5", "Sidesweipe, opposite direction");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6", "Rear to Side");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7", "Rear to Rear");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("77", "Other, Explain in Narrative");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("88", "Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: unit_type
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Unit_Type", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Passenger Car");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Passenger Van");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Pickup");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7", "Motor Home");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("8", "Bus");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("11", "Motorcycle");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("12", "Moped");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("13", "All terrain vehicle (atv)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("15", "Low Speed Vehicle");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("16", "(Sport) Utility Vehicle");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("17", "Cargo Van (10,000 lbs (4,536 kg) or less)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("18", "Motor Coach");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("19", "Other Light Truck (10,000 lbs (4,536 kg) or less)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("20", "Medium/Heavy Trucks (more than 10,000 lbs (4.536 k");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("21", "Farm Labor Vehicle");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("77", "Other, explain in narrative");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("88", "Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: most_harmful_event_v
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Most_Harmful_Event_for_Vehicle", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Overturn/Rollover");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Fire/Explosion");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Immersion");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "Jackknife");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5", "Cargo/Equipment Loss or Shift");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6", "Fell/Jumped From Motor Vehicle");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7", "Thrown or Falling Object");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("8", "Rain into Water/Canal");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("9", "Other Non Collision");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("10", "Pedestrian");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("11", "Pedalcycle");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("12", "Railway Vehicle (train, engine)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("13", "Animal");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("14", "Motor Vehicle in Transport");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("15", "Parked Motor Vehicle");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("16", "Work Zone/Maintenance Equipment");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("17", "Struck By Falling, Shifting Cargo or Anything Set");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("18", "Other Non Fixed Object");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("19", "Impact Attenuator/Crash Cushion");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("20", "Bridge Overhead Structure");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("21", "Bridge Pier or Support");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("22", "Bridge Rail");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("23", "Culvert");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("24", "Curb");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("25", "Ditch");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("26", "Embankment");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("27", "Guardrail Face");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("28", "Guardrail End");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("29", "Cable Barrier");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("30", "Concrete Traffic Barrier");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("31", "Other Traffic Barrier");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("32", "Tree (standing)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("33", "Utility Pole/Light Support");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("34", "Traffic Sign Support");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("35", "Traffic Signal Support");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("36", "Other Post, Pole, or Support");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("37", "Fence");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("38", "Mailbox");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("39", "Other Fixed Object (wall, building, tunnel, etc.)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: event_sequence
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Event_Sequence", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Overturn/Rollover");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Fire/Explosion");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Immersion");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "Jackknife");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5", "Cargo/Equipment Loss or Shift");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6", "Fell/Jumped From Motor Vehicle");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7", "Thrown or Falling Object");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("8", "Rain into Water/Canal");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("9", "Other Non Collision");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("10", "Pedestrian");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("11", "Pedalcycle");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("12", "Railway Vehicle (train, engine)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("13", "Animal");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("14", "Motor Vehicle in Transport");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("15", "Parked Motor Vehicle");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("16", "Work Zone/Maintenance Equipment");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("17", "Struck By Falling, Shifting Cargo or Anything Set");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("18", "Other Non Fixed Object");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("19", "Impact Attenuator/Crash Cushion");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("20", "Bridge Overhead Structure");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("21", "Bridge Pier or Support");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("22", "Bridge Rail");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("23", "Culvert");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("24", "Curb");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("25", "Ditch");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("26", "Embankment");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("27", "Guardrail Face");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("28", "Guardrail End");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("29", "Cable Barrier");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("30", "Concrete Traffic Barrier");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("31", "Other Traffic Barrier");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("32", "Tree (standing)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("33", "Utility Pole/Light Support");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("34", "Traffic Sign Support");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("35", "Traffic Signal Support");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("36", "Other Post, Pole, or Support");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("37", "Fence");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("38", "Mailbox");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("39", "Other Fixed Object (wall, building, tunnel, etc.)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: Road_Type
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Road_Type", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Interstate");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "U.S.");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "State");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "Country");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5", "Local");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6", "Tumpike/Toll");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7", "Forest Raod");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("8", "Private Road");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("9", "Parking Lot");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("77", "All other, Explain in Narrative");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: intersection_type
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Intersection_Type", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Not at Intersection");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Four Way Intersection");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "T Intersection");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "Y Intersection");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5", "Traffic Circle");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6", "Roundabout");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7", "Five Point, or More");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("77", "Other Explain in Narrative");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: vehicle_maneuver_action_prior
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Vehicle_Maneuver_Action_Prior", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Straight Ahead");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Turning Left");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "Backing");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5", "Turning Right");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6", "Changing Lances");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("8", "Parked");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("10", "Making U-Turn");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("11", "Overtaking/Passing");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("13", "Stopped in Traffic");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("14", "Slowing");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("15", "Negotiating a Curve");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("16", "Leaving Traffic Lane");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("17", "Entering Traffic lane");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("77", "Other, Explain in Narrative");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("88", "Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: condition_at_time_of_crash
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Condition_At_Time_Of_Crash", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Apparently Normal");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Asleep or Fatigued");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5", "Ill (sick) or Fainted");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6", "Seizure, Epilepsy, Blackout");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7", "Physically Impaired");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("8", "Emotional (depression, angry, disturbed, etc.)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("9", "Under the Influence of Medications/Drugs/Alcohol");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("77", "other, explain in narrative");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("88", "Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: trafficway_description
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Trafficway_Description", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Two Way, Not Divided");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Two Way, Not Divided, with a Continuous Left Turn Lane");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Two Way, Divided, Unprotected (painted >4feet) Median");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "Two Way, Divided, Positive Median Barrier");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5", "One Way Trafficway");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("88", "Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: traffic_control_device_type
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Traffic_Control_Device_Type", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "No Controls");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "School Zone Sign/Device");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5", "Traffic Controls Signal");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6", "Stop Sign");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7", "Yield Sign");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("8", "Flashing Signal");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("9", "Railway Crossing Devices");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("10", "Person (including Flagman, Officer, Guard, etc.)");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("13", "Warning Sign");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("77", "Other, Explain in Narrative");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("88", "Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: light_condition 
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Light_Condition", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Daylight");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Dusk");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Dawn");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4", "Dark Lighted");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5", "Dark Not Lighted");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6", "Dark Unknown Lighting");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("77", "Other");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("88", "Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: alcohol_test_status 
INSERT INTO `form_code_list` (`name`, `note`, `is_multiselect`) VALUES ("Alcohol_Test_Status", "Default FL - New", 1);
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Test Not Given");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Test Refused");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3", "Test Given");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("88", "Unknown if Tested");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

-- eCrash DB field: Sex
INSERT INTO `form_code_list` (`name`, `note`) VALUES ("Sex", "Default FL - New");
SET @formCodelistId = LAST_INSERT_ID();
INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodelistId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1", "Male");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2", "Female");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("88", "Unknown");
SET @formCodePairId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodelistId, @formCodePairId);