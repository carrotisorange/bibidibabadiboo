-- Form code list value mappings
SET @formCodeGroupId = (SELECT form_code_group_id FROM form_code_group WHERE description = 'Default TX' LIMIT 1);

-- eCrash DB field: contributing_circucmstance_v
INSERT INTO `form_code_list` (`name`, `note`) VALUES ('Contributing_Circumstances_Vehicle', 'Default TX - New');
SET @formCodeListId = LAST_INSERT_ID();

INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodeListId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5","Defective or No Headlamps");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6","Defective or No Stop Lamps");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7","Defective or No Tail Lamps");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("8","Defective or No Turn Signal Lamps");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("9","Defective or No Trailer Brakes");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("10","Defective or No Vehicle Brakes");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("11","Defective Steering Mechanism");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("12","Defective or Slick Tires");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("13","Defective Trailer Hitch");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("98","Other (Explain in Narrative)");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);


-- eCrash DB field: contributing_circucmstance_v
INSERT INTO `form_code_list` (`name`, `note`) VALUES ('Contributing_Circumstances_Person', 'Default TX - New');
SET @formCodeListId = LAST_INSERT_ID();

INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodeListId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1","Animal on Road - Domestic");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2","Animal on Road - Wild");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3","Backed without Safety");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4","Changed Lane when Unsafe");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("14","Disabled in Traffic Lane");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("15","Disregard Stop and Go Signal");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("16","Disregard Stop Sign or Light");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("17","Disregard Turn Marks at Intersection");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("18","Disregard Warning Sign at Construction");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("19","Distraction in Vehicle");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("20","Driver Inattention");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("21","Drove Without Headlights");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("22","Failed to Control Speed");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("23","Failed to Drive in Single Lane");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("24","Failed to Give Half of Roadway");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("25","Failed to Heed Warning Sign");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("26","Failed to Pass to Left Safely");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("27","Failed to Pass to Right Safely");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("28","Failed to Signal or Gave Wrong Signal");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("29","Failed to Stop at Proper Place");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("30","Failed to Stop for School Bus");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("31","Failed to Stop for Train");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("32","Failed to Yield ROW – Emergency Vehicle");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("33","Failed to Yield ROW – Open Intersection");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("34","Failed to Yield ROW – Private Drive");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("35","Failed to Yield ROW – Stop Sign");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("36","Failed to Yield ROW – To Pedestrian");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("37","Failed to Yield ROW – Turning Left");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("38","Failed to Yield ROW – Turn on Red");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("39","Failed to Yield ROW – Yield Sign");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("40","Fatigued or Asleep");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("41","Faulty Evasive Action");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("42","Fire in Vehicle");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("43","Fleeing or Evading Police");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("44","Followed Too Closely");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("45","Had Been Drinking");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("46","Handicapped Driver (Explain in Narrative)");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("47","Ill (Explain in Narrative)");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("48","Impaired Visibility (Explain in Narrative)");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("49","Improper Start from Parked Position");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("50","Load Not Secured");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("51","Opened Door Into Traffic Lane");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("52","Oversized Vehicle or Load");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("53","Overtake and Pass Insufficient Clear");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("54","Parked and Failed to Set Brakes");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("55","Parked in Traffic Lane");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("56","Parked without Lights");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("57","Passed in No Passing Lane");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("58","Passed on Right Shoulder");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("59","Pedestrian FTYROW to Vehicle");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("60","Unsafe Speed");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("61","Speeding – (Over Limit)");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("62","Taking Medication (Explain in Narrative)");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("63","Turned Improperly – Cut Corner on Left");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("64","Turned Improperly – Wide Right");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("65","Turned Improperly – Wrong Lane");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("66","Turned when Unsafe");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("67","Under Influence – Alcohol");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("68","Under Influence – Drug");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("69","Wrong Side – Approach or Intersection");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("70","Wrong Side – Not Passing");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("71","Wrong Way – One Way Road");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("73","Road Rage");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("74","Cell/Mobile Device Use - Talking");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("75","Cell/Mobile Device Use - Texting");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("76","Cell/Mobile Device Use - Other");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("77","Cell/Mobile Device Use - Unknown");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("98","Other (explain in narrative)");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);


-- eCrash DB field: drug_test_type
INSERT INTO `form_code_list` (`name`, `note`) VALUES ('Drug_Test_Type', 'Default TX - New');
SET @formCodeListId = LAST_INSERT_ID();

INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodeListId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2","Blood");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3","Urine");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4","Refused");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("96","None");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("98","Other (Explain in Narrative)");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);


-- eCrash DB field: alcohol_test_type
INSERT INTO `form_code_list` (`name`, `note`) VALUES ('Alcohol_Test_Type', 'Default TX - New');
SET @formCodeListId = LAST_INSERT_ID();

INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodeListId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1","Breath");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2","Blood");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3","Urine");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4","Refused");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("96","None");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("98","Other (Explain in Narrative)");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);

-- eCrash DB field: Ejection
INSERT INTO `form_code_list` (`name`, `note`) VALUES ('Ejection', 'Default TX - New');
SET @formCodeListId = LAST_INSERT_ID();

INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodeListId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1","No");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2","Yes");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3","Yes, Partial");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("97","Not Applicable");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("99","Unknown");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);


-- eCrash DB field: Safety_Equipment_Helmet
INSERT INTO `form_code_list` (`name`, `note`) VALUES ('Safety_Equipment_Helmet', 'Default TX - New');
SET @formCodeListId = LAST_INSERT_ID();

INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodeListId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1","Not Worn");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2","Worn, Damaged");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3","Worn, Not Damaged");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4","Worn, Unk. Damaged");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("97","Not Applicable");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("99","Unknown if Worn");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);

-- eCrash DB field: Safety_Equipment_Restraint1
INSERT INTO `form_code_list` (`name`, `note`) VALUES ('Safety_Equipment_Restraint', 'Default TX - New');
SET @formCodeListId = LAST_INSERT_ID();

INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodeListId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1","Shoulder and Lap Belt");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2","Shoulder Belt Only");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3","Lap Belt Only");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4","Child Seat, Facing Forward");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5","Child Seat, Facing Rear");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6","Child Seat, Unknown");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7","Child Booster Seat");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("96","None");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("97","Not Applicable");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("98","Other(Explain in Narrative)");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("99","Unknown");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);

-- eCrash DB field: road_condition
INSERT INTO `form_code_list` (`name`, `note`) VALUES ('Road_Surface_Condition', 'Default TX - New');
SET @formCodeListId = LAST_INSERT_ID();

INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodeListId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1","Dry");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2","Wet");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3","Standing Water");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4","Snow");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5","Slush");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6","Ice");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7","Sand, Mud, Dirt");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("98","Other (Explain in Narrative)");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("99","Unknown");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);


-- eCrash DB field: weather_condition
INSERT INTO `form_code_list` (`name`, `note`) VALUES ('Weather_Condition', 'Default TX - New');
SET @formCodeListId = LAST_INSERT_ID();

INSERT INTO `form_code_list_group_map` (`form_code_group_id`, `form_code_list_id`) VALUES (@formCodeGroupId, @formCodeListId);

INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("1","Clear");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("2","Cloudy");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("3","Rain");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("4","Sleet/Hail");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("5","Snow");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("6","Fog");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("7","Blowing Sand/Snow");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("8","Severe Crosswinds");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("98","Other (Explain the Narrative)");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
INSERT INTO `form_code_pair` (`code`, `description`) VALUES ("99","Unknown");
SET @formCodeMapId = LAST_INSERT_ID();
INSERT INTO `form_code_list_pair_map` (`form_code_list_id`, `form_code_pair_id`) VALUES (@formCodeListId,@formCodeMapId);
