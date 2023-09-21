USE ecrash_v3;

-- Insert new state group
INSERT INTO `form_code_group` (`description`) VALUES ('Default DE');
SET @formCodeGroupId = LAST_INSERT_ID();


SET @stateId = (SELECT state_id FROM state WHERE name_abbr = 'DE' LIMIT 1);

-- Delaware is mapped to default form code group, update the default value by new one.
UPDATE form_code_group_configuration fcgc
SET fcgc.form_code_group_id = @FormCodeGroupId
WHERE state_id = @stateId AND form_template_id = 2;

-- Delaware default form code list map
SET @defaultFormCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default' LIMIT 1);
INSERT INTO form_code_list_group_map (form_code_group_id, form_code_list_id)
SELECT @FormCodeGroupId, fcl.form_code_list_id FROM form_code_list_group_map fclgm
JOIN form_code_list fcl ON fcl.form_code_list_id = fclgm.form_code_list_id
WHERE form_code_group_id = @defaultFormCodeGroupId;


SET @name = 'Weather_Condition', @note = 'Default DE - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "Clear", @description = "Clear";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Cloudy", @description = "Cloudy";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Fog, smog, smoke", @description = "Fog, smog, smoke";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Rain", @description = "Rain";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Sleet, hail (freezing rain or drizzle)", @description = "Sleet, hail (freezing rain or drizzle)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Snow", @description = "Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Severe crosswinds", @description = "Severe crosswinds";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Blowing sand, soil, dirt, snow", @description = "Blowing sand, soil, dirt, snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Other", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Unknown", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Road_Surface_Condition', @note = 'Default DE - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "Dry", @description = "Dry";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Wet", @description = "Wet";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Snow", @description = "Snow";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Ice", @description = "Ice";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Sand, mud, dirt, oil, gravel", @description = "Sand, mud, dirt, oil, gravel";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Water (standing, moving)", @description = "Water (standing, moving)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Slush", @description = "Slush";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Other", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Unknown", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));



SET @name = 'Safety_Equipment_Restraint', @note = 'Default DE - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "01", @description = "None used - vehicle occupant";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Shoulder belt only used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Lap belt only used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Shoulder belt and lap belt used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Child safety seat used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Helmet used";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));




SET @name = 'Ejection', @note = 'Default DE - New', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "01", @description = "Not Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Totally Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Partially Ejected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Not applicable";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Transported_To', @note = 'Default DE - New', @is_multiselect = 0;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "01", @description = "AI DuPont Hospital";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Beebe Memorial Hospital";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Christiana Hospital";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Community Hospital (PA)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Crozer Emergency (PA)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Kent General Hospital";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Milford Memorial";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Millville Medical Ctr";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "Nanticoke Memorial";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Newark Emergency Ctr";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Peninsula Reg. Med. Ctr. (MD)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Salem Memorial Hosp. (NJ)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "St. Francis Hospital";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Union Hospital (MD)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "Veterans Hospital";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "16", @description = "Wilmington Hospital";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "17", @description = "Atlantic General (MD)";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "OTHR", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Alcohol_Drug_Use', @note = 'Default DE - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "01", @description = "Neither alcohol nor drugs suspected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Yes - alcohol suspected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Yes - drugs suspected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Yes - alcohol and drugs suspected";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Driver_Distracted_By', @note = 'Default DE - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "None", @description = "None";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Hand Held Cell Phone", @description = "Hand Held Cell Phone";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Grooming/Applying Makeup", @description = "Grooming/Applying Makeup";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Reading", @description = "Reading";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Attending to Children", @description = "Attending to Children";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Verbal Dispute", @description = "Verbal Dispute";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Loose Pet in Vehicle", @description = "Loose Pet in Vehicle";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Head Phones", @description = "Head Phones";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Tuning Radio", @description = "Tuning Radio";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Sexual Activity", @description = "Sexual Activity";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Eating", @description = "Eating";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Unknown", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "Other", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));


SET @name = 'Driver_Actions_At_Time_Of_Crash', @note = 'Default DE - New', @is_multiselect = 1;
SET @formCodelistId = (select ufn_set_form_code_list(@formCodeGroupId, @name, @note, @is_multiselect));

SET @code = "01", @description = "No Improper driving";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "02", @description = "Failed to yield right of way";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "03", @description = "Disregarded traffic signs, signals, road markings";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "04", @description = "Exceeded authorized speed limit";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "05", @description = "Driving too fast for conditions";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "06", @description = "Made an Improper turn";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "07", @description = "Wrong side or wrong way";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "08", @description = "Followed too closely";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "09", @description = "Failure to keep in proper lane or running off road";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "10", @description = "Operating vehicle in inattentive, careless, negligent or aggressive manner";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "11", @description = "Swerving or avoiding due to wind, slippery surface, vehicle, object, non-motorist in roadway, etc";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "12", @description = "Over-correcting/over-steering";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "13", @description = "Visibility obstructed";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "14", @description = "Inattention";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "15", @description = "Distracted";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "16", @description = "Fatigued/asleep";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "17", @description = "Operating Defective equipment";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "18", @description = "Improper Passing";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "19", @description = "Driving Under Influence";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "88", @description = "Other";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "20", @description = "Unsafe Lane Change";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));
SET @code = "99", @description = "Unknown";
SET @formCodePairId = (select ufn_set_form_code_pair(@formCodelistId, @code, @description));

