<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Exception;
use Zend\Soap\Client;
use Base\Adapter\Db\AutoExtractionDataAdapter;
use Base\Adapter\Db\AutoExtractionImageProcessAdapter;
use Base\Adapter\Db\AutozoningDataCoordinateAdapter;

class AutoExtractionService extends BaseService
{    
    /**
     * @var Base\Adapter\Db\AutoExtractionDataAdapter
     */
    protected $adapterAutoExtractionData;

    /**
     * @var Base\Adapter\Db\AutoExtractionImageProcessAdapter
     */
    protected $adapterAutoExtractionImageProcess;
    protected $adapterAutozoningDataCoordinate;
    
    public function __construct(
        AutoExtractionDataAdapter $adapterAutoExtractionData,
        AutoExtractionImageProcessAdapter $adapterAutoExtractionImageProcess,
        AutozoningDataCoordinateAdapter $adapterAutozoningDataCoordinate)
    {
        $this->adapterAutoExtractionData   = $adapterAutoExtractionData;
        $this->adapterAutoExtractionImageProcess   = $adapterAutoExtractionImageProcess;
        $this->adapterAutozoningDataCoordinate = $adapterAutozoningDataCoordinate;
    }
    
    public function getExtractedData($reportId)
    {
	return $this->adapterAutoExtractionData->getExtractedData($reportId);		
    }            

    public function hasAutoExtracted($reportId)
    {
        return $this->adapterAutoExtractionImageProcess->hasAutoExtracted($reportId);
    }

    public function updateHandwrittenReport($reportId)
    {
        return $this->adapterAutoExtractionImageProcess->updateHandwrittenReport($reportId);
    }
    
    /**
     * Get narrative data in auto_extraction_data table
     *
     * @param int $reportId
     * @return string narrative data
     */
    public function getNarrativeData($reportId)
    {
        return $this->adapterAutoExtractionData->getNarrativeData($reportId);
    }

    public function getCoordinateData($reportId) {
        //call the adapter here and extract the json coordinates
        $jsonString = <<<EOF
        {
            "Incident": {
                "Crash_Date": "",
                "Case_Identifier": "744,172,1183,209, 1",
                "State_Report_Number": "1189,171,1615,208, 1",
                "Crash_City": "719,271,1094,326, 1",
                "Loss_Street": "19,465,813,503, 1",
                "Loss_Cross_Street": "517,529,1373,568, 1",
                "Latitude": "1196,465,1615,505, 1",
                "Longitude": "1196,465,1615,505, 1",
                "Loss_State_Abbr": "",
                "Report_Type_Id": "",
                "Gps_Other": "",
                "Dispatch_Time": "",
                "Weather_Condition": "",
                "Road_Surface_Condition": "",
                "Manner_Crash_Impact": "",
                "Road_Type": "",
                "Intersection_Type": "",
                "Light_Condition": ""
            },
            "People": [
                {
                    "Party_Id": "",
                    "Person_Type": "",
                    "Unit_Number": "",
                    "First_Name": "27,1176,1616,1210,1",
                    "Middle_Name": "",
                    "Last_Name": "27,1176,1616,1210,1",
                    "Name_Suffix": "",
                    "Sex": "",
                    "Address": "27,1176,1616,1210,1",
                    "Address2": "",
                    "City": "27,1176,1616,1210,1",
                    "State": "27,1176,1616,1210,1",
                    "Zip_Code": "27,1176,1616,1210,1",
                    "Home_Phone": "",
                    "Date_Of_Birth": "",
                    "Drivers_License_Number": "",
                    "Drivers_License_Jurisdiction": "",
                    "Injury_Status": ""
                },
                {
                    "Party_Id": "",
                    "Person_Type": "",
                    "Unit_Number": "",
                    "First_Name": "26,115,1630,150,2",
                    "Middle_Name": "26,115,1630,150,2",
                    "Last_Name": "26,115,1630,150,2",
                    "Name_Suffix": "",
                    "Sex": "1181,1443,1299,1500,2",
                    "Address": "26,115,1630,150,2",
                    "Address2": "",
                    "City": "26,115,1630,150,2",
                    "State": "26,115,1630,150,2",
                    "Zip_Code": "26,115,1630,150,2",
                    "Home_Phone": "",
                    "Date_Of_Birth": "",
                    "Drivers_License_Number": "42,1879,353,1931,2",
                    "Drivers_License_Jurisdiction": "350,1020,551,1071,2",
                    "Injury_Status": "1142,1016,1374,1072,2"
                },
                {
                    "Party_Id": "",
                    "Person_Type": "",
                    "Unit_Number": "",
                    "First_Name": "518,903,1024,937,2",
                    "Middle_Name": "",
                    "Last_Name": "518,903,1024,937,2",
                    "Name_Suffix": "",
                    "Sex": "1182,901,1298,938,2",
                    "Address": "48,960,1611,996,2",
                    "Address2": "",
                    "City": "48,960,1611,996,2",
                    "State": "48,960,1611,996,2",
                    "Zip_Code": "48,960,1611,996,2",
                    "Home_Phone": "",
                    "Date_Of_Birth": "",
                    "Drivers_License_Number": "40,1020,352,1072,2",
                    "Drivers_License_Jurisdiction": "350,1880,551,1931,2",
                    "Injury_Status": "1298,1443,1502,1502,2",
                    "Safety_Equipment_Restraint": "34,1095,276,1147,2",
                    "Safety_Equipment_Helmet": "",
                    "Ejection": "1378,1020,1615,1072,2",
                    "Transported_To": "",
                    "Alcohol_Use_Suspected": "30,1288,273,1324,2",
                    "Drug_Use_Suspected": "904,1289,1102,1324,2",
                    "Driver_Distracted_By": "",
                    "Driver_Actions_At_Time_Of_Crash": "",
                    "Non_Motorist_Actions_At_Time_Of_Crash": "",
                    "Condition_At_Time_Of_Crash": "1091,1229,1615,1264,2",
                    "Alcohol_Test_Status": "",
                    "Alcohol_Test_Result": ""
                },
                {
                    "Party_Id": "",
                    "Person_Type": "",
                    "Unit_Number": "",
                    "First_Name": "514,1762,1029,1797,2",
                    "Middle_Name": "514,1762,1029,1797,2",
                    "Last_Name": "514,1762,1029,1797,2",
                    "Name_Suffix": "",
                    "Sex": "1181,1761,1298,1797,2",
                    "Address": "13,1822,1682,1858,2",
                    "Address2": "",
                    "City": "13,1822,1682,1858,2",
                    "State": "65,1522,1651,1561,2",
                    "Zip_Code": "13,1822,1682,1858,2",
                    "Home_Phone": "",
                    "Date_Of_Birth": "",
                    "Drivers_License_Number": "",
                    "Drivers_License_Jurisdiction": "",
                    "Injury_Status": "1145,1877,1378,1932,2",
                    "Safety_Equipment_Restraint": "39,1955,274,2007,2",
                    "Safety_Equipment_Helmet": "",
                    "Ejection": "1498,1444,1615,1501,2",
                    "Transported_To": "",
                    "Alcohol_Use_Suspected": "",
                    "Drug_Use_Suspected": "",
                    "Driver_Distracted_By": "",
                    "Driver_Actions_At_Time_Of_Crash": "",
                    "Non_Motorist_Actions_At_Time_Of_Crash": "",
                    "Condition_At_Time_Of_Crash": "1073,2084,1613,2121,2",
                    "Alcohol_Test_Status": "",
                    "Alcohol_Test_Result": "",
                    "Same_as_Driver_GUI": ""
                },
                {
                    "Party_Id": "",
                    "Person_Type": "",
                    "Unit_Number": "",
                    "First_Name": "518,1445,1018,1500,2",
                    "Middle_Name": "518,1445,1018,1500,2",
                    "Last_Name": "518,1445,1018,1500,2",
                    "Name_Suffix": "",
                    "Sex": "",
                    "Address": "65,1522,1651,1561,2",
                    "Address2": "",
                    "City": "65,1522,1651,1561,2",
                    "State": "13,1822,1682,1858,2",
                    "Zip_Code": "65,1522,1651,1561,2",
                    "Home_Phone": "",
                    "Date_Of_Birth": "",
                    "Drivers_License_Number": "",
                    "Drivers_License_Jurisdiction": "",
                    "Injury_Status": "",
                    "Safety_Equipment_Restraint": "35,1588,275,1640,2",
                    "Safety_Equipment_Helmet": "",
                    "Ejection": "1382,1879,1620,1932,2",
                    "Transported_To": "",
                    "Alcohol_Use_Suspected": "",
                    "Drug_Use_Suspected": "",
                    "Driver_Distracted_By": "",
                    "Driver_Actions_At_Time_Of_Crash": "",
                    "Non_Motorist_Actions_At_Time_Of_Crash": "",
                    "Condition_At_Time_Of_Crash": "",
                    "Alcohol_Test_Status": "",
                    "Alcohol_Test_Result": ""
                },
                {
                    "Party_Id": "",
                    "Person_Type": "",
                    "Unit_Number": "",
                    "First_Name": "520,274,1024,327,3",
                    "Middle_Name": "520,274,1024,327,3",
                    "Last_Name": "520,274,1024,327,3",
                    "Name_Suffix": "",
                    "Sex": "1181,271,1299,328,3",
                    "Address": "0,347,1631,386,3",
                    "Address2": "",
                    "City": "0,347,1631,386,3",
                    "State": "0,347,1631,386,3",
                    "Zip_Code": "0,347,1631,386,3",
                    "Home_Phone": "",
                    "Date_Of_Birth": "",
                    "Drivers_License_Number": "",
                    "Drivers_License_Jurisdiction": "",
                    "Injury_Status": "1302,270,1497,330,3",
                    "Safety_Equipment_Restraint": "38,415,275,467,3",
                    "Safety_Equipment_Helmet": "",
                    "Ejection": "1496,272,1613,328,3",
                    "Transported_To": "",
                    "Alcohol_Use_Suspected": "",
                    "Drug_Use_Suspected": "",
                    "Driver_Distracted_By": "",
                    "Driver_Actions_At_Time_Of_Crash": "",
                    "Non_Motorist_Actions_At_Time_Of_Crash": "",
                    "Condition_At_Time_Of_Crash": "",
                    "Alcohol_Test_Status": "",
                    "Alcohol_Test_Result": "",
                    "Address_Same_as_Driver_GUI": ""
                },
                {
                    "Party_Id": "",
                    "Person_Type": "",
                    "Unit_Number": "",
                    "First_Name": "513,594,1026,648,3",
                    "Middle_Name": "513,594,1026,648,3",
                    "Last_Name": "513,594,1026,648,3",
                    "Name_Suffix": "",
                    "Sex": "1181,588,1299,646,3",
                    "Address": "0,668,1641,707,3",
                    "Address2": "",
                    "City": "0,668,1641,707,3",
                    "State": "0,668,1641,707,3",
                    "Zip_Code": "0,668,1641,707,3",
                    "Home_Phone": "",
                    "Date_Of_Birth": "",
                    "Drivers_License_Number": "",
                    "Drivers_License_Jurisdiction": "",
                    "Injury_Status": "1302,591,1499,650,3",
                    "Safety_Equipment_Restraint": "39,733,274,788,3",
                    "Safety_Equipment_Helmet": "",
                    "Ejection": "1496,593,1614,649,3",
                    "Transported_To": "",
                    "Alcohol_Use_Suspected": "",
                    "Drug_Use_Suspected": "",
                    "Driver_Distracted_By": "",
                    "Driver_Actions_At_Time_Of_Crash": "",
                    "Non_Motorist_Actions_At_Time_Of_Crash": "",
                    "Condition_At_Time_Of_Crash": "",
                    "Alcohol_Test_Status": "",
                    "Alcohol_Test_Result": "",
                    "Address_Same_as_Driver_GUI": ""
                }
            ],
            "Vehicles": [
                {
                    "VinValidation_VinStatus": "",
                    "Unit_Number": "",
                    "Trailer_Unit_Number": "",
                    "License_Plate": "",
                    "Registration_State": "789,1000,905,1035,1",
                    "VIN": "1223,1001,1611,1036,1",
                    "Vehicle_Towed": "",
                    "Model_Year": "41,1057,118,1094,1",
                    "Make": "119,1056,213,1094,1",
                    "Model": "213,1058,306,1095,1",
                    "Insurance_Company": "47,1116,810,1153,1",
                    "Insurance_Policy_Number": "820,1117,1628,1153,1",
                    "Insurance_Expiration_Date": "",
                    "Damaged_Areas": "",
                    "Air_Bag_Deployed": "",
                    "Contributing_Circumstances_Vehicle": "",
                    "Posted_Statutory_SpeedLimit": "1194,1334,1342,1371,1",
                    "Unit_Type": "",
                    "Most_Harmful_Event_for_Vehicle": "",
                    "Event_Sequence": "",
                    "Vehicle_Maneuver_Action_Prior": "37,1745,274,1795,1",
                    "Trafficway_Description": "273,1743,532,1796,1",
                    "Traffic_Control_Device_Type": "42,1818,387,1899,1"
                },
                {
                    "VinValidation_VinStatus": "",
                    "Unit_Number": "",
                    "Trailer_Unit_Number": "",
                    "License_Plate": "",
                    "Registration_State": "787,1963,906,1998,1",
                    "VIN": "1226,1962,1607,2000,1",
                    "Vehicle_Towed": "",
                    "Model_Year": "40,2021,117,2057,1",
                    "Make": "121,2021,210,2058,1",
                    "Model": "212,2022,307,2059,1",
                    "Insurance_Company": "26,2079,839,2113,1",
                    "Insurance_Policy_Number": "827,2079,1624,2116,1",
                    "Insurance_Expiration_Date": "",
                    "Damaged_Areas": "",
                    "Air_Bag_Deployed": "",
                    "Contributing_Circumstances_Vehicle": "",
                    "Posted_Statutory_SpeedLimit": "",
                    "Unit_Type": "",
                    "Most_Harmful_Event_for_Vehicle": "",
                    "Event_Sequence": "",
                    "Vehicle_Maneuver_Action_Prior": "",
                    "Trafficway_Description": "",
                    "Traffic_Control_Device_Type": ""
                }
            ]
        }
        EOF;
        $jsonData = json_decode(trim($jsonString), true);

        return $jsonData;
    }

    public function getCoordinates($reportId) {
        $retVal = [];
        $data = $this->adapterAutozoningDataCoordinate->getCoordinateData($reportId);
        if(!empty($data)) {
            $data = json_decode($data);
            $report = $data->Report;
            $retVal = [
                'Incident'  => $report->Incident,
                'People'    => $report->People,
                'Vehicles'  => $report->Vehicles,
                'Citations' => $report->Citations
            ];
        }
        return $retVal;
    }
}
