<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Log\Logger;
use Zend\Authentication\AuthenticationService;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Helper\HeadTitle;

use Base\Controller\BaseController;
use Base\Service\UserService;
use Base\Service\EntryStageService;
use Base\Service\AgencyService;
use Base\Service\FormService;
use Base\Service\FormWorkTypeService;
use Base\Service\WorkTypeService;
use Base\Service\StateService;
use Base\Service\UserEntryStageService;
use Base\Service\UserNoteService;
use Base\Service\UserFormPermissionService;
use Base\Service\IsitService;
use Base\Service\ReportService;
use Base\Service\ReportEntryService;
use Base\Service\ReportEntryQueueService;
use Base\Service\RekeyService;
use Auth\Service\LNAAAuthService;
use Auth\Adapter\REST\LNAAAuthAdapter;
use Base\Helper\LnHelper;
use Admin\Form\SearchUsersForm;
use Admin\Form\UserForm;
use Admin\Form\UserNotesForm;
use Admin\Form\UserNoteHistoryForm;
use Admin\Form\ResetPasswordForm;
use Admin\Form\ConfigureTimeoutForm;
use Base\Form\KeyingVendorForm;
use Base\Service\KeyingVendorService;
use Zend\Session\Container;


class UsersController extends BaseController
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
    protected $logger;
    
    /**
     * @var Zend\Authentication\AuthenticationService
     */
    protected $serviceAuth;
    
    /**
     * @var Base\Service\UserService
     */
    protected $serviceUser;
    
    /**
     * @var Base\Service\EntryStageService
     */
    protected $serviceEntryStage;
    
    /**
     * @var Admin\Form\SearchUsersForm
     */
    protected $formUsersSearch;
    
    /**
     * @var Admin\Form\UserForm
     */
    protected $formUser;
    
    /**
     * @var Admin\Form\UserNoteHistoryForm
     */
    protected $formUserNoteHistory;
    
    /**
     * @var Admin\Form\UserNotesForm
     */
    protected $formUserNotes;
    
    /**
     * @var Admin\Form\ResetPasswordForm
     */
    protected $formResetPassword;
    
    /**
     * @var Admin\Form\ConfigureTimeout
     */
    protected $formConfigureTimeout;
    
    /**
     * @var Base\Service\AgencyService
     */
    protected $serviceAgency;
    
    /**
     * @var Base\Service\FormService
     */
    protected $serviceForm;
    
    /**
     * @var Base\Service\FormWorkTypeService
     */
    protected $serviceFormWorkType;
    
    /**
     * @var Base\Service\WorkTypeService
     */
    protected $serviceWorkType;
    
    /**
     * @var Base\Service\StateService
     */
    protected $serviceState;
    
    /**
     * @var Base\Service\ReportService
     */
    protected $serviceReport;
    
    /**
     * @var Base\Service\UserEntryStageService
     */
    protected $serviceUserEntryStage;
    
    /**
     * @var Base\Service\UserNoteService
     */
    protected $serviceUserNote;
    
    /**
     * @var Base\Service\UserFormPermissionService
     */
    protected $serviceUserFormPermission;
    
    /**
     * @var Base\Service\IsitService
     */
    protected $serviceIsit;
    
    /**
     * @var Auth\Service\LNAAAuthService
     */
    protected $serviceLnaaAuth;
    
    /**
     * @var Base\Service\ReportEntryService
     */
    protected $serviceReportEntry;
    
    /**
     * @var Base\Service\ReportEntryQueueService
     */
    protected $serviceReportEntryQueue;
    
    /**
     * @var Base\Service\RekeyService
     */
    protected $serviceRekey;
    
    /**
     * @var Zend\View\Renderer\PhpRenderer
     */
    protected $renderer;
    
    /**
     * @var Base\Helper\LnHelper
     */
    protected $lnHelper;
    
    /**
     * @var Zend\View\Helper\HeadTitle
     */
    protected $helperHeadTitle;
    
    /**
     * @var Base\Service\KeyingVendorService
     */
    protected $serviceKeyingVendor;
    
    public function __construct(
        Array $config,
        Container $session,
        Logger $logger,
        AuthenticationService $serviceAuth,
        UserService $serviceUser,
        AgencyService $serviceAgency,
        FormService $serviceForm,
        FormWorkTypeService $serviceFormWorkType,
        WorkTypeService $serviceWorkType,
        StateService $serviceState,
        UserEntryStageService $serviceUserEntryStage,
        UserNoteService $serviceUserNote,
        UserFormPermissionService $serviceUserFormPermission,
        IsitService $serviceIsit,
        LNAAAuthService $serviceLnaaAuth,
        ReportService $serviceReport,
        ReportEntryService $serviceReportEntry,
        ReportEntryQueueService $serviceReportEntryQueue,
        RekeyService $serviceRekey,
        PhpRenderer $renderer,
        SearchUsersForm $formUsersSearch,
        UserForm $formUser,
        UserNotesForm $formUserNotes,
        LnHelper $lnHelper,
        UserNoteHistoryForm $formUserNoteHistory,
        ResetPasswordForm $formResetPassword,
        HeadTitle $helperHeadTitle,
        ConfigureTimeoutForm $formConfigureTimeout,
        KeyingVendorService $serviceKeyingVendor)
    {
        $this->config = $config;
        $this->session = $session;
        $this->logger = $logger;
        $this->formUsersSearch = $formUsersSearch;
        $this->formUser = $formUser;
        $this->formUserNotes = $formUserNotes;
        $this->formUserNoteHistory = $formUserNoteHistory;
        $this->formResetPassword = $formResetPassword;
        $this->serviceUser = $serviceUser;
        $this->serviceAuth = $serviceAuth;
        $this->serviceAgency = $serviceAgency;
        $this->serviceForm = $serviceForm;
        $this->serviceFormWorkType = $serviceFormWorkType;
        $this->serviceWorkType = $serviceWorkType;
        $this->serviceState = $serviceState;
        $this->serviceUserEntryStage = $serviceUserEntryStage;
        $this->serviceUserNote = $serviceUserNote;
        $this->serviceUserFormPermission = $serviceUserFormPermission;
        $this->serviceIsit = $serviceIsit;
        $this->serviceLnaaAuth = $serviceLnaaAuth;
        $this->serviceReport = $serviceReport;
        $this->serviceReportEntry = $serviceReportEntry;
        $this->serviceReportEntryQueue = $serviceReportEntryQueue;
        $this->serviceRekey = $serviceRekey;
        $this->renderer = $renderer;
        $this->lnHelper = $lnHelper;
        $this->formConfigureTimeout = $formConfigureTimeout;
        $this->serviceKeyingVendor = $serviceKeyingVendor;
        
        parent::__construct();
        
        $this->view->helperHeadTitle = $helperHeadTitle;
    }
    
    public function indexAction()
    {
        if (!empty($this->request->isPost())) {
            $postParams = $this->request->getPost();
            $this->formUsersSearch->setInputFilter($this->formUsersSearch->getInputFilter());
            $this->formUsersSearch->setData($postParams);
            if ((!empty($postParams['Search'])) && ($this->formUsersSearch->isValid())) {
                // Based on the isValidated class, user list dataTable will retrieve the data's.
                $this->formUsersSearch->setAttribute('class', 'isValidated');
            } else if (empty($postParams['Search'])) {
                // Return JSON Model for user data table
                if (!$this->validateCsrfToken($this->request->getPost('csrf', ''))) {
                    // Csrf token validation for Ajax request
                    $this->json->data = $this->getInvalidCSRFJsonResponse();
                } else {
                    $searchParams = $this->getSearchParams();
                    $userSelect = $this->getUserListSelect($searchParams);
                    
                    $this->json->draw = $this->request->getPost('draw', 1);
                    $this->json->data = $this->serviceUser->getPageUserList($userSelect, $searchParams);
                    $this->json->recordsTotal = $this->serviceUser->getTotalRows($userSelect);
                    $this->json->recordsFiltered = $this->json->recordsTotal;
                    
                    // CSRF token hash will be used in view file
                    $this->json->csrf = $this->getCsrfTokenHash();
                }
                
                return $this->json;
            } else {
                $this->addFormMessages($this->formUsersSearch);
            }
        }

        $this->view->isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        
        $this->formUsersSearch->prepare();
        $this->view->form = $this->formUsersSearch;
        return $this->view;
    }
    
    /**
     * Collect the users list from the user service
     * @param array $searchCriteria User selected filters from the user search form
     * @return object/array User select object / user list array
     */
    protected function getUserListSelect(Array $searchCriteria = [])
    {
        if ($this->serviceAuth->hasIdentity()) {
            $showAdminUsers = ($this->serviceAuth->getIdentity()->role == UserService::ROLE_ADMIN);
            return $this->serviceUser->getUserList($searchCriteria, $showAdminUsers);
        }
    }
    
    private function getSearchParams()
    {
        return [
            'current_page' => $this->params()->fromRoute('page', 1),
            'offset' => $this->request->getPost('start', 0),
            'limit' => $this->request->getPost('length', 0),
            'entryStage' => $this->request->getPost('entryStage', []),
            'userRoleId' => $this->request->getPost('userRoleId', ''),
            'nameFirst' => $this->request->getPost('nameFirst', ''),
            'nameLast' => $this->request->getPost('nameLast', ''),
            'keyingVendorId' => $this->request->getPost('keyingVendorId')
        ];
    }
    
    public function addAction()
    {
        $userInfo = $this->serviceAuth->getIdentity();
        $requestParams = $this->request->getPost();
        $formAssigned = '';
        
        if ($this->request->isPost()) {
            $this->formUser->setInputFilter($this->formUser->addInputFilter());
            $this->formUser->setData($requestParams);
            if ($this->formUser->isValid()) {
                $userName = $requestParams['username'];
                $email = $requestParams['email'];
                $isInternalDomainInfo = $this->serviceUser->getUserIsInternalAndDomainInfo($userName, $email);
                $isInternal = $isInternalDomainInfo['isInternal'];
                $loggedInUserName = strtolower($userInfo->username);
                $internalUserExceptions = $this->config['internalUserExceptions'];
                if ($isInternal && !in_array($loggedInUserName, $internalUserExceptions)) {
                     $this->flashMessenger()->addErrorMessage('Internal Users cannot be created here. Please file a User Access Request ISIT ticket.');
                } else {
                    $password = $this->lnHelper->generatePassword();
                    $requestParams['password'] = md5($password);

                    $userId = $this->saveUser($requestParams);
                    if (!empty($userId)) {
                        $this->flashMessenger()->addSuccessMessage('User added succesfully');

                        //create the isit ticket request
                        $isUserAdded = $this->serviceIsit->addUser($userId);
                        if (empty($isUserAdded)) {
                            $this->logger->log(Logger::INFO, 'Error creating ISIT ticket for adding userId ' . $userId);
                            $this->flashMessenger()->addInfoMessage('Creation of ISIT Ticket for user failed.');
                        }
                    } else {
                        $this->flashMessenger()->addErrorMessage('User not created. Please contact the Administrator.');
                    }
                }
                
                return $this->redirect()->toRoute('users', ['action' => 'add']);
            } else {
                $formAssigned = $this->renderAssignedFormFromRequest($this->request->getPost());
                $this->handleInvalidForm($this->formUser);
            }
        }
        
        //rekey
        $this->view->rekeyId = $this->serviceRekey->getRekeyEntryStage();
        $this->view->eRekeyId = $this->serviceRekey->getERekeyEntryStage();
        
        $this->view->isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        
        $this->formUser->prepare();
        
        $this->view->form = $this->formUser;
        $this->view->formAssigned = $formAssigned;
        
        return $this->view;
    }
    
    public function editAction()
    {
        $isUpdateSuccess = false;
        $queryParams = $this->request->getQuery();
        $requestParams = (array) $this->request->getPost();
        $queryParams = (!empty($queryParams)) ? (array) $queryParams : [];
        $userId = $queryParams['userId'];
        $reasonCode = null;
        $message = null;
        $isReactivated = false;
        $userStatus = LNAAAuthAdapter::STATUS_DISABLED; //disabled by default
        
        $userInfo = $this->serviceUser->getIdentityData($userId);
        $userInternalInfo = $this->serviceUser->getUserIsInternalAndDomainInfo($userInfo['username'], $userInfo['email']);
        $isInternal = $userInternalInfo['isInternal'];
        $domain = $userInternalInfo['domain'];
        
        $userInfoDetails = explode("@", $userInfo['username']);
        $userNameWithoutDomain = $userInfoDetails[0];
        
        $loginID =  $userNameWithoutDomain . '@' . $domain;
        $adminSessionId = $this->serviceLnaaAuth->authUserAdmin();
        $data = $this->serviceLnaaAuth->adminGetUserData($adminSessionId, $loginID);
        if (!empty($data->user_data)) {
            $userData = $data->user_data;
            $userStatus = $userData->user_info->status;
        } else {
            $this->logger->log(Logger::INFO, 'No user data for user ' . $loginID .' in LNAA. Cannot determine status.');
        }

        $userEntryStages = [];
        foreach ($this->serviceUserEntryStage->getEntryStageByUserId($userId) as $userEntryStage) {
            $userEntryStages[] = $userEntryStage['entry_stage_id'];
        }
        $userInfo['entryStage'] = $userEntryStages;
        
        $formParams = (empty($this->request->isPost())) ? array_merge($queryParams, $userInfo) : $requestParams;
        $isEnabled = ($userStatus == LNAAAuthAdapter::STATUS_ENABLED)? 1 : 0;
        $formParams['isActive'] = $isEnabled;
        
        $operatorIdentity = $this->serviceAuth->getIdentity();
        $this->formUser->get('isActive')->setValue($formParams['isActive']);
        
        $options = [];
        if ($queryParams['userId'] == $operatorIdentity->userId) {
            $options['disabled'] = 'disabled';
            $this->formUser->get('isActive')
                ->setAttributes($options)
                ->setUncheckedValue($formParams['isActive']);
        }
        
        $this->formUser->setData($formParams);
        $formAssigned = $this->renderAssignedFormByUserId($userId);
        if ($this->request->isPost()) {
            $this->formUser->setInputFilter($this->formUser->addInputFilter());
            
            if ($this->formUser->isValid()) {
                if (!$isInternal && !$isEnabled && $requestParams['isActive']) {
                    $isReactivated = true;
                    $this->serviceUser->setPasswordActive($userId);
                }
                
                $oldRoleId = $userInfo['userRoleId'];
                $isRoleChanged = ($oldRoleId != $requestParams['userRoleId']);
                if ($isRoleChanged && $userInfo['isPending']) {
                    if (!$isInternal && $isReactivated) {
                        $this->serviceLnaaAuth->adminUpdateUserStatus($adminSessionId, $userInfo['username'], LNAAAuthAdapter::STATUS_ENABLED);
                    }
                    $this->flashMessenger()->addInfoMessage(
                        "Role can't be changed because this user still has pending ISIT ticket."
                    );
                } else {
                    $newRoleId = $requestParams['userRoleId'];
                    // role should not be changed untill we get approval through ISIT
                    $requestParams['userRoleId'] = $oldRoleId;
                    if (!$isInternal && ($queryParams['userId'] != $operatorIdentity->userId)) {
                        $requestParams['isPasswordActive'] = $this->request->getPost('isActive', 0);
                    }
                    $result = $this->saveUser($requestParams);
                    if (!empty($result) && $isRoleChanged) {
                        $userDomain = ($isInternal) ? $domain : null;
                        $this->serviceIsit->changeUserRole($userId, $newRoleId, $userDomain);
                    }
                    
                    if (!$isInternal) {
                        $username = $this->request->getPost('username', '');
                        $firstName = $this->request->getPost('nameFirst', '');
                        $lastName = $this->request->getPost('nameLast', '');
                        $email = $this->request->getPost('email', '');
                        $active = $this->request->getPost('isActive', 0);
                    
                        $updateResult = $this->serviceLnaaAuth->adminUpdateUser($adminSessionId, $username, $firstName, $lastName, $email);
                        if ($updateResult->status->code == LNAAAuthService::CODE_SUCCESS) 
                        {
                             //only update the user status if the user being updated is not the same as the logged in user
                            if ($queryParams['userId'] != $operatorIdentity->userId) {
                                $updateStatus = false;                        
                                if (!$active && $userStatus == LNAAAuthAdapter::STATUS_ENABLED) {
                                    $userStatus = LNAAAuthAdapter::STATUS_DISABLED;
                                    $reasonCode = LNAAAuthAdapter::REASON_CODE_REQ_BY_MGR;
                                    $updateStatus = true;
                                } elseif ($active && $userStatus != LNAAAuthAdapter::STATUS_ENABLED) {
                                    $userStatus = LNAAAuthAdapter::STATUS_ENABLED;
                                    $updateStatus = true;
                                }
                                //if we update the same status it will error out, so only update if the status will be different
                                if ($updateStatus) {
                                    $message = $requestParams['note'];
                                    $updateResult = $this->serviceLnaaAuth->adminUpdateUserStatus($adminSessionId, $username, $userStatus, $reasonCode, $message);
                                }
                            }
                        }     
                    }
                    if ((!$isInternal && ($updateResult->status->code != LNAAAuthService::CODE_SUCCESS)) || 
                            ($isInternal && empty($result))) {
                        $this->flashMessenger()->addErrorMessage('User not updated.');
                    } else {
                        $this->flashMessenger()->addSuccessMessage('User updated succesfully.');
                        $isUpdateSuccess = true;
                    }
                }
            } else {
                $formAssigned = $this->renderAssignedFormFromRequest($requestParams);
                $this->handleInvalidForm($this->formUser);
            }
        }
        $this->session->reportSpecific = [];
        $this->session->dateRangeReports = [];
        
        $this->view->isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        $this->view->isInternal = $isInternal;
        
        $this->formUser->prepare();
        $this->view->form = $this->formUser;
        
        $this->view->isUpdateSuccess = $isUpdateSuccess;
        
        //retrieve the assigned report
        $reportAssigned = $this->getAssignedReport($userId);
        //get date range assigned reports 
        $dateRangeReports = $this->getdateRangeAssignedReports($userId);
        $this->view->reportList = $reportAssigned['data'];
        $this->view->showHideReportList = (trim($reportAssigned['data']) == "" || empty($reportAssigned['data'])) ? "display:none;" : ""; 
        $this->view->dateRangereportList = $dateRangeReports['data'];
        $this->view->dateRangeReportsCount = $dateRangeReports['count'];
        $this->view->showHideDateRangeReportList = (trim($dateRangeReports['data']) == "" || empty($dateRangeReports['data'])) ? "display:none;" : ""; 
        $this->view->dateRangeReportsTableClass = $dateRangeReports['count'] > 7 ? "reportTableScroller" : "";                    
        $this->view->formAssigned = $formAssigned;
        $this->view->requestParams = $queryParams;
        
        //sending rekey id to form to hide the entry stage
        $this->view->rekeyId = $this->serviceRekey->getRekeyEntryStage();
        $this->view->eRekeyId = $this->serviceRekey->getERekeyEntryStage();
        
        return $this->view;
    }
    
    public function saveUser($fields)
    {
        $saveResult = $this->serviceUser->save($fields);
        $userId = (!empty($fields['userId'])) ? $fields['userId'] : $saveResult;
        if (empty($userId)) {
            $this->logger->log(Logger::ERR, 'Error while saving user, serialized user fields: ' . serialize($fields));
            return false;
        }
        
        $this->addNoteAction($userId, $fields['note']);
        $this->serviceUserFormPermission->removePermissionByUserId($userId);
        // if $fields['stateIds'] is not empty - we have forms assigned
        if (!empty($fields['stateIds']) && !empty($fields['workType'])) {
            $formIds = $fields['formIds'];
            $workTypes = $fields['workType'];
            foreach ($fields['stateIds'] as $key => $stateId) {
                // if operator did not select any work types for form - skipping
                if (empty($workTypes[$formIds[$key]])) continue;
                foreach ($workTypes[$formIds[$key]] as $workTypeId) {
                    $this->serviceUserFormPermission->add(
                        [
                            'form_id' => $formIds[$key],
                            'user_id' => $userId,
                            'work_type_id' => $workTypeId
                        ]
                    );
                }
            }
        }
        
        $this->serviceUserEntryStage->deleteByUserId($userId);
        if (!empty($fields['entryStage'])) {
            foreach ($fields['entryStage'] as $entryStageId) {
                $this->serviceUserEntryStage->add([
                    'user_id' => $userId,
                    'entry_stage_id' => $entryStageId,
                ]);
            }
        }              

        //Reserve Specific Report to user
        $reportsToAssign = $fields['reportIdToAssign'] ?? [];
        $savedReportIds  = $fields['reportIdAssigned'] ?? [];
        if (isset($this->session->reportSpecific) && count($this->session->reportSpecific)>0 && !empty($reportsToAssign))
        {           
            $this->setReportSessionData($userId, $this->session->reportSpecific, $reportsToAssign);
            $this->session->reportSpecific = [];
        }          

        //Unassign Reports from report_entry_queue
        $reportsToUnAssign = !empty($reportsToAssign) ? array_diff($savedReportIds, $reportsToAssign): $savedReportIds;        
        if (!empty($reportsToUnAssign)) {
            $this->unassignReport($userId, $reportsToUnAssign, UserService::REPORT_SPECIFIC);            
        } 


        //Reserve Date Range selected Reports    
        $dateRangeReportsToAssign = $fields['availableReportIds'] ?? [];          
        if (!empty($dateRangeReportsToAssign) && isset($this->session->dateRangeReports) && count($this->session->dateRangeReports)>0)
        {            
            $this->dateRangeReportAssign($userId, $dateRangeReportsToAssign);
            $this->session->dateRangeReports = [];
        }

        //Unassign date-range assigned reports
        $savedReports = $fields['userReportAssigned'] ?? [];
        $dateRangeAssignedReports = $fields['assignedReportIds'] ?? [];
        $dateReportsToUnAssign = array_diff($savedReports, $dateRangeAssignedReports);
        if (!empty($dateReportsToUnAssign)) {
            $this->unassignReport($userId, $dateReportsToUnAssign, UserService::DATERANGE_REPORTS);            
        }

        
        $rekeyFields = isset($fields['Rekey']) ? $fields['Rekey'] : [];
        $eRekeyFields = isset($fields['ERekey']) ? $fields['ERekey'] : [];
        $this->serviceRekey->updateFormPermissionsViaPost($rekeyFields, $userId, RekeyService::PAPER_ADDITIONAL_KEYING);
        
        $this->serviceRekey->updateFormPermissionsViaPost($eRekeyFields, $userId, RekeyService::ELECTRONIC_ADDITIONAL_KEYING);
        
        return $userId;
    }
    
    /**
     * Save the filtered Report to the user after verifying the valid and allowed reports
     * @param int $userId
     * @param array $reportsFiltered
     */
    public function dateRangeReportAssign($userId, $reportsFiltered = [])
    {
        if (!empty($reportsFiltered)) {                        
            foreach ($reportsFiltered as $reportId) {
                $reportFormId = $this->serviceReport->getReport($reportId);
                //checking for the Report Form has permission to the user
                $userReportFormPermission = $this->serviceUserFormPermission->getReportFormPermissionByFormId($userId, $reportFormId[0]['form_id']);
                //skip the formId if it has no permission for the user
                if (!$userReportFormPermission) {
                    continue;
                }                                           
                //check the user has entry stage permission to the report
                $checkEntryStagePermission = $this->serviceReportEntryQueue->reviewUserEntryStageForReport($userId, $reportId);
                if (!empty($checkEntryStagePermission)) {
                    $this->serviceReportEntryQueue->insertAssignedReport($userId, $reportId);
                }
                
            }
        }
    }
    
    /**
     * Assign the Report to the user after verifying it is valid
     * @param  integer $userId
     * @param  array $reportsData
     * @param  array $reportsToAssign
     */
    public function setReportSessionData($userId, $reportsData = [], $reportsToAssign = [])
    {
        if (!empty($reportsData)) {
            foreach ($reportsData as $reportId) {
                //check if the report is selected to assign, if not skip the reportId which is in session
                if(!in_array($reportId, $reportsToAssign)) {
                    continue;
                }
                //get form_id from report table
                $reportDetails = $this->serviceReport->getReport($reportId);
                $formId = (!empty($reportDetails[0])) ? $reportDetails[0]['form_id'] : null;
                
                $userReportFormPermission = $this->serviceUserFormPermission->getReportFormPermissionByFormId($userId, $formId);
                if ($userReportFormPermission) {
                    //check the user has entry stage permission to the report
                    $checkEntryStagePermission = $this->serviceReportEntryQueue->reviewUserEntryStageForReport($userId, $reportId);
                    if (!empty($checkEntryStagePermission)) {
                        $this->serviceReportEntryQueue->reserveAssignedReport($reportId, $userId);
                    }
                }
            }
        }       
        
    }
    
    /*
     * Getting agencies by state id.
     * Called via AJAX
     */
    public function getAgencyByStateIdAction()
    {
        if (!$this->validateCsrfToken($this->request->getPost('csrf'))) {
            $agencyList = $this->getInvalidCSRFJsonResponse();
        } else {
            $stateId = $this->request->getPost('stateId');
            $agencyList = $this->serviceAgency->fetchActiveAgencyIdNamePairs($stateId);
        }
        
        return $this->json->setVariables($agencyList);
    }
    
    /*
     * Getting forms by state id and agency id.
     * Called via AJAX
     */
    public function getFormAction()
    {
        if (!$this->validateCsrfToken($this->request->getPost('csrf'))) {
            $formList = $this->getInvalidCSRFJsonResponse();
        } else {
            $stateId = $this->request->getPost('stateId');
            $agencyId = $this->request->getPost('agencyId');
            $duration = $this->config['activeFormsDuration'];
            $formList = $this->serviceForm->getFormIdNamePairs($stateId, $agencyId, $duration);
        }
        
        return $this->json->setVariables($formList);
    }
    
    protected function handleInvalidForm($form)
    {
        // clearing 'Select Forms for User' section
        $stack = ['stateId' => '',
            'agencyId' => '',
            'formId' => '',
            'selectBy'=>'none',
        ];
        $form->setData($stack);
        $this->addFormMessages($form);
    }
    
    protected function renderAssignedFormFromRequest($request)
    {
        $formAssigned = '';
        if (!empty($request['stateIds'])) {
            $stateIds = $request['stateIds'];
            $agencyIds = $request['agencyIds'];
            $formIds = $request['formIds'];
            $workTypeSelected = (!empty($request['workType'])) ? $request['workType'] : null;
            
            foreach ($stateIds as $key => $stateId) {
                $formAssigned .= $this->renderAssignedForm(
                    $formIds[$key],
                    $agencyIds[$key],
                    $stateId,
                    (!empty($workTypeSelected[$formIds[$key]])) ? $workTypeSelected[$formIds[$key]] : null
                );
            }
        }

        return $formAssigned;
    }
    
    protected function renderAssignedForm(
        $formId = null,
        $agencyId = null,
        $stateId = null,
        Array $workTypeSelected = null,
        $rekey = null,
        $eRekey = null)
    {
        if (empty($formId)) return false;
        
        $this->view->workTypeList = $this->serviceWorkType->getAll();
        $formWorkTypes = $this->serviceFormWorkType->fetchWorkTypesByFormId($formId);
        $this->view->formWorkTypeList = array_column($formWorkTypes, 'work_type_id');
        
        $states = $this->serviceState->fetchStateIdNamePairs($stateId);
        if (!empty($states)) {
            $state = [
                'stateId' => current(array_keys($states)),
                'nameFull' => current(array_values($states))
            ];
        } else {
            $state = [
                'stateId' => null,
                'nameFull' => null
            ];
        }
        
        $this->view->state = $state;
        $this->view->form = $this->serviceForm->getFormInfo($formId);
        
        $this->view->agencyDetails = (empty($agencyId)) ?
            $this->serviceAgency->fetchActiveByFormId($formId) :
            $this->serviceAgency->getAgencyByAgencyId($agencyId);
        
        $this->view->workTypeSelected = $workTypeSelected;
        $this->view->rekey = $rekey;
        $this->view->eRekey = $eRekey;
        
        return $this->render('admin/users/assign-form');
    }
    
    /*
     * Assigning form on the user edit page.
     * Called via AJAX
     */
    public function assignFormAction()
    {
        if (!$this->validateCsrfToken($this->request->getPost('csrf'))) {
            return $this->json->setVariables($this->getInvalidCSRFJsonResponse());
        } else {
            if (is_numeric($this->request->getPost('userId'))) {
                $rekeyPermissions = $this->serviceRekey->getUserRekeyFormPermission(
                    $this->request->getPost('userId'),
                    RekeyService::PAPER_ADDITIONAL_KEYING
                );
            }
            
            echo $this->renderAssignedForm(
                $this->request->getPost('formId'),
                $this->request->getPost('agencyId'),
                $this->request->getPost('stateId'),
                isset($rekeyPermissions) ? $rekeyPermissions : null
            );
            exit(0);
        }
    }
    
    /**
     * Check availability of Report Id before Assigning to user
     * get the Assigned report ids back
     */
    protected function getAssignedReport($userId, $reportId = null)
    {
        if (!empty($reportId)) {
            $data = [];
            //check the requested report exist in report entry queue table
            $checkReportIdExist = $this->serviceReportEntryQueue->getReportDetails($reportId);
            if (empty($checkReportIdExist)) {
                $message = 'Report not exist in Queue'; 
                $result = ['message' => $message, 'data' => $data];
                return $result;
            }
            
            $entryStages = [
                EntryStageService::STAGE_ALL,
                EntryStageService::STAGE_DYNAMIC_VERIFICATION,
                EntryStageService::STAGE_REKEY,
                EntryStageService::STAGE_ELECTRONIC_REKEY
            ];
            
            if (in_array($checkReportIdExist[0]['nameInternal'], $entryStages)) {
                //check if already report assigned to anyone
                $checksAvailability = $this->serviceReportEntryQueue->checkReportAvailabilityForAssign($reportId);
                if ($checksAvailability) {
                    //condition to check if prevoiusly processed by same operator. if same operator, won't be allowed to assign.
                    $checkAlreadyProcessedby = $this->serviceReportEntry->getLastByReportId($reportId);
                    if (!empty($checkAlreadyProcessedby) && $checkAlreadyProcessedby['userId'] == $userId) {
                        $message = 'Entered Report ID already Processed by same operator. Not allowed to assign!';
                        return ['message' => $message, 'data' => $data];
                    }                    
                   
                    $reports = [$this->serviceReport->getRelatedInfo($reportId)];                   
                    $this->view->reports = $reports;
                    $this->view->reportId = $reportId;                                     
                    $this->view->isAssigned = false;                                               
                    $this->session->reportSpecific [] = $reportId;                                                                   
                    return ['message' => '', 'data' => $this->render('admin/users/report-list')];
                } else {
                    $message = 'Entered Report ID already assigned to other operators.';
                    return ['message' => $message, 'data' => $data];
                }
            } else {
                $message = 'Entered Report ID invalid.';
                return ['message' => $message, 'data' => $data];
            }
        }
        
        //get the Assigned reports of the user  
        $this->view->isAssigned = true;         
        $this->view->reports = $this->serviceReportEntryQueue->getReservedReport($userId);
        
        return ['message' => '', 'data' => $this->render('admin/users/report-list')];
    }

    /**     
     * get the Date Range based Assigned report list 
     */
    protected function getdateRangeAssignedReports($userId)
    {
        if (!empty($userId)) {
            $assignedReports = $this->serviceReportEntryQueue->getdateRangeAssignedReports($userId);
            $this->view->reports = $assignedReports;
            $this->view->isAssigned = true;                        
            return ['count' => count($assignedReports), 'data' => $this->render('admin/users/report-list-date-range')];
                
        }       
        
    }
    
    protected function renderAssignedFormByUserId($userId)
    {
        if (empty($userId)) {
            return false;
        }
        
        $userFormPermission = $this->serviceUserFormPermission->getPermissionByUserId($userId, true);
        
        $rekeyPermissions = $this->serviceRekey->getUserRekeyFormPermission($userId, RekeyService::PAPER_ADDITIONAL_KEYING);
        
        $electronicRekey = $this->serviceRekey->getUserRekeyFormPermission($userId, RekeyService::ELECTRONIC_ADDITIONAL_KEYING);
        
        $result = '';
        foreach ($userFormPermission as $permission) {
            $result .= $this->renderAssignedForm(
                $permission['formId'],
                $permission['agencyId'],
                $permission['stateId'],
                explode(' ', $permission['workTypeId']),
                (!empty($rekeyPermissions) && is_array($rekeyPermissions)) ? in_array($permission['formId'], $rekeyPermissions) : null,
                (!empty($electronicRekey) && is_array($electronicRekey)) ? in_array($permission['formId'], $electronicRekey) : null
            );
        }
        
        return $result;
    }
    
    /**
     * Check Report Id before Assigning to user
     * Render the Assigned report ids back
     */
    protected function renderAssigningReport($formId, $startDate, $endDate)
    {
        if (!empty($formId) && !empty($startDate) && !empty($endDate)) {
            // get the Report ids from report table with status having keying and in progress and selected date range
            $reportIds = $this->serviceReport->getReportByFormId($formId, $startDate, $endDate);
            if (empty($reportIds)) {
                return ['dateRangeReport'=> ''];
            }
            
            $result = array_column($reportIds, 'reportId'); 
            $dateRangeReports = [];
            foreach($result as $key => $value)
            {               
               $dateRangeReports [] = $this->serviceReport->getRelatedInfo($value);
            }           
            $this->view->reports = $dateRangeReports;             
            $this->view->isAssigned = false;
            $this->session->dateRangeReports[$formId]= $result;                     
            return ['dateRangeReport'=> $this->render('admin/users/report-list-date-range')];
        }
    }
    
    /*
     * Assigning Report based on selected date range in user edit page.
     * Called via AJAX
     */
    public function assignReportAction()
    {
        if (!$this->validateCsrfToken($this->request->getQuery('csrf'))) {
            return $this->json->setVariables($this->getInvalidCSRFJsonResponse());
        } else {
            $queryParams = (array) $this->request->getQuery();
            
            if (empty($queryParams)) {
                $returnValue['html'] = '';
            } else {
                $returnValue = $this->renderAssigningReport(
                    $queryParams['formId'],
                    (!empty($queryParams['startDate'])) ? $queryParams['startDate'] : null,
                    (!empty($queryParams['endDate'])) ? $queryParams['endDate'] : null
                );
            }            
            $response = ['html' => $returnValue['dateRangeReport']];
            return $this->json->setVariables($response);
        }
    }
    
    /**
     * set Report on the user edit page.
     * Called via AJAX
     */
    public function setReportAction()
    {
        if (!$this->validateCsrfToken($this->request->getQuery('csrf'))) {
            return $this->json->setVariables($this->getInvalidCSRFJsonResponse());
        } else {
            $queryParams = $this->request->getQuery();
            $requestParams = (!empty($queryParams)) ? (array) $queryParams : [];
            
            $returnValue = $this->getAssignedReport(
                $requestParams['userId'],
                $requestParams['reportId']
            );
            $response = ['html' => $returnValue['data'], 'message' => $returnValue['message']];
            
            return $this->json->setVariables($response);
        }
    }

    /*
     * UnAssigning Report for the user.
     * 
     */
    public function unassignReport($userId, $reportsToUnAssign, $unassignFrom)
    {        
        if (!empty($reportsToUnAssign)) { 
            foreach ($reportsToUnAssign as $reportId) {                
                $reportEntryQueueData = $this->serviceReportEntryQueue->getQueuedData($reportId);        
                //unassign user from report_entry_queue 
                if ($reportEntryQueueData['reportAssignedTo'] == $userId && $reportEntryQueueData['userIdAssignedTo'] == null && $unassignFrom == UserService::REPORT_SPECIFIC) {  
                    $this->serviceReportEntryQueue->unAssignReport($userId, $reportId);                   
                } elseif ($reportEntryQueueData['userIdAssignedTo'] == null && $unassignFrom == UserService::DATERANGE_REPORTS) {
                    //unassign user report from user_report
                    $this->serviceReportEntryQueue->removeUserReportRecord($reportId, $userId);
                }   

            }
        }                                             
        
    }
    
    public function showAddNoteFormAction() {
        $this->layout()->setTemplate('layout/popup');
        $this->view->helperHeadTitle->append('Create Note');
        
        if (! $this->request->isPost()) {
            $this->formUserNotes->prepare();
            $this->view->form = $this->formUserNotes;
            return $this->view;
        }
    }
    
    /*
     * Adds a note for an existing user
     */
    public function addNoteAction($userId = null, $note = null)
    {
        if (!$this->validateCsrfToken($this->request->getPost('csrf'))) {
            return $this->json->setVariables($this->getInvalidCSRFJsonResponse());
        } else {
            $userId = (!empty($userId)) ? $userId : $this->request->getPost('userId');
            $note = (!empty($note)) ? $note : $this->request->getPost('note');
            $result = $this->serviceUserNote->add($userId, $note);
            
            if ($this->request->isXmlHttpRequest()) {
                exit;
            }
            
            return $result;
        }
    }
    
    public function deleteUserAction()
    {
        $requestParams = (array) $this->request->getQuery();
        $userId = $requestParams['userId'];
        
        if ($this->validateCsrfToken($this->request->getQuery('csrf'))
            && !empty($userId)
            && $this->serviceUser->getIdentityData($userId)) {
            
            // @TODO: Session value will be updated through session container
            $_SESSION['redirectCsrfToken'] = $this->request->getQuery('csrf');
            
            // Delete user from the ecrash database
            $this->serviceUser->deleteById($userId);
            $this->serviceIsit->deleteUser($userId);
            $this->setCsrfRedirectToken($this->request->getQuery('csrf'));
        }
        
        exit(0);
    }
    
    public function cancelAction()
    {
        if (!$this->validateCsrfToken($this->request->getQuery('csrf'))) {
            $this->json->setVariables($this->getInvalidCSRFJsonResponse());
        } else {
            $this->setCsrfRedirectToken($this->request->getQuery('csrf'));
        }
        exit(0);
    }
    
    public function noteHistoryAction()
    {
        $this->layout()->setTemplate('layout/popup');
        $this->view->helperHeadTitle->append('Historic Notes');
        
        $requestParams = $this->request->getQuery();
        $requestParams = (!empty($requestParams)) ? (array) $requestParams : [];
        $userId = $requestParams['userId'];
        
        $this->view->notes = $this->serviceUserNote->fetchNoteHistory($userId);
        $this->view->form = $this->formUserNoteHistory;
        $this->view->requestParams = $requestParams;
        
        return $this->view;
    }
    
    /**
     * Reset password was a page that if you went to it and put a userId on the get string it would
     * just reset that user. Its now been broken up in to two parts, resetPasswordAction is the ajax
     * part and this is the result display part. This could probably just be made in to a dialog later.
     */
    public function resetPasswordPopupAction()
    {
        $this->layout()->setTemplate('layout/popup');
        $this->view->passwordSent = $this->request->getQuery('passwordSent');
        $this->view->form = $this->formResetPassword;
        
        $requestParams = (array) $this->request->getQuery();
        $this->view->requestParams = $requestParams;
        
        return $this->view;
    }
    
    public function resetPasswordAction()
    {
        if (!$this->validateCsrfToken($this->request->getQuery('csrf'))) {
            $response = $this->getInvalidCSRFJsonResponse();
        } else {
            $userId = $this->request->getQuery('userId');
            $passwordSent = false;
            $username = $this->serviceUser->getUsernameById($userId);
            
            $adminSessionId = $this->serviceLnaaAuth->authUserAdmin();
            $this->serviceLnaaAuth->adminUnlockUserAccount($adminSessionId, $username);
            $resetResult = $this->serviceLnaaAuth->adminResetUserPassword($adminSessionId, $username);
            
            if ($resetResult->status->code == LNAAAuthService::CODE_SUCCESS) {
                $passwordSent = true;
            }
            
            $response = ['passwordSent' => $passwordSent];
        }
        
        return $this->json->setVariables($response);
    }
    
    public function configureTimeoutAction()
    {
        $this->layout()->setTemplate('layout/popup');
        $this->view->helperHeadTitle->append('Timeout Warning');
        $this->view->form = $this->formConfigureTimeout;
        return $this->view;
    }
}
