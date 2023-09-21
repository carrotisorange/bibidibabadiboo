<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Controller;

use Zend\Log\Logger;
use Zend\Session\Container;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter;
use Zend\Db\Sql\Select;
use Exception;

use Base\Controller\BaseController;
use Base\Service\ReportService;
use Admin\Form\AutoExtractionMetric\AutoExtractionReportForm;
use Admin\Form\AutoExtractionMetric\AutoExtractionAccuracyForm;
use Admin\Form\AutoExtractionMetric\VolumeProductivityReportForm;
use Admin\Validator\CheckKeyingVendorId;
use Base\Service\StateService;
use Base\Service\ReportEntryService;
use Base\Service\AutoExtractionAccuracyService;
use Base\Service\EntryStageService;
use Base\Service\UserService;
use Base\Service\KeyingVendorService;
use Base\View\Helper\DataRenderer\ReportMaker;
use Zend\View\Renderer\PhpRenderer;

class AutoExtractionMetricsController extends BaseController
{
    private $comparedData = [];
    private $ignoredFields = ['Party_Id', 'Fatality_Involved', 'Trailer_Unit_Number', 'VinValidation_VinStatus', 'VIN_Original', 'Model_Year_Original', 'Make_Original', 'Model_Original'];
    private $universalStates = ['VI', 'GU', 'NB', 'PR', 'YT', 'BC', 'AB', 'MB', 'NL', 'NT', 'NS', 'NU', 'ON', 'PE', 'QC', 'SK'];
    private $universalSectionalStates = ['TX', 'PA', 'OH', 'TN', 'WI', 'MO', 'MI', 'NJ', 'GA', 'MN', 'MS', 'LA', 'AL', 'MD', 'CT', 'OK', 'OR', 'AR', 'MA', 'WV', 'IA', 'NE', 'VA', 'DE', 'NH','AZ', 'WA', 'KS', 'MT', 'ME', 'NV', 'KY', 'VT', 'RI', 'HI', 'CO', 'DC', 'AK', 'WY', 'ND', 'SC', 'NC', 'IL', 'ID', 'IN', 'SD', 'UT'];
    private $longFormStates = ['CA', 'NY', 'NM', 'FL'];

    const MANUAL_KEYING_UNIVERSAL_SECTIONAL_REPORT_TARGET = '15.97';
    const AUTO_KEYING_UNIVERSAL_REPORT_TARGET = '5.71';
    const AUTO_KEYING_UNIVERSAL_SECTIONAL_REPORT_TARGET = '8.87';
    const MANUAL_KEYING_LONGFORM_REPORT_TARGET = '18.60';
    const AUTO_KEYING_LONGFORM_REPORT_TARGET = '10.33';
    const MANUAL_KEYING_UNIVERSAL_REPORT_TARGET = '10.27';
    const ALL_UNIVERSAL_SECTIONAL_REPORT_TARGET = '80';
    const ALL_UNIVERSAL_REPORT_TARGET = '80';
    const ALL_LONGFORM_REPORT_TARGET = '80';

    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    private $logger;
    
    /**
     * @var Zend\Session\Container
     */
    private $session;
    
    /**
     * @var Admin\Form\AutoExtractionMetric\AutoExtractionReportForm
     */
    private $formAutoExtractionReport;
    
    /**
     * @var Admin\Form\AutoExtractionMetric\AutoExtractionAccuracyForm
     */
    private $formAutoExtractionAccuracy;

    /**
     * @var Admin\Form\AutoExtractionMetric\VolumeProductivityReportForm
     */
    private $formVolumeProductivity;

    /**
     * @var Base\Service\ReportService
     */
    private $serviceReport;

    /**
     * @var Base\Service\StateService
     */
    private $serviceState;
    
    /**
     * @var Base\Service\AutoExtractionAccuracyService
     */
    private $serviceAutoExtractionAccuracy;

    /**
     * @var Base\View\Helper\DataRenderer\ReportMaker
     */
    private $reportMaker;
    
    /**
     * @var Base\Service\KeyingVendorService
     */
    private $serviceKeyingVendor;
    
    /**
     * Constructor will be invoked from the ViewKeyedImageControllerFactory
     * @param array  $config        Application configuration
     * @param object $logger        Zend\Log\Logger
     * @param object $session   Zend\Session\Container;
     * @param object $formAutoExtractionReport   Admin\Form\AutoExtractionMetric\AutoExtractionReportForm;
     * @param object $formAutoExtractionAccuracy   Admin\Form\AutoExtractionMetric\AutoExtractionAccuracyForm;
     * @param object $formVolumeProductivity   Admin\Form\AutoExtractionMetric\VolumeProductivityReportForm;
     * @param object $serviceReport  Base\Service\StateService;
     * @param object $serviceAutoExtractionAccuracy   Base\Service\AutoExtractionAccuracyService
     * @param object $reportMaker  Base\View\Helper\DataRenderer\ReportMaker;
     * @param object $serviceKeyingVendor   Base\Service\KeyingVendorService
     */
    public function __construct(
        Array $config,
        Logger $logger,
        Container $session,
        AutoExtractionReportForm $formAutoExtractionReport,
        AutoExtractionAccuracyForm $formAutoExtractionAccuracy,
        VolumeProductivityReportForm $formVolumeProductivity,
        ReportService $serviceReport,
        AutoExtractionAccuracyService $serviceAutoExtractionAccuracy,
        ReportMaker $reportMaker,
        StateService $serviceState,
        ReportEntryService $serviceReportEntry,
        EntryStageService $serviceEntryStage,
        UserService $serviceUser,
        PhpRenderer $renderer,
        KeyingVendorService $serviceKeyingVendor)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->session = $session;
        $this->formAutoExtractionReport = $formAutoExtractionReport;
        $this->formAutoExtractionAccuracy = $formAutoExtractionAccuracy;
        $this->formVolumeProductivity = $formVolumeProductivity;
        $this->serviceReport = $serviceReport;
        $this->serviceAutoExtractionAccuracy = $serviceAutoExtractionAccuracy;
        $this->reportMaker = $reportMaker;
        $this->serviceState = $serviceState;
        $this->serviceReportEntry = $serviceReportEntry;
        $this->serviceEntryStage = $serviceEntryStage;
        $this->serviceUser = $serviceUser;
        $this->renderer = $renderer;
        $this->serviceKeyingVendor = $serviceKeyingVendor;

        parent::__construct();
    }
    
    public function autoExtractionReportAction()
    {
        $this->layout()->setTemplate('layout/metrics');
        
        if (!empty($this->request->isPost())) {
            $postParams = $this->request->getPost();
            $this->formAutoExtractionReport->setInputFilter($this->formAutoExtractionReport->addInputFilters());
            $this->formAutoExtractionReport->setData($postParams);

            if ((!empty($postParams['submit'])) && ($this->formAutoExtractionReport->isValid())) {
                // Based on the isValidated class, dataTable will retrieve the data's.
                $this->formAutoExtractionReport->setAttribute('class', 'default isValidated');
                /**
                 * @TODO: Try to use single request to render first page data.
                 * Now, the first request will validate the form. Once the form is valid then first page data will be retrieved via ajax request from the dataTable
                 */
            } else if (strcasecmp($postParams['export'], 'Export') == 0) {
                $this->exportReport();
                exit(0);
            } else if (empty($postParams['submit'])) {
                // Return JSON Model for data table
                if (!$this->validateCsrfToken($this->request->getPost('csrf', ''))) {
                    // Csrf token validation for Ajax request
                    $this->json->data = $this->getInvalidCSRFJsonResponse();
                } else {
                    $searchParams = $this->getSearchParams();
                    $reportSelect = $this->serviceReport->getAutoVsManualReportSelect($searchParams);
                    $offset = (!empty($searchParams['offset'])) ? $searchParams['offset'] : 0;
                    $limit = (!empty($searchParams['limit'])) ? $searchParams['limit'] : $this->config['pagination']['perpage'];
                    
                    // To set the select page rows.
                    $reportSelect->offset($offset);
                    $reportSelect->limit($limit);
                    
                    $this->json->draw = $this->request->getPost('draw', 1);
                    $this->json->data = $this->serviceReport->getAutoVsManualReportByState($reportSelect, $searchParams);
                    $this->json->recordsTotal = $this->serviceReport->getAutoVsManualReportByStateTotalRows($reportSelect, $searchParams);
                    $this->json->recordsFiltered = $this->json->recordsTotal;
                    
                    // CSRF token hash will be used in view file
                    $this->json->csrf = $this->getCsrfTokenHash();
                }
                
                return $this->json;
            } else {
                $this->addFormMessages($this->formAutoExtractionReport);
            }
        }
        
        $this->view->reportDuration = AutoExtractionReportForm::AUTO_EXTRACTION_REPORT_DURATION;
        $this->view->isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        $this->view->form = $this->formAutoExtractionReport;
        return $this->view;
    }

    public function exportReport()
    {
        $searchParams = $this->getSearchParams();
        $reportSelect = $this->serviceReport->getAutoVsManualReportSelect($searchParams);
        $data = $this->serviceReport->getAutoVsManualReportByState($reportSelect, $searchParams);
        
        $columns = [
            'state_abbr' => 'State',
            'report_id' => 'Report ID',
            'work_type' => 'Work Type',
            'creation_date' => 'Creation Date',
            'report_status' => 'Report Status',
            'auto_extraction' => 'Auto Extraction',
            'manually_keyed' => 'Manually Keyed',
            'auto_extraction_date' => 'Auto Extraction Date',
            'pass1_username' => 'Pass1 User',
            'pass1_start_date' => 'Pass1 Start Date',
            'pass1_end_date' => 'Pass1 End Date',
            'pass2_username' => 'Pass2 User',
            'pass2_start_date' => 'Pass2 Start Date',
            'pass2_end_date' => 'Pass2 End Date',
            'pass1_duration' => 'Pass1 Duration',
            'pass2_duration' => 'Pass2 Duration',
            'total_duration' => 'Total Time Spent (Per Report)',
        ];
        
        if ($this->serviceKeyingVendor->isLoggedInLNUser()) {
            $columns['vendor_name'] = 'Company Name';
        }

        if (strcasecmp($searchParams['state'], EntryStageService::STAGE_ALL) != 0) {
            $stateAbbr = $this->serviceState->getStateAbbrById($searchParams['state']);
        } else {
            $stateAbbr = ucfirst($searchParams['state']);
        }
        
        $reportDuration = $this->getReportDownloadDate($searchParams['fromDate'], $searchParams['toDate']);
        $reportHeader = [
            'reportName' => $stateAbbr . ' Auto Vs Manual Report ' . $reportDuration
        ];
        $this->export(ReportMaker::REPORT_FORMAT_XLS, $data, $columns, $reportHeader, ['border centeralign']);
    }

    private function getSearchParams()
    {
        return [
            'current_page' => $this->params()->fromRoute('page', 1),
            'offset' => $this->request->getPost('start', 0),
            'limit' => $this->request->getPost('length', 0),
            'fromDate' => $this->request->getPost('fromDate', ''),
            'toDate' => $this->request->getPost('toDate', ''),
            'state' => $this->request->getPost('state', ''),
            'keyingVendorId' => $this->request->getPost('keyingVendorId')
        ];
    }

    private function getReportDownloadDate($fromDate, $toDate)
    {
        if (date('Y-M', strtotime($fromDate)) == date('Y-M', strtotime($toDate))) {
            // Same year and same month
            $dateString = date('d-', strtotime($fromDate)) . date('dMY', strtotime($toDate));
        } else if (date('Y', strtotime($fromDate)) == date('Y', strtotime($toDate))) {
            // Same year and different month
            $dateString = date('dM-', strtotime($fromDate)) . date('dMY', strtotime($toDate));
        } else {
            // Different year and different month
            $dateString = date('dMY-', strtotime($fromDate)) . date('dMY', strtotime($toDate));
        }
        
        return $dateString;
    }
    
    /**
     * Auto vs Manual Keying report Difference Details
     */
    public function autoExtractionAccuracyAction()
    {
        $this->layout()->setTemplate('layout/metrics');
        
        $isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        
        $this->formAutoExtractionAccuracy->setInputFilter($this->formAutoExtractionAccuracy->addInputFilters());
        
        $page = $this->params()->fromQuery('page');
        $queryParams = $this->request->getQuery();
        $queryParams = (array) $queryParams;

        if (!empty($queryParams)) {
            $inputParams = $queryParams;
        } else {
            $inputParams = (array) $this->request->getPost();
        }

        $inputParams['fromDate'] = (!empty($inputParams['fromDate'])) ? $this->convertDateSeperator($inputParams['fromDate']) : '';
        $inputParams['toDate'] = (!empty($inputParams['toDate'])) ? $this->convertDateSeperator($inputParams['toDate']) : '';

        $this->formAutoExtractionAccuracy->setData($inputParams);
        /* End get query params during pagination */
        if (($this->request->isPost()
            || ($this->request->isGet()
                && array_key_exists('page', $inputParams)
                || array_key_exists('downloadType', $inputParams)))
            && $this->formAutoExtractionAccuracy->isValid(str_replace('-', '/', $inputParams))) {

            $fromDate = $inputParams['fromDate'];
            $toDate = $inputParams['toDate'];

            if ( empty($fromDate) || empty($toDate)) {
                $this->flashMessenger()->addErrorMessage('Date Range required.');
            }
            
            $select = $this->serviceAutoExtractionAccuracy->getAutoExtractionAccuracyData($inputParams);

            $paginator = new Paginator(new Adapter\ArrayAdapter($select));
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($this->config['pagination']['perpage']);
            $paginatorParams = $inputParams;
            $paginatorParams = $this->escapePaginatorParams($paginatorParams);

            if (isset($paginator) && array_key_exists('downloadType', $inputParams) && $inputParams['downloadType'] == ReportMaker::REPORT_FORMAT_XLS) {
                //Report export
                $reportHeader = [
                    'reportName' => 'Auto Extraction Accuracy'
                ];
                $columns = [
                    'dateKeyed' => 'Date Keyed',
                    'reportId' => 'Report #'
                ];
                if ($isLNUser) {
                    $columns['vendorName'] = 'Company Name';
                }
                $this->export(ReportMaker::REPORT_FORMAT_XLS, $paginator, $columns, $reportHeader, ['border centeralign']);
            } else {
                //Report Listing
                $this->view->paginator = $paginator;
                $this->view->paginatorParams = $paginatorParams;
                $this->view->format = ReportMaker::REPORT_FORMAT_XLS;
            }
             
        } else {
            $this->addFormMessages($this->formAutoExtractionAccuracy);
        }
        
        $this->view->isLNUser = $isLNUser;
        $this->view->form = $this->formAutoExtractionAccuracy;
        return $this->view;
    }

    public function autoExtractionAccuracyOverviewAction()
    {
        $this->layout()->setTemplate('layout/minimal');
        $reportId = $this->params()->fromQuery('reportId');

        $inputParams = $this->request->getQuery();
        $inputParams = (array) $inputParams;
        
        $reportInfo = $this->serviceReport->getRelatedInfo($reportId);
        $entryStagesExternal = $this->serviceEntryStage->getExternalNamePairs();

        // get auto-extract data
        $autoExtractionData = $this->serviceReportEntry->getAutoextractionDataDecompressed($reportId);
        $autoExtractionData['entryStage'] = EntryStageService::AUTO_EXTRACT;
        $autoExtractionData['entryData']['Report']['reportId'] = $reportId;

        $reportEntries = $this->serviceAutoExtractionAccuracy->getAccuracyComparisonData($reportId);
        
        $this->serviceAutoExtractionAccuracy->findPassDifference($autoExtractionData, $reportEntries[0], EntryStageService::STAGE_ALL);
        $stage2Difference = $this->serviceAutoExtractionAccuracy->findPassDifference($autoExtractionData, $reportEntries[1], EntryStageService::STAGE_DYNAMIC_VERIFICATION);
        
        $this->serviceAutoExtractionAccuracy->findNAUnitNUmber($autoExtractionData, $reportEntries[0], EntryStageService::STAGE_ALL);
        $this->serviceAutoExtractionAccuracy->findNAUnitNUmber($autoExtractionData, $reportEntries[1], EntryStageService::STAGE_DYNAMIC_VERIFICATION);

        $entryInfo[EntryStageService::AUTO_EXTRACT] = [
            'dateCompleted' => $autoExtractionData['date_created'],
            'username' => '',
            'nameFirst' => '',
            'nameLast' => '',
            'title' => 'Auto Extract',
        ];

        $extractAccuracyDetail = [];
        foreach ($reportEntries as $reportEntry) {
            $userInfo = $this->serviceUser->getIdentityData($reportEntry['userId']);
            $entryInfo[$reportEntry['entryStage']] = [
                'dateCompleted' => $reportEntry['dateUpdated'],
                'username' => $userInfo['username'],
                'nameFirst' => $userInfo['nameFirst'],
                'nameLast' => $userInfo['nameLast'],
                'title' => $entryStagesExternal[$reportEntry['entryStageId']],
                'keyingVendorId' => $userInfo['keyingVendorId']
            ];
        }       

        $total = $stage2Difference['totalCount'];
        $differenceTotal = $stage2Difference['discrepancyCount'];
        $extractAccuracyDetail['noOfFieldsExtracted'] = round((($total-$differenceTotal) / $total) * 100);
        $extractAccuracyDetail['noOfFieldsNotExtracted'] = round(($differenceTotal / $total) * 100);
        $extractAccuracyDetail['totalFields'] = $total;

        $this->view->reportId = $reportId;
        $this->view->reportInfo = $reportInfo;
        $this->view->entryInfo = $entryInfo;
        $this->view->extractAccuracyDetail = $extractAccuracyDetail;
        $this->view->entryData = $this->serviceAutoExtractionAccuracy->comparedData;
        $this->view->format = ReportMaker::REPORT_FORMAT_XLS;

        if (array_key_exists('downloadType', $inputParams)) {
            $this->view->export = true;
            $this->view->setTemplate('admin/auto-extraction-metrics/auto-extraction-accuracy-overview');
            $reportHtml = $this->renderer->render($this->view);
            $downloadFilename = "Change History Report - " . $reportId;

            $this->reportMaker->sendToBrowser(ReportMaker::REPORT_FORMAT_XLS, $downloadFilename, $reportHtml);
        } else {
            $this->view->export = false;
        }
        
        $this->view->isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        return $this->view;
    }

    protected function export($format, $data, $columns, $reportHeader, $style)
    {
        $this->reportMaker->output(
            $format,
            $data,
            $columns,
            [],
            $reportHeader,
            $style
        );
    }
    /* Volume and Productivity Form Page Display */
    public function volumeProductivityReportAction()
    {        
        //getting parameters from Form submit and also from Query param (export link)
        if($this->request->isPost()) {
            $inputParams = (array) $this->request->getPost();
            $this->formVolumeProductivity->setInputFilter($this->formVolumeProductivity->addInputFilters());
            $this->formVolumeProductivity->setData($inputParams);  
        } else {
            $inputParams = (array) $this->request->getQuery();    
        }            
        $reportsData = [];
        if ((!empty($inputParams) && ($this->request->isPost() && $this->formVolumeProductivity->isValid())) || (!empty($inputParams) && array_key_exists('downloadType', $inputParams))) {                                             
                // Below function returns the volume and productivity report data for selected date range and state.                
                $reportsData = $this->getVolumeProductivityReportData($inputParams);
                //print_r($reportsData);exit;
                $this->view->reports = $reportsData;  
                $this->view->reportInfo = $inputParams;
                $this->view->format = ReportMaker::REPORT_FORMAT_XLS;
                $this->view->export = false; 
                if (array_key_exists('downloadType', $inputParams) && $inputParams['downloadType'] == ReportMaker::REPORT_FORMAT_XLS) {
                    if (strcasecmp($inputParams['state'], EntryStageService::STAGE_ALL) != 0) {
                        $stateAbbr = $this->serviceState->getStateAbbrById($inputParams['state']);
                        } else {
                            $stateAbbr = ucfirst($inputParams['state']);
                        }
                        $reportDuration = $this->getReportDownloadDate($inputParams['fromDate'], $inputParams['toDate']);
                        $this->layout()->setTemplate('layout/minimal');                         
                        $this->view->reportHeader = "ecrash - Volume and Productivity Report";  
                        $this->view->stateAbbr  = $stateAbbr;
                        $this->view->export = true;
                        $this->view->setTemplate('admin/auto-extraction-metrics/volume-productivity-report');
                        $reportHtml = $this->renderer->render($this->view);
                        $downloadFilename = "ecrash - Volume and Productivity Report - ".$stateAbbr." - ".$reportDuration;                        
                        $this->reportMaker->sendToBrowser(ReportMaker::REPORT_FORMAT_XLS, $downloadFilename, $reportHtml);
                }                            
            } else {
                $this->addFormMessages($this->formVolumeProductivity);
            }        
        $this->layout()->setTemplate('layout/metrics');
        $this->view->reportDuration = VolumeProductivityReportForm::AUTO_EXTRACTION_REPORT_DURATION;
        $this->view->isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        $this->view->form = $this->formVolumeProductivity;              
        return $this->view;
    }

    /* Volume and Productivity Report data generation based on selected date */
    public function getVolumeProductivityReportData(Array $searchParams = [])
    {   
        set_time_limit(300); //set max execution time to 5 mins
        //select the entry stage data for selected dates
        $reportSelect = $this->serviceReport->getVolumeProductivityReportSelect($searchParams);                                        
        //prepare the selected data for table display in the UI
        $data = $this->serviceReport->getVolumeProductivityReportByState($reportSelect, $searchParams);
        $toDate = date('Y-m-d H:i:s', strtotime($searchParams['toDate'].'+1 day'));

        $totalAutoExtractionReport = 0;

        $totalUniversalManualReports = 0;
        $totalUniversalManualKeyingTime = 0; 
        $totalUniversalAutoReports = 0; 
        $totalUniversalAutoKeyingTime = 0;
        $totalUniversalTime = 0;
        $totalUniversalReports = 0;
        $allPassUniversalManualReports = 0;
        $allPassUniversalManualKeyingTime = 0;
        $allPassUniversalAutoReports = 0;
        $allPassUniversalAutoKeyingTime = 0;
        $allPassUniversalTime = 0;
        $allPassUniversalReports = 0;


        /* Universal-sectional*/
        $totalUniversalSectionalManualReports = 0;
        $totalUniversalSectionalManualKeyingTime = 0; 
        $totalUniversalSectionalAutoReports = 0; 
        $totalUniversalSectionalAutoKeyingTime = 0;
        $totalUniversalSectionalTime = 0;
        $totalUniversalSectionalReports = 0;
        $allPassUniversalSectionalManualReports = 0;
        $allPassUniversalSectionalManualKeyingTime = 0;
        $allPassUniversalSectionalAutoReports = 0;
        $allPassUniversalSectionalAutoKeyingTime = 0;
        $allPassUniversalSectionalTime = 0;
        $allPassUniversalSectionalReports = 0;
        /* Long Form */
        $totalLongFormManualReports = 0;
        $totalLongFormManualKeyingTime = 0; 
        $totalLongFormAutoReports = 0; 
        $totalLongFormAutoKeyingTime = 0;                    
        $totalLongFormTime = 0;
        $totalLongFormReports = 0;
        $allPassLongFormManualReports = 0;
        $allPassLongFormManualKeyingTime = 0;
        $allPassLongFormAutoReports = 0;
        $allPassLongFormAutoKeyingTime = 0;
        $allPassLongFormTime = 0;
        $allPassLongFormReports = 0;
        //categorize the report count and report processing time by universal, universalSectional, Longform states
        foreach ($data as $key => $value) 
        {
            if(is_object($value)) {
                $value = (array) $value;
            }
            $pass2Date = $value['pass2_start_date'];
            $validPass2Report = ($pass2Date > $toDate);
            if(in_array($value['state_abbr'], $this->universalStates))
            {
                if($value['auto_extraction'] == 'Yes'){
                    $totalAutoExtractionReport++;
                }

                if($value['manually_keyed']=="Auto")
                {
                    if($value['pass1_duration']!="" && $value['pass2_duration']!="" && (!$validPass2Report))
                    {
                    $totalUniversalAutoKeyingTime += $value['total_duration'];
                    $totalUniversalAutoReports++; 
                    }
                    $allPassUniversalAutoKeyingTime += $value['total_duration'];
                    $allPassUniversalAutoReports++;                          
                }else
                {
                    if($value['pass1_duration']!="" && $value['pass2_duration']!="" && (!$validPass2Report))
                    {
                    $totalUniversalManualKeyingTime += $value['total_duration'];
                    $totalUniversalManualReports++;
                    } 
                    $allPassUniversalManualKeyingTime += $value['total_duration'];
                    $allPassUniversalManualReports++;   
                }
                if($value['pass1_duration']!="" && $value['pass2_duration']!="" && (!$validPass2Report))
                {
                $totalUniversalTime += $value['total_duration']; 
                $totalUniversalReports++; 
                }
                $allPassUniversalTime += $value['total_duration']; 
                $allPassUniversalReports++;
                
            } 
            else if(in_array($value['state_abbr'], $this->universalSectionalStates))
            {
                if($value['auto_extraction'] == 'Yes'){
                    $totalAutoExtractionReport++;
                }

                if($value['manually_keyed']=="Auto")
                {
                    if($value['pass1_duration']!="" && $value['pass2_duration']!="" && (!$validPass2Report))
                    {
                    $totalUniversalSectionalAutoKeyingTime += $value['total_duration']; 
                    $totalUniversalSectionalAutoReports++;
                    }
                    $allPassUniversalSectionalAutoKeyingTime += $value['total_duration'];
                    $allPassUniversalSectionalAutoReports++;                                             
                }else
                {
                    if($value['pass1_duration']!="" && $value['pass2_duration']!="" && (!$validPass2Report))
                    {
                    $totalUniversalSectionalManualKeyingTime += $value['total_duration']; 
                    $totalUniversalSectionalManualReports++; 
                    }
                    $allPassUniversalSectionalManualKeyingTime += $value['total_duration'];
                    $allPassUniversalSectionalManualReports++;
                }
                    if($value['pass1_duration']!="" && $value['pass2_duration']!="" && (!$validPass2Report))
                {
                    $totalUniversalSectionalTime += $value['total_duration']; 
                    $totalUniversalSectionalReports++;
                }
                $allPassUniversalSectionalTime += $value['total_duration']; 
                $allPassUniversalSectionalReports++;
            } 
            else if(in_array($value['state_abbr'], $this->longFormStates))
            {
                if($value['auto_extraction'] == 'Yes'){
                    $totalAutoExtractionReport++;
                }

                if($value['manually_keyed']=="Auto")
                {
                    if($value['pass1_duration']!="" && $value['pass2_duration']!="" && (!$validPass2Report))
                    {
                    $totalLongFormAutoKeyingTime += $value['total_duration']; 
                    $totalLongFormAutoReports++; 
                    }
                    $allPassLongFormAutoKeyingTime += $value['total_duration'];
                    $allPassLongFormAutoReports++;   
                                            
                }else
                {
                    if($value['pass1_duration']!="" && $value['pass2_duration']!="" && (!$validPass2Report))
                    {
                    $totalLongFormManualKeyingTime += $value['total_duration']; 
                    $totalLongFormManualReports++;  
                    }
                    $allPassLongFormManualKeyingTime += $value['total_duration'];
                    $allPassLongFormManualReports++;                    
                }
                if($value['pass1_duration']!="" && $value['pass2_duration']!="" && (!$validPass2Report))
                {
                    $totalLongFormTime += $value['total_duration']; 
                    $totalLongFormReports++;
                }
                $allPassLongFormTime += $value['total_duration']; 
                $allPassLongFormReports++;
            }                                           
            
            
        }
        //converting time format and preparing final array that is used to process in UI
        $reportsData = [
            'manualkeying' => [
                'time'=> number_format($totalUniversalManualKeyingTime+$totalUniversalSectionalManualKeyingTime+$totalLongFormManualKeyingTime, 2, '.', ','), 'total'=> number_format($totalUniversalManualReports+$totalUniversalSectionalManualReports+$totalLongFormManualReports),
                'allPasstime'=> number_format($allPassUniversalManualKeyingTime+$allPassUniversalSectionalManualKeyingTime+$allPassLongFormManualKeyingTime, 2, '.', ','), 'allPasstotal'=> number_format($allPassUniversalManualReports+$allPassUniversalSectionalManualReports+$allPassLongFormManualReports)
                    ],
            'autokeying' => [
                'time'=> number_format($totalUniversalAutoKeyingTime+$totalUniversalSectionalAutoKeyingTime+$totalLongFormAutoKeyingTime, 2, '.', ','), 'total'=> number_format($totalUniversalAutoReports+$totalUniversalSectionalAutoReports+$totalLongFormAutoReports),
                'allPasstime'=> number_format($allPassUniversalAutoKeyingTime+$allPassUniversalSectionalAutoKeyingTime+$allPassLongFormAutoKeyingTime, 2, '.', ','), 'allPasstotal'=> number_format($allPassUniversalAutoReports+$allPassUniversalSectionalAutoReports+$allPassLongFormAutoReports)
                    ],
            'universal' => ['manual' => [
                'time'=> number_format($totalUniversalManualKeyingTime, 2, '.', ','), 'total'=> number_format($totalUniversalManualReports), 'actual' => $this->actualTimeCalculate($totalUniversalManualKeyingTime, $totalUniversalManualReports), 'target' => self::MANUAL_KEYING_UNIVERSAL_REPORT_TARGET],
                            'auto' => [
                'time'=> number_format($totalUniversalAutoKeyingTime, 2, '.', ','), 'total'=> number_format($totalUniversalAutoReports), 'actual' => $this->actualTimeCalculate($totalUniversalAutoKeyingTime, $totalUniversalAutoReports), 'target' => self::AUTO_KEYING_UNIVERSAL_REPORT_TARGET],
                            'both' => [
                'time'=> number_format($totalUniversalTime, 2, '.', ','), 'total'=> number_format($totalUniversalReports),
                'allPasstime'=> number_format($allPassUniversalTime, 2, '.', ','), 'allPasstotal'=> number_format($allPassUniversalReports)
                            ],
                            'allPassmanual' => [
                'time'=> number_format($allPassUniversalManualKeyingTime, 2, '.', ','), 'total'=> number_format($allPassUniversalManualReports), 'actual' => $this->actualTimeCalculate($allPassUniversalManualKeyingTime, $allPassUniversalManualReports)],
                            'allPassauto' => [
                'time'=> number_format($allPassUniversalAutoKeyingTime, 2, '.', ','), 'total'=> number_format($allPassUniversalAutoReports), 'actual' => $this->actualTimeCalculate($allPassUniversalAutoKeyingTime, $allPassUniversalAutoReports)],
                        ],
            'universal-sectional' => ['manual' => [
                'time' => number_format($totalUniversalSectionalManualKeyingTime, 2, '.', ','), 'total'=> number_format($totalUniversalSectionalManualReports), 'actual' => $this->actualTimeCalculate($totalUniversalSectionalManualKeyingTime, $totalUniversalSectionalManualReports), 'target' => self::MANUAL_KEYING_UNIVERSAL_SECTIONAL_REPORT_TARGET],
                            'auto' => [
                'time' => number_format($totalUniversalSectionalAutoKeyingTime, 2, '.', ','), 'total'=> number_format($totalUniversalSectionalAutoReports), 'actual' => $this->actualTimeCalculate($totalUniversalSectionalAutoKeyingTime, $totalUniversalSectionalAutoReports), 'target' => self::AUTO_KEYING_UNIVERSAL_SECTIONAL_REPORT_TARGET],
                            'both' => [
                'time'=> number_format($totalUniversalSectionalTime, 2, '.', ','), 'total'=> number_format($totalUniversalSectionalReports),
                'allPasstime'=> number_format($allPassUniversalSectionalTime, 2, '.', ','), 'allPasstotal'=> number_format($allPassUniversalSectionalReports)
                            ],
                            'allPassmanual' => [
                'time' => number_format($allPassUniversalSectionalManualKeyingTime, 2, '.', ','), 'total'=> number_format($allPassUniversalSectionalManualReports), 'actual' => $this->actualTimeCalculate($allPassUniversalSectionalManualKeyingTime, $allPassUniversalSectionalManualReports)],
                            'allPassauto' => [
                'time' => number_format($allPassUniversalSectionalAutoKeyingTime, 2, '.', ','), 'total'=> number_format($allPassUniversalSectionalAutoReports), 'actual' => $this->actualTimeCalculate($allPassUniversalSectionalAutoKeyingTime, $allPassUniversalSectionalAutoReports)],
                        ],
            'longform' => ['manual' => [
                'time' => number_format($totalLongFormManualKeyingTime, 2, '.', ','), 'total'=> number_format($totalLongFormManualReports), 'actual' => $this->actualTimeCalculate($totalLongFormManualKeyingTime, $totalLongFormManualReports), 'target' => self::MANUAL_KEYING_LONGFORM_REPORT_TARGET],
                            'auto' => [
                'time' => number_format($totalLongFormAutoKeyingTime, 2, '.', ','), 'total'=> number_format($totalLongFormAutoReports), 'actual' => $this->actualTimeCalculate($totalLongFormAutoKeyingTime, $totalLongFormAutoReports), 'target' => self::AUTO_KEYING_LONGFORM_REPORT_TARGET],
                            'both' => [
                'time'=> number_format($totalLongFormTime, 2, '.', ','), 'total'=> number_format($totalLongFormReports),
                'allPasstime'=> number_format($allPassLongFormTime, 2, '.', ','), 'allPasstotal'=> number_format($allPassLongFormReports)
                            ],
                            'allPassmanual' => [
                'time' => number_format($allPassLongFormManualKeyingTime, 2, '.', ','), 'total'=> number_format($allPassLongFormManualReports), 'actual' => $this->actualTimeCalculate($allPassLongFormManualKeyingTime, $allPassLongFormManualReports)],
                            'allPassauto' => [
                'time' => number_format($allPassLongFormAutoKeyingTime, 2, '.', ','), 'total'=> number_format($allPassLongFormAutoReports), 'actual' => $this->actualTimeCalculate($allPassLongFormAutoKeyingTime, $allPassLongFormAutoReports)],
                    ],
        ];


        $manualautoActual = [
            'manualauto' => [
            'universal' => ['actual' => $this->actualPercentCalculate($reportsData['universal']['manual']['actual'], $reportsData['universal']['auto']['actual']), 'target' => self::ALL_UNIVERSAL_REPORT_TARGET],
            'universal-sectional' => ['actual' => $this->actualPercentCalculate($reportsData['universal-sectional']['manual']['actual'], $reportsData['universal-sectional']['auto']['actual']), 'target' => self::ALL_UNIVERSAL_SECTIONAL_REPORT_TARGET],
            'longform' => ['actual' => $this->actualPercentCalculate($reportsData['longform']['manual']['actual'], $reportsData['longform']['auto']['actual']), 'target' => self::ALL_LONGFORM_REPORT_TARGET]
        ]];               
        $efficiency = [
            'efficiency' => [
                'manual' => [
                    'universal' => $this->actualPercentCalculate(self::MANUAL_KEYING_UNIVERSAL_REPORT_TARGET, $reportsData['universal']['manual']['actual']),
                    'universal-sectional' => $this->actualPercentCalculate(self::MANUAL_KEYING_UNIVERSAL_SECTIONAL_REPORT_TARGET, $reportsData['universal-sectional']['manual']['actual']),
                    'longform' =>$this->actualPercentCalculate(self::MANUAL_KEYING_LONGFORM_REPORT_TARGET, $reportsData['longform']['manual']['actual'])
                ],
                'auto' => [
                    'universal' => $this->actualPercentCalculate(self::AUTO_KEYING_UNIVERSAL_REPORT_TARGET, $reportsData['universal']['auto']['actual']),
                    'universal-sectional' => $this->actualPercentCalculate(self::AUTO_KEYING_UNIVERSAL_SECTIONAL_REPORT_TARGET, $reportsData['universal-sectional']['auto']['actual']),
                    'longform' =>$this->actualPercentCalculate(self::AUTO_KEYING_LONGFORM_REPORT_TARGET, $reportsData['longform']['auto']['actual'])
                ],
                'both' => [
                    'universal' => ($manualautoActual['manualauto']['universal']['actual'] - self::ALL_UNIVERSAL_REPORT_TARGET),
                    'universal-sectional' => ($manualautoActual['manualauto']['universal-sectional']['actual'] - self::ALL_UNIVERSAL_SECTIONAL_REPORT_TARGET),
                    'longform' => ($manualautoActual['manualauto']['longform']['actual'] - self::ALL_LONGFORM_REPORT_TARGET)
                ]

            ]
        ];

        $percentage = ['percentage' => 
                [
                'manual' => [
                    'universal' => $this->manualAutoPercent($totalUniversalManualReports, $totalUniversalAutoReports),
                    'universal-sectional' => $this->manualAutoPercent($totalUniversalSectionalManualReports, $totalUniversalSectionalAutoReports),
                    'longform' => $this->manualAutoPercent($totalLongFormManualReports, $totalLongFormAutoReports)
                ],
                'auto' => [
                    'universal' => $this->manualAutoPercent($totalUniversalAutoReports, $totalUniversalManualReports),
                    'universal-sectional' => $this->manualAutoPercent($totalUniversalSectionalAutoReports, $totalUniversalSectionalManualReports),
                    'longform' => $this->manualAutoPercent($totalLongFormAutoReports, $totalLongFormManualReports)
                ],
                'manualkeying' => $this->manualAutoPercent(($totalUniversalManualReports+$totalUniversalSectionalManualReports+$totalLongFormManualReports), ($totalUniversalAutoReports+$totalUniversalSectionalAutoReports+$totalLongFormAutoReports)),
                'autokeying' => $this->manualAutoPercent(($totalUniversalAutoReports+$totalUniversalSectionalAutoReports+$totalLongFormAutoReports), ($totalUniversalManualReports+$totalUniversalSectionalManualReports+$totalLongFormManualReports))
            ]
        ];

        $autoExtractionOnly = ['auto_extraction_total' => $totalAutoExtractionReport,
        'report_count_total' => count($data)];

        $reportsData = array_merge($reportsData, $efficiency, $manualautoActual, $percentage, $autoExtractionOnly);  
        return $reportsData;       
           
    }

    
    public function actualTimeCalculate($time, $count)
    {
        if($count==0){      
            return 0;
        }
        $time = $time/$count;
        $actualTime = round($time, 2);
        return $actualTime;
    }

    public function actualPercentCalculate($manual, $auto)
    {
        if($auto==0){        
            return 0;
        }
        $percent = (($manual-$auto)/$auto)*100;
        $actualPercent = $percent;
        return round($actualPercent);
    }    

    public function manualAutoPercent($manual, $auto)
    {
       if($manual==0){
            return 0;
        }
        $percent = $manual/($manual+$auto);
        $actualPercent = $percent*100;
        return round($actualPercent);
    }

}
