<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Base\Adapter\Db\UserAccuracyAdapter;
use Base\Adapter\Db\UserAccuracyInvalidAdapter;
use Base\Adapter\Db\AutoExtractionDataAdapter;
use Base\Adapter\Db\AutoExtractionAccuracyAdapter;
use Base\Service\ReportEntryService;
use Base\Service\FormService;
use Base\Service\EntryStageService;

class UserAccuracyService extends BaseService
{
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Base\Adapter\Db\UserAccuracyAdapter
     */
    protected $adapterUserAccuracy;
    
    /**
     * @var Base\Adapter\Db\UserAccuracyInvalidAdapter
     */
    protected $adapterUserAccuracyInvalid;
   
    /**
     * @var Base\Adapter\Db\AutoExtractionDataAdapter
     */
    protected $adapterAutoExtractionData;
    
    /**
     * @var Base\Adapter\Db\AutoExtractionAccuracyAdapter
     */
    protected $adapterAutoExtractAccuracy;
    
    /**
     * @var Base\Service\ReportEntryService
     */
    protected $serviceReportEntry;
    
    /**
     * @var Base\Service\FormService
     */
    protected $serviceForm;
    
     
    /**
     * A report entry in these stages will be considered authoritative and
     * other entries for the same report will be compared against it.
     *
     * Note that if there is no entry data in an authoritative stage the
     * accuracy metric can/will not be calculated.
     *
     * @var array
     */
    public $authoritativeStages = [
        EntryStageService::STAGE_DIFFERENCE_VERIFICATION,
        EntryStageService::STAGE_DYNAMIC_VERIFICATION,
    ];

    /**
     * Report entries that are found in these stages will be ignored
     *
     * @var array
     */
    public $ignoredStages = [
        EntryStageService::STAGE_EDIT,
        EntryStageService::STAGE_INVALID_VIN
    ];
    
    public function __construct(
        Array $config,
        UserAccuracyAdapter $adapterUserAccuracy,
        UserAccuracyInvalidAdapter $adapterUserAccuracyInvalid,
        AutoExtractionDataAdapter $adapterAutoExtractionData,
        AutoExtractionAccuracyAdapter $adapterAutoExtractAccuracy,
        ReportEntryService $serviceReportEntry,
        FormService $serviceForm)
    {
        $this->config = $config;
        $this->adapterUserAccuracy = $adapterUserAccuracy;
        $this->adapterAutoExtractionData   = $adapterAutoExtractionData;
        $this->adapterAutoExtractAccuracy = $adapterAutoExtractAccuracy;
        $this->adapterUserAccuracyInvalid = $adapterUserAccuracyInvalid;
        $this->serviceReportEntry = $serviceReportEntry;
        $this->serviceForm = $serviceForm;
    }

    /**
     * Will return a select to pull userAccuracy info for userId(s)
     *
     * This will not return userAccuracyInvalid data.
     * Try not to use this outside of paginators, use getAllByUserIds instead.
     *
     * @param array|int $userIds
     * @param string $fromDate
     * @param string $toDate
     * @param int $stateId
     * @param int $formId
     * @param int $formAgencyId
     * @return Zend_Db_Select
     */
    public function getSelectAllByUserIds(
        Array $userIds,
        $fromDate = null,
        $toDate = null,
        $stateId = null,
        $formId = null,
        $formAgencyId = null)
    {
        $result = $this->adapterUserAccuracy->getSelectAllByUserIds(
            $userIds,
            $fromDate,
            $toDate,
            $stateId,
            $formId,
            $formAgencyId
        );

        return $result;
    }

    /**
     * Will return userAccuracy info for userid(s)
     *
     * This will not return userAccuracyInvalid data.
     *
     * @param array|int $userIds
     * @param string $fromDate
     * @param string $toDate
     * @param int $stateId
     * @param int $formId
     * @param int $formAgencyIdyId
     * @return array|FALSE
     */
    public function getAllByUserIds(
        Array $userIds,
        $fromDate = null,
        $toDate = null,
        $stateId = null,
        $formId = null,
        $formAgencyId = null)
    {
        $result = $this->adapterUserAccuracy->getAllByUserIds(
            $userIds,
            $fromDate,
            $toDate,
            $stateId,
            $formId,
            $formAgencyId
        );

        return $result;
    }

    /**
     * Gets number of fields keyed for criteria
     *
     * @param array|int $userIds
     * @param string $fromDate
     * @param string $toDate
     * @param int $stateId
     * @param int $formId
     * @param int $formAgencyId
     * @return int
     */
    public function getCountKeyed(
        Array $userIds,
        $fromDate = null,
        $toDate = null,
        $stateId = null,
        $formId = null,
        $formAgencyId = null)
    {
        return $this->adapterUserAccuracy->getCountKeyed(
            $userIds,
            $fromDate,
            $toDate,
            $stateId,
            $formId,
            $formAgencyId
        );
    }

    /**
     * Calculates the accuracy score
     *
     * @param int $countInvalid
     * @param int $countKeyed
     * @return int
     */
    public function calculateAccuracyScore(
        $countInvalid = null,
        $countKeyed = null)
    {
        $userAccuracy = ($countKeyed > 0)
            ? round((($countKeyed - $countInvalid) / $countKeyed), 2)
            : null;
        
        return $userAccuracy;
    }
    
    /**
     * Checks if there are any accuracy records for a report id
     *
     * @param int $reportId
     * @return bool
     */
    protected function hasRecord($reportId)
    {
        $return = ($this->adapterUserAccuracy->getCountRecords($reportId) != 0);
        return $return;
    }
    
    /**
     * Writes to the UserAccuracy table the differences from initial and verification passes for a report
     *
     * @param int $reportId
     * @return int|NULL - Number of differences written, NULL means unable to calculate (not 0 differences)
     */
    public function createMetricData($reportId)
    {
        // Currently writing multiple records for 1 report should not occur
        if ($this->hasRecord($reportId)) {
            return;
        }

        $numDifferences = null;
        $entryData = $this->serviceReportEntry->getReportEntryDataDecompressed($reportId);
        if (count($entryData) == 0) {
            return;
        }
        
        $authoritativeEntry = $this->getAuthoritativeEntry($entryData);
        if (empty($authoritativeEntry)) {
            return;
        }
        
        $authoritativeEntry['entryData']['Report'] = $this->getAccuracyComparisonFormat(
            $authoritativeEntry['formId'],
            $authoritativeEntry['entryData']['Report']
        );
        
        $numDifferences = 0;
        foreach ($entryData as $entry) {
            if ($entry['reportEntryId'] == $authoritativeEntry['reportEntryId']
                || in_array($entry['entryStage'], $this->ignoredStages)) {

                continue;
            }
            
            //@Todo, need to update the existing accuracy calculation without converting into flatten data for performance improvement.
            $entry['entryData']['Report'] = $this->getAccuracyComparisonFormat(
                $entry['formId'],
                $entry['entryData']['Report']
            );
            
            $userAccuracyId = $this->adapterUserAccuracy->insertNew(
                $entry['userId'],
                $reportId
            );
            
            $numDifferences += $this->insertDifferences(
                $userAccuracyId,
                $entry['entryData']['Report'],
                $authoritativeEntry['entryData']['Report']
            );
        }
        
        return $numDifferences;
    }

    /**
     * Formats the data in a way that it is easy to do accuracy comparisons. (flattens it)
     * 
     * @param int $formId
     * @param array $data - Most likely common formatted entry data, though other data should work as well
     * @return array 
     */
    public function getAccuracyComparisonFormat($formId, $data)
    {
        $transformer = $this->serviceForm->getDataTransformerByFormId($formId);
        $data = $transformer->toCommon($data);
        $data = $transformer->toFlat($data);
        return $data;
    }

    /**
     * Inserts difference records for an accuracy record
     *
     * Note:
     * If the fields are not present in entry data on the first pass but
     * appear on the second pass they wont be counted. The logic is that
     * since every field on the form is recorded they can't be held accountable
     * for fields they couldn't key.
     *
     * @param int $userAccuracyId
     * @param array $entryData
     * @param array $authoritativeData
     * @return int
     */
    protected function insertDifferences($userAccuracyId, Array $entryData, Array $authoritativeData)
    {
        $differences = array_udiff_assoc(
            $entryData,
            $authoritativeData,
            'strcasecmp'
        );

        //@TODO: This needs to ignore vin status and the vin original fields
        $numDifferences = 0;
        if (!empty($differences)) {
            foreach ($differences as $formAttributeName => $userValue) {

                $authoritativeValue =
                    (isset($authoritativeData[$formAttributeName])
                        && !empty($authoritativeData[$formAttributeName]))
                    ? $authoritativeData[$formAttributeName]
                    : '';

                $userValue = is_null($userValue) ? '' : $userValue;

                if (trim($authoritativeValue) != ''
                    || trim($userValue) != '') {

                    $this->adapterUserAccuracyInvalid->insertNew(
                        $userAccuracyId,
                        $formAttributeName,
                        $authoritativeValue,
                        $userValue
                    );

                    $numDifferences ++;
                }
            }
        }

        return $numDifferences;
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

    /**
     * Returns the first entry data record that has an authoritative entryStage
     *
     * @param array $entryData
     * @return array
     */
    public function getAuthoritativeEntry(Array $entryData)
    {
        $authoritativeEntry = null;
        foreach ($entryData as $entry) {
            if (in_array($entry['entryStage'], $this->authoritativeStages)) {
                $authoritativeEntry = $entry;
                break;
            }
        }
        
        return $authoritativeEntry;
    }

}