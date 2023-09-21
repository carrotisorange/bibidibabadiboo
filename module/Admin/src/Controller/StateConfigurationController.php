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
use Base\Service\StateConfigurationService;
use Admin\Form\StateConfigurationForm;
use Base\Service\StateService;

class StateConfigurationController extends BaseController
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
     * @var Admin\Form\StateConfigurationForm
     */
    private $stateConfigurationForm;

    /**
     * @var Zend\Session\Container
     */
    private $session;
    
    /**
     * @var Base\Service\StateConfigurationService
     */
    private $serviceStateConfiguration;

    /**
     * @var Base\Service\StateService
     */
    private $serviceState;

    /**
     * Constructor will be invoked from the ViewKeyedImageControllerFactory
     * @param array  $config        Application configuration
     * @param object $logger        Zend\Log\Logger
     * @param object $session   Zend\Session\Container;
     * @param object $stateConfigurationForm   Admin\Form\StateConfigurationForm;
     * @param object $serviceStateConfiguration   Base\Service\StateConfigurationService;
     * @param object $serviceState   Base\Service\StateService;
     */
    public function __construct(
        Array $config,
        Logger $logger,
        Container $session,
        StateConfigurationForm $stateConfigurationForm,
        StateConfigurationService $serviceStateConfiguration,
        StateService $serviceState)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->stateConfigurationForm = $stateConfigurationForm;
        $this->session = $session;
        $this->serviceStateConfiguration = $serviceStateConfiguration;
        $this->serviceState = $serviceState;

        parent::__construct();
    }
    
    public function indexAction()
    {
        $this->layout()->setTemplate('layout/metrics');

        $inputParams = (array) $this->request->getPost();

        /* Start get query params during pagination */
        $page = $this->params()->fromQuery('page');
        $queryParams = $this->request->getQuery();
        $queryParams = (array) $queryParams;

        if (!empty($queryParams)) {
            $inputParams = $queryParams;
        }

        $stateList = $this->serviceState->getStateConfigurationList();

        $paginator = new Paginator(new Adapter\ArrayAdapter($stateList));
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($this->config['pagination']['perpage']);
        $paginatorParams = $this->escapePaginatorParams($inputParams);

        $this->view->title = 'State Configuration';
        $this->view->paginator = $paginator;
        $this->view->paginatorParams = $paginatorParams;

        return $this->view;
    }

    public function updateConfigurationAction()
    {
        $this->layout()->setTemplate('layout/metrics');
        $stateId = $this->params()->fromRoute('id');

        $this->stateConfigurationForm->setInputFilter($this->stateConfigurationForm->addInputFilters());
        
        /* Start get query params during pagination */
        $page = $this->params()->fromQuery('page');
        $queryParams = $this->request->getQuery();
        $queryParams = (array) $queryParams;

        if (!empty($queryParams)) {
            $inputParams = $queryParams;
        } else {
            $inputParams = (array) $this->request->getPost();
        }

        $this->stateConfigurationForm->setData($inputParams);

        $stateInfo = $this->serviceState->fetchStateIdNamePairs($stateId);

        $stateName = $stateInfo[$stateId];

        if ($this->request->isPost() && $this->stateConfigurationForm->isValid($inputParams)) {

            $workTypeValues = (empty($inputParams['workType'])) ? [] : $inputParams['workType'];
            $autoExtractionvalue = $inputParams['autoExtraction'];

            $this->serviceStateConfiguration->insertOrUpdateSetting($stateId, $autoExtractionvalue, $workTypeValues);

            $this->flashMessenger()->addSuccessMessage('Configuration updated successfully.');
        } else {
            $work_types = $this->serviceStateConfiguration->getWorkTypeIDPerState($stateId);
            $this->stateConfigurationForm->get('workType')->setValue(explode(',',$work_types));

            $autoExtraction = $this->serviceStateConfiguration->getAutoExtractionValue($stateId);
            $this->stateConfigurationForm->get('autoExtraction')->setValue($autoExtraction);

            $this->addFormMessages($this->stateConfigurationForm);
        }

        $this->view->stateId = $stateId;
        $this->view->form  = $this->stateConfigurationForm;
        $this->view->stateName = $stateName;

        return $this->view;
    }
}
