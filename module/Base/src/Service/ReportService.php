<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Exception;
use Zend\Db\Sql\Select;

use Base\Adapter\Db\ReportAdapter;
use Base\Adapter\Db\ReadOnly\ReportAdapter as ReadOnlyReportAdapter;
use Base\Adapter\Db\ReportNoteAdapter;
use Base\Adapter\Db\ReportCruAdapter;
use Base\Service\StateConfigurationService;
use Base\Service\ReportEntryService;
use Base\Adapter\Db\ReportEntryDataAdapter;

class ReportService extends BaseService
{
    const ENTRY_FLOW_ENTRY = 'entry';
    const ENTRY_FLOW_VIEW = 'view';
    const ENTRY_FLOW_BAD = 'bad image';
    const ENTRY_FLOW_DISCARD = 'discard';
    const ENTRY_FLOW_DEAD = 'dead';
    
    const REPORT_TYPE_ACCIDENT = 'A';
    const REPORT_TYPE_THEFT = 'B';
    const REPORT_TYPE_THEFT_RECOVERY = 'C';
    const REPORT_TYPE_FIRE = 'F';
    const REPORT_TYPE_DRIVERS_EXCHANGE = 'DE';
    /**
     * Manual keying universal report target value minuets/report
     */
    const MANUAL_KEYING_UNIVERSAL_REPORT_TARGET = '10.27';
    /**
     * Manual keying universal-sectional report target value minuets/report
     */
    const MANUAL_KEYING_UNIVERSAL_SECTIONAL_REPORT_TARGET = '15.97';
    /**
     * Manual keying longform report target value minuets/report
     */
    const MANUAL_KEYING_LONGFORM_REPORT_TARGET = '18.60';
    /**
     * Auto keying universal report target value minuets/report
     */
    const AUTO_KEYING_UNIVERSAL_REPORT_TARGET = '5.71';
    /**
     * Auto keying universal-sectional report target value minuets/report
     */
    const AUTO_KEYING_UNIVERSAL_SECTIONAL_REPORT_TARGET = '8.87';
    /**
     * Auto keying longform report target value minuets/report
     */
    const AUTO_KEYING_LONGFORM_REPORT_TARGET = '10.33';
    /**
     * All universal reports target percentage (10.27 - 5.71) / 5.71
     */
    const ALL_UNIVERSAL_REPORT_TARGET = '80';
    /**
     * All universal-sectional reports target percentage (15.97 - 8.87) / 8.87
     */
    const ALL_UNIVERSAL_SECTIONAL_REPORT_TARGET = '80';
    /**
     * All longform reports target percentage (18.60 - 10.33) / 10.33
     */
    const ALL_LONGFORM_REPORT_TARGET = '80';
    
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
    
    public function __construct(
        Array $config,
        Logger $logger,
        ReportAdapter $adapterReport,
        ReadOnlyReportAdapter $adapterReadOnlyReport,
        ReportNoteAdapter $adapterReportNote,
        ReportCruAdapter $adapterReportCru,
        StateConfigurationService $serviceStateConfiguration,
        ReportEntryService $serviceReportEntry,
        ReportEntryDataAdapter $adapterReportEntryData)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterReport = $adapterReport;
        $this->adapterReadOnlyReport = $adapterReadOnlyReport;
        $this->adapterReportNote = $adapterReportNote;
        $this->adapterReportCru = $adapterReportCru;
        $this->serviceStateConfiguration = $serviceStateConfiguration;
        $this->serviceReportEntry = $serviceReportEntry;
        $this->adapterReportEntryData = $adapterReportEntryData;
    }
    
    /**
    * get Report Data for selected date range and formId
    * @param integer $formId
    * @param date $startDate
    * @param date $endDate
    * @return array
    */
    public function getReportByFormId($formId, $startDate, $endDate)
    {
        try {
            return $this->adapterReport->getReportByFormId($formId, $startDate, $endDate);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at ' . $e->getFile() . ' on line ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        }// @codeCoverageIgnoreStart
    }

    /**
     * Get count of reports (per agency and work type) based on user.
     *
     * @param string $nameLast
     * @param string $nameFirst
     * @param string $dateStart
     * @param string $dateEnd
     * @return array - Associative row/column layout
     */
    public function getCountWorkTypeByUsersName(
        $keyingVendorId,
        $isLNUser,
        $nameLast = null,
        $nameFirst = null,
        $dateStart = null,
        $dateEnd = null)
    {
        return $this->adapterReadOnlyReport->getCountWorkTypeByUsersName($keyingVendorId, $isLNUser, $nameLast, $nameFirst, $dateStart, $dateEnd);
    }

    /**
     * Checks if a report has any notes attached.
     *
     * @param integer $reportId
     * @return boolean
     */
    public function hasNotes($reportId)
    {
        return $this->adapterReportNote->hasReportNotes($reportId);
    }

    /**
     * Sets a report's reorder date.
     *
     * @param integer $reportId
     * @param string $dateReordered Y-m-d format
     */
    public function setDateReordered($reportId, $dateReordered)
    {
        $this->adapterReport->setDateReordered($reportId, $dateReordered);
    }
    
    /**
     * Gets basic information about a report (form, type, status, etc)
     *
     * @param integer $reportId
     * @return array
     */
    public function getRelatedInfo($reportId)
    {
        return $this->adapterReport->getRelatedInfo($reportId);
    }

    public function getCruData($reportId)
    {
        $rowData = $this->adapterReportCru->getCruData($reportId);
        
        if ($rowData) {
            return [
                'reportCruId' => $rowData['report_cru_id'],
                'dateCreated' => $rowData['date_created'],
                'dateUpdated' => $rowData['date_updated'],
                'reportId' => $rowData['report_id'],
                'cruOrderId' => $rowData['cru_order_id'],
                'cruSequenceNbr' => $rowData['cru_sequence_nbr'],
                'cruStateNbr' => $rowData['cru_state_nbr'],
                'cruAgencyId' => $rowData['cru_agency_id'],
                'cruAgencyName' => $rowData['cru_agency_name'],
                'cruCheckinDate' => $rowData['cru_checkin_date'],
                'cruDateOfLoss' => $rowData['cru_date_of_loss'],
            ];
        } else {
            return [];
        }
    }

    public function updateForm($formId, $reportId)
    {
        if (empty($formId) || empty($reportId)) {
            return;
        }
        
        //add log if form id was changed to track changing of forms
        $this->logger->log(Logger::ERR, "Changing form id to $formId in report table with report id: $reportId");
        
        return $this->adapterReport->updateForm($formId, $reportId);
    }

    public function getHashKey($reportId)
    {
        return $this->adapterReport->getHashKey($reportId);
    }
    
    public function getReport($reportId)
    {
        return $this->adapterReport->getReport($reportId);
    }

    public function fetchKeyedImages(Array $searchParams = [])
    {
        return $this->adapterReport->fetchKeyedImages($searchParams);
    }
    
    public function getReportStateId($reportId)
    {
        return $this->adapterReport->getReportStateId($reportId);
    }

    public function getReportWorkTypeId($reportId)
    {
        return $this->adapterReport->getReportWorkTypeId($reportId);
    }

    public function isNewApp($reportId)
    {
        $reportStateId = $this->adapterReport->getReportStateId($reportId);
        $reportWorkTypeId = $this->adapterReport->getReportWorkTypeId($reportId);
        $isRolloutState = $this->serviceStateConfiguration->isRollOutState($reportStateId, $reportWorkTypeId);
        $reportEntryData = $this->adapterReportEntryData->getLastReportEntryDataByReportId($reportId);
        
        /**
         * If Current Report State is in the Roll Out States Continue Loading
         *  Otherwise Prompt a Message to go back to Old Keying App
         */
        if ($isRolloutState && empty($reportEntryData)) {
            // continue loading the report form in new application
            $isNewApp = 1;
        } else if (!empty($reportEntryData) && $reportEntryData['newApp'] == 1) {
            // Pass 1 already done in new app but rolled back to old app.
            $isNewApp = 1;
        } else {
            // prompt a message to go back to old Keying App
            $isNewApp = 0;
        }
        
        return $isNewApp;
    }
    
    public function isAutoExtractionEnabledForState($reportId)
    {
        $stateId = $this->adapterReport->getReportStateId($reportId);

        return $this->serviceStateConfiguration->getAutoExtractionValue($stateId);
    }

    public function getAutoVsManualReportSelect($searchCriteria)
    {
        return $this->adapterReadOnlyReport->prepareAutoVsManualReportSelectByState($searchCriteria);
    }

    public function getAutoVsManualReportByState(Select $select, Array $searchCriteria = [])
    {
        return $this->adapterReadOnlyReport->fetchAutoVsManualReportByState($select, $searchCriteria);
    }

    public function getAutoVsManualReportByStateTotalRows(Select $select, Array $searchCriteria = [])
    {
        return $this->adapterReadOnlyReport->fetchAutoVsManualReportTotalRows($select, $searchCriteria);
    }

    public function updateReportKeyingType($reportId, $hasAutoExtracted = 0, $hasAutoKeyed = 0)
    {
        return $this->adapterReport->updateReportKeyingType($reportId, $hasAutoExtracted, $hasAutoKeyed);
    }

    public function isAutoKeyed($reportId)
    {
        return $this->adapterReport->isAutoKeyed($reportId);
    }

    public function getVolumeProductivityReportSelect(Array $searchCriteria = [])
    {
        return $this->adapterReadOnlyReport->prepareVolumeProductivityReportSelectByState($searchCriteria);
    }

    public function getVolumeProductivityReportByState(Select $select, Array $searchCriteria = [])
    {
        return $this->adapterReadOnlyReport->fetchVolumeProductivityReportByState($select, $searchCriteria);
    }
}
