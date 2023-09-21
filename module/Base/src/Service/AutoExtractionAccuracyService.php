<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */
namespace Base\Service;

use Base\Adapter\Db\AutoExtractionAccuracyAdapter;
use Base\Adapter\Db\ReportEntryDataAdapter;
use Base\Service\ReportEntryService;
use Base\Service\AutoExtractionService;
use Base\Service\UserAccuracyService;
use Base\Adapter\Db\FormFieldAdapter;
use Base\Adapter\Db\ReportAdapter;

class AutoExtractionAccuracyService extends BaseService
{
    public $comparedData = [];
    private $ignoredFields = ['Party_Id', 'Fatality_Involved', 'Trailer_Unit_Number', 'VinValidation_VinStatus', 'VIN_Original', 'Model_Year_Original', 'Make_Original', 'Model_Original'];
    /**
     * @var Array
     */
    private $config;
       
    /**
     * @var Base\Adapter\Db\AutoExtractionAccuracyAdapter
     */
    protected $adapterAutoExtractAccuracy;
    
    /**
     * @var Base\Service\ReportEntryService
     */
    protected $serviceReportEntry;
    
    /**
     * @var Base\Service\AutoExtractionService
     */
    protected $serviceAutoExtraction;
    
    /**
     * @var Base\Service\UserAccuracyService
     */
    protected $serviceUserAccuracy;

    /**
     * @var Base\Adapter\Db\ReportEntryDataAdapter
     */
    protected $adapterReportEntryData;

    /**
     * @var Base\Adapter\Db\FormFieldAdapter
     */
    protected $adapterFormField;

    /**
     * @var Base\Adapter\Db\ReportAdapter
     */
    protected $adapterReport;
     
    public function __construct(
        Array $config,
        AutoExtractionAccuracyAdapter $adapterAutoExtractAccuracy,
        ReportEntryService $serviceReportEntry,
        AutoExtractionService $serviceAutoExtraction,
        UserAccuracyService $serviceUserAccuracy,
        ReportEntryDataAdapter $adapterReportEntryData,
        FormFieldAdapter $adapterFormField,
        ReportAdapter $adapterReport)
    {
        $this->config = $config;
        $this->adapterAutoExtractAccuracy = $adapterAutoExtractAccuracy;
        $this->serviceReportEntry = $serviceReportEntry;
        $this->serviceAutoExtraction = $serviceAutoExtraction;
        $this->serviceUserAccuracy = $serviceUserAccuracy;
        $this->adapterReportEntryData = $adapterReportEntryData;
        $this->adapterFormField = $adapterFormField;
        $this->adapterReport = $adapterReport;
    }

    /**
     * Checks if there are any Autoextraction accuracy records for a report id
     *
     * @param int $reportId
     * @return bool
     */
    protected function hasAutoExtractAccuracyCalculated($reportId)
    {
        return $this->adapterAutoExtractAccuracy->hasAutoExtractAccuracyCalculated($reportId);
      
    }
    
   /**
     * Fetch Autoextract Accuracy records for the given Date Range
     *
     * @param string $fromDate
     * @param string $toDate

     * @return bool
     */
    public function getAutoExtractionAccuracyData($searchCriteria)
    {
        $accuracyData = [];
        $entryDataCompressed = $this->adapterAutoExtractAccuracy->getAutoExtractionAccuracyData($searchCriteria) ;

        foreach ($entryDataCompressed as $compressedRow) {
            if (isset($compressedRow['accuracy_details'])) {
                $compressedRow['accuracy_details'] = $this->serviceReportEntry->decompressData($compressedRow['accuracy_details']);
            }
            $accuracyData[] = $compressedRow;
        }
        
        return $accuracyData;
    }
    
    /**
     * Stores Autoextract and Manaul Keying differnece 
     *
     * @param int $reportId
     * @return bool
     */
    public function createAutoExtractionAccuracyMetricData($reportId)
    {
        $entryData = [];
        
        // Check Auto Extarction Accuracy already calculated for this report 
        $hasAccuracyCalculated = $this->hasAutoExtractAccuracyCalculated($reportId);
        if (!empty($hasAccuracyCalculated)) {
            return;
        }
        
        // Check Auto Extarction Data exist for this Report  
        if (!$this->serviceAutoExtraction->hasAutoExtracted($reportId)) {
            return;
        }
        
        // Fetching Auto extarction Data
        $autoExtractData = $this->serviceReportEntry->getAutoextractionDataDecompressed($reportId);

        array_push($entryData, $autoExtractData);
        
        // Fetching Pass 2 Keying Data
        $formAndEntryId = $this->serviceReportEntry->getMaxCompletedFormAndEntryId($reportId);
        $reportEntryId = $formAndEntryId['report_entry_id'];
        $lastEntryData = $this->serviceReportEntry->getReportEntryDataDecompressed($reportId,$reportEntryId);
        $lastEntryData = current($lastEntryData);
        
        array_push($entryData, $lastEntryData);
        
        if (count($entryData) > 1) {
            $authoritativeEntry = $this->serviceUserAccuracy->getAuthoritativeEntry($entryData);
            
            if (!empty($authoritativeEntry)) {
                $authoritativeEntry['entryData']['Report'] = $this->serviceUserAccuracy->getAccuracyComparisonFormat(
                    $authoritativeEntry['formId'],
                    $authoritativeEntry['entryData']['Report']
                );

                $diffData = [];
                foreach($entryData as $entry) {
                    if ($entry['reportEntryId'] == $authoritativeEntry['reportEntryId']
                        || in_array($entry['entryStage'], $this->serviceUserAccuracy->ignoredStages)) {

                        continue;
                    }

                    $formId = $formAndEntryId['form_id'];
                    $entry['entryData']['Report'] = $this->serviceUserAccuracy->getAccuracyComparisonFormat(
                        $formId,
                        $entry['entryData']['Report']
                    );
                    
                    $diffData = $this->getAutoManualDifferences(
                        $entry['entryData']['Report'],
                        $authoritativeEntry['entryData']['Report']
                    );
                }
                
                if (!empty($diffData)) {
                    $encodeData = $this->compress(json_encode($diffData));
                    $userId = $lastEntryData['userId'];
                    $this->adapterAutoExtractAccuracy->insertNew(
                        $userId,
                        $reportId,
                        $encodeData);
                }
            }
        }
    }

    /**
     * Inserts difference records for an accuracy record
     *
     * Note:
     * If the fields are not present in entry data on the Auto extract Data but
     * appear on the second pass that will be counted. The logic is that
     * find Auto vs Manual Keying difference data.
     *
     * @param array $entryData
     * @param array $authoritativeData
     * @return array
     */
    protected function getAutoManualDifferences(Array $entryData, Array $authoritativeData)
    {
        $differences = array_udiff_assoc(
            $authoritativeData,
            $entryData,
            'strcasecmp'
        );
        
        //@TODO: This needs to ignore vin status and the vin original fields
        if (!empty($differences)) {
             $autoData = [];
             $manualData = [];
            foreach ($differences as $formAttributeName => $userValue) {

               $authoritativeValue =
                    (isset($entryData[$formAttributeName])
                        && !empty($entryData[$formAttributeName]))
                    ? $entryData[$formAttributeName]
                    : '';
                 $userValue = is_null($userValue) ? '' : $userValue;
                
                if (trim($authoritativeValue) != ''
                    || trim($userValue) != '') {
                    
                    $autoData[$formAttributeName] = $authoritativeValue;
                    $manualData[$formAttributeName] = $userValue;
                }
            }
        }
        
        $diffResult = [];
        $diffResult['manual']= $manualData;
        $diffResult['auto']= $autoData;
        
        return $diffResult;
    }

    public function getAccuracyComparisonData($reportId, $reportEntryId = null)
    {
        $entryDataDecompressed = [];
        $entryDataCompressed = $this->adapterReportEntryData->fetchAccuracyReportEntryData($reportId, $reportEntryId);

        if (is_array($entryDataCompressed)) {
            foreach ($entryDataCompressed as $compressedRow) {
                if (isset($compressedRow['entryData'])) {
                    $compressedRow['entryData'] = $this->serviceReportEntry->decompressData($compressedRow['entryData']);
                }

                $entryDataDecompressed[] = $compressedRow;
            }
        }

        return $entryDataDecompressed;
    }
    
    public function getCriticalityFields($reportId)
    {
        $reportInfo = $this->adapterReport->getRelatedInfo($reportId);
        return $this->adapterFormField->getCriticalityFieldsByFormSystemId($reportInfo['formSystemId']);
    }

    public function arraySearchByMultipleKey($array, $search)
    {
        $result = [];

        foreach ($array as $key => $value) {
            foreach ($search as $k => $v) {
                if (!isset($value[$k]) || strtolower($value[$k]) != strtolower($v)) {
                    continue 2;
                }
            }
            $result[$key] = $value;
        }

        return $result;
    }

    public function convertDropdownArrayToString($field) 
    {
        $fieldCodeDescription = [];
        if (!empty($field)){
            foreach ($field as $dropdownField) {
                $fieldCodeDescription = array_merge($fieldCodeDescription, array_filter(array_values($dropdownField), 'strlen'));
            }
        }
        
        $dropdownValues = implode('|', $fieldCodeDescription);
        return trim($dropdownValues);
    }

    public function compareData(&$autoExtractData, &$stageData, $reportId) 
    {
        $codeDescriptionPairFields = $this->serviceReportEntry->getFormFieldsCodePair($reportId);
        $codeDescriptionPairFields = array_map('strtolower', $codeDescriptionPairFields);

        $discrepancyData = ['standard' => [], 'ignored' => []];
        foreach ($autoExtractData as $fieldKey => $field) {
            $codePairKey = $this->serviceReportEntry->convertInputFieldName($fieldKey);

            if (in_array($fieldKey, $this->ignoredFields)) {
                array_push($discrepancyData['ignored'], $fieldKey);
                continue;
            }
            
            if (!empty($stageData) && array_key_exists($fieldKey, $stageData)) {
                if ((is_array($field) || is_array($stageData[$fieldKey])) || in_array($codePairKey, $codeDescriptionPairFields)) {
                    if (is_array($field)){
                        $autoExtractDropdown = $this->convertDropdownArrayToString($field);
                    } else {
                        $autoExtractDropdown = $field;
                    }
                    if (is_array($stageData[$fieldKey])){
                        $stageDataDropdown = $this->convertDropdownArrayToString($stageData[$fieldKey]);
                    } else {
                        $stageDataDropdown = $stageData[$fieldKey];
                    }

                    if (strcasecmp($autoExtractDropdown, $stageDataDropdown)) {
                        array_push($discrepancyData['standard'], $fieldKey);
                    }

                    $autoExtractData[$fieldKey] = $autoExtractDropdown;
                    $stageData[$fieldKey] = $stageDataDropdown;
                } else {
                    if (array_key_exists($fieldKey, $stageData)) {
                        if (strcasecmp(trim($field), trim($stageData[$fieldKey]))) {
                            array_push($discrepancyData['standard'], $fieldKey);
                        }
                    }
                }
            } else {
                if (in_array($codePairKey, $codeDescriptionPairFields)){
                    $autoExtractDropdown = $this->convertDropdownArrayToString($field);
                    $autoExtractData[$fieldKey] = $autoExtractDropdown;
                }
            }
        }

        return $discrepancyData;
    }

    public function passOverviewResult($arrayKey, $autoExtract, $stageData, $passStage, $hasDiscrepancy) 
    {
        $autoextract = ['value' => $autoExtract, 'class' => ''];
        $currentStage = ['value' => $stageData, 'class' => ''];

        $this->comparedData[$arrayKey][EntryStageService::AUTO_EXTRACT] = $autoextract;
        $this->comparedData[$arrayKey][$passStage] = $currentStage;

        if ($hasDiscrepancy){
            $this->comparedData[$arrayKey][EntryStageService::AUTO_EXTRACT]['class'] = 'dataModifiedPass1';
            if ($passStage == EntryStageService::STAGE_DYNAMIC_VERIFICATION) {
                $this->comparedData[$arrayKey][$passStage]['class'] = 'dataModifiedPass2';
            } else {
                $this->comparedData[$arrayKey][$passStage]['class'] = 'dataModifiedPass1';
            }
        }
    }

    public function setDropdownArrayToString($field, $fieldKey, $reportId) 
    {
        $codeDescriptionPairFields = $this->serviceReportEntry->getFormFieldsCodePair($reportId);
        $codeDescriptionPairFields = array_map('strtolower', $codeDescriptionPairFields);

        $codePairKey = $this->serviceReportEntry->convertInputFieldName($fieldKey);

        if (in_array($codePairKey, $codeDescriptionPairFields)) {
            return $this->convertDropdownArrayToString($field);
        } else {
            return '';
        }
    }
    
    /**
     * Find the missing in pass data
     * @param string    $arrayKey   Contains section and field name
     * @param array     $autoExtract Auto extraction data of the field
     * @param array     $stageData  Pass1/Pass2 data of the field
     * @param string    $passStage  EntryStageService::STAGE_ALL OR EntryStageService::STAGE_DYNAMIC_VERIFICATION
     * @return void
     */
    public function passOverviewResultAdditional($arrayKey, $autoExtract, $stageData, $passStage) 
    {
        $autoExtract['class'] = 'dataModifiedPass1';
        $this->comparedData[$arrayKey][EntryStageService::AUTO_EXTRACT] = $autoExtract;

        if ($passStage == EntryStageService::STAGE_ALL) {
            $currentStage = ['value' => $stageData, 'class' => 'dataModifiedPass1'];
            $this->comparedData[$arrayKey][EntryStageService::STAGE_ALL] = $currentStage;
            if (empty($stageData)) {
                $this->comparedData[$arrayKey][EntryStageService::STAGE_DYNAMIC_VERIFICATION] = $currentStage;
            } else {
                $this->comparedData[$arrayKey][EntryStageService::STAGE_DYNAMIC_VERIFICATION] = $autoExtract;
            }
        } else {
            if (!array_key_exists(EntryStageService::STAGE_ALL, $this->comparedData[$arrayKey])) {
                $this->comparedData[$arrayKey][EntryStageService::STAGE_ALL] = $autoExtract;
            }
            $currentStage = ['value' => $stageData, 'class' => 'dataModifiedPass2'];
            $this->comparedData[$arrayKey][EntryStageService::STAGE_DYNAMIC_VERIFICATION] = $currentStage;
        }
    }

    public function setMissingData($fieldKey, $missingKey, $emptyDiscrepancy, $passStage, $value) 
    {
        $emptyDiscrepancy = ["value" => "", "class" => ""];
        if ($passStage == EntryStageService::AUTO_EXTRACT) {
            $this->comparedData[$missingKey][EntryStageService::AUTO_EXTRACT] = $emptyDiscrepancy;
        }

        if (!array_key_exists(EntryStageService::STAGE_ALL, $this->comparedData[$missingKey])) {
            $this->comparedData[$missingKey][EntryStageService::STAGE_ALL] = $emptyDiscrepancy;
            $this->comparedData[$missingKey][EntryStageService::STAGE_DYNAMIC_VERIFICATION] = $emptyDiscrepancy;
        }

        $this->comparedData[$missingKey][$passStage]['value'] = $value;
        if ($passStage == EntryStageService::STAGE_ALL || $passStage == EntryStageService::STAGE_DYNAMIC_VERIFICATION) {    
            $this->comparedData[$missingKey][$passStage]['class'] = 'dataMissing';
        }
    }

    public function setCriticalityData($fieldKey, $resultKey, $criticalityFields) 
    {
        $codePairKey = $this->serviceReportEntry->convertInputFieldName($fieldKey);
        if (array_key_exists($codePairKey, $criticalityFields)) {
            $this->comparedData[$resultKey]['is_critical'] = $criticalityFields[$codePairKey]['is_critical'];
            $this->comparedData[$resultKey]['is_major'] = $criticalityFields[$codePairKey]['is_major'];
            $this->comparedData[$resultKey]['is_minor'] = $criticalityFields[$codePairKey]['is_minor'];
        }        
    }

    public function checkEmptyData($data) 
    {
        $arrayData = $data;
        unset($arrayData['Party_Id']);
        $filterArray = array_filter($arrayData);

        return (empty($filterArray)) ? true : false;  
    }

    /**
     * Find the difference between auto-extract and pass data
     *
     * @param array $autoExtractionData
     * @param array $reportEntry
     * @param string $passStage
     * @return array
     */
    public function findPassDifference($autoExtractionData, $reportEntry, $passStage) 
    {
        $autoextractEntryData = $autoExtractionData['entryData']['Report'];
        $stageEntryData = $reportEntry['entryData']['Report'];
        $internalFieldNames = ['People', 'Vehicles', 'Citations'];

        $criticalityFields = $this->getCriticalityFields($stageEntryData['reportId']);
        $codeDescriptionPairFields = $this->serviceReportEntry->getFormFieldsCodePair($stageEntryData['reportId']);
        $codeDescriptionPairFields = array_map('strtolower', $codeDescriptionPairFields);
        $autoExtractTotalCount = 0;
        $stage2DiscrepancyCount = 0;

        foreach ($autoextractEntryData as $internalKey => $autoExtractData) {
            if (in_array($internalKey, $internalFieldNames)) {
                if (isset($stageEntryData[$internalKey]))
                    $passData = $stageEntryData[$internalKey];

                $i = 0;
                foreach ($autoExtractData as $autoExtractInternal) {
                    if ($internalKey == 'Vehicles') {
                        if (empty($autoExtractInternal['Unit_Number'])) {
                            continue;
                        }
                        $unitData = $this->arraySearchByMultipleKey($passData, ['Unit_Number' => $autoExtractInternal['Unit_Number']]);
                        $currentStageData = current($unitData);
                        $matchIdentifier = (!empty($autoExtractInternal['Unit_Number']) && trim($autoExtractInternal['Unit_Number'])) ? trim($autoExtractInternal['Unit_Number']) : 'NU';
                    } else if ($internalKey == 'People') {
                        if (empty($autoExtractInternal['Unit_Number']) && empty($autoExtractInternal['Person_Type'])) {
                            continue;
                        }
                        $unitData = $this->arraySearchByMultipleKey($passData, ['Unit_Number' => $autoExtractInternal['Unit_Number'], 'Person_Type' => $autoExtractInternal['Person_Type']]);
                        $currentStageData = current($unitData);
                        $matchIdentifier = (!empty($autoExtractInternal['Unit_Number']) && trim($autoExtractInternal['Unit_Number'])) ? trim($autoExtractInternal['Unit_Number']) : 'NU';
                    } else {
                        if (empty($autoExtractInternal['Party_Id'])) {
                            continue;
                        }
                        $unitData = $this->arraySearchByMultipleKey($passData, ['Party_Id' => $autoExtractInternal['Party_Id']]);
                        $currentStageData = current($unitData);
                        $matchIdentifier = (!empty($autoExtractInternal['Party_Id']) && trim($autoExtractInternal['Party_Id'])) ? trim($autoExtractInternal['Party_Id']) : 'NP';
                    }
                    $discrepancyFields = $this->compareData($autoExtractInternal, $currentStageData, $stageEntryData['reportId']);

                    foreach ($autoExtractInternal as $fieldKey => $autoExtract) {
                        $resultKey = $internalKey . '_' . $matchIdentifier . '_' . $fieldKey . '_' . $i;

                        if (!empty($currentStageData)) {
                            if (array_key_exists($fieldKey, $currentStageData)) {
                                if (!empty($discrepancyFields['standard']) && in_array($fieldKey, $discrepancyFields['standard'])) {
                                    $this->passOverviewResult($resultKey, $autoExtract, $currentStageData[$fieldKey], $passStage, 1);
                                } else {
                                    $this->passOverviewResult($resultKey, $autoExtract, $currentStageData[$fieldKey], $passStage, 0);
                                }
                                
                                if ($passStage == EntryStageService::STAGE_DYNAMIC_VERIFICATION) {
                                    $autoExtractTotalCount ++;
                                }

                                if (!empty($discrepancyFields['ignored']) && in_array($fieldKey, $discrepancyFields['ignored'])) {
                                    $ignoredData = ['value' => $autoExtract, 'class' => 'dataIgnored'];
                                    $this->comparedData[$resultKey][EntryStageService::AUTO_EXTRACT] = $ignoredData;
                                    $this->comparedData[$resultKey][$passStage] = $ignoredData;
                                    $this->comparedData[$resultKey][$passStage]['value'] = $currentStageData[$fieldKey];
                                }
                            } else {
                                $missingResultKey = $internalKey . '_' . $matchIdentifier . '_' . $fieldKey . '_' . $i;
                                $emptyDiscrepancy = ["value" => "", "class" => ""];
                                if (is_array($autoExtract)) {
                                    $autoExtract = $this->setDropdownArrayToString($autoExtract, $fieldKey, $stageEntryData['reportId']);
                                }
                                $this->setMissingData($fieldKey, $missingResultKey, $emptyDiscrepancy, EntryStageService::AUTO_EXTRACT, $autoExtract);
                                $this->setMissingData($fieldKey, $missingResultKey, $emptyDiscrepancy, $passStage, '');
                            }
                        } else {
                            if (is_array($autoExtract)) {
                                $autoExtract = $this->setDropdownArrayToString($autoExtract, $fieldKey, $stageEntryData['reportId']);
                            }
                            $this->passOverviewResult($resultKey, $autoExtract, $currentStageData[$fieldKey], $passStage, 1);
                        }

                        // set criticality fields
                        $this->setCriticalityData($fieldKey, $resultKey, $criticalityFields);
                    }

                    if (!empty($currentStageData) && !empty($autoExtractInternal)) {
                        $autoExtractMissing = array_diff_key($currentStageData, $autoExtractInternal);
                        foreach ($autoExtractMissing as $missingKey => $missing) {
                            $resultKey = $internalKey . '_' . $matchIdentifier . '_' . $missingKey . '_' . $i;
                            $emptyDiscrepancy = ["value" => "", "class" => ""];
                            if (is_array($missing)) {
                                $missing = $this->setDropdownArrayToString($missing, $fieldKey, $stageEntryData['reportId']);
                            }
                            $this->setMissingData($missingKey, $resultKey, $emptyDiscrepancy, EntryStageService::AUTO_EXTRACT, '');
                            $this->setMissingData($missingKey, $resultKey, $emptyDiscrepancy, $passStage, $missing);

                            // set criticality fields
                            $this->setCriticalityData($fieldKey, $resultKey, $criticalityFields);
                        }
                    }
                    
                    if ($passStage == EntryStageService::STAGE_DYNAMIC_VERIFICATION) {
                        $stage2DiscrepancyCount += count($discrepancyFields['standard']);
                    }
                    
                    if (!empty($passData)){
                        unset($passData[key($unitData)]);
                    }
                    $i ++;
                }
                
                /* Find pass additional data */
                foreach ($passData as $missingData) {
                    if (($internalKey == 'People' || $internalKey == 'Vehicles') && empty($missingData['Unit_Number'])) {
                        continue;
                    }
                    foreach ($missingData as $fieldKey => $newData) {
                        if ($internalKey == 'People' || $internalKey == 'Vehicles') {
                            $unitNumberOrPartyId = (!empty($missingData['Unit_Number'])) ? $missingData['Unit_Number'] : 'NU';
                        } else {
                            $unitNumberOrPartyId = (!empty($missingData['Party_Id'])) ? $missingData['Party_Id'] : 'NP';
                        }
                        $resultKey = $internalKey . '_'. $unitNumberOrPartyId . '_' . $fieldKey . '_' . $i;
                        if (is_array($newData)) {
                            $newData = $this->setDropdownArrayToString($newData, $fieldKey, $stageEntryData['reportId']);
                        }
                        $autoextract = ['value' => '', 'class' => ''];
                        $this->passOverviewResultAdditional($resultKey, $autoextract, $newData, $passStage);

                        // set criticality fields
                        $this->setCriticalityData($fieldKey, $resultKey, $criticalityFields);
                    }
                    $i ++;
                }
            } else {
                
                if ($internalKey == 'Incident') {
                    $discrepancyFields = $this->compareData($autoExtractData, $stageEntryData['Incident'], $stageEntryData['reportId']);

                    foreach ($autoExtractData as $fieldKey => $incidentData) {
                        $resultKey = 'Incident_'. $fieldKey;
                        if (array_key_exists($fieldKey, $stageEntryData['Incident'])) {
                            if (!empty($discrepancyFields['standard']) && in_array($fieldKey, $discrepancyFields['standard'])) {
                                $this->passOverviewResult($resultKey, $incidentData, $stageEntryData['Incident'][$fieldKey], $passStage, 1);
                            } else {
                                $this->passOverviewResult($resultKey, $incidentData, $stageEntryData['Incident'][$fieldKey], $passStage, 0);
                            }

                            if ($passStage == EntryStageService::STAGE_DYNAMIC_VERIFICATION) {
                                $autoExtractTotalCount ++;
                            }
                            
                            if (!empty($discrepancyFields['ignored']) && in_array($fieldKey, $discrepancyFields['ignored'])) {
                                $this->comparedData[$resultKey][EntryStageService::AUTO_EXTRACT]['class'] = 'dataIgnored';
                            }
                        } else {
                            $emptyDiscrepancy = ["value" => "", "class" => ""];

                            $this->setMissingData($fieldKey, $resultKey, $emptyDiscrepancy, EntryStageService::AUTO_EXTRACT, $incidentData);
                            $this->setMissingData($fieldKey, $resultKey, $emptyDiscrepancy, $passStage, '');
                        }

                        // set criticality fields
                        $this->setCriticalityData($fieldKey, $resultKey, $criticalityFields);
                    }

                    $autoExtractMissing = array_diff_key($stageEntryData['Incident'], $autoExtractData);
                    foreach ($autoExtractMissing as $missingKey => $missing) {
                        $missingResultKey = $internalKey . '_' . $missingKey;
                        $emptyDiscrepancy = ["value" => "", "class" => ""];
                        $this->setMissingData($missingKey, $missingResultKey, $emptyDiscrepancy, EntryStageService::AUTO_EXTRACT, '');
                        $this->setMissingData($missingKey, $missingResultKey, $emptyDiscrepancy, $passStage, $missing);                       
                    }
                    
                    if ($passStage == EntryStageService::STAGE_DYNAMIC_VERIFICATION) {
                        $stage2DiscrepancyCount += count($discrepancyFields['standard']);
                    }
                }
            }
        }

        return ['totalCount' => $autoExtractTotalCount, 'discrepancyCount' => $stage2DiscrepancyCount];
    }

    /**
     * Find the missing entry in passes based on order
     *
     * @param array $autoExtractionData
     * @param array $reportEntry
     * @param array $stage2Difference
     * @param string $passStage
     * @return void(0)
     */
    public function findNAUnitNUmber($autoExtractionData, $reportEntry, $passStage)
    {
        $autoExtractEntryData = $autoExtractionData['entryData']['Report'];
        $stageEntryData = $reportEntry['entryData']['Report'];
        $criticalityFields = $this->getCriticalityFields($stageEntryData['reportId']);

        // Get the people data which are not having unit number
        $autoExtractUnitNAPeople = $this->arraySearchByMultipleKey($autoExtractEntryData['People'], ['Unit_Number' => '']);
        $stageUnitNAPeople = $this->arraySearchByMultipleKey($stageEntryData['People'], ['Unit_Number' => '']);

        // Compare the auto-extracttion data with passes based on order
        $s = 0;
        foreach ($autoExtractUnitNAPeople as $otherParty) {
            $stageMatchPeople = $this->arraySearchByMultipleKey($stageUnitNAPeople, ['Person_Type' => $otherParty['Person_Type']]);
            $stagePeople = current($stageMatchPeople);
            $discrepancyFields = $this->compareData($otherParty, $stagePeople, $stageEntryData['reportId']);

            foreach ($otherParty as $fieldKey => $peopleData) {
                $autoextract = ['value' => $peopleData, 'class' => ''];
                $resultKey = 'People_NU_' . $fieldKey . '_' . $s;

                if (is_array($peopleData)) {
                    $peopleData = $this->setDropdownArrayToString($peopleData, $fieldKey, $stageEntryData['reportId']);
                }

                if (!empty($stagePeople)) {
                    if (array_key_exists($fieldKey, $stagePeople)) {

                        if (!empty($discrepancyFields['standard']) && in_array($fieldKey, $discrepancyFields['standard'])) {
                            $this->passOverviewResult($resultKey, $peopleData, $stagePeople[$fieldKey], $passStage, 1);
                        } else {
                            $this->passOverviewResult($resultKey, $peopleData, $stagePeople[$fieldKey], $passStage, 0);
                        }

                        if (!empty($discrepancyFields['ignored']) && in_array($fieldKey, $discrepancyFields['ignored'])) {
                            $this->comparedData[$resultKey][EntryStageService::AUTO_EXTRACT]['class'] = 'dataIgnored';
                            $this->comparedData[$resultKey][$passStage]['class'] = 'dataIgnored';
                        }
                    }
                } else {
                    $this->passOverviewResultAdditional($resultKey, $autoextract, '', $passStage);
                }

                if (!empty($stageMatchPeople)){
                    unset($stageUnitNAPeople[key($stageMatchPeople)]);
                }

                // set criticality fields
                $this->setCriticalityData($fieldKey, $resultKey, $criticalityFields);
            }
            $s ++;
        }

        // add the remaing other party as additional data
        foreach ($stageUnitNAPeople as $newPeople) {
            $checkEmpty = $this->checkEmptyData($newPeople);
            if ($checkEmpty) {
                continue;
            }
            foreach ($newPeople as $fieldKey => $newData) {
                $resultKey = 'People_NU_' . $fieldKey . '_' . $s;
                $autoextract = ['value' => '', 'class' => ''];
                
                if (is_array($newData)) {
                    $newData = $this->setDropdownArrayToString($newData, $fieldKey, $stageEntryData['reportId']);
                }
                $this->passOverviewResultAdditional($resultKey, $autoextract, $newData, $passStage);

                // set criticality fields
                $this->setCriticalityData($fieldKey, $resultKey, $criticalityFields);
            }
            $s ++;
        }
    }
}
