<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Data\Controller;

use Zend\Log\Logger;
use Zend\View\Renderer\PhpRenderer;
use Zend\Session\Container;
use Exception;

use Base\Controller\BaseController;
use Base\Service\UserService;
use Base\Service\WorkTypeService;
use Base\Service\ReportEntryService;
use Base\Service\ReportEntryQueueService;
use Base\Service\RekeyService;
use Base\Service\ReportService;
use Base\Service\EntryStageService;
use Base\Service\FormService;
use Base\Service\FormCodeGroupConfigurationService;
use Base\Service\FormCodeMapService;
use Base\Service\AgencyService;
use Base\Service\FormFieldAttributeService;
use Base\Service\ReportCruService;
use Base\Service\ReportQueueService;
use Base\Service\UserEntryPrefetchService;
use Base\Service\ReportFlagService;
use Base\Service\ReportStatusService;
use Base\Service\ReportNoteService;
use Base\Service\ImageServerService;
use Base\Service\UserAccuracyService;
use Base\Service\FormFieldService;
use Base\Service\AutoExtractionService;
use Base\Service\EntryStage\Handler\DynamicVerification;
use Base\Service\DataTransformerService;
use Base\Service\AutoExtractionAccuracyService;
use Base\View\Helper\DataRenderer\ReportMaker;
use Data\Form\WorkTypeSelectionForm;
use Data\Form\ReportForm\FormContainer\Universal;
use Data\Form\ReportForm\FormModifier;
use Data\Form\ReportForm\FieldContainer;

class ReportEntryController extends BaseController
{
    /**
     * @var Array
     */
    protected $config;

    /**
     * @var Zend\Session\Container
     */
    protected $session;
    
    /**
     * @var Zend\Log\Logger
     */
    private $logger;
    
    /**
     * @var Zend\View\Renderer\PhpRenderer
     */
    protected $serviceViewRenderer;
    
    /**
     * @var Data\Form\WorkTypeSelectionForm
     */
    private $formWorkTypeSelection;

    /**
     * @var Base\Service\WorkTypeService
     */
    protected $serviceUser;
    
    /**
     * @var Base\Service\WorkTypeService
     */
    protected $serviceWorkType;

    /**
     * @var Base\Service\ReportEntryService
     */
    protected $serviceReportEntry;

    /**
     * @var Base\Service\ReportEntryQueueService
     */
    protected $serviceReportEntryQueue;

    /**
     * @var Base\Service\ReportService
     */
    protected $serviceReport;

    /**
     * @var Base\Service\EntryStageService
     */
    protected $serviceEntryStage;

    /**
     * @var Base\Service\FormService
     */
    protected $serviceForm;

    /**
     * @var Base\Service\FormCodeGroupConfigurationService
     */
    protected $serviceFormCodeGroupConfiguration;

    /**
     * @var Base\Service\FormCodeMapService
     */
    protected $serviceFormCodeMap;

    /**
     * @var Base\Service\AgencyService
     */
    protected $serviceAgency;
    
    /**
     * @var Base\Service\FormFieldAttributeService
     */
    protected $serviceRekey;

    /**
     * @var Base\Service\RekeyService
     */
    protected $serviceFormFieldAttribute;

    /**
     * @var Base\Service\ReportCruService
     */
    protected $serviceReportCru;

    /**
     * @var Base\Service\ReportQueueService
     */
    protected $serviceReportQueue;

    /**
     * @var Base\Service\UserEntryPrefetchService
     */
    protected $serviceUserEntryPrefetch;

    /**
     * @var Base\Service\ReportFlagService
     */
    protected $serviceReportFlag;

    /**
     * @var Base\Service\ReportStatusService
     */
    protected $serviceReportStatus;

    /**
     * @var Base\Service\ReportNoteService
     */
    protected $serviceReportNote;
    
    /**
     * @var Base\Service\ImageServerService
     */
    protected $serviceImageServer;
    
    /**
     * @var Base\Service\UserAccuracyService
     */
    protected $serviceUserAccuracy;

    /**
     * @var Base\Service\FormFieldService
     */
    protected $serviceFormField;

    /**
     * @var Base\Service\DataTransformerService
     */
    protected $serviceDataTransformer;

        /**
     * @var Base\View\Helper\ReportMaker
     */
    private $reportMaker;
    
    /**
     * @var Base\Service\AutoExtractionService
     */
    protected $serviceAutoExtraction;

     /**
     * @var Base\Service\AutoExtractionAccuracyService
     */
    protected $serviceAutoExtractionAccuracy;

    public function __construct(
        Array $config,
        Container $session,
        Logger $logger,
        PhpRenderer $serviceViewRenderer,
        WorkTypeSelectionForm $formWorkTypeSelection,
        UserService $serviceUser,
        WorkTypeService $serviceWorkType,
        ReportEntryService $serviceReportEntry,
        ReportEntryQueueService $serviceReportEntryQueue,
        ReportService $serviceReport,
        EntryStageService $serviceEntryStage,
        FormService $serviceForm,
        FormCodeGroupConfigurationService $serviceFormCodeGroupConfiguration,
        FormCodeMapService $serviceFormCodeMap,
        AgencyService $serviceAgency,
        RekeyService $serviceRekey,
        FormFieldAttributeService $serviceFormFieldAttribute,
        ReportCruService $serviceReportCru,
        ReportQueueService $serviceReportQueue,
        UserEntryPrefetchService $serviceUserEntryPrefetch,
        ReportFlagService $serviceReportFlag,
        ReportStatusService $serviceReportStatus,
        ReportNoteService $serviceReportNote,
        ImageServerService $serviceImageServer,
        FormFieldService $serviceFormField,
        UserAccuracyService $serviceUserAccuracy,
        AutoExtractionService $serviceAutoExtraction,
        DataTransformerService $serviceDataTransformer,
        AutoExtractionAccuracyService $serviceAutoExtractionAccuracy,
        ReportMaker $reportMaker,
        PhpRenderer $renderer)
    {
        $this->config = $config;
        $this->session = $session;
        $this->logger = $logger;
        $this->formWorkTypeSelection = $formWorkTypeSelection;
        $this->serviceViewRenderer = $serviceViewRenderer;
        $this->serviceUser = $serviceUser;
        $this->serviceWorkType = $serviceWorkType;
        $this->serviceReportEntry = $serviceReportEntry;
        $this->serviceReportEntryQueue = $serviceReportEntryQueue;
        $this->serviceReport = $serviceReport;
        $this->serviceEntryStage = $serviceEntryStage;
        $this->serviceForm = $serviceForm;
        $this->serviceFormCodeGroupConfiguration = $serviceFormCodeGroupConfiguration;
        $this->serviceFormCodeMap = $serviceFormCodeMap;
        $this->serviceAgency = $serviceAgency;
        $this->serviceRekey = $serviceRekey;
        $this->serviceFormFieldAttribute = $serviceFormFieldAttribute;
        $this->serviceReportCru = $serviceReportCru;
        $this->serviceReportQueue = $serviceReportQueue;
        $this->serviceUserEntryPrefetch = $serviceUserEntryPrefetch;
        $this->serviceReportFlag = $serviceReportFlag;
        $this->serviceReportStatus = $serviceReportStatus;
        $this->serviceReportNote = $serviceReportNote;
        $this->serviceImageServer = $serviceImageServer;
        $this->serviceFormField = $serviceFormField;
        $this->serviceUserAccuracy = $serviceUserAccuracy;
        $this->serviceAutoExtraction = $serviceAutoExtraction;
        $this->serviceDataTransformer = $serviceDataTransformer;
        $this->serviceAutoExtractionAccuracy = $serviceAutoExtractionAccuracy;
        $this->reportMaker = $reportMaker;
        $this->renderer = $renderer;

        parent::__construct();
    }

    public function indexAction()
    {
        return $this->forward()->dispatch(ReportEntryController::class, ['action' => 'select-work-type']);
    }
    
    /**
     * Allows the user to select a work type to be worked, or auto-loads if only one available.
     */
    public function selectWorkTypeAction()
    {
        $this->session->rekey = null;
        $userId = !empty($this->identity()) ? $this->identity()->userId : null;
        $this->formWorkTypeSelection->setData($this->request->getPost());

        $availableWorkTypes = $this->serviceWorkType->getAllowedByUser($userId);
        $this->formWorkTypeSelection->get('workType')->setValueOptions(
            ['' => 'Select a Work Type'] + $availableWorkTypes
        );

        //dropdown depends on what user can do
        $keyingType = [RekeyService::NO_ADDITIONAL_KEYING => RekeyService::ADD_KEY_STR_NONE];
        if ($this->serviceUser->ifUserSetUpForRekey($userId) == 1) {
            $keyingType[RekeyService::PAPER_ADDITIONAL_KEYING] = RekeyService::ADD_KEY_STR_PAPER;
        }

        if ($this->serviceUser->ifUserSetUpForElectronicRekey($userId) == 1) {
            $keyingType[RekeyService::ELECTRONIC_ADDITIONAL_KEYING] = RekeyService::ADD_KEY_STR_ELECTRONIC;
        }
        
        $this->formWorkTypeSelection->get('addKeying')->setValueOptions(
            ['' => 'Select Additional Keying Type'] + $keyingType
        );

        $selectedWorkTypeId = false;
        if (empty($availableWorkTypes)) {
            $this->flashMessenger()->addInfoMessage('No work types have been assigned.');
        } elseif ($this->request->isPost()) {
            if ($this->formWorkTypeSelection->isValid()) {
                $selectedWorkTypeId = $this->request->getPost('workType');
                $keyingType = $this->request->getPost('addKeying');
                $this->session->rekey = $keyingType;
            } else {
                $this->flashMessenger()->addInfoMessage('Please select a valid work type and a valid additional keying type.');
            }
        } elseif (count($availableWorkTypes) == 1 && ($this->serviceUser->ifUserSetUpForRekey($userId) == 0)
            && ($this->serviceUser->ifUserSetUpForElectronicRekey($userId) == 0)) {
            // Only default the selection if the display parameter wasn't passed.
            reset($availableWorkTypes);
            if ($this->params()->fromRoute('display', 1) == 1) {
                $selectedWorkTypeId = key($availableWorkTypes);
            } else {
                $this->formWorkTypeSelection->get('workType')->setValue(key($availableWorkTypes));
            }
        }

        if (!empty($selectedWorkTypeId)) {
            $this->serviceReportEntry->revertInProgress($userId);
            $keyingVendorId = (!empty($this->identity()) && !empty($this->identity()->keyingVendorId)) ? 
                    $this->identity()->keyingVendorId : $this->serviceUser->getKeyingVendorIdByUserId($userId);
            
            $reportId = $this->getReportIdWithValidation();
            // $reportId = $this->serviceReportEntryQueue->pull($userId, $selectedWorkTypeId, $keyingVendorId, $this->session->rekey);
            
            if ($reportId) {
                if ($this->serviceReport->isNewApp($reportId)) {
                    $this->session->showReportEntryPopup = true;
                    $this->session->reportEntryFlow = [];
                    $this->session->reportEntryFlow[$reportId] = ReportService::ENTRY_FLOW_ENTRY;
                    $this->session->selectedWorkTypeId = $selectedWorkTypeId;
                    $this->session->reportId = $reportId;

                return $this->redirect()->toUrl('report-entry/edit-one-window');
                } else {
                    $this->cleanup();
                    $this->flashMessenger()->addInfoMessage('Please use Old App for this report, '.$reportId);
                }
            } else {
                $this->flashMessenger()->addInfoMessage('No items available to be worked.');
            }
        }
        $this->view->form = $this->formWorkTypeSelection;

        return $this->view;
    }

    public function editOneWindowAction() {
        return $this->view;
    }

    public function editAction()
    {
        $this->layout()->setTemplate('layout/minimal');
        // $this->imageViewData();
        // $this->_displayData();
        return $this->view;
    }

    private function _displayData() {
        $userId = !empty($this->identity()) ? $this->identity()->userId : null;
        $reportId = $this->getReportIdWithValidation();
        $entryFlow = $this->getReportEntryFlow($reportId);
        $reportEntryId = null;
        $entryStage = null;
        $agency = null;
        $hasNotes = 0;
        $hasAutoExtracted = 0;
        $hasAutoKeyed = 0;
        $showAutoExtractionAlert = 0;
        
        $alternativeFormId = $this->params()->fromQuery('alternativeFormId');

        $entryStages = $this->serviceEntryStage->getInternalNamePairs();
        $rowReportInfo = $this->getReportData($entryFlow, $reportId, $this->serviceReport, $this->serviceReportEntryQueue);

        $formId = empty($alternativeFormId) ? $rowReportInfo['formId'] : $alternativeFormId;
        
        //checks whether autoextraction enabled for the state corresponding to the report
        $autoExtractionEnabledForState = $this->serviceReport->isAutoExtractionEnabledForState($reportId);
        
        //checks whether data extracted from ML
        $hasAutoExtracted = $this->serviceAutoExtraction->hasAutoExtracted($reportId);

        if (!empty($rowReportInfo['agencyId'])) {
            $agency = $this->serviceAgency->getAgencyByAgencyId($rowReportInfo['agencyId']);
            if (!empty($agency['keying_rpt_nbr_fmt'])) {
                $this->view->reportNumFormat = $agency['keying_rpt_nbr_fmt'];
            }
        }
        
        if ($entryFlow != ReportService::ENTRY_FLOW_VIEW) {
            $hasNotes = $this->serviceReport->hasNotes($reportId);
        }

        switch ($entryFlow)
        {
            case ReportService::ENTRY_FLOW_VIEW: 
                if (empty($alternativeFormId) && $rowReportInfo['oldFormId'] != $rowReportInfo['newFormId']) {
                    $formId = $rowReportInfo['oldFormId'];
                }
                if (!empty($this->session->reportEntryId[$reportId])) {
                    $reportEntryId = $this->session->reportEntryId[$reportId];
                    $reportEntryInfo = $this->serviceReportEntry->getReportEntryInfo($reportEntryId);
                    $formId = $reportEntryInfo['formId'];
                    $this->view->additionalFooterData = $this->getAdditionalFooterData($reportEntryInfo);
                    $this->view->refreshParentOnSaveAction = true;

                    if ($this->serviceReportEntry->getMaxCompletedId($reportId) == $reportEntryId) {
                        $entryStage = EntryStageService::STAGE_EDIT;
                    } else {
                        $entryStage = EntryStageService::STAGE_NONE;
                    }
                } else {
                    $entryStage = EntryStageService::STAGE_EDIT;
                }
                break;

            case ReportService::ENTRY_FLOW_BAD:
                $entryStage = EntryStageService::STAGE_BAD;
                break;

            case ReportService::ENTRY_FLOW_DISCARD:
                $entryStage = EntryStageService::STAGE_NONE;
                break;

            case ReportService::ENTRY_FLOW_DEAD:
                $entryStage = EntryStageService::STAGE_NONE;
                break;

            case ReportService::ENTRY_FLOW_ENTRY:
                $entryStage = empty($rowReportInfo['entryStageId']) ? null : $entryStages[$rowReportInfo['entryStageId']];
                break;

            default:
                throw new Exception('Unknown report entry flow given : ' . $entryFlow);
        }

        if (empty($this->session->reportData[$reportId]) 
                && $entryStage != EntryStageService::STAGE_NONE) {

            if ($entryFlow == ReportService::ENTRY_FLOW_ENTRY) {
                // Entries are always the next pass after the last completed one.
                $passNumber = $this->serviceReportEntry->getMaxCompletedPass($reportId) + 1;

                if ($this->config['autoExtractionEnabled'] == 1 && $autoExtractionEnabledForState == 1 && $entryStage == EntryStageService::STAGE_ALL) {
                    if ($hasAutoExtracted == 1) {
                        $hasAutoKeyed = 1;
                    } else {
                        $showAutoExtractionAlert = 1;
                    }
                }
            } elseif ($entryFlow == ReportService::ENTRY_FLOW_BAD) {
                // Bad image is ALWAYS the final pass
                $passNumber = $this->serviceReportEntry->getMaxPotentialPassNumber(
                    $rowReportInfo['entryStageProcessGroupId']
                );

            } elseif ($entryFlow == ReportService::ENTRY_FLOW_VIEW) {
                // View takes the same information as the last pass.
                $passNumber = $this->serviceReportEntry->getMaxCompletedPass($reportId);
            }
            if (empty($alternativeFormId)) {
                $entrystageId = array_search($entryStage, $entryStages);
                $reportEntryId = $this->serviceReportEntry->checkInprogressReport($reportId, $entrystageId);
                if(empty($reportEntryId)) {
                     $this->serviceReportEntry->add($reportId, $formId, $userId, $entrystageId, $passNumber);
                }
            } else {
                $this->serviceReportEntry->updateFormIdByReportId($formId, $reportId);
            }
        }

        if (in_array($entryFlow,
                        [
                            ReportService::ENTRY_FLOW_BAD,
                            ReportService::ENTRY_FLOW_DISCARD,
                            ReportService::ENTRY_FLOW_DEAD
                        ]) && $this->serviceRekey->isQueuedForRekey($reportId)) {

            $entryStage = EntryStageService::STAGE_REKEY;
        }
        
        $formContainer = $this->getFormContainer(
            $formId, $reportId, $reportEntryId, $entryStage, $rowReportInfo['isObsolete']
        );

        $pageData = $formContainer->getPageData();
        $renderButtons = $formContainer->getButtons($entryFlow);
        $reportCruResult = $this->serviceReportCru->getCruData($rowReportInfo['reportId']);

        if ($reportCruResult) {
            $this->view->cruOrderId = $reportCruResult['cru_order_id'];
            $this->view->cruSequenceNbr = $reportCruResult['cru_sequence_nbr'];
        }
		
		// unset the clear button if the autoextraction is disabled or Ml process has not completed.
		if($this->config['autoExtractionEnabled'] == 0 || $autoExtractionEnabledForState == 0 || $entryStage != EntryStageService::STAGE_ALL || $showAutoExtractionAlert == 1) {
            if(array_key_exists('clear', $renderButtons)) {
                unset($renderButtons['clear']);
            }
        }
        
        $this->view->csrf = $this->getCsrfElement();
        $this->view->renderButtons = $renderButtons;
        $this->view->reportId = $reportId;
        $this->view->formName = $formContainer->getFormNameExternal();
        $this->view->formInternalName = $formContainer->getFormNameInternal();
        $this->view->agencyName = (empty($agency)) ? '' : $agency['name'];
        $this->view->pageData = $pageData;
        $this->view->entryFlow = $entryFlow;
        $this->view->hasNotes = $hasNotes;
        $this->view->alternativeFormId = $alternativeFormId;
        $this->view->entryStage = $entryStage;
        $this->view->showAutoExtractionAlert = $showAutoExtractionAlert;

        if ($entryStage == EntryStageService::STAGE_ALL) {
            $this->view->hasAutoExtracted = $hasAutoExtracted;
            $this->view->hasAutoKeyed = $hasAutoKeyed;
        }
        
        if ($rowReportInfo['isObsolete']) {
            $this->view->reportIsObsolete = true;
            $this->view->reportObsoletedBy = $rowReportInfo['reportIdObsoletedBy'];
            $this->view->readOnlyForm = 1;
        }

        $this->view->updated = in_array('updated', $rowReportInfo['flags']);
        $this->view->formMismatch = in_array('form mismatch', $rowReportInfo['flags']);

        if ($entryFlow == ReportService::ENTRY_FLOW_VIEW) {
            $renderButtons = $this->view->renderButtons;
            if ($rowReportInfo['oldFormId'] != $rowReportInfo['newFormId']) {
                $renderButtons['save'] = null;
            } else {
                $renderButtons['save'] = 1;
            }
            $this->view->renderButtons = $renderButtons;
        }

        $this->view->coordinates = $this->serviceAutoExtraction->getCoordinates($reportId);
        return $this->view;
    }
    
    /**
     * Loads and initializes the form edit process for the active report.
     */
    public function displayAction()
    {
        $this->layout()->setTemplate('layout/minimal');
        $userId = !empty($this->identity()) ? $this->identity()->userId : null;
        $reportId = $this->getReportIdWithValidation();
        $entryFlow = $this->getReportEntryFlow($reportId);
        $reportEntryId = null;
        $entryStage = null;
        $agency = null;
        $hasNotes = 0;
        $hasAutoExtracted = 0;
        $hasAutoKeyed = 0;
        $showAutoExtractionAlert = 0;
        
        $alternativeFormId = $this->params()->fromQuery('alternativeFormId');

        $entryStages = $this->serviceEntryStage->getInternalNamePairs();
        $rowReportInfo = $this->getReportData($entryFlow, $reportId, $this->serviceReport, $this->serviceReportEntryQueue);

        $formId = empty($alternativeFormId) ? $rowReportInfo['formId'] : $alternativeFormId;
        
        //checks whether autoextraction enabled for the state corresponding to the report
        $autoExtractionEnabledForState = $this->serviceReport->isAutoExtractionEnabledForState($reportId);
        
        //checks whether data extracted from ML
        $hasAutoExtracted = $this->serviceAutoExtraction->hasAutoExtracted($reportId);

        if (!empty($rowReportInfo['agencyId'])) {
            $agency = $this->serviceAgency->getAgencyByAgencyId($rowReportInfo['agencyId']);
            if (!empty($agency['keying_rpt_nbr_fmt'])) {
                $this->view->reportNumFormat = $agency['keying_rpt_nbr_fmt'];
            }
        }
        //if has auto extracted data
        $this->view->coordinates = $this->serviceAutoExtraction->getCoordinates($reportId);
        $res = $this->serviceAutoExtraction->getCoordinates($reportId);
        
        if ($entryFlow != ReportService::ENTRY_FLOW_VIEW) {
            $hasNotes = $this->serviceReport->hasNotes($reportId);
        }

        switch ($entryFlow)
        {
            case ReportService::ENTRY_FLOW_VIEW: 
                if (empty($alternativeFormId) && $rowReportInfo['oldFormId'] != $rowReportInfo['newFormId']) {
                    $formId = $rowReportInfo['oldFormId'];
                }
                if (!empty($this->session->reportEntryId[$reportId])) {
                    $reportEntryId = $this->session->reportEntryId[$reportId];
                    $reportEntryInfo = $this->serviceReportEntry->getReportEntryInfo($reportEntryId);
                    $formId = $reportEntryInfo['formId'];
                    $this->view->additionalFooterData = $this->getAdditionalFooterData($reportEntryInfo);
                    $this->view->refreshParentOnSaveAction = true;

                    if ($this->serviceReportEntry->getMaxCompletedId($reportId) == $reportEntryId) {
                        $entryStage = EntryStageService::STAGE_EDIT;
                    } else {
                        $entryStage = EntryStageService::STAGE_NONE;
                    }
                } else {
                    $entryStage = EntryStageService::STAGE_EDIT;
                }
                break;

            case ReportService::ENTRY_FLOW_BAD:
                $entryStage = EntryStageService::STAGE_BAD;
                break;

            case ReportService::ENTRY_FLOW_DISCARD:
                $entryStage = EntryStageService::STAGE_NONE;
                break;

            case ReportService::ENTRY_FLOW_DEAD:
                $entryStage = EntryStageService::STAGE_NONE;
                break;

            case ReportService::ENTRY_FLOW_ENTRY:
                $entryStage = empty($rowReportInfo['entryStageId']) ? null : $entryStages[$rowReportInfo['entryStageId']];
                break;

            default:
                throw new Exception('Unknown report entry flow given : ' . $entryFlow);
        }

        if (empty($this->session->reportData[$reportId]) 
                && $entryStage != EntryStageService::STAGE_NONE) {

            if ($entryFlow == ReportService::ENTRY_FLOW_ENTRY) {
                // Entries are always the next pass after the last completed one.
                $passNumber = $this->serviceReportEntry->getMaxCompletedPass($reportId) + 1;

                if ($this->config['autoExtractionEnabled'] == 1 && $autoExtractionEnabledForState == 1 && $entryStage == EntryStageService::STAGE_ALL) {
                    if ($hasAutoExtracted == 1) {
                        $hasAutoKeyed = 1;
                    } else {
                        $showAutoExtractionAlert = 1;
                    }
                }
            } elseif ($entryFlow == ReportService::ENTRY_FLOW_BAD) {
                // Bad image is ALWAYS the final pass
                $passNumber = $this->serviceReportEntry->getMaxPotentialPassNumber(
                    $rowReportInfo['entryStageProcessGroupId']
                );

            } elseif ($entryFlow == ReportService::ENTRY_FLOW_VIEW) {
                // View takes the same information as the last pass.
                $passNumber = $this->serviceReportEntry->getMaxCompletedPass($reportId);
            }
            if (empty($alternativeFormId)) {
                $entrystageId = array_search($entryStage, $entryStages);
                $reportEntryId = $this->serviceReportEntry->checkInprogressReport($reportId, $entrystageId);
                if(empty($reportEntryId)) {
                     $this->serviceReportEntry->add($reportId, $formId, $userId, $entrystageId, $passNumber);
                }
            } else {
                $this->serviceReportEntry->updateFormIdByReportId($formId, $reportId);
            }
        }

        if (in_array($entryFlow,
                        [
                            ReportService::ENTRY_FLOW_BAD,
                            ReportService::ENTRY_FLOW_DISCARD,
                            ReportService::ENTRY_FLOW_DEAD
                        ]) && $this->serviceRekey->isQueuedForRekey($reportId)) {

            $entryStage = EntryStageService::STAGE_REKEY;
        }

        $formContainer = $this->getFormContainer(
            $formId, $reportId, $reportEntryId, $entryStage, $rowReportInfo['isObsolete']
        );

        $pageData = $formContainer->getPageData();
        $renderButtons = $formContainer->getButtons($entryFlow);
        $reportCruResult = $this->serviceReportCru->getCruData($rowReportInfo['reportId']);

        if ($reportCruResult) {
            $this->view->cruOrderId = $reportCruResult['cru_order_id'];
            $this->view->cruSequenceNbr = $reportCruResult['cru_sequence_nbr'];
        }
		
		// unset the clear button if the autoextraction is disabled or Ml process has not completed.
		if($this->config['autoExtractionEnabled'] == 0 || $autoExtractionEnabledForState == 0 || $entryStage != EntryStageService::STAGE_ALL || $showAutoExtractionAlert == 1) {
            if(array_key_exists('clear', $renderButtons)) {
                unset($renderButtons['clear']);
            }
        }
        
        $this->view->csrf = $this->getCsrfElement();
        $this->view->renderButtons = $renderButtons;
        $this->view->reportId = $reportId;
        $this->view->formName = $formContainer->getFormNameExternal();
        $this->view->formInternalName = $formContainer->getFormNameInternal();
        $this->view->agencyName = (empty($agency)) ? '' : $agency['name'];
        $this->view->pageData = $pageData;
        $this->view->entryFlow = $entryFlow;
        $this->view->hasNotes = $hasNotes;
        $this->view->alternativeFormId = $alternativeFormId;
        $this->view->entryStage = $entryStage;
        $this->view->showAutoExtractionAlert = $showAutoExtractionAlert;

        if ($entryStage == EntryStageService::STAGE_ALL) {
            $this->view->hasAutoExtracted = $hasAutoExtracted;
            $this->view->hasAutoKeyed = $hasAutoKeyed;
        }
        
        if ($rowReportInfo['isObsolete']) {
            $this->view->reportIsObsolete = true;
            $this->view->reportObsoletedBy = $rowReportInfo['reportIdObsoletedBy'];
            $this->view->readOnlyForm = 1;
        }

        $this->view->updated = in_array('updated', $rowReportInfo['flags']);
        $this->view->formMismatch = in_array('form mismatch', $rowReportInfo['flags']);

        if ($entryFlow == ReportService::ENTRY_FLOW_VIEW) {
            $renderButtons = $this->view->renderButtons;
            if ($rowReportInfo['oldFormId'] != $rowReportInfo['newFormId']) {
                $renderButtons['save'] = null;
            } else {
                $renderButtons['save'] = 1;
            }
            $this->view->renderButtons = $renderButtons;
        }

        return $this->view;
    }

    /**
     * Gets the appropriate form container for interacting with the various forms in a common way
     *
     * @param int $formId
     * @param int $reportId
     * @param int $reportEntryId
     * @param string $entryStage
     * @param bool $isObsolete
     * @return Data\Form\ReportForm\FormContainer
     */
    function getFormContainer(
    $formId, $reportId, $reportEntryId, $entryStage, $isObsolete)
    {
        $template = $this->serviceForm->getTemplateNameInternal($formId);
        $formContainer = null;
        
        switch ($template) {
            // @TODO: Will be removed once the new form is created.
            //case 'silverlight':
            case FormService::TEMPLATE_UNIVERSAL:
            case FormService::SYSTEM_UNIVERSAL:
                $formContainer = new Universal(
                    $formId,
                    $reportId,
                    $reportEntryId,
                    $entryStage,
                    $isObsolete,
                    $this->serviceForm,
                    $this->session,
                    $this->serviceReport,
                    $this->serviceEntryStage,
                    $this->serviceFormFieldAttribute,
                    $this->serviceAutoExtraction,
                    new FormModifier(),
                    new FieldContainer($this->logger),
                    $this->config,
                    $this->serviceReportEntry,
                    new DynamicVerification(
                        new FormModifier(),
                        $this->serviceReportEntry
                    )
                );
                break;
            default:
                throw new Exception('Unknown template "' . $template . '" for formId ' . $formId);
                break;
        }

        return $formContainer;
    }

    /**
     * Gets additional form footer data specific to an entry.
     *
     * @param array $reportEntry
     * @return array
     */
    protected function getAdditionalFooterData(array $reportEntry)
    {
        $additionalFooterData = [];

        if (!empty($reportEntry)) {
            $additionalFooterData['completedDate'] = $reportEntry['dateUpdated'];
            $additionalFooterData['userLoginId'] = $reportEntry['userName'];
            $additionalFooterData['userFullName'] = $reportEntry['nameFirst'] . ' ' . $reportEntry['nameLast'];
            $additionalFooterData['PassExternalName'] = $reportEntry['passName'];

            if (isset($reportEntry['countEdits'])) {
                $additionalFooterData['PassExternalName'] .= $reportEntry['countEdits'];
            }
        }

        return $additionalFooterData;
    }

    /**
     * Validates and saves form input data for a report.
     * This will also load the next available report.
     */
    public function saveAction()
    {
        $userId = !empty($this->identity()) ? $this->identity()->userId : null;
        $reportId = $this->getReportIdWithValidation();
        $entryFlow = $this->getReportEntryFlow($reportId);
        $reportData = $this->params()->fromPost();
        $entryStage = $reportData['entryStage'];
        
        if ($this->validateCsrfToken($this->request->getPost('csrf'))) {
            if (isset($reportData['element16_field'])) {
                unset($reportData['element16_field']);
            }

            if (isset($reportData['imageHandwritten']) && $reportData['imageHandwritten']) {
                $this->serviceAutoExtraction->updateHandwrittenReport($reportId);
                unset($reportData['imageHandwritten']);
            }
            
            if (isset($reportData['checkUpdated']) && $reportData['checkUpdated']) {
                $this->serviceReportFlag->add($reportId, ReportFlagService::FLAG_UPDATED, $userId);
                unset($reportData['checkUpdated']);
            } else {
                $this->serviceReportFlag->remove($reportId, ReportFlagService::FLAG_UPDATED, $userId);
            }
            if (isset($reportData['checkFormMismatch']) && $reportData['checkFormMismatch']) {
                $this->serviceReportFlag->add($reportId, ReportFlagService::FLAG_FORM_MISMATCH, $userId);
                unset($reportData['checkFormMismatch']);
            } else {
                $this->serviceReportFlag->remove($reportId, ReportFlagService::FLAG_FORM_MISMATCH, $userId);
            }
            
            $this->logger->log(Logger::DEBUG, 'Saving report. [Report Id: ' . $reportId . ']');

            if ($reportData['reportId'] != $reportId) {
                throw new Exception('Input reportId ('
                    . $reportData['reportId']
                    . ') does not match session reportId ('
                    . $reportId
                    . ')'
                );
            }
            if (!empty($reportData['alternativeFormId'])) {
                $this->serviceReport->updateForm($reportData['alternativeFormId'], $reportId);
            }
            
            $reportEntryId = $this->serviceReportEntry->getLastIdByReportAndUser($reportId, $userId);

            if ($entryStage == EntryStageService::STAGE_ALL) {
                $hasAutoExtracted = $reportData['hasAutoExtracted'];
                $hasAutoKeyed = $reportData['hasAutoKeyed'];
                $this->serviceReport->updateReportKeyingType($reportId, $hasAutoExtracted, $hasAutoKeyed);
            }
            $oldMemoryLimit = ini_get('memory_limit');
            ini_set( 'memory_limit', '2048M' );
            $this->serviceReportEntry->insertOrUpdateData($reportId, $reportEntryId, $reportData);
            $isReportEntryComplete = $this->serviceReportEntry->complete($reportEntryId);

            if (!$isReportEntryComplete) {
                $this->logger->log(Logger::DEBUG, "[Report Id: $reportId][User Id: $userId] - Updating report entry id $reportEntryId as 'complete' failed");
            }
            
            $this->cleanCurrentReportSession();            
            ini_set( 'memory_limit', $oldMemoryLimit );            
            $this->moveReportToNextStage($entryFlow, $reportId, $reportEntryId, $reportData);
            $this->view->redirectParentToExit = ($entryFlow == ReportService::ENTRY_FLOW_ENTRY);

            //remove record from user report table after keying
            $this->serviceReportEntryQueue->removeUserReportRecord($reportId, $userId);
            $this->pullNextReport($entryFlow, $reportId);
        } else {
            $this->serviceReportEntry->revertInProgress($userId, $reportId);
            $this->flashMessenger()->addInfoMessage('Tokens did not match.');

            return $this->redirect()->toRoute('index');
        }
    }

    /**
     * Takes care of advancing reports through the various flow stages
     *
     * @param string $entryFlow - ReportService::ENTRY_FLOW_*
     * @param int $reportId
     * @param mixed $reportData
     * @return void
     */
    protected function moveReportToNextStage($entryFlow, $reportId, $entryId, $reportData)
    {
        $hasNextStage = null;
        $userId = !empty($this->identity()) ? $this->identity()->userId : null;
        if ($entryFlow == ReportService::ENTRY_FLOW_ENTRY) {
            $hasNextStage = $this->serviceReportEntryQueue->moveToNextStage($reportId);
            //unassign the assingned report to user in report entry queue
            $this->serviceReportEntryQueue->unAssignReport($userId, $reportId);
        } elseif ($entryFlow == ReportService::ENTRY_FLOW_BAD) {
            $this->serviceReportQueue->remove(
                ReportQueueService::QUEUE_BAD_IMAGE, $reportId, ReportQueueService::REMOVAL_REASON_REMOVED
            );
            $hasNextStage = false;
        }
        
        if (!$hasNextStage) {
            $this->serviceReportStatus->set(
                $reportId, ReportStatusService::STATUS_COMPLETE, $userId
            );
            
            // User Keying Accuracy
            $this->serviceUserAccuracy->createMetricData($reportId);
            
             // Auto Extraction Keying Accuracy
            $this->serviceAutoExtractionAccuracy->createAutoExtractionAccuracyMetricData($reportId);
        }
    }

    /**
     * Takes care of pulling the next report to be keyed (if necessary)
     *
     * @param string $entryFlow - ReportService::ENTRY_FLOW_*
     * @param int $reportId
     * @return void
     */
    protected function pullNextReport($entryFlow, $reportId)
    {
        $pullFromQueue = null;

        switch ($entryFlow)
        {
            case ReportService::ENTRY_FLOW_VIEW:
            case ReportService::ENTRY_FLOW_DEAD:
                // Dead & View do not need to pull anything
                break;

            case ReportService::ENTRY_FLOW_BAD:
                $pullFromQueue = ReportQueueService::QUEUE_BAD_IMAGE;
                break;

            case ReportService::ENTRY_FLOW_DISCARD:
                $pullFromQueue = ReportQueueService::QUEUE_DISCARDED;
                break;

            case ReportService::ENTRY_FLOW_ENTRY:
                $this->pullNextPrefetchReport();
                break;

            default:
                throw new Exception('Unknown report entry flow given: ' . $entryFlow);
        }

        if (!empty($pullFromQueue)) {
            $this->serviceReportQueue->remove($pullFromQueue, $reportId, ReportQueueService::REMOVAL_REASON_REMOVED);
            $userId = !empty($this->identity()) ? $this->identity()->userId : null;
            $keyingVendorId = (!empty($this->identity()) && !empty($this->identity()->keyingVendorId)) ? 
                    $this->identity()->keyingVendorId : $this->serviceUser->getKeyingVendorIdByUserId($userId);
            $this->setNextReportId(
                $this->serviceReportQueue->pull($pullFromQueue, $userId, $keyingVendorId), $entryFlow
            );
        }
    }

    public function exitAction()
    {
        $this->cleanup();
        
        if ($this->request->isXmlHttpRequest()) {
            exit;
        } else {
            return $this->redirect()->toRoute('report-entry', ['action' => 'select-work-type', 'display' => 0]);
        }
    }
    
    /**
     * Outputs a form's value lists from the database to be consumed by form code.
     */
    public function valueListAction()
    {
        $reportId = $this->getReportIdWithValidation();
        $entryFlow = $this->getReportEntryFlow($reportId);

        $rowReportInfo = $this->getReportData(
            $entryFlow, $reportId, $this->serviceReport, $this->serviceReportEntryQueue
        );
        $rowFormInfo = $this->serviceForm->getFormInfo($rowReportInfo['formId']);

        $agencyId = $rowFormInfo['agencyId'];
        $stateId = $rowFormInfo['stateId'];
        $formTemplateId = $rowFormInfo['formTemplateId'];
		
        $rs = $this->serviceFormCodeGroupConfiguration->fetchFormCodeConfiguration($formTemplateId, $stateId, $agencyId);
        $formCodeGroupId = $rs['form_code_group_id'];
        
        $valueLists = $this->getFormValueLists($formCodeGroupId);

        // output to screen.
        header('Content-type: text/javascript');
        echo 'eCrash.valueLists = ' . json_encode($valueLists) . ';';
        exit;
    }

    public function imageViewerPdfAction()
    {
        $this->layout()->setTemplate('layout/minimal');

        $reportId = $this->getReportIdWithValidation();

        $this->serviceImageServer->pullImageFromServer($reportId);

        $this->view->reportId = $reportId;
        $this->view->imagePath = 'images/reports/' . $reportId . '.pdf';

        return $this->view;
    }

    private function imageViewData() {
        $reportId = $this->getReportIdWithValidation();
        $this->serviceImageServer->pullImageFromServer($reportId);
        $this->view->reportId = $reportId;
        $this->view->imagePath = 'images/reports/' . $reportId . '.pdf';
        return $this->view;
    }

    /**
     * Called via AJAX
     */
    public function addPageAction()
    {
        if ($this->validateCsrfToken($this->request->getPost('csrf'))) {
            $reportId = $this->getReportIdWithValidation();
            $pageName = basename($this->request->getPost('pageName'));

            /* @var $formContext Form/ReportForm/FormContext */
            $formContext = $this->session->formContext[$reportId];
            $result = $formContext->addPage($pageName);
        } else {
            $result = $this->getInvalidCSRFJsonResponse();
        }
        
        return $this->json->setVariables($result);
    }
    
    /**
     * Fetches another image for the user to work after the current one.
     *
     * Called via AJAX
     */
    public function prefetchNextImageAction()
    {
        $selectedWorkTypeId = $this->session->selectedWorkTypeId;
        $userId = !empty($this->identity()) ? $this->identity()->userId : null;
        $currentReportId = $this->getReportIdWithValidation();
        $entryFlow = $this->getReportEntryFlow($currentReportId);

        if ($entryFlow != ReportService::ENTRY_FLOW_ENTRY) {
            return $this->json->setVariables(['success' => true]);
        }

        if ($this->serviceUserEntryPrefetch->fetchReportIdByUserId($userId)) {
            $this->logger->log(Logger::INFO, 'User already has an image prefetched; not prefetching another.');
        } else {
            $this->logger->log(Logger::INFO, 'Prefetching next image.');
            $keyingVendorId = (!empty($this->identity()) && !empty($this->identity()->keyingVendorId)) ? 
                    $this->identity()->keyingVendorId : $this->serviceUser->getKeyingVendorIdByUserId($userId);
            $reportId = $this->serviceReportEntryQueue->pull($userId, $selectedWorkTypeId, $keyingVendorId, $this->session->rekey);

            if ($reportId) {
                $this->logger->log(Logger::INFO, 'Prefetched reportId: ' . $reportId);
                $this->serviceImageServer->pullImageFromServer($reportId);
                $this->serviceUserEntryPrefetch->addUserEntry($userId, $reportId);
            }
        }

        return $this->json->setVariables(['success' => !empty($reportId)]);
    }
    
    /**
     * Display all notes or add a note
     */
    public function notesAction()
    {
        $this->layout()->setTemplate('layout/minimal');
        $reportId = $this->getReportIdWithValidation();
        $entryFlow = $this->getReportEntryFlow($reportId);

        if ($this->request->isPost()) {
            if ($this->validateCsrfToken($this->request->getPost('csrf'))) {
                $this->serviceReportNote->add(
                    $reportId, $this->identity()->userId, $this->request->getPost('note'), $entryFlow
                );

                if ($this->request->isXmlHttpRequest()) {
                    return $this->json->setVariables(['success' => !empty($reportId)]);
                }
            } else {
                return $this->json->setVariables($this->getInvalidCSRFJsonResponse());
            }
        }

        $notes = $this->serviceReportNote->getReportNotesWithUsers($reportId);

        $this->view->viewOnly = strtolower($this->params()->fromQuery('viewOnly'));
        $this->view->notes = $notes;
        $this->view->csrf = $this->getCsrfElement();

        return $this->view;
    }
    
    /**
     * Send the image to the bad image queue.
     */
    public function badImageAction()
    {
        if ($this->validateCsrfToken($this->request->getPost('csrf'))) {
            $reportId = $this->getReportIdWithValidation();
            $userId = !empty($this->identity()) ? $this->identity()->userId : null;

            $this->serviceReportStatus->set($reportId, ReportStatusService::STATUS_BAD_IMAGE, $userId);
            $this->serviceReportEntryQueue->remove($reportId, ReportEntryQueueService::REMOVAL_REASON_REMOVED);
            $this->serviceReportEntry->revertInProgress($userId, $reportId);
            $this->serviceReportQueue->add(ReportQueueService::QUEUE_BAD_IMAGE, $reportId, date('Y-m-d H:i:s'), $userId);
            //remove record from user report table for all user
            $this->serviceReportEntryQueue->removeUserReportRecord($reportId);

            $this->pullNextPrefetchReport();
        } else {
            $this->serviceReportEntry->revertInProgress($userId, $reportId);
            $this->flashMessenger()->addInfoMessage('Tokens did not match.');

            return $this->redirect()->toRoute('index');
        }
    }
    
    /**
     * Send the image to the discarded queue.
     */
    public function discardImageAction()
    {
        if ($this->validateCsrfToken($this->request->getPost('csrf'))) {
            $reportId = $this->getReportIdWithValidation();
            $entryFlow = $this->getReportEntryFlow($reportId);
            $userId = !empty($this->identity()) ? $this->identity()->userId : null;

            // Send this report to the discarded queue.
            $this->serviceReportStatus->set($reportId, ReportStatusService::STATUS_DISCARDED, $userId);
            $this->serviceReportQueue->remove(ReportQueueService::QUEUE_BAD_IMAGE, $reportId, ReportQueueService::REMOVAL_REASON_MOVED);
            $this->serviceReportEntry->revertInProgress($userId, $reportId);
            $this->serviceReportQueue->add(ReportQueueService::QUEUE_DISCARDED, $reportId, date('Y-m-d H:i:s'), $userId);

            // Pull the next one or refresh our parent window.
            $keyingVendorId = (!empty($this->identity()) && !empty($this->identity()->keyingVendorId)) ? 
                    $this->identity()->keyingVendorId : $this->serviceUser->getKeyingVendorIdByUserId($userId);
            $this->setNextReportId(
                $this->serviceReportQueue->pull(ReportQueueService::QUEUE_BAD_IMAGE, $userId, $keyingVendorId), $entryFlow
            );
        } else {
            $this->flashMessenger()->addInfoMessage('Tokens did not match.');

            return $this->redirect()->toRoute('index');
        }
    }

    /**
     * Send an image back to the keying queue.
     */
    public function rekeyImageAction()
    {
        if ($this->validateCsrfToken($this->request->getPost('csrf'))) {
            $reportId = $this->getReportIdWithValidation();
            $entryFlow = $this->getReportEntryFlow($reportId);
            $userId = !empty($this->identity()) ? $this->identity()->userId : null;

            $reportInfo = $this->serviceReport->getRelatedInfo($reportId);

            // set the status back to available and delete any uncompleted ReportEntry rows.
            $this->serviceReportStatus->set($reportId, ReportStatusService::STATUS_KEYING, $userId);
            $this->serviceReportQueue->remove(ReportQueueService::QUEUE_BAD_IMAGE, $reportId, ReportQueueService::REMOVAL_REASON_REMOVED);
            $this->serviceReportQueue->remove(ReportQueueService::QUEUE_DISCARDED, $reportId, ReportQueueService::REMOVAL_REASON_REMOVED);
            $this->serviceReportEntry->revertInProgress($userId, $reportId);

            if ($this->serviceRekey->isQueuedForRekey($reportId)) {
                $this->serviceReportEntryQueue->insertForRekey(
                        $reportId, $reportInfo['workTypeId'], $reportInfo['priority'], $reportInfo['formId'], $reportInfo['agencyId']
                );
            } else {
                $this->serviceReportEntryQueue->add($reportId);
            }

            switch ($entryFlow)
            {
                case ReportService::ENTRY_FLOW_BAD:
                    $queueName = ReportQueueService::QUEUE_BAD_IMAGE;
                    break;

                case ReportService::ENTRY_FLOW_DISCARD:
                    $queueName = ReportQueueService::QUEUE_DISCARDED;
                    break;

                default:
                    throw new Exception('Unknown entry flow for next rekey pull.');
            }

            // Pull the next one or refresh our parent window.
            $keyingVendorId = (!empty($this->identity()) && !empty($this->identity()->keyingVendorId)) ? 
                    $this->identity()->keyingVendorId : $this->serviceUser->getKeyingVendorIdByUserId($userId);
            $this->setNextReportId(
                $this->serviceReportQueue->pull($queueName, $userId, $keyingVendorId), $entryFlow
            );
        } else {
            $this->flashMessenger()->addInfoMessage('Tokens did not match.');

            return $this->redirect()->toRoute('index');
        }
    }

    /**
     * Mark an image as being reordered or not (dead).
     */
    public function reorderImageAction()
    {
        if ($this->validateCsrfToken($this->request->getPost('csrf'))) {
            $reportId = $this->getReportIdWithValidation();
            $userId = !empty($this->identity()) ? $this->identity()->userId : null;
            $reorder = $this->params()->fromPost('reorder', null);

            if (is_null($reorder)) {
                throw new Exception('Reorder was not included in form submission; no fallback allowed. Aborting!');
            }

            if ($reorder) {
                $this->serviceReportStatus->set($reportId, ReportStatusService::STATUS_REORDERED, $userId);
                $this->serviceReport->setDateReordered($reportId, $this->request->getPost('reorder-date'));
            } else {
                $this->serviceReportStatus->set($reportId, ReportStatusService::STATUS_DEAD, $userId);
            }

            $this->serviceReportQueue->remove(ReportQueueService::QUEUE_DISCARDED, $reportId, ReportQueueService::REMOVAL_REASON_REMOVED);

            // Pull the next one or refresh our parent window.
            $keyingVendorId = (!empty($this->identity()) && !empty($this->identity()->keyingVendorId)) ? 
                    $this->identity()->keyingVendorId : $this->serviceUser->getKeyingVendorIdByUserId($userId);
            $this->setNextReportId(
                $this->serviceReportQueue->pull(ReportQueueService::QUEUE_DISCARDED, $userId, $keyingVendorId), ReportService::ENTRY_FLOW_DISCARD
            );
        } else {
            $this->flashMessenger()->addInfoMessage('Tokens did not match.');

            return $this->redirect()->toRoute('index');
        }
    }

    /**
     * Handle any actions if the user exits the form.
     *
     * Called via AJAX, no response expected
     */
    public function cleanupAction()
    {
        $this->cleanup();
        exit;
    }

    /**
     * Action for the 'all pass' / 'pass overview' page that is triggered from view keyed reports
     */
    public function passOverviewAction()
    {
        $this->layout()->setTemplate('layout/minimal');
        $reportId = $this->params()->fromQuery('reportId');

        $inputParams = $this->request->getQuery();
        $inputParams = (array) $inputParams;
        
        $reportInfo = $this->serviceReport->getRelatedInfo($reportId);

        $entryStagesExternal = $this->serviceEntryStage->getExternalNamePairs();
        $entryStagesInternal = $this->serviceEntryStage->getInternalNamePairs();

        $reportEntries = $this->serviceReportEntry->getReportEntryDataDecompressed($reportId);

        $reportEntriesCommon = [];
        $editPassNumber = 1;
        $metadataPathSuperset = [];

        foreach ($reportEntries as $reportEntry) {

            $userInfo = $this->serviceUser->getIdentityData($reportEntry['userId']);
            $entryInfo[$reportEntry['reportEntryId']] = [
                'dateCompleted' => $reportEntry['dateUpdated'],
                'username' => $userInfo['username'],
                'nameFirst' => $userInfo['nameFirst'],
                'nameLast' => $userInfo['nameLast'],
                'title' => $entryStagesExternal[$reportEntry['entryStageId']],
            ];

            if ($entryStagesInternal[$reportEntry['entryStageId']] == EntryStageService::STAGE_EDIT) {
                $entryInfo[$reportEntry['reportEntryId']]['title'] .= ' ' . $editPassNumber++;
            }

            if (!empty($reportEntry['reportEntryId']) && !empty($reportEntry['entryData'])) {
                $transformer = $this->serviceReportEntry->getDataTransformerByEntryId($reportEntry['reportEntryId']);
                $reportEntryCommon = $transformer->toCommon($reportEntry['entryData']['Report']);
                $reportEntriesCommon[$reportEntry['reportEntryId']] = $reportEntryCommon;
                $reportEntryMetadata = $reportEntryCommon->getMetadata();
                if (!empty($reportEntryMetadata['path'])) {
                    $metadataPathSuperset = array_merge($metadataPathSuperset, $reportEntryMetadata['path']);
                }
            }
        }

        $passOverviewEntryData = $this->passOverviewGetValueDiff($reportEntriesCommon);

        $passOverviewEntryDataNames = array_keys($passOverviewEntryData);
        
        $flatData = $this->transformPassOverviewNamesToFlatData($passOverviewEntryDataNames);

        $overviewEntryNames2FlatData = array_combine($passOverviewEntryDataNames, $flatData);

        $transformers = [
            $this->serviceDataTransformer->getDataTransformerUniversal()
            //$this->_modelFactory->getDataTransformerIyetek()
        ];

        foreach ($transformers as $transformer) {
            $flatData2CommonNames[get_class($transformer)] = $transformer->transformFlatDataToVendorNames(
                $flatData, ['path' => $metadataPathSuperset]
            );
        }
        
        //@TODO: run this code only for silverlight form, otherwise $iyetekIdStructure should be just an empty array

        foreach ($passOverviewEntryData as $name => $entries) {
            $isDataModified = (count(array_unique($entries)) > 1);
            $passOverviewEntryData[$name] = ['isDataModified' => $isDataModified];

            $nameDetails = $this->getAllPassFieldNameDetails($name);
            foreach ($entries as $reportEntryId => $data) {
                $data = ['value' => $data];
                $transformerClass = get_class($this->serviceReportEntry->getDataTransformerByEntryId($reportEntryId));
                $flatDataPath = $overviewEntryNames2FlatData[$name];
                // it might not be set because for example there is a field lossStateAbbr in Universal form, but
                // there is no such field in Iyetek form
                if (isset($flatData2CommonNames[$transformerClass][$flatDataPath])) {
                    // for incident field 'index' will be empty
                    $data['formSpecificFieldName'] = $flatData2CommonNames[$transformerClass][$flatDataPath];
                }
                $passOverviewEntryData[$name]['values'][$reportEntryId] = $data;
            }
        }

        $this->view->reportId = $reportId;
        $this->view->reportInfo = $reportInfo;
        $this->view->entryInfo = $entryInfo;
        $this->view->entryData = $passOverviewEntryData;
        $this->view->format = ReportMaker::REPORT_FORMAT_XLS;
        
       if (array_key_exists('downloadType', $inputParams)) {
            $this->view->export = true;
            $this->view->setTemplate('data/report-entry/pass-overview');
            $reportHtml = $this->renderer->render($this->view);

            $this->reportMaker->sendToBrowser(ReportMaker::REPORT_FORMAT_XLS, "Change History Report", $reportHtml);
        } else {
            $this->view->export = false;
        }

        return $this->view;
    }

        /**
     * Creates a diff of report entries for use with pass overview
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

    /**
     * Add entity field values to pass overview values
     *
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

    protected function transformPassOverviewNamesToFlatData($passOverviewNames)
    {
        $flatData = str_replace(' ', '/', $passOverviewNames);
        $flatData = preg_replace_callback(
                '/\/(\d+)\//', function($matches) {
            return '/' . --$matches[1] . '/';
        }, $flatData
        );

        return $flatData;
    }

    protected function getAllPassFieldNameDetails($fieldName)
    {
        if (empty($fieldName)) {
            return;
        }

        $result = null;
        $nameDetails = explode(' ', $fieldName);
        switch (count($nameDetails)) {
            case 2:
                $keys = ['entity', 'fieldName'];
                break;
            case 3:
                $keys = ['entity', 'index', 'fieldName'];
                break;
            default:
                $keys = [];
        }
        if (!empty($keys)) {
            $result = array_combine($keys, $nameDetails);
        }

        return $result;
    }

    protected function getIyetekIdStructure($passOverviewEntryData)
    {
        if (empty($passOverviewEntryData)) {
            return;
        }

        $result = [];
        foreach ($passOverviewEntryData as $name => $data) {
            // example of $name: 'person 1 iyetekId'
            $nameDetails = $this->getAllPassFieldNameDetails($name);
            if ($nameDetails['fieldName'] == 'iyetekId') {
                foreach ($data as $reportEntryId => $entryData) {
                    if (!empty($entryData)) {
                        $key = $nameDetails['entity'] . ' ' . $nameDetails['index'];
                        $result[$key][$reportEntryId] = $entryData;
                    }
                }
            }
        }

        return $result;
    }

    /**
     *
     * @param string $entryFlow
     * @param integer $reportId
     * @param ReportService $dbReport
     * @param ReportEntryQueueService $reportEntryQueue
     */
    protected function getReportData(
        $entryFlow,
        $reportId,
        ReportService $serviceReport,
        ReportEntryQueueService $serviceReportEntryQueue)
    {
        $rowReport = $serviceReport->getRelatedInfo($reportId);

        switch ($entryFlow)
        {
            case ReportService::ENTRY_FLOW_ENTRY:
                $reportData = $serviceReportEntryQueue->getQueuedData($reportId);
                if (empty($reportData)) {
                    throw new Exception('Report is no longer in the queue.');
                }
                break;

            case ReportService::ENTRY_FLOW_DEAD:
            case ReportService::ENTRY_FLOW_DISCARD:
            case ReportService::ENTRY_FLOW_BAD:
            case ReportService::ENTRY_FLOW_VIEW:
                $entryStages = $this->serviceEntryStage->getInternalNamePairs();
                $reportData = [
                    'reportId' => $reportId,
                    'workTypeId' => $rowReport['workTypeId'],
                    'formId' => $rowReport['formId'],
                    'agencyId' => $rowReport['agencyId'],
                    'entryStageId' => array_search(EntryStageService::STAGE_EDIT, $entryStages),
                    'entryStageProcessGroupId' => $rowReport['entryStageProcessGroupId'],
                ];
                break;
        }
        $reportData['formTypeCode'] = $rowReport['formTypeCode'];
        $reportData['isObsolete'] = $rowReport['isObsolete'];
        $reportData['reportIdObsoletedBy'] = $rowReport['reportIdObsoletedBy'];
        $reportData['flags'] = $rowReport['flags'];
        $reportData['oldFormId'] = $rowReport['oldFormId'];
        $reportData['newFormId'] = $rowReport['newFormId'];

        return $reportData;
    }

    /**
     * Run all actions to clean up after the user; They've aborted whatever they were doing.
     */
    protected function cleanup()
    {
        $userId = !empty($this->identity()) ? $this->identity()->userId : null;

        $this->logger->log(Logger::INFO, 'Doing cleanup in report entry.');

        $this->serviceReportQueue->unassignUser($userId);
        $this->serviceReportEntryQueue->unassignUser($userId);

        if (isset($this->session->reportId)) {
            $this->serviceReportEntry->revertInProgress($userId, $this->session->reportId);
            $this->cleanCurrentReportSession();
        }
        $this->serviceUserEntryPrefetch->removeUserReports($userId);
    }

    protected function cleanCurrentReportSession()
    {
        unset(
            $this->session->formContext[$this->session->reportId], $this->session->reportData[$this->session->reportId],
            $this->session->reportEntryId[$this->session->reportId], $this->session->reportId
        );
    }

    /**
     * Pulls reports with prefetching for the entry flow
     *
     * 2 reports are pulled during regular entry.
     *  The first one is worked and the 2nd one is prepared
     * (image fetched etc) so they can go straight to it
     * after completing the first. This WILL set you to the
     * entry flow.
     *
     * @return void
     */
    protected function pullNextPrefetchReport()
    {
        $userId = !empty($this->identity()) ? $this->identity()->userId : null;
        $selectedWorkTypeId = $this->session->selectedWorkTypeId;

        // Check if a report is already prefetched.
        $reportId = $this->serviceReportEntry->pullPrefetchByUser($userId);

        // Otherwise pull a new record from the main location.
        if (empty($reportId)) {
            $keyingVendorId = (!empty($this->identity()) && !empty($this->identity()->keyingVendorId)) ? 
                    $this->identity()->keyingVendorId : $this->serviceUser->getKeyingVendorIdByUserId($userId);
            $reportId = $this->serviceReportEntryQueue->pull($userId, $selectedWorkTypeId, $keyingVendorId, $this->session->rekey);
        }
        $this->setNextReportId($reportId, ReportService::ENTRY_FLOW_ENTRY);
    }
    
    protected function setNextReportId($reportId, $entryFlow)
    {
        if ($reportId) {
            if ($this->serviceReport->isNewApp($reportId)) {
                $this->session->reportId = $reportId;
                $this->session->reportEntryFlow[$reportId] = $entryFlow;
                $this->view->refreshMainReportWindow = true;
            } else {
                switch ($entryFlow) {
                    case ReportService::ENTRY_FLOW_ENTRY:
                        $this->view->redirectParentToExit = true;
                        break;
                    case ReportService::ENTRY_FLOW_DISCARD:
                    case ReportService::ENTRY_FLOW_BAD:
                    case ReportService::ENTRY_FLOW_DEAD:
                        $this->view->refreshParent = true;
                        break;
                    case ReportService::ENTRY_FLOW_VIEW:
                        break;
                    default:
                        throw new Exception('Unknown entry flow given.');
                }
                
                $this->cleanup();
                $this->flashMessenger()->addInfoMessage('Please use Old App for this report, '.$reportId);
            }
        } else {
            switch ($entryFlow) {
                case ReportService::ENTRY_FLOW_ENTRY:
                    $this->view->redirectParentToExit = true;
                    break;
                case ReportService::ENTRY_FLOW_DISCARD:
                case ReportService::ENTRY_FLOW_BAD:
                case ReportService::ENTRY_FLOW_DEAD:
                    $this->view->refreshParent = true;
                    break;
                case ReportService::ENTRY_FLOW_VIEW:
                    break;
                default:
                    throw new Exception('Unknown entry flow given.');
            }

            $this->flashMessenger()->addInfoMessage('No more items available to be worked.');
        }
        $this->view->setTemplate('data/report-entry/save');

        echo $this->serviceViewRenderer->render($this->view);
        exit;
    }

    /**
     * Caches the value list array to save a trip to the database to load relatively static data.
     * @param integer $formId
     * @return array [valueListName => [key => value, ...], ...];
     */
    protected function getFormValueLists($formCodeGroupId)
    {
        // @TODO: Handling cache needs to be done 
        /*$cache = $this->getInvokeArg('bootstrap')
                ->getPluginResource('cachemanager')
                ->getCacheManager()
                ->getCache('genericFile');

        $valueLists = $cache->load('valueList_formCodeGroup_' . $formCodeGroupId);
        if ($this->cacheDisabled || empty($valueLists)) {*/
            //Build and cache form KV pairs for selects and other options.
            $valueLists = [];
            $prepareValueList = function($valueList) {
                return [
                    'keys' => array_map('strval', array_keys($valueList)),
                    'values' => array_column($valueList, 'value'),
                    'class_name' => array_column($valueList, 'class_name'),
                    'length' => count($valueList),
                ];
            };

            foreach ($this->serviceFormCodeMap->getAllFormCodePairs($formCodeGroupId) as $key => $valueList) {
                $valueLists[$key] = $prepareValueList($valueList);
            }
            
            $valueLists['dynamicFields'] = (object) ["keys" => [], "values" => [], "length" => 0]; // set for dynamic data loading
            /*$cache->save($valueLists);
        }*/

        return $valueLists;
    }

    /**
     * Determines the active reportId and runs sanity checks against it.
     *
     * @return integer
     */
    protected function getReportIdWithValidation()
    {
        // $reportId = $this->session->reportId;

        $reportId = '902173837';

        // Basic value check
        if (empty($reportId)) {
            unset($this->session->reportId);
            $this->flashMessenger()->addInfoMessage('No report is active.');
            return $this->redirect()->toRoute('index');
        }
        // @TODO: Have to done
        // reportId is an important piece of information; add it to all future log entries.
        //$this->getLog()->setEventItem('reportId', $reportId);

        return $reportId;
    }

    protected function getReportEntryFlow($reportId)
    {
        $entryFlow = ReportService::ENTRY_FLOW_VIEW;
        if (!empty($this->session->reportEntryFlow[$reportId])) {
            $entryFlow = $this->session->reportEntryFlow[$reportId];
        }

        return $entryFlow;
    }

    /**
     * Called via AJAX
     */
    function getAlternativeFormsListAction()
    {
        $result = [];
        if (!$this->validateCsrfToken($this->request->getQuery('csrf'))) {
            $result = $this->getInvalidCSRFJsonResponse();
        } else {
            $reportId = $this->request->getQuery('reportId');
            $formId = $this->request->getQuery('alternativeFormId');
            $reportInfo = $this->serviceReport->getRelatedInfo($reportId);
            $currentUserInfo = !empty($this->identity()) ? $this->identity() : null;

            if (!empty($reportInfo) && !empty($currentUserInfo)) {
                $formId = empty($formId) ? $reportInfo['formId'] : $formId;
                $result = $this->serviceForm->getAlternativeFormPairs(
                    $formId, $currentUserInfo->userId, $reportInfo['workTypeId']
                );
            }
        }

        return $this->json->setVariables($result);
    }
    
    function getAutozoningCoordinatesAction() {
        $reportId = $this->getReportIdWithValidation();
        return $this->json->setVariables([
            'coordinates' => $this->serviceAutoExtraction->getCoordinateData($reportId)
        ]);
    }
    /**
     * Called via AJAX
     */
    function getAutozoningRenderFieldAction() {
        $coordinates = $this->session->autozoningKey ?? '';
        return $this->json->setVariables([
            'coordinates' => $coordinates
        ]);
    }
    /**
     * Called via AJAX
     */
    function setAutozoningRenderFieldAction() {
        $coordinates = $this->request->getQuery('coordinates');
        $this->session->autozoningKey = $coordinates;
        return $this->json->setVariables([
            'message' => "Coordinates Sent."
        ]);
    }
}
