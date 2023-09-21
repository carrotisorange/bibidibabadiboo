<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */
namespace Admin\Controller;

use Zend\Log\Logger;
use Zend\Session\Container;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter;
use Zend\Validator\Date;

use Base\Controller\BaseController;
use Base\Service\ReportService;
use Base\View\Helper\DataRenderer\ReportMaker;
use Base\Service\AgencyService;
use Base\Service\ReportStatusService;
use Base\Service\UserAccuracyService;
use Base\Service\UserAccuracyInvalidService;
use Base\Service\UserService;
use Base\Service\ReportEntryService;
use Base\Service\VinStatusService;
use Base\Service\WorkTypeService;
use Base\Service\KeyingVendorService;
use Admin\Form\Metric\VinStatusByOperatorForm;
use Admin\Form\Metric\ImageStatusByAgencyForm;
use Admin\Form\Metric\OperatorByAgencyStatsForm;
use Admin\Form\Metric\OperatorKeyingAccuracyForm;
use Admin\Form\Metric\OperatorSummaryStatsForm;
use Admin\Form\Metric\VinStatusSummaryForm;
use Admin\Form\Metric\SlaStatusSummaryForm;
use Admin\Validator\CheckKeyingVendorId;

use DateTime;
use DateTimeZone;

class MetricsController extends BaseController
{
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    private $logger;
    
    /**
     * @var Admin\Form\Metric\ImageStatusByAgencyForm
     */
    private $formImageStatusByAgency;
    
    /**
     * @var Admin\Form\Metric\OperatorSummaryStatsForm
     */
    private $formOperatorSummaryStats;
    
    /**
     * @var Admin\Form\Metric\OperatorByAgencyStatsForm
     */
    private $formOperatorByAgencyStats;
    
    /**
     * @var Admin\Form\Metric\VinStatusSummaryForm
     */
    private $formVinStatusSummary;
    
    /**
     * @var Admin\Form\Metric\VinStatusByOperatorForm
     */
    private $formVinStatusByOperator;
	
    /**
     * @var Admin\Form\Metric\SlaStatusSummaryForm
     */
    private $formSlaStatusSummary;
    
    /**
     * @var Base\View\Helper\DataRenderer\ReportMaker
     */
    private $reportMaker;
    
    /**
     * @var Zend\Session\Container
     */
    private $session;
    
    /**
     * @var Base\Service\AgencyService
     */
    private $serviceAgency;
    
    /**
     * @var Base\Service\ReportStatusService
     */
    private $serviceReportStatus;
    
    /**
     * @var Zend\Validator\Date
     */
    private $date;
    
    /**
     * @var Admin\Form\Metric\OperatorKeyingAccuracyForm
     */
    private $formOperatorKeyingAccuracy;
    
    /**
     * @var Base\Service\UserAccuracyService
     */
    private $serviceUserAccuracy;
    
    /**
     * @var Base\Service\UserAccuracyInvalidService
     */
    private $serviceUserInvalidAccuracy;
    
    /**
     * @var Base\Service\UserService
     */
    private $serviceUserService;
    
    /**
     * @var Base\Service\VinStatusService
     */
    private $serviceVinStatusService;
	
    /**
     * @var Base\Service\WorkTypeService
     */
    protected $serviceWorkType;
    
    /**
     * @var Base\Service\KeyingVendorService
     */
    private $serviceKeyingVendor;
    
    /**
     * Constructor will be invoked from the MetricsControllerFactory
     * @param array  $config        Application configuration
     * @param object $logger        Zend\Log\Logger
     * @param object $formImageStatusByAgency   Admin\Form\Metric\ImageStatusByAgencyForm
     * @param object $formOperatorByAgencyStats   Admin\Form\Metric\OperatorByAgencyStatsForm
     * @param object $formOperatorKeyingAccuracy   Admin\Form\Metric\OperatorKeyingAccuracyForm
     * @param object $formOperatorSummaryStats   Admin\Form\Metric\OperatorSummaryStatsForm
     * @param object $formVinStatusSummary   Admin\Form\Metric\VinStatusSummaryForm
     * @param object $formVinStatusByOperator   Admin\Form\Metric\VinStatusByOperatorForm
     * @param object $formSlaStatusSummary   Admin\Form\Metric\SlaStatusSummaryForm
     * @param object $serviceReport   Base\Service\ReportService
     * @param object $serviceAgency   Base\Service\AgencyService
     * @param object $serviceReportStatus   Base\Service\ReportStatusService
     * @param object $date   Zend\Validator\Date
     * @param object $serviceUserAccuracy   Base\Service\UserAccuracyService
     * @param object $serviceUserAccuracyInvalid   Base\Service\UserAccuracyInvalidService
     * @param object $serviceUser   Base\Service\UserService
     * @param object $serviceReportEntry   Base\Service\ReportEntryService
     * @param object $serviceVinStatus   Base\Service\VinStatusService
     * @param object $serviceVinStatus   Base\Service\WorkTypeService
     * @param object $serviceKeyingVendor   Base\Service\KeyingVendorService
     */
    public function __construct(
        Array $config,
        Logger $logger,
        Container $session,
        ImageStatusByAgencyForm $formImageStatusByAgency,
        OperatorByAgencyStatsForm $formOperatorByAgencyStats,
        OperatorKeyingAccuracyForm $formOperatorKeyingAccuracy,
        OperatorSummaryStatsForm $formOperatorSummaryStats,
        VinStatusSummaryForm $formVinStatusSummary,
        VinStatusByOperatorForm $formVinStatusByOperator,
	SlaStatusSummaryForm $formSlaStatusSummary,
        ReportService $serviceReport,
        ReportMaker $reportMaker,
        AgencyService $serviceAgency,
        ReportStatusService $serviceReportStatus,
        Date $date,
        UserAccuracyService $serviceUserAccuracy,
        UserAccuracyInvalidService $serviceUserAccuracyInvalid,
        UserService $serviceUser,
        ReportEntryService $serviceReportEntry,
        VinStatusService $serviceVinStatus,
	WorkTypeService $serviceWorkType,
        KeyingVendorService $serviceKeyingVendor)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->formImageStatusByAgency = $formImageStatusByAgency;
        $this->formOperatorByAgencyStats = $formOperatorByAgencyStats;
        $this->formOperatorKeyingAccuracy = $formOperatorKeyingAccuracy;
        $this->serviceReport = $serviceReport;
        $this->reportMaker = $reportMaker;
        $this->session = $session;
        $this->serviceAgency = $serviceAgency;
        $this->serviceReportStatus = $serviceReportStatus;
        $this->date = $date;
        $this->serviceUserAccuracy = $serviceUserAccuracy;
        $this->serviceUserAccuracyInvalid = $serviceUserAccuracyInvalid;
        $this->serviceUser = $serviceUser;
        $this->serviceReportEntry = $serviceReportEntry;
        $this->formOperatorSummaryStats = $formOperatorSummaryStats;
        $this->formVinStatusSummary = $formVinStatusSummary;
        $this->serviceVinStatus = $serviceVinStatus;
	$this->serviceWorkType = $serviceWorkType;
        $this->formVinStatusByOperator = $formVinStatusByOperator;
    	$this->formSlaStatusSummary = $formSlaStatusSummary;
        $this->serviceKeyingVendor = $serviceKeyingVendor;
        
        parent::__construct();
    }
    
    public function imageStatusByAgencyAction()
    {
        $this->layout()->setTemplate('layout/metrics');
        
        /* Start get query params during pagination */
        $page = $this->params()->fromQuery('page');
        $queryParams = $this->request->getQuery();
        $queryParams = (array) $queryParams;
        
        if (!empty($queryParams)) {
            $inputParams = $queryParams;
        } else {
            $inputParams = (array) $this->request->getPost();
        }
        
        $stateId = ($this->request->isPost()) ? $inputParams['state'] : $this->params()->fromQuery('state');
        $this->formImageStatusByAgency->setInputFilter($this->formImageStatusByAgency->addInputFilters());
        $this->view->form = $this->formImageStatusByAgency;
        $agencyList = $this->serviceAgency->getAllByState($stateId);
        $agencyOptions['all'] = 'All';

        foreach ($agencyList as $agencyOption) {
            $agencyOptions[$agencyOption['agency_id']] = $agencyOption['name'];
        }
        
        $this->formImageStatusByAgency->get('agency')->setValueOptions($agencyOptions);
        /* End get query params during pagination */
        $this->formImageStatusByAgency->setData($inputParams);
        
        if (($this->request->isPost()
            || ($this->request->isGet()
                && array_key_exists('page', $inputParams)
                || array_key_exists('downloadType', $inputParams)))
            && $this->formImageStatusByAgency->isValid(str_replace('-', '/', $inputParams))) {
            
            $agency = $inputParams['agency'];
            $result = $this->serviceReportStatus->getReportCount(
                $inputParams['keyingVendorId'],
                $stateId,
                $agency
            );
            $result = array_map(
                [$this , 'agregateStatuses'],
                $result
            );
            
            if (strcasecmp($agency, 'all')) {
                // If a specific agency is used we don't want the full list
                $agencyList = [$agencyList[$agency]];
            }
            
            $result = $this->fillBlankAgencies($agencyList, $result);
            $totals = $this->getAgencyStatusTotals($result);
            $paginator = new Paginator(new Adapter\ArrayAdapter($result));
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($this->config['pagination']['perpage']);
            $paginatorParams = $inputParams;
            $paginatorParams = $this->escapePaginatorParams($paginatorParams);
            $columns = $this->getImageStatusByAgencyColumnMap();
            if (isset($result) && !empty($inputParams['downloadType']) && $inputParams['downloadType'] == ReportMaker::REPORT_FORMAT_XLS) {
                //Report export
                $reportHeader = [
                    'reportName' => 'Image Status by Agency'
                ];
                $this->export(ReportMaker::REPORT_FORMAT_XLS, $paginator, $columns, $reportHeader, ['border centeralign']);
            } else {
                //Report Listing
                $this->view->paginator = $paginator;
                $this->view->paginatorParams = $paginatorParams;
                $this->view->format = ReportMaker::REPORT_FORMAT_XLS;
                $this->view->totals = $totals;
                $this->view->result = $result;
            }
        } else {
            $this->addFormMessages($this->formImageStatusByAgency);
        }
        
        $this->view->isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        return $this->view;
    }

    protected function getImageStatusByAgencyColumnMap()
    {
        return [
            'name' => 'Agency Name',
            'available' => 'AVAILABLE',
            'bad' => 'BAD IMAGE',
            'discarded' => 'DISCARDED',
            'reordered' => 'DISCARDED Re-Ordered=Y',
            'dead' => 'DISCARDED Re-Ordered=N',
            'inProgress' => 'PROCESSING',
            'complete' => 'COMPLETED',
        ];
    }
        
    protected function fillBlankAgencies($agencyList, $result)
    {
        if (!is_array($result)) {
            $result = [];
        }
        $columns = array_keys($this->getImageStatusByAgencyColumnMap());
        $blankRow = array_fill_keys($columns, 0);
        foreach ($agencyList as $agency) {
            if (!isset($result[$agency['name']])) {
                $blankRow['name'] = $agency['name'];
                $result[$agency['name']] = $blankRow;
            }
        }
        return $result;
    }

    protected function getAgencyStatusTotals($agencyData)
    {
        $totals = [];
        foreach ($agencyData as $agency) {
            foreach ($agency as $status => $count) {
                if (is_numeric($count)) {
                    $existingCount = isset($totals[$status]) ? $totals[$status] : 0;
                    $totals[$status] = $existingCount + $count;
                }
            }
        }
        return $totals;
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

    /**
     * Gets the Agencies for a particular state - returns a JSON string - no viewer.
     */
    public function getAgenciesByStateAction()
    {
        $this->view->setTerminal(true);
        $returnAgencies = [];
        $haveError = [];
        $stateId = $this->params()->fromQuery('stateId');
        if (!isset($haveError['Error'])) {
            $agencyList = $this->serviceAgency->getAllByState($stateId);
            if (!empty($agencyList)) {
                foreach ($agencyList as $agency) {
                    $returnAgencies[$agency['agency_id']] = $agency['name'];
                }
            }
            return $this->json->setVariables($returnAgencies);
        } else {
            return $this->json->setVariables($haveError);
        }
    }

    /**
     * Get counts based on operator by agency and work type
     */
    public function operatorByAgencyStatsAction()
    {
        $this->layout()->setTemplate('layout/metrics');
        
        $isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        
        $this->formOperatorByAgencyStats->setInputFilter($this->formOperatorByAgencyStats->addInputFilters());
       
        /* Start get query params during pagination */
        $page = $this->params()->fromQuery('page');
        $queryParams = $this->request->getQuery();
        $queryParams = (array) $queryParams;

        if (!empty($queryParams)) {
            $inputParams = $queryParams;
        } else {
            $inputParams = (array) $this->request->getPost();
        }

        if (!empty($inputParams['fromDate'])) {
            $inputParams['fromDate'] = $this->convertDateSeperator($inputParams['fromDate']);
        }

        if (!empty($inputParams['toDate'])) {
            $inputParams['toDate'] = $this->convertDateSeperator($inputParams['toDate']);
        }
        
        /* End get query params during pagination */
        $this->formOperatorByAgencyStats->setData($inputParams);

        if (($this->request->isPost()
            || ($this->request->isGet()
                && array_key_exists('page', $inputParams)
                || array_key_exists('downloadType', $inputParams)))
            && $this->formOperatorByAgencyStats->isValid(str_replace('-', '/', $inputParams))) {
            
            $data = $this->serviceReport->getCountWorkTypeByUsersName(
                $inputParams['keyingVendorId'],
                $isLNUser,    
                $inputParams['fromDate'],
                $inputParams['toDate'],
                $inputParams['firstName'],
                $inputParams['lastName']
            );
            $paginator = new Paginator(new Adapter\ArrayAdapter($data));
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($this->config['pagination']['perpage']);
            $paginatorParams = $this->escapePaginatorParams($inputParams);
            
            $columns = [
                'username' => 'Username',
                'name_first' => 'First Name',
                'name_last' => 'Last Name',
                'agency_name' => 'Agency',
                'work_type_name' => 'Work Type',
                'count_keyed' => 'Count Keyed',
                'average_elements' => 'Keyed Elements / Reports'
            ];
            
            if ($isLNUser) {
                $columns['vendor_name'] = 'Company Name';
            }
            
            if (isset($data) && array_key_exists('downloadType', $inputParams) && $inputParams['downloadType'] == ReportMaker::REPORT_FORMAT_XLS) {
                //Report export
                $reportHeader = [
                    'reportName' => 'Operator by Agency Stats'
                ];
                $this->export(ReportMaker::REPORT_FORMAT_XLS, $data, $columns, $reportHeader, ['border centeralign']);
            } else {
                //Report Listing
                $this->view->paginator = $paginator;
                $this->view->paginatorParams = $paginatorParams;
                $this->view->columns = $columns;
                $this->view->format = ReportMaker::REPORT_FORMAT_XLS;
            }
        } else {
            $this->addFormMessages($this->formOperatorByAgencyStats);
        }
        $this->view->isLNUser = $isLNUser;
        $this->view->form = $this->formOperatorByAgencyStats;

        return $this->view;
    }

    /**
     * Searches for keying accuracy statistics based on date and operator
    */
    public function operatorKeyingAccuracyAction()
    {
        $this->layout()->setTemplate('layout/metrics');
        
        $isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        
        $this->formOperatorKeyingAccuracy->setInputFilter($this->formOperatorKeyingAccuracy->addInputFilters());
        /* Start get query params during pagination */
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

        $this->formOperatorKeyingAccuracy->setData($inputParams);
        /* End get query params during pagination */
        if (($this->request->isPost()
            || ($this->request->isGet()
                && array_key_exists('page', $inputParams)
                || array_key_exists('downloadType', $inputParams)))
            && $this->formOperatorKeyingAccuracy->isValid(str_replace('-', '/', $inputParams))) {

            $nameUser = $inputParams['userName'];
            $nameFirst = $inputParams['firstName'];
            $nameLast = $inputParams['lastName'];
            $fromDate = $inputParams['fromDate'];
            $toDate = $inputParams['toDate'];
            $formState = $inputParams['formState'];
            $formId = $inputParams['formId'];
            $formAgencyId = $inputParams['formAgencyId'];
            $keyingVendorId = $inputParams['keyingVendorId'];
            $userIds = [];

            if (empty($nameUser) && (empty($nameFirst) || empty($nameLast))) {
                $this->flashMessenger()->addErrorMessage(
                    'UserId or First and Last Name is required.'
                );
            } elseif (!empty($nameFirst) && !empty($nameLast)) {
                $userIds = $this->serviceUser->getUserIdsByName($keyingVendorId, $nameFirst, $nameLast);
            } elseif (!empty($nameUser)) {
                $userId = $this->serviceUser->getUserIdByUserName($nameUser, $keyingVendorId);
                $userIds = [$userId];
            }

            $userIds_to_pass = [];
            foreach ($userIds as $userId) {
                $userIds_to_pass[] = $userId;
            }

            if (count($userIds_to_pass) > 0) {
                $select = $this->serviceUserAccuracy->getSelectAllByUserIds(
                    $userIds_to_pass,
                    $fromDate,
                    $toDate,
                    $formState,
                    $formId,
                    $formAgencyId);

                $countKeyed = $this->serviceUserAccuracy->getCountKeyed(
                    $userIds_to_pass,
                    $fromDate,
                    $toDate,
                    $formState,
                    $formId,
                    $formAgencyId);

                $countInvalid = $this->serviceUserAccuracyInvalid->getCountInvalid(
                    $userIds_to_pass,
                    $fromDate,
                    $toDate,
                    $formState,
                    $formId,
                    $formAgencyId);

                $userAccuracyInvalidTable = $this->serviceUserAccuracyInvalid;

                $paginator = new Paginator(new Adapter\ArrayAdapter($select));
                $paginator->setCurrentPageNumber($page);
                $paginator->setItemCountPerPage($this->config['pagination']['perpage']);
                $paginatorParams = $inputParams;
                $paginatorParams = $this->escapePaginatorParams($paginatorParams);

                if (isset($paginator) && array_key_exists('downloadType', $inputParams) && $inputParams['downloadType'] == ReportMaker::REPORT_FORMAT_XLS) {
                    //Report export
                    $reportHeader = [
                        'reportName' => 'Operator Keying Accuracy'
                    ];
                    $columns = [
                        'dateKeyed' => 'Date Keyed',
                        'formState' => 'State',
                        'agencyName' => 'Agency Name',
                        'formName' => 'Form Name',
                        'reportId' => 'Report #',
                        'countKeyed' => '# Fields Keyed',
                        'countInvalid' => '# Incorrect'
                    ];
                    
                    if ($isLNUser) {
                        $columns['vendorName'] = 'Company Name';
                    }
                    $this->export(ReportMaker::REPORT_FORMAT_XLS, $paginator, $columns, $reportHeader, ['border centeralign']);
                } else {
                    //Report Listing
                    $this->view->paginator = $paginator;
                    $this->view->paginatorParams = $paginatorParams;
                    $this->view->countInvalid = $countInvalid;
                    $this->view->countKeyed = $countKeyed;
                    $this->view->userAccuracyScore = $this->serviceUserAccuracy->calculateAccuracyScore(
                        $countInvalid,
                        $countKeyed
                    );
                    $this->view->userAccuracyInvalidTable = $userAccuracyInvalidTable;
                    $this->view->format = ReportMaker::REPORT_FORMAT_XLS;
                }
            } 
        } else {
            $this->addFormMessages($this->formOperatorKeyingAccuracy);
        }
        
        $this->view->isLNUser = $isLNUser;
        $this->view->form = $this->formOperatorKeyingAccuracy;

        return $this->view;
    }

    public function operatorSummaryStatsAction()
    {
        $this->layout()->setTemplate('layout/metrics');
        
        $isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        
        $this->formOperatorSummaryStats->setInputFilter($this->formOperatorSummaryStats->addInputFilters());
        /* Start get query params during pagination */
        $page = $this->params()->fromQuery('page');
        $queryParams = $this->request->getQuery();
        $queryParams = (array) $queryParams;
        
        if (!empty($queryParams)) {
            $inputParams = $queryParams;
        } else {
            $inputParams = (array) $this->request->getPost();
        }

        if (!empty($inputParams['fromDate'])) {
            $inputParams['fromDate'] = $this->convertDateSeperator($inputParams['fromDate']);
        }
        
        if (!empty($inputParams['toDate'])) {
            $inputParams['toDate'] = $this->convertDateSeperator($inputParams['toDate']);
        }
        /* End get query params during pagination */
        $this->formOperatorSummaryStats->setData($inputParams);

        if (($this->request->isPost()
            || ($this->request->isGet()
                && array_key_exists('page', $inputParams)
                || array_key_exists('downloadType', $inputParams)))
            && $this->formOperatorSummaryStats->isValid(str_replace('-', '/', $inputParams))) {

            $data = $this->serviceReportEntry->getEntryAvgStatistics(
                $inputParams['fromDate'],
                $inputParams['toDate'],
                $inputParams['keyingVendorId'],
                $inputParams['firstName'],
                $inputParams['lastName']
            );

            $paginator = new Paginator(new Adapter\ArrayAdapter($data));
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($this->config['pagination']['perpage']);
            $paginatorParams = $inputParams;
            $paginatorParams = $this->escapePaginatorParams($paginatorParams);

            $columns = [
                'username' => 'Username',
                'name_last' => 'Last Name',
                'name_first' => 'First Name',
                'keyedTotal' => 'Total Keyed',
                'keyedPerHourAvg' => 'Avg Keyed/Hour',
                'keyedFieldsAvg' => 'Avg Fields Keyed',
            ];
            
            if ($isLNUser) {
                $columns['vendor_name'] = 'Company Name';
            }

            if (isset($data) && array_key_exists('downloadType', $inputParams) && $inputParams['downloadType'] == ReportMaker::REPORT_FORMAT_XLS) {
                //Report export
                $reportHeader = [
                    'reportName' => 'Operator Summary Stats'
                ];
                $this->export(ReportMaker::REPORT_FORMAT_XLS, $data, $columns, $reportHeader, ['border centeralign']);
            } else {
                //Report Listing
                $this->view->paginator = $paginator;
                $this->view->paginatorParams = $paginatorParams;
                $this->view->format = ReportMaker::REPORT_FORMAT_XLS;
                $this->view->columns = $columns;
            } 
        } else {
            $this->addFormMessages($this->formOperatorSummaryStats);
        }
        
        $this->view->isLNUser = $isLNUser;
        $this->view->form = $this->formOperatorSummaryStats;

        return $this->view;
    }

    /**
     * Get the number of vins in each status in an optional date range
     */
    public function vinStatusSummaryAction()
    {
        $this->layout()->setTemplate('layout/metrics');
        
        $this->formVinStatusSummary->setInputFilter($this->formVinStatusSummary->addInputFilters());
        /* Start get query params during pagination */
        $page = $this->params()->fromQuery('page');
        $queryParams = $this->request->getQuery();
        $queryParams = (array) $queryParams;
        
        if (!empty($queryParams)) {
            $inputParams = $queryParams;
        } else {
            $inputParams = (array) $this->request->getPost();
        }

        if (!empty($inputParams['fromDate'])) {
            $inputParams['fromDate'] = $this->convertDateSeperator($inputParams['fromDate']);
        }
        
        if (!empty($inputParams['toDate'])) {
            $inputParams['toDate'] = $this->convertDateSeperator($inputParams['toDate']);
        }
        /* End get query params during pagination */
        $this->formVinStatusSummary->setData($inputParams);

        if (($this->request->isPost()
            || ($this->request->isGet()
                && array_key_exists('page', $inputParams)
                || array_key_exists('downloadType', $inputParams)))
            && $this->formVinStatusSummary->isValid(str_replace('-', '/', $inputParams))) {

            $paginatorParams = $inputParams;
            $paginatorParams = $this->escapePaginatorParams($paginatorParams);
            $columns = [
                'E' => 'Error',
                'H' => 'Hold',
                'V' => 'Valid',
                'total' => 'Total'
            ];
            $data = $this->serviceVinStatus->getVinStatusCount(
                    $paginatorParams['keyingVendorId'], 
                    $paginatorParams['fromDate'], 
                    $paginatorParams['toDate']
            );
            ksort($data);
            $data['total'] = array_sum($data);
            
            if (isset($data) && array_key_exists('downloadType', $inputParams) && $inputParams['downloadType'] == ReportMaker::REPORT_FORMAT_XLS) {
                //Report export
                $reportHeader = [
                    'reportName' => 'Vin Status by Summary'
                ];
                $this->export(ReportMaker::REPORT_FORMAT_XLS, ['data' => $data], $columns, $reportHeader, ['border centeralign']);
            } else {
                //Report Listing
                if (!empty($data)) {
                    $this->view->summary = $data;
                    $this->view->paginatorParams = $paginatorParams;
                    $this->view->format = ReportMaker::REPORT_FORMAT_XLS;
                    $this->view->colName = $columns;
                }
            }
        } else {
            $this->addFormMessages($this->formVinStatusSummary);
        }
        
        $this->view->isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        $this->view->form = $this->formVinStatusSummary;

        return $this->view;
    }

    public function vinStatusByOperatorAction()
    {
        $this->layout()->setTemplate('layout/metrics');
        
        $isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        
        $this->formVinStatusByOperator->setInputFilter($this->formVinStatusByOperator->addInputFilters());
        /* Start get query params during pagination */
        $page = $this->params()->fromQuery('page');
        $queryParams = $this->request->getQuery();
        $queryParams = (array) $queryParams;

        if (!empty($queryParams)) {
            $inputParams = $queryParams;
        } else {
            $inputParams = (array) $this->request->getPost();
        }

        if (!empty($inputParams['fromDate'])) {
            $inputParams['fromDate'] = $this->convertDateSeperator($inputParams['fromDate']);
        }
        
        if (!empty($inputParams['toDate'])) {
            $inputParams['toDate'] = $this->convertDateSeperator($inputParams['toDate']);
        }
        /* End get query params during pagination */
        $this->formVinStatusByOperator->setData($inputParams);

        if (($this->request->isPost()
            || ($this->request->isGet()
                && array_key_exists('page', $inputParams)
                || array_key_exists('downloadType', $inputParams)))
            && $this->formVinStatusByOperator->isValid(str_replace('-', '/', $inputParams))) {
            
            $data = $this->serviceVinStatus->getVinStatusCountByOperator(
                $inputParams['keyingVendorId'],
                $inputParams['fromDate'],
                $inputParams['toDate'],
                $inputParams['firstName'],
                $inputParams['lastName']);

            $paginator = new Paginator(new Adapter\ArrayAdapter($data));
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($this->config['pagination']['perpage']);
            $paginatorParams = $inputParams;
            $paginatorParams = $this->escapePaginatorParams($paginatorParams);

            $columns = [
                'userName' => 'Username',
                'nameFirst' => 'First Name',
                'nameLast' => 'Last Name',
                'H' => 'H',
                'V' => 'V',
                'E' => 'E',
                'total' => 'total'
            ];
            if ($isLNUser) {
                $columns['vendorName'] = 'Company Name';
            }

            if (isset($data) && array_key_exists('downloadType', $inputParams) && $inputParams['downloadType'] == ReportMaker::REPORT_FORMAT_XLS) {
                //Report export
                $reportHeader = [
                    'reportName' => 'Vin Status by Operator'
                ];
                $this->export(ReportMaker::REPORT_FORMAT_XLS, $data, $columns, $reportHeader, ['border centeralign']);
            } else {
                //Report Listing
                $this->view->paginator = $paginator;
                $this->view->paginatorParams = $paginatorParams;
                $this->view->format = ReportMaker::REPORT_FORMAT_XLS;
                $this->view->columns = $columns;
            }
        } else {
            $this->addFormMessages($this->formVinStatusByOperator);
        }
        
        $this->view->isLNUser = $isLNUser;
        $this->view->form = $this->formVinStatusByOperator;

        return $this->view;
    }
    
    protected function agregateStatuses($elem)
    {
        $elem[ReportStatusService::STATUS_COMPLETE] += $elem[ReportStatusService::STATUS_TRANSLATED];
        return $elem;
    }

    public function imageViewerPdfAction()
    {
        $this->session->reportId = $this->params()->fromQuery('reportId');
        return $this->forward()->dispatch('Data\Controller\ReportEntryController', ['action' => 'image-viewer-pdf']);
    }
	
	/**
     * SLA Status Summary 
     */
    
    public function slaStatusSummaryAction()
    {
        $this->layout()->setTemplate('layout/metrics');
        
        $isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        
        $this->formSlaStatusSummary->setInputFilter($this->formSlaStatusSummary->addInputFilters());
        
        $page = $this->params()->fromQuery('page');
        $queryParams = $this->request->getQuery();
        $queryParams = (array) $queryParams;

        if (!empty($queryParams)) {
            $inputParams = $queryParams;
        } else {
            $inputParams = (array) $this->request->getPost();
        }

        $this->formSlaStatusSummary->setData($inputParams);
        /* End get query params during pagination */
        if (($this->request->isPost()
            || ($this->request->isGet()
                && array_key_exists('page', $inputParams)
                || array_key_exists('downloadType', $inputParams)))
            && $this->formSlaStatusSummary->isValid(str_replace('-', '/', $inputParams))) {
           
            $select = $this->serviceReportEntry->getSlaStatusSummarybyState($inputParams);
            $result = $this->SLASetPriorityColumn($select);	

            $ec_total = $this->serviceReportEntry->getSlaStatusSummaryTotal($inputParams,WorkTypeService::WORK_TYPE_ECRASH);	
            $cg_total = $this->serviceReportEntry->getSlaStatusSummaryTotal($inputParams,WorkTypeService::WORK_TYPE_CGF);	
			 
            $paginator = new Paginator(new Adapter\ArrayAdapter($result));
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($this->config['pagination']['perpage']);
            $paginatorParams = $inputParams;
            $paginatorParams = $this->escapePaginatorParams($paginatorParams);
             

            if (isset($paginator) && array_key_exists('downloadType', $inputParams) && $inputParams['downloadType'] == ReportMaker::REPORT_FORMAT_XLS) {
                //Report export
                $reportHeader = [
                    'reportName' => 'SLA STATUS Summary'
                ];
                $columns = [
                    'priority' => 'Priority',
                    'stage' => 'Entry Stage',
                    'workType' => 'Work Type',
                    'stateAbbr' => 'State',
                    'agencyName' => 'Agency Name',
                    'reportId' => 'Report #',
                    'formTypeDescription' => 'Report Type',
                    'dateCreated' => 'Creation Date',
                    'estDue' => 'EST',
                    'phtDue' => 'PHT',
                    'wtTatHours' => 'TAT Hours',
                    'tatHours' => 'Remaining Time to Process',
                    'userId' => 'Assigned Keyer',
                    'reportStatus' => 'Status',
                    'flag' => 'Is Prioritized'
                ];
                if ($isLNUser) {
                    $columns['vendorName'] = 'Company Name';
                }
                $columns['reportStatus'] = 'Status';
                
                $this->export(ReportMaker::REPORT_FORMAT_XLS, $paginator, $columns, $reportHeader, ['border centeralign']);
            } else {
                //Report Listing
                $this->view->paginator = $paginator;
                $this->view->paginatorParams = $paginatorParams;
                $this->view->format = ReportMaker::REPORT_FORMAT_XLS;
                $this->view->ec_total = $ec_total;
                $this->view->cg_total = $cg_total;
            }
             
        } else {
            $this->addFormMessages($this->formSlaStatusSummary);
        }
        
        $this->view->isLNUser = $isLNUser;
        $this->view->form = $this->formSlaStatusSummary;
        return $this->view;
    }	

    protected function SLASetPriorityColumn($result)
    {
        $count =1;		         
        foreach ($result as $key => $entry) {			
            $dateCreated= date("Y-m-d g:i A ", strtotime($entry['dateCreated'])) ;
            $estDue= date("Y-m-d g:i A ", strtotime($entry['dueDate'])) ;

            $phtDue = new DateTime($entry['dueDate'],new DateTimeZone("America/New_York")); 
            $phtDue->setTimezone(new DateTimeZone("Asia/Manila")); 
            $phtDue= $phtDue->format("Y-m-d g:i A ") ;		

            /*Remaining time to process for PHT **/
            $phtTimeZone = new DateTimeZone("Asia/Manila");
            $phtTimeNow = new DateTime("now",$phtTimeZone); 	
            $phtDueDate = new DateTime($phtDue,$phtTimeZone);
            $interval = $phtDueDate->diff($phtTimeNow);

            $tatHour   = $interval->h + ($interval->days * 24);
            $tatMinutes = $interval->i;
            $tatSeconds = $interval->s; 

            $tatHours = "$tatHour:$tatMinutes:$tatSeconds";

            if ($interval->invert == 0) {  // Is 1 if the interval represents a negative time period and 0 otherwise
                    $tatHours = "- $tatHours";
            } else {
                    $tatHours = "$tatHours";
            }

            $result[$key]['dateCreated'] = $dateCreated; 
            $result[$key]['priority'] = $count; 
            $result[$key]['estDue'] = $estDue;
            $result[$key]['phtDue'] = $phtDue;
            $result[$key]['tatHours'] = $tatHours;
            $count +=1;
        }
        return $result;
    } 
   
}
