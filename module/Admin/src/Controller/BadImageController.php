<?php

namespace Admin\Controller;

use Zend\Log\Logger;
use Zend\Session\Container;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter;
use Zend\Validator\Date;
use Exception;

use Base\Controller\BaseController;
use Base\View\Helper\DataRenderer\ReportMaker;
use Base\Service\ReportStatusService;
use Base\Service\ReportQueueService;
use Base\Service\ReportService;
use Admin\Form\BadImageSearchForm;
use Base\Service\KeyingVendorService;

class BadImageController extends BaseController
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
     * @var Admin\Form\Metric\BadImageSearchForm
     */
    private $badImageSearchForm;

    /**
     * @var Base\View\Helper\ReportMaker
     */
    private $reportMaker;

    /**
     * @var Zend\Session\Container
     */
    private $_session;

    /**
     * @var Base\Service\ReportQueueService
     */
    private $serviceReportQueue;

    /**
     * @var Base\Service\ReportStatusService
     */
    private $serviceReportStatus;

    /**
     * @var Base\Service\ReportService
     */
    private $serviceReport;
    
    /**
     * @var Base\Service\KeyingVendorService
     */
    private $serviceKeyingVendor;
    
    public function __construct(
        Array $config,
        Logger $logger,
        Container $session,
        ReportMaker $reportMaker,
        Date $date,
        BadImageSearchForm $badImageSearchForm,
        ReportQueueService $serviceReportQueue,
        ReportStatusService $serviceReportStatus,
        ReportService $serviceReport,
        KeyingVendorService $serviceKeyingVendor)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->reportMaker = $reportMaker;
        $this->session = $session;
        $this->date = $date;
        $this->badImageSearchForm = $badImageSearchForm;
        $this->serviceReportQueue = $serviceReportQueue;
        $this->serviceReportStatus = $serviceReportStatus;
        $this->serviceReport = $serviceReport;
        $this->serviceKeyingVendor = $serviceKeyingVendor;

        parent::__construct();
    }

    public function indexAction()
    {
        $this->layout()->setTemplate('layout/metrics');

        $this->badImageSearchForm->setInputFilter($this->badImageSearchForm->addInputFilters());
        
        $badImageStatuses = [
            ReportStatusService::STATUS_BAD_IMAGE,
            ReportStatusService::STATUS_DISCARDED,
            ReportStatusService::STATUS_REORDERED,
            ReportStatusService::STATUS_DEAD
        ];

        /* Start get query params during pagination */
        $page = $this->params()->fromQuery('page');
        $queryParams = $this->request->getQuery();
        $queryParams = (array) $queryParams;

        if (!empty($queryParams)) {
            $inputParams = $queryParams;
        } else {
            $inputParams = (array) $this->request->getPost();
        }

        /* End get query params during pagination */
        /*To do: Need to implement this via ACL. but in current code block no where processdiscarded action is used.
        if (!$this->isAclAllowed('admin:bad-image', 'process-discarded')) {
            $form->restrictStatusToBadImage();
        }*/
        $this->badImageSearchForm->setData($inputParams);

        if (($this->request->isPost()
        || ($this->request->isGet()
            && array_key_exists('page', $inputParams)
            || array_key_exists('downloadType', $inputParams)))
        && $this->badImageSearchForm->isValid($inputParams)) {

            $reportStatus = $inputParams['reportStatus'];

            switch ( $reportStatus ) {
                case ReportStatusService::STATUS_BAD_IMAGE:
                    $select = $this->serviceReportQueue->selectByQueue($reportStatus);
                    $this->session->searchName = "Bad Image Queue";
                    break;
                    
                case ReportStatusService::STATUS_DISCARDED:
                    $select = $this->serviceReportQueue->selectByQueue($reportStatus);
                    $this->session->searchName = "Discarded Reports";
                    break;

                case ReportStatusService::STATUS_REORDERED:
                    $select = $this->serviceReportQueue->selectByStatus(ReportStatusService::STATUS_REORDERED);
                    $this->session->searchName = "Discarded Reports";
                    break;

                case ReportStatusService::STATUS_DEAD:
                    $select = $this->serviceReportQueue->selectByStatus(ReportStatusService::STATUS_DEAD);
                    $this->session->searchName = "Discarded Reports";
                    break;

                default:
                    $this->logger->log(Logger::NOTICE, 'Invalid status selected.');
                    $this->flashMessenger()->addErrorMessage('Invalid status selected','warning');
                    return $this->view;
            }

            $paginator = $this->getPaginator($page, $select, $inputParams);

            $paginatorParams = $this->escapePaginatorParams($inputParams);

            if (array_key_exists('downloadType', $inputParams) && $inputParams['downloadType'] == ReportMaker::REPORT_FORMAT_XLS) {

                $this->export($paginator);
                
            } else {
                //Report Listing
                $this->view->paginator = $paginator;
                $this->view->paginatorParams = $paginatorParams;
                $this->view->format = ReportMaker::REPORT_FORMAT_XLS;
                $this->view->badImageStatuses = $badImageStatuses;
                $this->view->reportStatus = $reportStatus;
                $this->view->serviceReportStatus = $this->serviceReportStatus;
            }
            
        } else {
            $this->addFormMessages($this->badImageSearchForm);
        }

        $this->view->isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        $this->view->form  = $this->badImageSearchForm;

        return $this->view;
    }

    protected function getPaginator($page, $select, $inputParams)
    {
        $paginator = $this->serviceReportQueue->addInputCriteria($select, $inputParams);
        $paginator->setItemCountPerPage($this->config['pagination']['perpage']);
        $paginator->setCurrentPageNumber($page);
        return $paginator;
    }

    protected function export($paginator)
    {
        //Report export
        $reportHeader = [
            'reportName' => 'Bad Image Queue'
        ];

        $columns = [
            'reportId' => 'Report Id',
            'status' => 'Status',
            'lastName' => 'Operator Last Name',
            'firstName' => 'Operator First Name',
            'dateEntered' => 'Date',
            'passGroup' => 'Pass Group',
            'filename' => 'Filename',
            'stateAbbr' => 'State',
            'agencyName' => 'Agency'
        ];
        
        if ($this->serviceKeyingVendor->isLoggedInLNUser()) {
            $columns['vendorName'] = 'Company';
        }

        $this->reportMaker->output(
            ReportMaker::REPORT_FORMAT_XLS,
            $paginator,
            $columns,
            [],
            $reportHeader,
            ['border centeralign']
        );
    }

    public function reportEntryAction()
    {
        $reportId = $this->params()->fromQuery('reportId');
        $userId = !empty($this->identity()) ? $this->identity()->userId : null;
        $status = $this->serviceReportStatus->getStatusByReportId($reportId);
        $queueName = null;
        $entryFlow = null;

        switch ($status) {
            case ReportStatusService::STATUS_BAD_IMAGE:
                $queueName = ReportQueueService::QUEUE_BAD_IMAGE;
                $entryFlow = ReportService::ENTRY_FLOW_BAD;
                break;

            case ReportStatusService::STATUS_DISCARDED:
                $queueName = ReportQueueService::QUEUE_DISCARDED;
                $entryFlow = ReportService::ENTRY_FLOW_DISCARD;
                break;

            case ReportStatusService::STATUS_REORDERED:
            case ReportStatusService::STATUS_DEAD:
                $entryFlow = ReportService::ENTRY_FLOW_DEAD;
                break;

            default:
                throw new Exception('Unknown report status in the bad image controller.');
        }
       
        if ($queueName) {
            $this->serviceReportQueue->assign($queueName, $reportId, $userId);
        }

        $this->session->reportId = $reportId;
        $this->session->reportEntryFlow = [];
        $this->session->reportEntryFlow[$reportId] = $entryFlow;

        return $this->forward()->dispatch('Data\Controller\ReportEntryController', ['action' => 'edit']);
    }

    public function getUiInfoAction()
    {
        $this->view->setTerminal(true);
        $reportId = $this->params()->fromQuery('reportId');

        return $this->json->setVariables(['isNewApp' => $this->serviceReport->isNewApp($reportId)]);
    }
}
