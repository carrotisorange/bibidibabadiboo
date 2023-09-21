<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Controller;

use Zend\Log\Logger;
use Zend\Session\Container;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter;

use Base\Controller\BaseController;
use Base\Service\ReportService;
use Admin\Form\QualityControlForm;
use Base\Adapter\Db\FormFieldAdapter;
use Base\Service\StateService;
use Base\Service\ReportEntryService;
use Base\Service\AutoExtractionAccuracyService;
use Base\Service\EntryStageService;
use Base\Service\FormFieldService;
use Base\Service\QualityControlRemarkService;
use Base\Service\UserService;
use Base\Service\WorkTypeService;
use Base\View\Helper\DataRenderer\ReportMaker;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Zend\Authentication\AuthenticationService;
use Zend\View\Renderer\PhpRenderer;

class QualityControlController extends BaseController
{
    /**
     * @var Array
     */
    private $config;

    /**
     * @var Zend\Session\Container
     */
    private $session;
    
    /**
     * @var Admin\Form\QualityControlForm;
     */
    private $formQualityControl;
    
    /**
     * @var Base\Service\AutoExtractionAccuracyService
     */
    private $serviceAutoExtractionAccuracy;

    /**
     * @var Base\View\Helper\DataRenderer\ReportMaker
     */
    private $reportMaker;

    const URI_SAVE = 'URI_SAVE';
    const URI_HISTORY = 'URI_HISTORY';
    const URI_CURRENT = 'URI_CURRENT';
    /**
     * Constructor will be invoked from the ViewKeyedImageControllerFactory
     * @param array  $config        Application configuration
     * @param object $logger        Zend\Log\Logger
     * @param object $session   Zend\Session\Container;
     * @param object $formQualityControl   Admin\Form\QualityControlForm;
     * @param object $formVolumeProductivity   Admin\Form\AutoExtractionMetric\VolumeProductivityReportForm;
     * @param object $serviceReport  Base\Service\StateService;
     * @param object $serviceAutoExtractionAccuracy   Base\Service\AutoExtractionAccuracyService
     * @param object $reportMaker  Base\View\Helper\DataRenderer\ReportMaker;
     */
    public function __construct(
        Array $config,
        Logger $logger,
        AuthenticationService $serviceAuth,
        Container $session,
        QualityControlForm $formQualityControl,
        ReportService $serviceReport,
        AutoExtractionAccuracyService $serviceAutoExtractionAccuracy,
        ReportMaker $reportMaker,
        StateService $serviceState,
        WorkTypeService $serviceWorkType,
        ReportEntryService $serviceReportEntry,
        EntryStageService $serviceEntryStage,
        UserService $serviceUser,
        QualityControlRemarkService $serviceQualityControlRemark,
        FormFieldService $serviceFormField,
        PhpRenderer $renderer)
    {
        $this->config = $config;
        $this->serviceAuth = $serviceAuth;
        $this->logger = $logger;
        $this->session = $session;
        $this->formQualityControl = $formQualityControl;
        $this->serviceReport = $serviceReport;
        $this->reportMaker = $reportMaker;
        $this->serviceAutoExtractionAccuracy = $serviceAutoExtractionAccuracy;
        $this->serviceState = $serviceState;
        $this->serviceWorkType = $serviceWorkType ;
        $this->serviceReportEntry = $serviceReportEntry;
        $this->serviceEntryStage = $serviceEntryStage;
        $this->serviceUser = $serviceUser;
        $this->serviceQualityControlRemark = $serviceQualityControlRemark;
        $this->serviceFormField = $serviceFormField;
        $this->renderer = $renderer;
        
        parent::__construct();
    }

    public function indexAction()
    {
        return $this->forward()->dispatch(QualityControlController::class, ['action' => 'select-filter']);
    }

    public function selectFilterAction() 
    {
        $this->unloadUserReport();
        $this->layout()->setTemplate('layout/metrics');
        $filter = $this->searchFilter(QualityControlRemarkService::FETCH_NONE_AUDITED);

        if ($filter === false) {
            return $this->redirect()->toRoute('quality-control', [
                'action' => 'select-filter'
            ]);
        }

        /**
         * Overheads for filtering
         */
        $this->formQualityControl->setInputFilter($this->formQualityControl->addInputFilters());
        $page = $this->params()->fromQuery('page');
        $inputParams = $filter['inputParams'];
        $serviceParams = $filter['serviceParams'];
        $inputParams = (array) $inputParams;
        $isPaginateRequest = ($this->request->isGet() && array_key_exists('page', $inputParams));
        $this->formQualityControl->setData($inputParams);
        
        /**
         * filter is passed
         */
        if (!is_null($filter['filterType']) || $isPaginateRequest) {
            switch($filter['filterType']) {
                case 'weekly':
                case 'date-range':
                    //fetch reports for qc
                    $reports = $this->serviceQualityControlRemark->getReportRaw([
                        'fromDate' => $serviceParams['fromDate'],
                        'toDate'   => $serviceParams['toDate'],
                        'workType' => $serviceParams['workType'],
                        'state' => $serviceParams['state']
                    ], 'random', 1500, QualityControlRemarkService::FETCH_NONE_AUDITED);
                break;

                case 'reportId':
                    $reports = [$filter['report']];
                break;
            }
            $paginator = new Paginator(new Adapter\ArrayAdapter($reports));
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($this->config['pagination']['perpage']);
            $paginatorParams = $inputParams;
            $paginatorParams = $this->escapePaginatorParams($paginatorParams);

            if (isset($paginator) && array_key_exists('downloadType', $inputParams) && $inputParams['downloadType'] == ReportMaker::REPORT_FORMAT_XLS) {
                $reportHeader = [
                    'reportName' => 'Qc Report'
                ];
                $columns = [
                    'dateKeyed' => 'Date Keyed',
                    'reportId' => 'Report #'
                ];
                $this->export(ReportMaker::REPORT_FORMAT_XLS, $paginator, $columns, $reportHeader, ['border centeralign']);
            } else {
                $this->view->paginator = $paginator;
                $this->view->paginatorParams = $paginatorParams;
                $this->view->format = ReportMaker::REPORT_FORMAT_XLS;
            }
        }

        $this->view->weeks = QualityControlRemarkService::WEEK_NAMES;
        $this->view->filterType = $inputParams['filterType'] ?? 'weekly';
        $this->view->remarkMonthScope = [];
        $this->view->postParams = $inputParams;
        $this->view->form = $this->formQualityControl;
        
        return $this->view;
    }

    private function fetchURI($action)
    {
        switch ($action) {
            case self::URI_SAVE:
                $this->session->requestURIEncoded = base64_encode($this->request->getUri()->getQuery());
            break;

            case self::URI_CURRENT:
                return $this->request->getUri()->getQuery();
            break;

            case self::URI_HISTORY;
                return base64_decode($this->session->requestURIEncoded);
        }
    }
    /**
     * if parameter is filled
     * then the action will be decode
     */
    protected function saveRequestURIOrFetch($action = 'save')
    {
        if ($action == 'save') {
            $requestURI = $this->request->getUri()->getQuery();    
            $this->session->requestURIEncoded = base64_encode($requestURI);
            return $requestURI;
        } else {
            $session = $this->session->requestURIEncoded;
            return base64_decode($session);
        }
    }

    protected function searchFilter($type)
    {
        $inputParams = $this->request->getQuery();
        $redirectTo  = $this->url()->fromRoute('quality-control', ['action' => 'select-filter']);
        if ($type == QualityControlRemarkService::FETCH_AUDITED) {
            $redirectTo = $this->url()->fromRoute('quality-control', ['action' => 'qcied-reports']);
        }

        /**
         * FilterType values ['reportId' , 'weekly','date-range']
         * inputParams values that will be used for view fields
         * service params are values that will be processed by the database
         */
        $retVal = [
            'filterType' => null,
            'inputParams' => [],
            'serviceParams' => [
                'fromDate' => '',
                'toDate'   => '',
                'workType' => '',
                'state' => '',
                'reportID' => ''
            ],
            'report' => null,
            'redirectTo' => $redirectTo
        ];
        
        if (isset($inputParams['btnSearch'])) {
            $retVal['serviceParams'] = $inputParams;
            if (!empty($inputParams['reportID'])) {
                $error = "";
                //getReport->pass non-qcied
                $report = $this->serviceQualityControlRemark->getReport($inputParams['reportID'], QualityControlRemarkService::FETCH_NONE_AUDITED);
                if (!$report) {
                    $error = 'Report Not Found';
                } else {
                    $isReportAudit = $this->serviceQualityControlRemark->isReportAudit($inputParams['reportID']);
                    /**
                     * throws error because 
                     * user is searching for qcied report
                     * but report is not been qcied/audited yet
                     */
                    if ($type == QualityControlRemarkService::FETCH_AUDITED && !$isReportAudit) {
                        $error = 'Report is not yet audited.';
                    } else if ($type == QualityControlRemarkService::FETCH_NONE_AUDITED && $isReportAudit) {
                        $error = 'This report might already been audited, please check it on qc report page.';
                    }
                }
                $retVal['filterType'] = 'reportId';
                if (empty($error)) {
                    $dateCreatedToTime = strtotime($report['date_created']);
                    //Get report date range
                    $fromDate = date('m/01/Y', $dateCreatedToTime);
                    $toDate = date('m/t/Y' , $dateCreatedToTime);
                    $fromDate =  date('Y-m-d' , strtotime($fromDate));
                    $toDate   =  date('Y-m-d' , strtotime($toDate));
                    $retVal['serviceParams']['toDate'] = $toDate;
                    $retVal['serviceParams']['fromDate'] = $fromDate;
                    $retVal['inputParams'] = $inputParams;
                    $retVal['report'] = $report;
                } else {
                    $this->flashMessenger()->addErrorMessage($error);
                    return false;
                }
            } else if (strtolower($inputParams['filterType']) == 'weekly') {
                $retVal['filterType'] = 'weekly';

                if (!isset($inputParams['week']) || empty($inputParams['week'])) {
                    $this->flashMessenger()->addErrorMessage('Week must be selected in-order to use this filter.');
                    return false;
                }
                list($fromDate, $toDate) = $this->serviceQualityControlRemark->getDateRange($inputParams);
                $retVal['serviceParams']['toDate'] = $toDate;
                $retVal['serviceParams']['fromDate'] = $fromDate;
                $retVal['inputParams'] = $inputParams;
            } else {
                $retVal['filterType'] = 'date-range';
                $fromDate =  date('Y-m-d', strtotime($inputParams['fromDate']));
                $toDate   =  date('Y-m-d', strtotime($inputParams['toDate']));
                //rewriting url parameter so pagination will be valid
                $inputParams['fromDate'] = date('m/d/Y', strtotime($inputParams['fromDate']));
                $inputParams['toDate'] = date('m/d/Y', strtotime($inputParams['toDate']));
                $retVal['serviceParams']['toDate'] = $toDate;
                $retVal['serviceParams']['fromDate'] = $fromDate;
                $retVal['serviceParams']['fromDate'] = $fromDate;
                $retVal['inputParams'] = $inputParams;
            }
        }
        return $retVal;
    }

    public function reportSummaryAction()
    {
        $this->layout()->setTemplate('layout/metrics');
        $this->unloadUserReport();
        $this->fetchURI(self::URI_SAVE);
        $this->view->uriString = $this->fetchURI(self::URI_CURRENT);

        $filter = $this->searchFilter(QualityControlRemarkService::FETCH_AUDITED);

        if ($filter == false) {
            return $this->redirect()->toRoute('quality-control', ['action' => 'report-summary']);
        }
        $serviceParams = $filter['serviceParams'];
        $inputParams = $filter['inputParams'];

        $this->view->postParams = $inputParams;
        $this->view->completeURI = $this->getRequest()->getUriString();

        if (!is_null($filter['filterType'])) {
            $workTypeName = $this->getWorkTypeName($inputParams['workType']);
            $stateAbbr = $this->getStateAbbr($inputParams['state']);

            $this->view->workTypeName = $workTypeName;
            $this->view->stateAbbr = $stateAbbr;
            
            $reportSummary = $this->reportSummaryByParam([
                'fromDate' => $serviceParams['fromDate'],
                'toDate'   => $serviceParams['toDate'],
                'state' => $serviceParams['state'],
                'workType' => $serviceParams['workType']
            ]);

            if (!$reportSummary) {
                $this->flashMessenger()->addErrorMessage("No Audited Report Found.");
            } else {
                $reports = $reportSummary['reports'];
                $this->view->filterOptions = $this->serviceQualityControlRemark->getFilterOptions($reportSummary['reports']);

                if (isset($inputParams['filter'])) {
                    $reports = $this->serviceQualityControlRemark->filterResultByParam($reports, $inputParams['filter']);
                    $summarizedReports = $this->serviceQualityControlRemark->summarizeReports($reports);
                    $fieldSummaries = $this->serviceQualityControlRemark->getReportsFieldSummaries($reports);
                    $reportSummary['criticalityPercentage'] = $summarizedReports['criticalityPercentage'];
                    $reportSummary['criticalitySummary'] = $summarizedReports['criticalitySummary'];
                    $reportSummary['contributors'] = $summarizedReports['contributors'];
                    $reportSummary['fieldSummaries'] = $fieldSummaries;
                }

                $this->view->inputParams = $inputParams;
                $this->view->fromDate = $reportSummary['fromDate'];
                $this->view->toDate = $reportSummary['toDate'];
                $this->view->fieldSummaries = $reportSummary['fieldSummaries'];
                $this->view->reports = $reports;
                $this->view->reportCountTotal = $reportSummary['reportCountTotal'];
                $this->view->reportFieldCountTotal = $reportSummary['reportFieldCountTotal'];
                $this->view->reportWithDiscrepanciesTotal = $reportSummary['reportWithDiscrepanciesTotal'];
                $this->view->criticalitySummary = $reportSummary['criticalitySummary'];
                $this->view->criticalityPercentage = $reportSummary['criticalityPercentage'];
                $this->view->contributors = $reportSummary['contributors'];
                $this->view->setTemplate('admin/quality-control/report-quality-remark');

                $reportHTML = $this->renderer->render($this->view);

                if (isset($inputParams['export'])) {
                    $downloadFilename = "ecrash - Quality Control Productivity Report".$inputParams['fromDate']."_TO_".$inputParams['toDate'];     
                    $this->reportMaker->sendToBrowser(ReportMaker::REPORT_FORMAT_XLS, $downloadFilename, $reportHTML);
                }
            }
        }
        $this->formQualityControl->setData($inputParams);
        $this->view->weeks = QualityControlRemarkService::WEEK_NAMES;
        $this->view->filterType = $inputParams['filterType'] ?? 'weekly';
        $this->view->form  = $this->formQualityControl;

        return $this->view;
    }

    private function reportSummaryByParam($params) 
    {
        if (isset( $params['fromDate'] , $params['toDate'])) {
            $fromDate = $params['fromDate'];
            $toDate = $params['toDate'];
            $reports = $this->serviceQualityControlRemark->getRemarks($params);

            if (!$reports) {
                return false;
            }
            $fieldSummaries = $this->serviceQualityControlRemark->getReportsFieldSummaries($reports);
            $view = $this->serviceQualityControlRemark->summarizeReports($reports);
            $view['fromDate'] = $fromDate;
            $view['toDate'] = $toDate;
            $view['fieldSummaries'] = $fieldSummaries;
            $view['inputParams'] = $params;
            
            return $view;
        } else {

            return false;
        }
    }

    public function reportAndImageAction()
    {
        $request = $this->params()->fromQuery();
        $userId = $this->identity()->userId;
        $reportId = $request['reportId'];
        $fromDate = $request['fromDate'] ?? '';
        $toDate = $request['toDate'] ?? '';

        if (empty($fromDate) || empty($toDate)) {
            $report = $this->serviceQualityControlRemark->getReport($reportId);
            $reportDateScope = $this->serviceQualityControlRemark->generateDateRangeByDate($report['date_created']);
            $fromDate = $reportDateScope['fromDate'];
            $toDate = $reportDateScope['toDate'];
        }

        $loadReport = $this->serviceQualityControlRemark->loadOrUnloadReport($reportId, $userId, 'load');
        $this->session->reportId = $reportId;
        $this->session->fromDate = $fromDate;
        $this->session->toDate = $toDate;

        if (!$loadReport) {
            $this->flashMessenger()->addErrorMessage("Report {$reportId} is currently being processed.");
            return $this->redirect()->toRoute('quality-control', ['action' => 'select-filter']);
        }
        $openedReport = $this->serviceQualityControlRemark->getReportOpenedByUser($userId);

        if ($openedReport['report_id'] != $reportId) {
            $url = "report-and-image?reportId={$openedReport['report_id']}";
            $link = "<a href='{$url}'>{$openedReport['report_id']}</a>";
            $this->flashMessenger()->addErrorMessage("You are currently working on report {$link}, please complete before working on new report.");
            return $this->redirect()->toRoute('quality-control', ['action' => 'select-filter']);
        }
        $report = $this->serviceQualityControlRemark->getAuditedReport($reportId);
        $this->view->url = '/admin/quality-control/remark-entry';
        return $this->view;
    }

    public function unloadUserReport()
    {
        $this->serviceQualityControlRemark->unloadUserReport($this->identity()->userId);
    }

    public function unloadUserReportAction()
    {
        $this->serviceQualityControlRemark->unloadUserReport($this->identity()->userId);

        return $this->json->setVariables([
            'redirectURL' => '', 
            'action' => 'close'
        ]);
    }

    
    public function remarkEntryAction()
    {
        $this->layout()->setTemplate('layout/minimal');
        $userId = $this->identity()->userId;
        $reportId = $this->session->reportId;

        $reportInfo = $this->serviceQualityControlRemark->getReport($reportId);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            //used to fetch next random report
            $fetchParam = $post['fetchParam'];
            $fields = $post['fields'];
            $general = $post['general'];
            //append user_id
            $general['userId'] = $userId;
            $retVal = $this->serviceQualityControlRemark->addOrUpdate($general, $fields);

            $this->serviceQualityControlRemark->loadOrUnloadReport($reportId, $userId, 'unload');
            
            $isEdit = strtolower($this->serviceQualityControlRemark->isEdit);
            $dateRange = $this->serviceQualityControlRemark->generateDateRangeByDate($reportInfo['date_created']);

            $fetchParam = array_merge($this->serviceQualityControlRemark->extractFetchParamByReport($fetchParam, $reportInfo), $dateRange);
            $reportInfo = $this->serviceQualityControlRemark->getReportRandom($fetchParam, 1)[0];
            
            if ($isEdit) {
                $redirectURL = '/admin/quality-control/report-summary?' . 
                $this->fetchURI(self::URI_HISTORY);
            } else {
                if (!is_null($reportInfo)) {
                    $redirectURL = "/admin/quality-control/report-and-image?reportId={$reportInfo['report_id']}&fromDate={$dateRange['fromDate']}&toDate={$dateRange['toDate']}";
                } else {
                    $redirectURL = "/admin/quality-control/select-filter";
                    $this->flashMessenger()->addErrorMessage("No report found within date range");
                }
            }
            
            return $this->json->setVariables([
                'retVal' => $retVal,
                'redirectURL' => $redirectURL,
                'action' => $isEdit ? 'edit' : 'create'
            ]);
        }


        $this->layout()->setTemplate('layout/minimal');
        $reportRemarks = $this->serviceQualityControlRemark->getReportRemarks($reportId);
        $reportRemarks = $this->serviceQualityControlRemark->formatRemarksKeyPair($reportRemarks, ['field_name', 'remark_value']);
        //edit if remarks are not emptty otherwise
        $isEdit = !empty($reportRemarks);
        $entryInfo = [];
        $entryStagesExternal = $this->serviceEntryStage->getExternalNamePairs();
        $reportData = $this->serviceReportEntry->fetchPassTwoByReportId($reportId);
        $this->serviceAutoExtractionAccuracy->findPassDifference($reportData, $reportData, EntryStageService::STAGE_ALL);
        $totalFieldsAndDescrepancyCount = $this->serviceAutoExtractionAccuracy->findPassDifference($reportData, $reportData, 
        EntryStageService::STAGE_DYNAMIC_VERIFICATION);
        $totalFieldCount = $totalFieldsAndDescrepancyCount['totalCount'];
        $totalDiscrepancyCount = $totalFieldsAndDescrepancyCount['discrepancyCount'];
        $userInfo = $this->serviceUser->getIdentityData($reportData['userId']);
        $entryInfo[$reportData['entryStage']] = [
            'dateCompleted' => $reportData['dateUpdated'],
            'username' => $userInfo['username'],
            'nameFirst' => $userInfo['nameFirst'],
            'nameLast' => $userInfo['nameLast'],
            'title' => $entryStagesExternal[$reportData['entryStageId']],
        ];
        $convertedData = $this->serviceQualityControlRemark->convertFieldToLabel($this->serviceAutoExtractionAccuracy->comparedData);
        $this->view->reportId = $reportId;
        $this->view->reportInfo = $reportInfo;
        $this->view->entryInfo = $entryInfo;
        $this->view->entryData = $convertedData;
        $this->view->format = ReportMaker::REPORT_FORMAT_XLS;
        $this->view->totalFieldCount = $totalFieldCount;
        $this->view->totalDiscrepancyCount = $totalDiscrepancyCount;
        $this->view->remarks = $reportRemarks;
        $this->view->isEdit = $isEdit;
        $this->view->fetchParam = $fetchParam ?? [];
        $this->view->form = $this->formQualityControl;

        return $this->view;
    }
    

    /* Creates a diff of report entries for use with pass overview
     *  Current returned format:
     * 'field/key' => array('entry' => 'value);
     *
     * @param array $reportEntriesCommon
     * @return array
     */
    protected function passOverviewGetValueDiff($reportEntriesCommon)
    {
        $defaultValues = array_fill_keys(array_keys($reportEntriesCommon), null);
        $values = [];

        foreach ($reportEntriesCommon as $entryId => $reportEntry) {
            foreach ($reportEntry as $sectionName => $section) {
                if (!strcasecmp($sectionName, 'incident')) {
                    $values = $this->passOverviewAddValues(
                        $values, $entryId, $sectionName, $section, $defaultValues
                    );
                } else {
                    foreach ($section as $index => $entity) {
                        $values = $this->passOverviewAddValues(
                            $values, $entryId, $sectionName, $entity, $defaultValues, $index
                        );
                    }
                }
            }
        }
        // SORT_NATURAL for ksort available only from php 5.4, that's why using uksort here
        uksort($values, 'strnatcmp');

        return $values;
    }

    public function noissueAction()
    {   
        $reportId = $this->session->reportId;
        $fromDate = $this->session->fromDate;
        $toDate = $this->session->toDate;
        $post = $this->request->getPost();
        $userId = $this->identity()->userId;
        //fetch current report
        $reportInfo = $this->serviceQualityControlRemark->getReport($reportId);
        $this->serviceQualityControlRemark->loadOrUnloadReport($reportId, $userId, 'unload');
        $this->serviceQualityControlRemark->noissue($reportId, $userId);
        $fetchParam = $post['fetchParam'];
        $fetchParam = array_merge($this->serviceQualityControlRemark->extractFetchParamByReport($fetchParam, $reportInfo), [
            'fromDate' => $fromDate,
            'toDate'   => $toDate
        ]);
        
        //fetch random report
        $reportInfo = $this->serviceQualityControlRemark->getReportRandom($fetchParam, 1, QualityControlRemarkService::FETCH_NONE_AUDITED)[0];
        
        if (!is_null($reportInfo)) {
            $redirectURL = "/admin/quality-control/report-and-image?reportId={$reportInfo['report_id']}&fromDate={$fromDate}&toDate={$toDate}";
        } else {
            $redirectURL = "/admin/quality-control/select-filter";
            $this->flashMessenger()->addErrorMessage("No report found within range");
        }

        return $this->json->setVariables([
            'redirectURL' => $redirectURL
        ]);
    }

    protected function getStateAbbr($stateId) 
    {
        $retVal = null;
        if (!empty($stateId)) {
            $retVal = $this->serviceState->getStateAbbrById($stateId);
        }

        return $retVal;
    }

    protected function getWorkTypeName($workTypeId) 
    {
        $retVal = null;

        if (!empty($workTypeId)) {
            $workTypes = $this->serviceWorkType->getAll();
            foreach ($workTypes as $key => $row) {
                if ($row['work_type_id'] == $workTypeId) {
                    $retVal = $row['name_external'];
                    break;
                }
            }
        }
        
        return $retVal;
    }

    /*
     * @param array $values
     * @param id $entryId
     * @param array $sectionName - Like 'incident' or 'person'
     * @param array $entity - An individual 'person' or 'vehicle' record's fields
     * @param array $defaultValues -
     * @param int $index - Index of the 'person' or 'vehicle'
     * @return array $values + entity values
     */
    protected function passOverviewAddValues($values, $entryId, $sectionName, $entity, $defaultValues, $index = null)
    {
        foreach ($entity as $fieldName => $value) {
            if (is_array($value)) {
                $value = implode('|', $value);
            }
            $key = is_null($index) ? $sectionName . ' ' . $fieldName : $sectionName . ' ' . ($index + 1) . ' ' . $fieldName;

            if (!isset($values[$key])) {
                $values[$key] = $defaultValues;
            }
            $values[$key][$entryId] = $value;
        }

        return $values;
    }
}
