<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */
namespace Base\Service;

use Zend\Log\Logger;

use Base\Adapter\Db\ReadOnly\ReportEntryAdapter as ReadOnlyReportEntryAdapter;
use Base\Adapter\Db\ReportEntryAdapter;
use Base\Adapter\Db\ReportEntryDataAdapter;
use Base\Adapter\Db\EntryStageProcessAdapter;
use Base\Adapter\Db\FormFieldCommonAdapter;
use Base\Adapter\Db\UserEntryPrefetchAdapter;
use Base\Adapter\Db\ReportEntryDataValueAdapter;
use Base\Service\EntryStageService;
use Base\Service\FormService;
use Base\Service\FormFieldService;
use Base\Service\AutoExtractionService;
use Base\Adapter\Db\ReportAdapter;
use Base\Adapter\Db\FormCodeMapAdapter;
use Base\Adapter\Db\FormCodeGroupConfigurationAdapter;

class ReportEntryService extends BaseService
{
    /**
     * The report is being processed
     */
    const STATUS_IN_PROGRESS = 'in progress';
    
    /**
     * The report has been fully processed
     */
    const STATUS_COMPLETE = 'complete';
    
    /**
     * @var Array
     */
    protected $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Adapter\Db\ReportEntryAdapter
     */
    protected $adapterReportEntry;

    /**
     * @var Base\Adapter\Db\ReportEntryDataAdapter
     */
    protected $adapterReportEntryData;

    /**
     * @var Base\Adapter\Db\EntryStageProcessAdapter
     */
    protected $adapterEntryStageProcess;
    
    /**
     * @var Base\Adapter\Db\FormFieldCommonAdapter
     */
    protected $adapterFormFieldCommon;
    
    /**
     * @var Base\Adapter\Db\ReadOnly\ReportEntryAdapter
     */
    protected $adapterReadOnlyReportEntry;
    
    /**
     * @var Base\Service\FormService
     */
    protected $serviceForm;
    
    /**
     * @var Base\Service\FormFieldService
     */
    protected $serviceFormField;
    
    /**
     * @var Base\Adapter\Db\UserEntryPrefetchAdapter
     */
    protected $adapterUserEntryPrefetch;
    
    /**
     * @var Base\Adapter\Db\ReportEntryDataValueAdapter
     */
    protected $adapterReportEntryDataValue;

    /**
     * @var Base\Adapter\Db\ReportAdapter
     */
    protected $adapterReport;

    /**
     * @var Base\Adapter\Db\FormCodeMapAdapter
     */
    protected $adapterFormCodeMap;

    /**
     * @var Base\Adapter\Db\FormCodeGroupConfigurationService
     */
    protected $adapterFCGC;
    
    /**
     * @var Base\Service\AutoExtractionService
     */
    protected $serviceAutoExtraction;
    
    protected $internalFieldNames = ['Incident', 'People', 'Vehicles', 'Citations'];

    public function __construct(
        Array $config,
        Logger $logger,
        ReportEntryAdapter $adapterReportEntry,
        ReadOnlyReportEntryAdapter $adapterReadOnlyReportEntry,
        ReportEntryDataAdapter $adapterReportEntryData,
        EntryStageProcessAdapter $adapterEntryStageProcess,
        FormFieldCommonAdapter $adapterFormFieldCommon,
        FormService $serviceForm,
        UserEntryPrefetchAdapter $adapterUserEntryPrefetch,
        ReportAdapter $adapterReport,
        FormCodeMapAdapter $adapterFormCodeMap,
        FormCodeGroupConfigurationAdapter $adapterFCGC,
        ReportEntryDataValueAdapter $adapterReportEntryDataValue,
        FormFieldService $serviceFormField,
        AutoExtractionService $serviceAutoExtraction)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterReportEntry = $adapterReportEntry;
        $this->adapterReadOnlyReportEntry = $adapterReadOnlyReportEntry;
        $this->adapterReportEntryData   = $adapterReportEntryData;
        $this->adapterEntryStageProcess   = $adapterEntryStageProcess;
        $this->adapterFormFieldCommon   = $adapterFormFieldCommon;
        $this->serviceForm   = $serviceForm;
        $this->adapterUserEntryPrefetch   = $adapterUserEntryPrefetch;
        $this->adapterReport   = $adapterReport;
        $this->adapterFormCodeMap = $adapterFormCodeMap;
        $this->adapterFCGC   = $adapterFCGC;
        $this->adapterReportEntryDataValue = $adapterReportEntryDataValue;
        $this->serviceFormField = $serviceFormField;
        $this->serviceAutoExtraction = $serviceAutoExtraction;
    }
    
    public function getLastByReportId($reportId)
    {
        if (empty($reportId)) {
            return;
        }
        
        return $this->adapterReportEntry->fetchLastByReportId($reportId);
    }
    
    /**
     * Determine the last reportEntryId for a specific reportId and userId
     *
     * @param integer $reportId
     * @param integer $userId
     * @return integer|null
     */
    public function getLastIdByReportAndUser($reportId, $userId)
    {
        return $this->adapterReportEntry->getLastIdByReportAndUser($reportId, $userId);
    }

    public function add(
        $reportId,
        $formId,
        $userId,
        $entryStageId,
        $passNumber,
        $status = ReportEntryService::STATUS_IN_PROGRESS)
    {
        return $this->adapterReportEntry->add($reportId, $formId, $userId, $entryStageId, $passNumber, $status);
    }

    /**
     * Marks the report entry completed for a user
     *
     * @param integer $reportEntryId
     * @return bool - If any rows were updated
     */
    public function complete($reportEntryId)
    {
        return $this->adapterReportEntry->completeReportEntry($reportEntryId);
    }

    /**
     * Reverts (by removing) any 'in progress' entries.
     *
     * @param integer $userId
     * @param integer $reportId
     */
    public function revertInProgress($userId, $reportId = null)
    {
        $this->adapterReportEntry->revertInProgress($userId, $reportId);
    }
    
    /**
     * Insert or update a row in report_entry_data and update count_keyed in report_entry
     * @TODO - Why does this need reportId?
     *
     * @param int $reportId
     * @param int $reportEntryId
     * @param json $entryData
     */
    public function insertOrUpdateData($reportId, $reportEntryId, $entryData)
    {
        $narrativeData = null;
        $origEntryData = $this->preProcessFormData($entryData, $reportId, $reportEntryId);
        $latestReportEntryData = $this->adapterReportEntry->fetchLastByReportId($reportId);
        if ($latestReportEntryData['passNumber'] > 1) {
            $narrativeData = $this->serviceAutoExtraction->getNarrativeData($reportId);
        }
        if (!empty($narrativeData)) {
            $entryData = $this->appendNarrativeData($narrativeData, $origEntryData);
        } else {
            $entryData = $origEntryData;
        }
        
        //save the updated keyed data with narrative
        $this->adapterReportEntryData->insertOrUpdateRow(
            $reportId,
            $reportEntryId,
            $this->compressData($entryData)
        );
        
        $this->insertEntryDataValues($reportEntryId, $entryData);
        
        //we use $origEntryData because narrative is not keyed by keyers for now
        $this->adapterReportEntry->updateCountKeyed(
            $reportEntryId,
            $this->countFieldsKeyed($reportEntryId, $origEntryData)
        );
    }
    
    /**
     * Sorts elements within group.
     * @param type $data
     * @return array
     */
    public function sortGroupElements($data)
    {
        if (empty($data)) {
            return;
        }

        foreach ($data as $group => $value) {
            // if group have multiple instances - sort them out
            if (is_array($data[$group]) && isset($value[0])) {
                /** @TODO: Will be removed in next release.
                 * check and unset people section if all elements has the empty data
                 * also this condition will be removed in future
                 */
                if ($group == 'People') {
                    $peopleData = $value;
                    array_walk($value, function($item, $index) use (&$peopleData) {
                        $checkEmptyElement = array_filter($item);
                        if (count($checkEmptyElement) == 0) {
                            unset($peopleData[$index]);
                        }
                    });
                    $value = $peopleData;
                }                    
                ksort($value);
                $data[$group] = array_values($value);
                ksort($data[$group]);
            }
        }

        return $data;
    }
    
    /**
     * To set party id as a incremental value in case of deletion in vehicle/other party details
     *
     * @param array $data - Entry data
     * @return array
     */
    public function setIncrementalPartyIdForPeople($data)
    {
        if (isset($data['People'])) {
            $newPartyId = 1;
            $citations = [];

            // To store Party_Id as a sequence number in people array and update the mapped citations Party_Id accordingly.
            foreach($data['People'] as $key => $people) {
                if (isset($data['Citations'])) {
                    foreach($data['Citations'] as $index => $citation) {
                        if ($data['Citations'][$index]['Party_Id'] == $people['Party_Id']) {
                            $citation['Party_Id'] = (string)$newPartyId;
                            array_push($citations, $citation);
                        }
                    }
                }
                $data['People'][$key]['Party_Id'] = (string)$newPartyId;
                $newPartyId ++;
            }

            $data['Citations'] = $citations;
        }
        
        return $data;
    }
    
    /**
     * Gets values of a field under a path from common data
     *
     * @param array $commonData
     * @param string $fieldName - form_field_common.name
     * @param string|array $path - form_field_common.path - Blank means it is at base path
     * @return array
     */
    protected function getCommonPathData($commonData, $fieldName, $path = '')
    {
        $commonPathData = [];

        if (empty($path)) {
            // There is no more path left, means we are at the level the value should be at
            if (isset($commonData[$fieldName])) {
                $value = $commonData[$fieldName];
                if (is_array($value)) {
                    // "Enums" will come through as array values
                    sort($value);
                    $value = implode(' ', $value);
                }
                $commonPathData[] = $value;
            }
        } else {
            if (!is_array($path)) {
                // Trimming out the cross field mapping path notation
                $path = preg_replace('(\[:[a-z A-Z]?:\=[a-z A-Z 0-9 / =]*])', '', $path);
                $path = explode('/', $path);
            }

            $level = array_shift($path);

            if (isset($commonData[$level])) {
                if (!empty($path) && preg_match('/\[[a-z A-Z]?\]/', $path[0])) {
                    // This matches against the [a],[b] array notation in the next path level
                    //  and branches out to cover each sub-array
                    array_shift($path);
                    foreach ($commonData[$level] as $levelData) {
                        $arrayData = $this->getCommonPathData($levelData, $fieldName, $path);
                        $commonPathData = array_merge($arrayData, $commonPathData);
                    }
                } else {
                    $commonPathData = $this->getCommonPathData($commonData[$level], $fieldName, $path);
                }
            }
        }

        return $commonPathData;
    }

    public function getDataTransformerByEntryId($reportEntryId) {
        $formSystemId = $this->adapterReportEntry->fetchFormSystemId($reportEntryId);
        $transformer = $this->serviceForm->getDataTransformerByFormSystemId($formSystemId);

        return $transformer;
    }

    /**
     * Insert specific values in to the seperate raw values table
     *
     * @param int $reportEntryId
     * @param array $reportData
     */
    public function insertEntryDataValues($reportEntryId, $entryData)
    {
        $valueFields = $this->adapterFormFieldCommon->fetchAllValueFields();
        $transformer = $this->getDataTransformerByEntryId($reportEntryId);
        $commonData = $transformer->toCommon($entryData);

        foreach ($valueFields as $valueField) {
            $data = $this->getCommonPathData($commonData, $valueField['name'], $valueField['path']);

            foreach ($data as $dataValue) {
                $this->adapterReportEntryDataValue->insertField(
                    $reportEntryId,
                    $valueField['form_field_common_id'],
                    $dataValue
                );
            }
        }
    }
    
    /*
     * Get misc info of a report entry
     * (info like: entry date, who entered it, etc....)
     *
     * @param int $reportId
     * @param int $reportEntryId
     * @return array
     */
    public function getReportEntryInfo($reportEntryId)
    {
        $entryInfo = $this->adapterReportEntryData->fetchReportEntryInfo($reportEntryId);
        if ($entryInfo['entryStage'] == EntryStageService::STAGE_EDIT) {
            $entryInfo['countEdits'] = $this->adapterReportEntry->countEditPasses($entryInfo['reportId'], $reportEntryId);
        }

        return $entryInfo;
    }

    /**
     * Gets the maximum (most recent) pass number for a report entry
     *
     * @param int $reportId - Id of the report entry to check passes for
     * @return int|FALSE - The number of the last pass or FALSE if no records
     */
    public function getMaxCompletedPass($reportId)
    {
        return $this->adapterReportEntry->getMaxCompletedPass($reportId);
    }
    
    /**
     * Gets the most recent authoritive (completed) report entry id.
     *
     * @param integer $reportId
     * @return integer
     */
    public function getMaxCompletedId($reportId)
    {
        return $this->adapterReportEntry->getMaxCompletedId($reportId);
    }
    
    /**
     * Will return the highest pass a entry process group CAN do
     *
     * @param int $reportId
     * @return int
     */
    public function getMaxPotentialPassNumber($reportId)
    {
        return $this->adapterEntryStageProcess->getMaxPotentialPassNumber($reportId);
    }
    
    /**
     * Get information about report entries including decompressed report entry data
     *
     * @param int $reportId
     * @return array
     */
    public function getReportEntryDataDecompressed($reportId, $reportEntryId = null)
    {
        $entryDataDecompressed = [];
        $entryDataCompressed = $this->adapterReportEntryData->fetchAllReportEntryData($reportId, $reportEntryId);

        if (is_array($entryDataCompressed)) {
            foreach ($entryDataCompressed as $compressedRow) {
                if (isset($compressedRow['entryData'])) {
                    $compressedRow['entryData'] = $this->decompressData($compressedRow['entryData']);
                }

                $entryDataDecompressed[] = $compressedRow;
            }
        }

        return $entryDataDecompressed;
    }
    
    public function updateFormIdByReportId($formId, $reportId)
    {
        if (empty($formId) || empty($reportId)) {
            return;
        }

        return $this->adapterReportEntry->updateFormIdByReportId($formId, $reportId);
    }

    /**
     * Decompress a report entry data record
     *
     * @param string $compressedData
     * @return array
     */
    public function decompressData($compressedData)
    {
        return json_decode(gzuncompress(substr($compressedData, 4)), true);
    }

    /**
     * Compress a report entry array (for storage in the database)
     *
     * @param array $entryData
     * @return string
     */
    public function compressData($entryData)
    {
        $reportEntryData['Report'] = $entryData;
        $encodeData = json_encode($reportEntryData);
        return $this->compress($encodeData);
    }

    /**
     * Pulls (and removes from prefetch) a prefetched report entry for a given user, if available.
     *
     * @param <type> $userId
     * @return integer|null
     */
    public function pullPrefetchByUser($userId, $remove = true)
    {
        $reportId = $this->adapterUserEntryPrefetch->fetchReportIdByUserId($userId);

        if (!empty($reportId) && $remove) {
            $this->adapterUserEntryPrefetch->remove($reportId, $userId);
        }

        return $reportId;
    }

    /**
     * Count the number of fields keyed by an operator in entry data
     *
     * @param array $reportData
     * @return int
     */
    protected function countFieldsKeyed($reportEntryId, $reportData)
    {
        $formSystem = $this->getFormSystemByEntryId($reportEntryId);
        if (empty($formSystem)) {
            $formSystem = [];
        }
        $count = 0;
        switch (current($formSystem)) {
            case FormService::SYSTEM_UNIVERSAL:
                $reportData = $this->filterMetaFields($reportData);
                $count = $this->getKeyedFieldsCount($reportData);
                break;
            
            default:
                $this->logger->log(Logger::ERR, 'Unable to calculate count keyed for entry ' . var_export($reportEntryId, true)
                    . ' due to invalid form system ' . var_export($formSystem, true));
                break;
        }
        
        return $count;
    }
    
    /**
     * Gets the number of fields keyed-in
     *
     * @param type $arrayData
     * @return array count
     */
    public function getKeyedFieldsCount($arrayData) {
        $arrayCount = 0;
        
        foreach ($arrayData as $key => $value) {
            if (!is_array($value) && !is_object($value)) {
                if (strlen(trim($value)) > 0)
                    $arrayCount ++;
            } else {
                if (is_numeric(current(array_keys($value))) && !in_array($key, $this->internalFieldNames)) {
                    // To count the non-multidimentional array data. i.e: incident
                    $arrayCount ++;
                } else {
                    // To count the multidimentional array data. i.e: people, vehicle
                    $arrayCount += $this->getKeyedFieldsCount($value);
                }
            }
        }
        
        return $arrayCount;
    }

    /**
     * Gets form system (id and internal name) by entry id
     *
     * @param type $reportEntryId
     * @return array(id => internal_name)
     */
    public function getFormSystemByEntryId($reportEntryId) {
        return $this->adapterReportEntry->getFormSystemByEntryId($reportEntryId);
    }

    /**
     * Removes non-keyed auto generated junk fields from entry data (for use in metrics)
     *
     * @param array $entryData
     * @return array
     */
    public function filterMetaFields($entryData)
    {
        $entryDataFiltered = [];
        /**
         * 'strict' must be exactly the same (in_array), 'any' will use stripos
         * Try to use strict when possible (when fields are not dynamic)
         */
        $noCompareFields = [
            'strict' => [
                'entryStage',
                'formSubmit',
                '_pages',
                'module',
                'controller',
                'action',
                'reportId',
                'entryFlow',
                'hasnotes'
            ],
            'any' => [
                'Person_PartyId_Hidden-t',
                'Person_PersonType_Hidden-t',
                'Person_VehicleUnitNumber_Hidden-t',
                'Vehicle_UnitNumber-t',
            ]
            ];

        foreach ($entryData as $field => $value) {
            if (!in_array($field, $noCompareFields['strict'])) {
                foreach ($noCompareFields['any'] as $string) {
                    if (stripos($field, $string) !== false) {
                        continue 2;
                    }
                }

                $entryDataFiltered[$field] = $value;
            }
        }

        return $entryDataFiltered;
    }
    
    /**
     * @param string $dateStart YYYY-mm-dd
     * @param string $dateEnd YYYY-mm-dd
     * @param null|string $nameFirst
     * @param null|string $nameLast
     * @return array
     */
    public function getEntryAvgStatistics($dateStart, $dateEnd, $keyingVendorId, $nameFirst = null, $nameLast = null)
    {
        return $this->adapterReadOnlyReportEntry->fetchEntryAvgStatistics($dateStart, $dateEnd, $keyingVendorId, $nameFirst, $nameLast);
    }

    /**
     * Get report entry data for the specific pass of the report id
     *
     * @param int $reportId
     * @param int $reportEntryId
     * @return array
     */
    public function fetchOnePassByReportId($reportId, $reportEntryId)
    {
        $reportEntryData = $this->adapterReportEntryData->fetchByEntryId($reportId, $reportEntryId);

        if (!empty($reportEntryData['entryData'])) {
            $reportEntryData['entryData'] = $this->decompressData($reportEntryData['entryData']);
        }

        return $reportEntryData;
    }

    /**
     * Get report entry data for last pass based on report id
     *
     * @param int $reportId
     * @param bool $isOldReport if true then unserialize, trim and convert to json data
     * @return array
     */
    public function fetchLastPassByReportId($reportId, $isOldReport = false)
    {
        $reportEntryData = $this->adapterReportEntry->fetchLastPassByReportId($reportId, $isOldReport);

        if (!empty($reportEntryData['entryData'])) {
            if ($isOldReport) {
                $entryData = unserialize($reportEntryData['entryData']);
                //silverlight data is json string so convert to php array first like universal data
                if (!is_array($entryData)) {
                    $entryData = json_decode($entryData, true); 
                }
                $reportEntryData['entryData'] = json_encode($entryData, JSON_PRETTY_PRINT);
            } else {
                $reportEntryData['entryData'] = $this->decompressData($reportEntryData['entryData']);
            }
        }
        
        return $reportEntryData;
    }


    public function fetchPassTwoByReportId($reportId)
    {
        $reportEntryData = $this->adapterReportEntry->fetchPassTwoByReportId($reportId);
        
        if (!empty($reportEntryData['entryData'])) {
            $reportEntryData['entryData'] = $this->decompressData($reportEntryData['entryData']);
        }
        
        return $reportEntryData;
    }

    public function cleanUp($timeLength, $timeUnit)
    {
        return $this->adapterReportEntry->cleanUp($timeLength, $timeUnit);
    }

    public function logCleanableRecords($timeLength, $timeUnit)
    {
        return $this->adapterReportEntry->logCleanableRecords($timeLength, $timeUnit);
    }
    
    /**
     * Preprocess the form data before storing into database.
     *
     * @param string $entryData
     * @param int $reportId
     * @param int $reportEntryId
     * @return array
     */
    public function preProcessFormData($entryData, $reportId, $reportEntryId)
    {
        $formValues = $this->getMultiSelectFormCodePairs($reportId);
        $codeDescriptionPairFields = $this->getFormFieldsCodePair($reportId);
        $formMultiSelectCodePairs = $formValues['codePairs'];
        
        $codeDescriptionPairFields = array_map('strtolower', $codeDescriptionPairFields);
        
        // Replace semicolon separated value 1;2 => [{Code => 1 , Description => 'code description'}] for multiselect dropdowns.
        array_walk_recursive(
            $entryData,
            function (&$value, $key) use ($formMultiSelectCodePairs, $codeDescriptionPairFields) {
                // To convert Weather_Condition into weathercondition
                $codePairKey = $this->convertInputFieldName($key);
                
                if (is_string($key) && $value != '' && in_array($codePairKey, $codeDescriptionPairFields)) {
                    $value = rtrim($value, ';');
                    foreach (explode(';', $value) as $fieldValue) {
                        $fieldValue = trim($fieldValue);
                        
                        if (!empty($formMultiSelectCodePairs[$key])) {
                            /**
                             * $formMultiSelectCodePairs will contains array("Weather_Condition" => ["1" => "Clear", "OTHER/UNKNOWN" => "OTHER/UNKNOWN"]).
                             * All codes are converted into lowercase to avoid the case sensitive issue, while comparing user
                             * selected value with the code in the database.
                             */
                            $formCodes = array_keys($formMultiSelectCodePairs[$key]);
                            $formCodeDescriptions = array_values($formMultiSelectCodePairs[$key]);
                            $formCodeIndex = array_search(
                                strtolower($fieldValue),
                                array_map('strtolower', $formCodes)
                            );
                        } else {
                            // To populate text field as code/description.
                            $formCodeIndex = false;
                        }
                        
                        if ($formCodeIndex !== false
                            && strcasecmp($fieldValue, $formCodeDescriptions[$formCodeIndex]) != 0) {
                            // Code and Discriptions are not identical, i.e: 1 => Clear
                            $selectedCodePairs[] = [
                                'Code' => $fieldValue,
                                'Description' => $formCodeDescriptions[$formCodeIndex]
                            ];
                        } else {
                            // Code and Discriptions are identical i.e: Alcohol => Alcohol
                            $selectedCodePairs[] = [
                                'Code' => '',
                                'Description' => $fieldValue
                            ];
                        }
                    }
                    
                    $value = $selectedCodePairs;
                }
            }
        );
        
        $entryData['CountKeyed'] = $this->countFieldsKeyed($reportEntryId, $entryData);
        $entryData['FormName'] = ucwords($formValues['formSystem']);

        $entryData = $this->sortGroupElements($entryData);
        $entryData = $this->setIncrementalPartyIdForPeople($entryData);

        return $entryData;
    }
    
    /**
     * Get multi-select dropdown values
     *
     * @param int $reportId
     * @return array
     */
    public function getMultiSelectFormCodePairs($reportId)
    {
        $reportInfo = $this->adapterReport->getRelatedInfo($reportId);
        $rowFormInfo = $this->serviceForm->getFormInfo($reportInfo['formId']);
        
        $formConfiguration = $this->adapterFCGC->fetchFormCodeConfiguration($rowFormInfo['formTemplateId'], $rowFormInfo['stateId'], $rowFormInfo['agencyId']);
        $formCodeGroupId = $formConfiguration['form_code_group_id'];
        $formCodePairs = $this->adapterFormCodeMap->getFieldMultiselectCodePairs($formCodeGroupId);
        
        return ['codePairs' => $formCodePairs, 'formSystem' => $reportInfo['formSystem']];
    }
    
    /**
     * Get form fields which contains Code/Description value in entry data JSON
     *
     * @param int $reportId
     * @return array
     */
    public function getFormFieldsCodePair($reportId)
    {
        $reportInfo = $this->adapterReport->getRelatedInfo($reportId);
        return $this->serviceFormField->getCodeDescriptionPairFieldsByFormSystemId($reportInfo['formSystemId']);
    }
    
    public function convertInputFieldName($inputName) {
        return strtolower(str_replace('_', '', $inputName));
    }
    
    /**
     * Get information about Auto extraction entry data
     *
     * @param int $reportId
     * @return array
     */
    public function getAutoextractionDataDecompressed($reportId)
    {
        $reportEntryData = $this->adapterReportEntryData->fetchAutoextractionData($reportId);

        if (!empty($reportEntryData['entryData'])) {
            $reportEntryData['entryData'] = $this->decompressData($reportEntryData['entryData']);
        }

        return $reportEntryData;
    }

    public function getMaxCompletedFormAndEntryId($reportId)
    {
        return $this->adapterReportEntry->getMaxCompletedFormAndEntryId($reportId);
    }
	
	public function getSlaStatusSummarybyState($searchCriteria)
    {
        return $this->adapterReportEntry->getSlaStatusSummarybyState($searchCriteria);
    }
	
	public function getSlaStatusSummaryTotal($searchCriteria,$workType)
    {
        return $this->adapterReportEntry->getSlaStatusSummaryTotal($searchCriteria,$workType);
    }
    
    public function checkInprogressReport($reportId, $entryStageId)
    {
        return $this->adapterReportEntry->checkInprogressReport($reportId, $entryStageId);
    }
    
    /**
     * Append narrative data to keyed data
     *
     * @param string $narrativeData
     * @param array $entryData
     * @return array $entryData
     */
    public function appendNarrativeData($narrativeData, $entryData)
    {
        if (!isset($entryData['Incident']['Narrative'])) {
            $entryData['Incident']['Narrative'] = $narrativeData;
        }
        return $entryData;
    }
    
}
