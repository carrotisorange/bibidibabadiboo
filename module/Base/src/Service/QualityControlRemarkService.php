<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Base\Adapter\Db\QualityControlRemarkAdapter;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class QualityControlRemarkService extends BaseService
{
    /**
     * @var Array
     */
    protected $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Adapter\Db\ReportAdapter
     */
    protected $adapterReport;

    /**
     * @var Base\Adapter\Db\ReadOnly\ReportAdapter
     */
    protected $adapterReadOnlyReport;

    /**
     * @var Base\Adapter\Db\ReportNoteAdapter
     */
    protected $adapterReportNote;

    /**
     * @var Base\Service\StateConfigurationService
     */
    protected $serviceStateConfiguration;

    /**
     * @var Base\Service\ReportEntryService
     */
    protected $serviceReportEntry;

    /**
     * @var Base\Adapter\Db\ReportCruAdapter
     */
    protected $adapterReportCru;

    /**
     * @var Base\Adapter\Db\ReportEntryDataAdapter
     */
    protected $adapterReportEntryData;
    
    const WEEK_NAMES = ["First_Week", "Second_Week", "Third_Week", "Fourth_Week"];

    const FETCH_AUDITED = 'AUDITED_REPORT';
    const FETCH_NONE_AUDITED = 'NONE_AUDITED_REPORT';
    const FETCH_DEFAULT = 'DEFAULT';

    public $errors = [];
    public $correctedFields = [];

    public $isEdit = null;

    public function __construct(
        Array $config,
        Logger $logger,
        QualityControlRemarkAdapter $adapterQualityControlRemark)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterQualityControlRemark = $adapterQualityControlRemark;
        $this->errors = [];
    }

    public function addOrUpdate($general, $fields)
    {
        $correctedFields = [];

        foreach ($fields as $field) {
            if (!empty(trim($field['value']))) {
                array_push($correctedFields, $field);
            }
        }
        
        if (!empty($correctedFields)) {
            $adapterResult = $this->adapterQualityControlRemark->addOrUpdate($general, $correctedFields);
            $this->isEdit = !empty($this->adapterQualityControlRemark->qcRemarks);
            return $adapterResult;
        }

        $this->errors[] = "There are no fields found for update";
        return false;
    }
    

    public function noissue($reportId, $userId)
    {
       return $this->adapterQualityControlRemark->noIssue($reportId, $userId);
    }

    public function getRemarks($condition)
    {   return $this->adapterQualityControlRemark->getRemarks([
            'fromDate' => $condition['fromDate'],
            'toDate' => $condition['toDate'],
            'workType' => $condition['workType'],
            'state' => $condition['state'],
        ], 'qc.report_id asc');
    }

    public function getReportRemarks($reportId)
    {
        return $this->adapterQualityControlRemark->getRemarks([
            'reportId' => $reportId
        ], 'qc.field_name asc');
    }

    public function formatRemarksKeyPair($remarks, $keyPair = [])
    {
        $retVal = [];
        foreach ($remarks as $key => $row) {
            $retVal[$row[$keyPair[0]]] = $row[$keyPair[1]];
        }
        return $retVal;
    }

    public function getReportsForQc($condition)
    {
        return $this->getReports($condition, self::FETCH_NONE_AUDITED);
    }

    public function loadOrUnloadReport($reportId, $userId, $type)
    {
        return $this->adapterQualityControlRemark->loadOrUnloadReport($reportId, $userId, $type);
    }

    public function unloadUserReport($userId)
    {
        return $this->adapterQualityControlRemark->unloadUserReport($userId);
    }

    public function getReportOpenedByUser($userId)
    {
        return $this->adapterQualityControlRemark->getReportOpenedByUser($userId);
    }
    
    public function getDateRange($param)
    {
        $year = $param['year'];
        $month = $param['month'];
        $week = $param['week'];

        $startWeek = current($week);
        $endWeek = end($week);

        switch ($startWeek) {
            case 'First_Week': 
                $fromDate = date('Y-m-d', strtotime("{$month}/1/{$year}"));
            break;
            case 'Second_Week': 
                $fromDate = date('Y-m-d', strtotime("{$month}/9/{$year}"));
            break;
            case 'Third_Week': 
                $fromDate = date('Y-m-d', strtotime("{$month}/15/{$year}"));
            break;
            case 'Fourth_Week': 
                $fromDate = date('Y-m-d', strtotime("{$month}/22/{$year}"));
            break;
        }

        switch ($endWeek) {
            case 'First_Week': 
                $toDate = date('Y-m-d', strtotime("{$month}/8/{$year}"));
            break;
            case 'Second_Week': 
                $toDate = date('Y-m-d', strtotime("{$month}/14/{$year}"));
            break;
            case 'Third_Week': 
                $toDate = date('Y-m-d', strtotime("{$month}/21/{$year}"));
            break;
            case 'Fourth_Week': 
                $toDate = date('Y-m-d', strtotime("{$month}/31/{$year}"));
            break;
        }

        return [
            $fromDate,
            $toDate
        ];
    }

    public function generateDateRangeByDate($reportEntryCreatedDate)
    {
        $reportEntryCreatedDate = strtotime($reportEntryCreatedDate);
        $fromDate = date('Y/m/01', $reportEntryCreatedDate);
        $toDate = date('Y/m/t', $reportEntryCreatedDate);

        return [
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ];
    }   
    
    public function extractFetchParamByReport($parameters = [], $report)
    {
        $retVal = [];
        $conditions = [
            'workType' => [
                'retValKey' => 'workType',
                'reportKey' => 'work_type_id'
            ],
            'state' => [
                'retValKey' => 'state',
                'reportKey' => 'state_id'
            ],
            'reportType' => [
                'retValKey' => 'reportType',
                'reportKey' => 'form_type_id'
            ]
        ];

        foreach($conditions as $key => $row) {
            if (isset($parameters[$row['retValKey']]) && $parameters[$row['retValKey']] == 'true') {
                $retVal[$row['retValKey']] = $report[$row['reportKey']];
            }
        }
        return $retVal;
    }
    
    public function getReport($reportId, $fetchOption = self::FETCH_DEFAULT)
    {
        return $this->getReportRaw([
            'reportId' => $reportId
        ], null, 1, $fetchOption)[0] ?? false;
    }

    
    public function getAuditedReport($reportId)
    {   
        return $this->getReportRaw([
            'reportId' => $reportId
        ], null, 1, self::FETCH_AUDITED)[0] ?? false;
    }

    public function isReportAudit($reportId) {
        return $this->adapterQualityControlRemark->isReportAudit($reportId);
    }

    public function getReportRaw($condition, $order ,$limit, $fetchOption = self::FETCH_DEFAULT) {
        return $this->adapterQualityControlRemark->getReports([
            'conditions' => $condition,
            'order' => $order,
            'limit' => $limit
        ], $fetchOption);
    }

    public function getReportRandom($condition, $limit , $fetchOption = self::FETCH_DEFAULT) {
        $conditions = [
            'state','workType','reportType'
        ];

        foreach($conditions as $key => $row) {
            if (isset($condition[$row]) && $condition[$row] == FALSE) {
                unset($condition[$row]);
            }
        }

        return $this->getReportRaw($condition, 'random', $limit, $fetchOption);
    }

    //Dynamic condition for fetching reports
    public function getReports($condition = [], $fetchOption = self::FETCH_DEFAULT)
    {
        return $this->getReportRaw($condition, 'random', 1000, $fetchOption);
    }

    public function getReportsFieldSummaries($reports)
    {
        $retVal = [];
        $totalFieldCount = 0;

        foreach ($reports as $key => $row) {
            $fieldName = $row['field_name'];
            if( !isset($retVal[$fieldName]) ) 
                $retVal[$fieldName] = [
                    'field_name' => $fieldName,
                    'total_count' => 0,
                    'percentage' => 0
                ];   
            $retVal[$fieldName]['total_count']++;
            $totalFieldCount++;
        }
        
        foreach ($retVal as $key => $row) {
            $retVal[$key]['percentage'] = round(($row['total_count'] / $totalFieldCount) * 100, 2) ;
        }

        $percentage = array_column($retVal, 'percentage');
        array_multisort($percentage, SORT_DESC, $retVal);
        $retVal = array_slice($retVal, 0, 10);

        return $retVal;
    }

    //generate filter options for report-summary
    public function getFilterOptions($reports)
    {
        $retVal = [
            'fields' => [],
            'keyers' => [],
            'contributors' => [],
            'criticalities' => [
                'minor','major','critical','no_issue'
            ],
            'states' => [],
            'workTypes' => []
        ];

        foreach ($reports as $key => $row) {
            $fieldName = $row['field_name'];
            $keyerName = $row['keyerUsername'];
            $contributors = $row['username'];
            $state = $row['stateAbbr'];
            $workTypeName = $row['workTypeName'];
        
            $this->appendFilterOptionValues($retVal, 'fields', $fieldName);
            $this->appendFilterOptionValues($retVal, 'keyers', $keyerName);
            $this->appendFilterOptionValues($retVal, 'contributors', $contributors);
            $this->appendFilterOptionValues($retVal, 'states', $state);
            $this->appendFilterOptionValues($retVal, 'workTypes', $workTypeName);
        }

        return $retVal;
    }

    private function appendFilterOptionValues(&$retVal, $field, $value) {   
        if (!in_array($value, $retVal[$field])) {
            array_push($retVal[$field], $value);
        }
    }

    public function filterResultByParam($results = [], $param = [])
    {
        $retVal = [];
        foreach ($results as $row) {
            $isFilterPassed = true;

            if ($this->checkParamSetAndNotEmpty($param, 'fields')) {
                if (!in_array($row['field_name'], $param['fields'])) {
                    $isFilterPassed = false;
                }
            }
            
            if ($this->checkParamSetAndNotEmpty($param, 'keyers')) {
                if(!in_array($row['keyerUsername'], $param['keyers'])) {
                    $isFilterPassed = false;
                }
            }

            if ($this->checkParamSetAndNotEmpty($param, 'states')) {
                if($row['stateAbbr'] != $param['states']) {
                    $isFilterPassed = false;
                }
            }

            if ($this->checkParamSetAndNotEmpty($param, 'workTypes')) {   
                if($row['workTypeName'] != $param['workTypes']) {
                    $isFilterPassed = false;
                }
            }

            if ($this->checkParamSetAndNotEmpty($param, 'criticalities')) {
                if($row['criticality'] != $param['criticalities']) {
                    $isFilterPassed = false;
                }
            }

            if ($isFilterPassed) {
                $retVal[] = $row;
            }
        }

        return $retVal;
    }

    private function checkParamSetAndNotEmpty($param, $key) 
    {
        return !empty($param[$key]);
    }


    public function summarizeReports($reports)
    {
        $criticalitySummary = [
            'total' => 0
        ];
        $contributors = [];
        $reportIds = [];
        $reportWithDiscrepancies = [];

        foreach ($reports as $key => $field) {
            if (!isset($criticalitySummary[$field['criticality']])) {
                $criticalitySummary[$field['criticality']] = 0;
            }
            $criticalitySummary[$field['criticality']]++;
            
            if (!isset($contributors[$field['username']])) {
                $contributors[$field['username']] = 0;   
            }
            $contributors[$field['username']] ++;
            
            if (!in_array($field['report_id'], $reportIds)) {
                $reportIds[] = $field['report_id'];
            }

            if ($field['field_name'] != $this->adapterQualityControlRemark::NO_ISSUE_FIELD) {
                if (!in_array($field['report_id'], $reportWithDiscrepancies)) {
                    $reportWithDiscrepancies[] = $field['report_id'];
                }
            }
            $criticalitySummary['total']++;
        }

        $criticalityPercentage = [
            'total' => $criticalitySummary['total']
        ];

        foreach ($criticalitySummary as $key => $row) {
            if ($row > 0) {
                $criticalityPercentage[$key] = round(
                    ($criticalitySummary[$key] / $criticalitySummary['total']) * 100 , 2
                );
            }
        }
        $retVal = [];
        $retVal['reports'] = $reports;
        $retVal['reportCountTotal'] = count($reportIds);
        $retVal['reportFieldCountTotal'] =  count($reports);
        $retVal['reportWithDiscrepanciesTotal'] = count($reportWithDiscrepancies);
        $retVal['criticalityPercentage'] = $criticalityPercentage;
        $retVal['reportWithDiscrepancies'] = $reportWithDiscrepancies;
        $retVal['criticalitySummary'] = $criticalitySummary;
        $retVal['contributors'] = $contributors;

        return $retVal;
    }

    public function convertFieldToLabel($comparedData)
    {
        $convertedData = [];
        $comparedDataKey = str_replace('_', ' ', array_keys($comparedData));
        $counter = 0;
        
        foreach ($comparedData as $key => $row) {   
            $rowData = $row;
            $rowData['labelName'] = $comparedDataKey[$counter];
            $convertedData[$key] = $rowData;
            $counter++;
        }
        
        return $convertedData;
    }
}
