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
use Base\Service\ReportEntryService;
use Base\View\Helper\DataRenderer\ReportMaker;
use Base\Service\ReportFlagService;
use Admin\Form\ViewKeyedImageForm;
use Base\Service\KeyingVendorService;

class ViewKeyedImageController extends BaseController
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
     * @var Base\Service\ReportService
     */
    private $serviceReport;
    
    /**
     * @var Base\Service\ReportEntryService
     */
    private $serviceReportEntry;
    
    /**
     * @var Admin\Form\ViewKeyedImageForm
     */
    private $viewKeyedImageForm;

    /**
     * @var Base\View\Helper\ReportMaker
     */
    private $reportMaker;

    /**
     * @var Zend\Session\Container
     */
    private $session;
    
    /**
     * @var Base\Service\ReportFlagService
     */
    private $serviceReportFlag;
    
    /**
     * @var Base\Service\KeyingVendorService
     */
    private $serviceKeyingVendor;
    
    /**
     * Constructor will be invoked from the ViewKeyedImageControllerFactory
     * @param array  $config                        Application configuration
     * @param object $logger                        Zend\Log\Logger
     * @param object $session                       Zend\Session\Container
     * @param object $viewKeyedImageForm            Admin\Form\ViewKeyedImageForm
     * @param object $serviceReport                 Base\Service\ReportService
     * @param object $serviceReportEntry            Base\Service\ReportEntryService
     * @param object $reportMaker                   Base\View\Helper\ReportMaker
     * @param object $serviceReportFlag             Base\Service\ReportFlagService
     * @param object $serviceKeyingVendor           Base\Service\KeyingVendorService
     */
    public function __construct(
        Array $config,
        Logger $logger,
        Container $session,
        ViewKeyedImageForm $viewKeyedImageForm,
        ReportService $serviceReport,
        ReportEntryService $serviceReportEntry,
        ReportMaker $reportMaker,
        ReportFlagService $serviceReportFlag,
        KeyingVendorService $serviceKeyingVendor)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->viewKeyedImageForm = $viewKeyedImageForm;
        $this->serviceReport = $serviceReport;
        $this->serviceReportEntry = $serviceReportEntry;
        $this->reportMaker = $reportMaker;
        $this->session = $session;
        $this->serviceReportFlag = $serviceReportFlag;
        $this->serviceKeyingVendor = $serviceKeyingVendor;

        parent::__construct();
    }
    
    public function indexAction()
    {
        $this->layout()->setTemplate('layout/metrics');
        
        $this->viewKeyedImageForm->setInputFilter($this->viewKeyedImageForm->addInputFilters());
        /* Start get query params during pagination */
        $page = $this->params()->fromQuery('page');
        $queryParams = $this->request->getQuery();
        $queryParams = (array) $queryParams;
        
        if (!empty($queryParams)) {
            $inputParams = $queryParams;
        } else {
            $inputParams = (array) $this->request->getPost();
        }
        
        $this->viewKeyedImageForm->setData($inputParams);
        
        $isPostRequest = $this->request->isPost();
        $isGetRequestWithParam = $this->request->isGet() && array_key_exists('page' , $inputParams);

        /**
         * If form is submitted
         */
        if ($isPostRequest || $isGetRequestWithParam) {          
            $isAllowedSearch = true;
            
            /**
             * Id Search keys
             */
            $idSearchNames =  ['cruOrderId','reportId','returnAllPasses'];

            /**
             * fuzzy search fields
             */
            $fuzzySearchNames = [
                'processingStartTime',
                'processingEndTime',
                'stateId',
                'agencyId',
                'vin',
                'licensePlate',
                'registrationState',
                'caseIdentifier',
                'reportType',
                'crashDate',
                'partyLastName',
                'partyFirstName',
                'operatorLastName',
                'operatorFirstName',
            ];

            $searchFilter = [];
            $idSearched = [];

            /**
             * Check if either one of id search
             * is not empty
             */
            foreach ($idSearchNames as $name) {
                if (in_array($name, array_keys($inputParams)) && !empty($inputParams[$name])) {
                    $idSearched[$name] = $inputParams[$name];

                }
            }

            /**
             * ID Search is used
             */
            if (!empty($idSearched)) {
                //set search filter to id search keys
                $searchFilter = $idSearched;

                //check if input params is not empty then add field dynamic validation
                if (isset($idSearched['reportId'])) {
                    $this->viewKeyedImageForm->reportIdInputFilterIfNotEmpty();
                    $this->viewKeyedImageForm->get('cruOrderId')->setAttribute('disabled', 'disabled');
                }
                if (isset($idSearched['cruOrderId'])) {
                    $this->viewKeyedImageForm->get('reportId')->setAttribute('disabled', 'disabled');
                }

                //disabling lower search inputs
                foreach ($fuzzySearchNames as $element) {
                    $this->viewKeyedImageForm->get($element)->setAttribute('disabled', 'disabled');
                }
            } else {
                /**
                 * Fuzzy search used
                 * then use not empty fuzzey search fields for filtering
                 */
                foreach ($fuzzySearchNames as $name) {
                    if (isset($inputParams[$name]) && !empty($inputParams[$name])) {
                        $searchFilter[$name] = $inputParams[$name];
                    }
                }

                /**
                 * Used to check if start and and date is valid
                 */
                $isTimeRangeValid = $this->viewKeyedImageForm->timeProcessingRangeValidate();
                /**
                 * used to check if fuzzy search filter condition is met.
                 * return false if not met the condition requirement
                 */
                $isAllowedSearch = $this->viewKeyedImageForm->checkAddTotalUsedFilter([
                    'reportType','stateId' , 'agencyId' , 'vin',
                    'licensePlate' , 'caseIdentifier','crashDate',
                    'registrationState',
                    'processingStartTime',
                ] , $inputParams);
            }

            $isValidSearch = $this->viewKeyedImageForm->isValid();

            /**
             * Do search only if every filter check condition is met.
             */
            if ($isValidSearch && $isAllowedSearch) {
                $select = $this->serviceReport->fetchKeyedImages($searchFilter);
            
                $paginator = $this->getPaginator($select);

                if ($paginator) {
                    $paginator->setCurrentPageNumber($page);
                }
                
                $this->view->paginator = $paginator;
                $this->view->paginatorParams = $inputParams;
                $this->view->reportMaker = $this->reportMaker;
            } else {
                $this->addFormMessages($this->viewKeyedImageForm);
            }
        }
        if (empty($inputParams['processingStartTime']) || empty($inputParams['processingEndTime'])) {
            $this->viewKeyedImageForm->setData([
                'processingStartTime' => date('Y-m-d'),
                'processingEndTime'=> date('Y-m-d')
            ]);
        }
        
        $this->view->isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        $this->view->form = $this->viewKeyedImageForm;

        return $this->view;
    }

    protected function getPaginator($select)
    {
        if (empty($select)) {
            return [];
        }

        $paginator = new Paginator(new Adapter\ArrayAdapter($select));

        return $paginator;
    }

    public function exportAction()
    {
        $inputParams = $this->request->getQuery();
        $inputParams = (array) $inputParams;
        $select = $this->serviceReport->fetchKeyedImages($inputParams);
        $paginator = $this->getPaginator($select);

        $reportHeader = [
            'reportName' => 'View keyed images'
        ];

        $columns = [
            'stateName' => 'State',
            'agencyName' => 'Agency',
            'reportId' => 'Report ID',
            'cru_order_id' => 'CRU Order ID',
            'caseIdentifier' => 'Report Number',
            'operatorName' => 'Operator Name',
            'driverName' => 'Party Name',
            'vin' => 'VIN #',
            'crashDate' => 'Incident Date'
        ];
        
        if ($this->serviceKeyingVendor->isLoggedInLNUser()) {
            $columns['vendorName'] = 'Company';
        }

        if ($inputParams['downloadType'] == ReportMaker::REPORT_FORMAT_PDF) {
            $this->reportMaker->output(ReportMaker::REPORT_FORMAT_PDF, $paginator, $columns, [], $reportHeader, [ReportMaker::REPORT_FORMAT_PDF]);
        } else {
            $this->reportMaker->output(ReportMaker::REPORT_FORMAT_XLS, $paginator, $columns, [], $reportHeader, ['border centeralign']);
        }
    }

    public function reportEntryAction()
    {
         /** @todo Need to migrate this section. */
        $reportId = $this->params()->fromQuery('reportId');

     

        if (empty($reportId)) {
            throw new Exception('Invalid report given');
        }

        $reportEntryId = $this->params()->fromQuery('reportEntryId');
        $this->session->reportId = $reportId;
        $this->session->reportEntryId = [];
        $this->session->reportEntryFlow = [];
        $this->session->reportEntryId[$reportId] = $reportEntryId;
        $this->session->reportEntryFlow[$reportId] = ReportService::ENTRY_FLOW_VIEW;

        return $this->forward()->dispatch('Data\Controller\ReportEntryController', ['action' => 'edit']);
    }
    
    /**
     * Download keyed data of old report keyed in old keying app
     *
     */
    public function exportOldKeyedReportAction()
    {    
        $reportId = $this->params()->fromQuery('reportId');
        if (empty($reportId)) {
            throw new Exception('Invalid report given');
        }
        $lastReportEntryData = $this->serviceReportEntry->fetchLastPassByReportId($reportId, true);
        if (!empty($lastReportEntryData['entryData'])) {
            $entryData = $lastReportEntryData['entryData'];
            $reportName = "OldKeyingData-ReportId_" . $reportId;
            $this->reportMaker->sendToBrowser(ReportMaker::REPORT_FORMAT_TEXT, $reportName, $entryData);
        } else {
            throw new Exception('No Keyed Data in Old Keying App for Report Id: ' . $reportId);
        }
    }

    public function checkCommandCenterFlagAction()
    {
        $result = false;
        $inputParams = (array) $this->request->getPost();

        $reportId = preg_replace("/\r|\n/", "", $inputParams['reportId']);
        if (empty($reportId)) {
            throw new Exception('Invalid report ID given');
        }
        
        $flagCount = $this->serviceReportFlag->getCountWithFlag($reportId, 'command center edited');
        
        if ($flagCount > 0) {
            $result = true;
        }
        
        return $this->json->setVariables([
            'result' => $result,
            'isNewApp' => $this->serviceReport->isNewApp($reportId)
        ]);
    }
}
