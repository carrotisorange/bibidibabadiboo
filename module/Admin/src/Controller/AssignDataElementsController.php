<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Controller;

use Zend\Log\Logger;
use Zend\Session\Container;
use Interop\Container\ContainerInterface;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter;
use Zend\View\Helper\HeadTitle;

use Base\View\Helper\DataRenderer\ReportMaker;
use Base\Service\AgencyService;
use Base\Service\FormFieldAttributeService;
use Base\Service\FormNoteService;
use Base\Controller\BaseController;
use Base\Service\FormService;
use Admin\Form\AssignDataElementsForm;
use Data\Form\ReportForm\FieldContainer;
use Data\Form\ReportForm\Form;


class AssignDataElementsController extends BaseController
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
     * @var Base\Service\FormService
     */
    private $serviceForm;
    
    /**
     * @var Admin\Form\AssignDataElementsForm
     */
    private $formAssignDataElements;

    /**
     * @var Base\View\Helper\ReportMaker
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
     * @var Base\Service\FormFieldAttributeService
     */
    private $serviceFormFieldAttribute;

     /**
     * @var Base\Service\FormNoteService
     */
     private $serviceFormNote;

    /**
     * @var Data\Form\ReportForm\FieldContainer
     */
    private $fieldContainer;

    /**
     * @var Data\Form\ReportForm\Form
     */
    private $formHandler;

    /**
     * @var Zend\View\Helper\HeadTitle
     */
    protected $helperHeadTitle;
    
    /**
     * Constructor will be invoked from the ViewKeyedImageControllerFactory
     * @param array  $config        Application configuration
     * @param object $logger        Zend\Log\Logger;
     * @param object $serviceUser   Admin\Form\ViewKeyedImageForm;
     * @param object $serviceService   Base\Service\FormService;
     * @param object $serviceAgency   Base\Service\AgencyService;
     * @param object $serviceFormFieldAttribute   Base\Service\FormFieldAttributeService;
     * @param object $container   Interop\Container\ContainerInterface;
     * @param object $serviceFormNote   Base\Service\FormnoteService;
     * @param object $fieldContainer   Data\Form\ReportForm\FieldContainer;
     * @param object $formHandler   Data\Form\ReportForm\Form;
     * @param object $helperHeadTitle   Zend\View\Helper\HeadTitle;
     */
    public function __construct(
        Array $config,
        Logger $logger,
        Container $session,
        AssignDataElementsForm $formAssignDataElements,
        FormService $serviceForm,
        ReportMaker $reportMaker,
        AgencyService $serviceAgency,
        FormFieldAttributeService $serviceFormFieldAttribute,
        ContainerInterface $container,
        FormNoteService $serviceFormNote,
        FieldContainer $fieldContainer,
        Form $formHandler,
        HeadTitle $helperHeadTitle)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->formAssignDataElements = $formAssignDataElements;
        $this->serviceForm = $serviceForm;
        $this->reportMaker = $reportMaker;
        $this->session = $session;
        $this->serviceAgency = $serviceAgency;
        $this->serviceFormFieldAttribute = $serviceFormFieldAttribute;
        $this->container = $container;
        $this->serviceFormNote = $serviceFormNote;
        $this->fieldContainer = $fieldContainer;
        $this->formHandler = $formHandler;

        parent::__construct();

        $this->view->helperHeadTitle = $helperHeadTitle;
    }
    
    public function indexAction()
    {
        $this->layout()->setTemplate('layout/metrics');
        $this->view->helperHeadTitle->append(' Assign "Data Elements" to "Forms"');
        $this->view->form = $this->formAssignDataElements;
        $this->view->format = ReportMaker::REPORT_FORMAT_XLS;
        
        return $this->view;
    }

    public function showNotesAction()
    {
        $this->layout()->setTemplate('layout/minimal');
        $formId = $this->params()->fromRoute('formId');

        $this->view->formId = $formId;
        $this->view->csrf = $this->getCsrfElement();
        $this->view->notes = $this->serviceFormNote->fetchNotes($formId);

        return $this->view;
    }

    public function addNoteAction()
    {
        $inputParams = (array) $this->request->getPost();
        $formId = $this->params()->fromQuery('formId');
        $stateId = $this->params()->fromQuery('stateId');

        if ($this->validateCsrfToken($inputParams['csrf'])) {
            $note = $inputParams['note'];
            $userId = !empty($this->identity()) ? $this->identity()->userId : null;
            $newNoteId = $this->serviceFormNote->insertNote($formId, $userId, $note);

            if ($newNoteId > 0) {
                $this->flashMessenger()->addSuccessMessage('Note saved successfully.');
            } else {
                $this->flashMessenger()->addErrorMessage('Note not saved.');
            }
        } else {
            $this->flashMessenger->addErrorMessage('Invalid token, note not saved.');
        }
        
        return $this->forward()->dispatch('Admin\Controller\AssignDataElementsController', 
            ['action' => 'show-notes', 'formId'=> $formId]
        );
    }

    public function saveChangesAction()
    {
        $inputParams = (array) $this->request->getPost();
        $this->formAssignDataElements->setInputFilter($this->formAssignDataElements->addInputFilters());
        $this->formAssignDataElements->setData($this->request->getPost());

        if (($this->request->isPost() && $this->formAssignDataElements->isValid($inputParams)))
        {
            $formId = $inputParams['formId'];

            if (!empty($formId)) {
                $formInfo = $this->serviceForm->getFormInfo($formId);

                $attributeGroupId = $formInfo['formFieldAttributeGroupId'];
                $note = $inputParams['note'];
                $userId = !empty($this->identity()) ? $this->identity()->userId : null;

                $newNoteId = $this->serviceFormNote->insertNote($formId, $userId, $note);

                $this->serviceFormFieldAttribute->updateAttributesByGroupId($attributeGroupId, $inputParams['element']);

                $this->serviceForm->updateAttributeGroup($formId, $attributeGroupId);

                $this->flashMessenger()->addSuccessMessage('Data Elements Saved Successfully.');
            }

        } else {
            $this->addFormMessages($this->formAssignDataElements);
        }
        
        return $this->redirect()->toRoute('assign-data-elements', [
            'controller' => 'AssignDataElementsController',
            'action' => 'index'
        ]);
    }

    public function fetchAgenciesJsonAction()
    {
        $response = '';
        
        if ($this->validateCsrfToken($this->params()->fromQuery('csrfToken'))) {
            $stateId = $this->params()->fromQuery('stateId');
            $response = $this->serviceAgency->fetchActiveAgencyIdNamePairs($stateId);
        } else {
            $response = $this->getInvalidCSRFJsonResponse();
        }

        return $this->json->setVariables($response);
    }

    public function fetchFormsJsonAction()
    {
        $request = $this->getRequest();
        $response = '';

        if ($this->validateCsrfToken($this->params()->fromQuery('csrfToken'))) {
            $stateId = $this->params()->fromQuery('stateId');
            $agencyId = $this->params()->fromQuery('agencyId');
            $response = $this->serviceForm->getFormIdNamePairs($stateId, $agencyId);
        } else {
            $response = $this->getInvalidCSRFJsonResponse();
        }

        return $this->json->setVariables($response);
    }

    public function fetchFormAttrsJsonAction() 
    {
        $response = '';

        if ($this->validateCsrfToken($this->params()->fromQuery('csrfToken'))) {
            $formId = $this->params()->fromQuery('formId');
            $this->session->adeFormId = $formId;
            $response = [
                    'workTypeAssigned' => "", //TODO - CRU Archive, CRU GoForward, eCrash",
                    'attributes' => $this->getFormFieldAttributes($formId)
                ];
        } else {
            $response = $this->getInvalidCSRFJsonResponse();
        }
        
        return $this->json->setVariables($response);
    }

    public function exportAction()
    {
        if (empty($this->session->adeFormId)) {
            exit(0);
        }
        $attrs = $this->getFormFieldAttributes($this->session->adeFormId);
        $paginator = new Paginator(new Adapter\ArrayAdapter($attrs));
        $reportHeader = [
            'reportName' => 'Assign Data Elements'
        ];
        $columns = [
            'id' => 'ID',
            'label' => 'Data Element',
            'available' => 'Available',
            'required' => 'Required',
            'skipped' => 'Skipped'
        ];
        $this->reportMaker->output(
            ReportMaker::REPORT_FORMAT_XLS,
            $paginator, $columns,
            [],
            $reportHeader,
            ['border centeralign']
        );
    }

    protected function getFormFieldAttributes($formId)
    {
        $formInfo = $this->serviceForm->getFormInfo($formId);
        $result = $this->formHandler->processForm($formInfo['formTemplate']);
        $fields = $this->fieldContainer->getFieldsFromCommon($formInfo['formFieldAttributeGroupId']);

        if (count($fields) > 0) {
            $formAttributesList = $this->serviceFormFieldAttribute->fetchByGroupId(
                $formInfo['formFieldAttributeGroupId']
            );
            $attrs = $this->getAttributeEntries($fields, $formAttributesList);
            return $attrs;
        }
    }

    protected function getAttributeEntries($fields, $formAttributesList)
    {
        $entries = [];
        foreach($formAttributesList as $attr) { 
            $entry = [];
            $entry['label'] = $attr['fieldName'];
            $entry['available'] = ($attr['isAvailable'] == 1);
            $entry['required'] = ($attr['isRequired'] == 1);
            $entry['skipped'] = ($attr['isSkipped'] == 1);
            $entries[] = $entry;
        }
        return $entries;
    }
    

}
