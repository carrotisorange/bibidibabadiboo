<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Auth\Controller;

use Zend\Session\SessionManager;
use Zend\Session\Container;
use Zend\Log\Logger;
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\HelperPluginManager;
use Zend\Mvc\Plugin\FlashMessenger;
use Zend\View\Helper\HeadTitle;
use Exception;
use RunTimeException;

use Base\Controller\BaseController;
use Base\Service\UserService;
use Auth\Form\LoginForm;
use Auth\Form\ForgotPasswordForm;
use Auth\Form\SecurityQuestionForm;
use Auth\Form\ChangePasswordForm;
use Auth\Adapter\REST\LNAAAuthAdapter;
use Auth\Service\LNAAAuthService;
use Auth\Service\LNAAAdapterService;
use Auth\Adapter\Soap\IpRestrictAdapter;
use Base\Helper\LnHelper;

/**
 * AuthController contains user authentication related actions such as Login or ForgotPassword.
 */
class AuthController extends BaseController
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
     * @var Base\Service\UserService
     */
    protected $serviceUser;

    /**
     * @var Zend\Authentication\AuthenticationService
     */
    private $serviceAuth;

    /**
     * @var use Auth\Service\LNAAAuthService
     */
    private $lnaaAuthService;
    
    /**
     * @var Auth\Service\LNAAAdapterService
     */
    private $lnaaAdapterService;
    
    /**
     * @var Auth\Form\LoginForm
     */
    protected $loginForm;
    
    /**
     * @var Auth\Form\ForgotPasswordForm
     */
    protected $forgotpasswordForm;
    
    /**
     * @var Auth\Form\SecurityQuestionForm
     */
    protected $securityQuestionForm;
    
    /**
     * @var Auth\Form\ChangePasswordForm
     */
    protected $changePasswordForm;
    
    /**
     * @var Base\Helper\LnHelper
     */
    protected $lnHelper;
    
    /**
     * @var Zend\View\Helper\HeadTitle
     */
    protected $helperHeadTitle;
    
    public function __construct(
        Array $config,
        Container $session,
        Logger $logger,
        AuthenticationService $serviceAuth,
        UserService $serviceUser,        
        LNAAAuthService $lnaaAuthService,
        LNAAAdapterService $lnaaAdapterService,
        LoginForm $loginForm,
        ForgotPasswordForm $forgotpasswordForm,
        SecurityQuestionForm $securityQuestionForm,
        ChangePasswordForm $changePasswordForm,
        LnHelper $lnHelper,
        HeadTitle $helperHeadTitle)
    {
        $this->config = $config;
        $this->session = $session;
        $this->logger = $logger;
        $this->serviceAuth = $serviceAuth;
        $this->serviceUser = $serviceUser;             
        $this->LnaaAuthService = $lnaaAuthService;
        $this->lnaaAdapterService = $lnaaAdapterService;
        $this->loginForm = $loginForm;
        $this->forgotPasswordForm = $forgotpasswordForm;
        $this->securityQuestionForm = $securityQuestionForm;
        $this->changePasswordForm = $changePasswordForm;
        $this->lnHelper = $lnHelper;
        
        parent::__construct();
        
        $this->view->helperHeadTitle = $helperHeadTitle;
    }
    
    /**
     * Authenticates user with the given username and password.
     */
    public function loginAction()
    {
        // If user already logged in, just re-direct to index Page
        if ($this->serviceAuth->hasIdentity()) {
            return $this->redirect()->toRoute('index');
        }
        $this->loginForm->prepare();
        $errorMessage = '';
        $this->view->helperHeadTitle->prepend('Welcome to');
        
        // Check if user has submitted the form
        if ($this->request->isPost()) {
            $formData = $this->request->getPost();
            $this->loginForm->setData($formData);
            
            if ($this->loginForm->isValid()) {
                $userName = $this->request->getPost('username', '');
                $password = $this->request->getPost('password', '');
                $loginAttemptCount = 0;
                $lnaaResult = null;
                
                //Check if username contains a domain - it is an internal user
                $userNameInfo = explode("@", $userName);
                $userNameWithoutDomain = $userNameInfo[0];
                $userInternalInfo = $this->serviceUser->getUserIsInternalAndDomainInfo($userName);
                $isInternal = $userInternalInfo['isInternal'];
                $domain = $userInternalInfo['domain'];
  
                //Initailzing LNAAAdapter through seperate service
                $this->lnaaAdapterService->initLNAAAdapter($userNameWithoutDomain, $password, $domain);
                $result = $this->serviceAuth->authenticate($this->lnaaAdapterService);
                
                //To mitigate the Session fixation attack
                $this->session->getManager()->regenerateId();
                
                //if user Credentails is valid
                if ($result->isValid()) {
                    // Find the Userdetails with given UserName.
                    $userId = $this->serviceUser->getUserIdByUsername($userName);
                    $this->serviceUser->login($userId); 
                    $userInfo = $this->serviceUser->getUserRowInfoByUsername($userName);
                    if (empty($userId)) {
                        $errorMessage = 'Invalid User ID.';
                        $this->serviceAuth->clearIdentity(); 
                    } else if (!empty($userInfo['userId']) && !($userInfo['isActive'] == true)) {
                        $errorMessage = 'Inactive User ID. Please contact your administrator.';
                        $this->serviceAuth->clearIdentity(); 
                    } else {
                        $lnaaResult = $this->serviceAuth->getIdentity();
                        $identity = (object) array_merge($lnaaResult, $userInfo);
                        $sessionId = $identity->session_id;
                        if ($lnaaResult['code'] == LNAAAuthService::PASSWORD_RESET_REQUIRED || $lnaaResult['code'] == LNAAAuthService::PASSWORD_EXPIRED) {
                            $this->serviceAuth->clearIdentity();
                            if ($isInternal) {
                                $errorMessage = ($lnaaResult['code'] == LNAAAuthService::PASSWORD_RESET_REQUIRED) ? 'Password Reset Required.' : 'Password Expired.';
                                $errorMessage .= ' Please contact Helpdesk.';
                                return $this->redirect()->toRoute('login');
                            } else {
                                $resultData = [
                                    'loginId' => $userName,
                                    'session_id' => $identity->session_id
                                ];
                                return $this->redirect()->toRoute('change-password', $resultData);
                            }
                        }
                        $this->serviceAuth->getStorage()->write($identity);
                        $loginAttemptCount = $identity->loginAttemptCount;
                        //if internal user, just login all the way
                        if ($isInternal) {
                            $resultData = [
                                'loginId' => $userName,
                                'sessionID' => $identity->session_id
                            ];
                            return $this->redirect()->toRoute('login', $resultData);
                        } else {
                            $maxIdleDays = $this->config['app']['user']['maxIdleDays'];
                            // compare the LastLoginTime of the user with our maxIdleDays Configuration.
                            if (!$this->serviceUser->checkLastLoginTime($identity, $maxIdleDays)) {
                                //Change reasonCode and message if maxIdleDays config value changes. Currently set to 90 days.
                                    $reasonCode = LNAAAuthAdapter::REASON_CODE_NOACT_90DAYS; 
                                    $message = "No activity within $maxIdleDays days.";
                                $this->serviceUser->setPasswordInactive($userName, $reasonCode, $message);
                                $this->serviceAuth->clearIdentity();
                                $errorMessage = $message . "User disabled. Please contact your system Administrator.";
                            } else {
                                /**
                                 * In some instances when user of this application comes to the website through proxies
                                 * clientIp returned will have multiple comma separated values ($_SERVER['HTTP_X_FORWARDED_FOR'] reads
                                 * from the X-FORWARDED-FOR header):
                                 * client, proxy1, proxy2, ... https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Forwarded-For
                                 * In that case we need to make sure we return the first IP which is the actual client's IP
                                 */
                                $clientIp = explode(',', $this->lnHelper->getClientIP());
                                $clientIp = trim($clientIp[0]);
                                //IP RESTRICT CHECK 
                                $allow = $this->serviceUser->IpRestrictCheck($identity->username, $clientIp);

                                if ($allow == UserService::IPRESTRICT_ALLOW) {
                                    $resultData = [
                                        'loginId' => $userName,
                                        'sessionID' => $identity->session_id
                                    ];
                                    return $this->redirect()->toRoute('login', $resultData);
                                } else if ($allow == UserService::IPRESTRICT_ROAMING) {
                                    $cookieName = $this->config['session']['cookie'];
                                    if (isset($_COOKIE[$cookieName])) {
                                        $resultData = [
                                            'loginId' => $userName,
                                            'sessionID' => $identity->session_id
                                        ];
                                        return $this->redirect()->toRoute('login', $resultData);
                                    } else {
                                        $this->serviceAuth->clearIdentity();
                                            $resultLoginData = [
                                                'loginId' => $userName,
                                                'from' => 'ip_restriction'
                                            ];
                                            return $this->redirect()->toRoute('security-question', $resultLoginData);
                                        }
                                } else {
                                    $this->serviceAuth->clearIdentity();
                                    $errorMessage = 'Your account does not have access at this time. Please contact your System Administrator.'; 
                                    return $this->redirect()->toRoute('login');
                                }
                            }
                        }
                    }
                } else {
                    $isDisabled = false;
                    $lnaaResult = $result->getIdentity();
                    if (isset($lnaaResult['code']) && $lnaaResult['code'] == LNAAAuthService::ACCOUNT_DISABLED) {
                        $errorMessage = 'Inactive User ID. Please contact your administrator.';
                        $this->serviceAuth->clearIdentity();
                        $isDisabled = true;
                    }
                    if (!$isDisabled) {
                        $loginAttemptCount = $this->serviceUser->increaseLoginAttempt($userName);
                        $errorMessage = 'Invalid User ID or Password.';
                        $this->serviceAuth->clearIdentity();

                        $maxLoginAttemptCount = $this->config['app']['user']['maxLoginAttemptCount'];
                        if ($loginAttemptCount >= $maxLoginAttemptCount) {
                                $reasonCode = LNAAAuthAdapter::REASON_CODE_OTHER;
                                $message = "Exceeded number of failed log in attempts";
                                $this->serviceUser->setPasswordInactive($userName, $reasonCode, $message, $isInternal);
                                $errorMessage = 'You have exceeded the number of failed log in attempts. User disabled. Please contact your system Administrator.';
                        }
                    }                    
                }                                             
                if (empty($errorMessage)) {
                    // Reset the logged users reportentry realted details 
                    $this->serviceUser->resetUser($identity->userId);
                    // Refresh the identity with the latest information
                    $identity = (object)$this->serviceUser->getIdentityData($identity->userId);
                    if ($identity->role == UserService::ROLE_OPERATOR || $identity->role == UserService::ROLE_SUPER_OPERATOR) {
                        return $this->redirect()->toRoute('reportentry');
                    } else {
                        return $this->redirect()->toRoute('index');
                    }
                }
            } else {
                $errorMessage = 'The User ID and Password cannot be empty.';
            }
        }
        
        if (!empty($errorMessage)) {
            $this->flashMessenger()->addErrorMessage($errorMessage);
        }
        
        $this->view->form = $this->loginForm;
        $this->view->errorMessage = $errorMessage;
        return $this->view;
    }
    
    public function logoutAction()
    {
        if ($this->serviceAuth->hasIdentity()) {
            $this->serviceUser->updateLastActivity($this->identity()->userId, true);
            // Reset the logged users reportentry realted details
            $this->serviceUser->resetUser($this->identity()->userId);
            $this->serviceAuth->clearIdentity();
        }
        $this->session->getManager()->destroy();
        $this->session->getManager()->regenerateId();
        $this->session->getManager()->destroy();
        return $this->redirect()->toRoute('login');
    }
    
    public function changePasswordAction()
    {
        $client = $this->LnaaAuthService;
        $userName = $this->params()->fromRoute('loginId');
        $sessionId = $this->params()->fromRoute('session_id');
        $questions = [];
        $queueResult = $client->userGetUserSecurityQuestions($sessionId);
        $resultMessage = '';
        // @TODO: The following sec_questions variable will be removed in future
        $this->view->sec_questions = 1;
        
        if (empty($queueResult->sec_questions)) {
            $adminsessionId = $client->authUserAdmin();
            $lnaaQuestions = $client->adminGetDomainSecurityQuestions($adminsessionId);
            $questions = $this->objectToArray($lnaaQuestions->sec_questions);
            
            $this->changePasswordForm->prepareQuestion($questions);
            $this->view->sec_questions = 0;
        }
        $data = ['result' => $questions, 'isChangeUserPwd' => false];
        $this->changePasswordForm->prepare()->setData($data);
        
        $this->changePasswordForm->add($this->getCsrfElement());
        
        // Check if user has submitted the form
        if ($this->request->isPost()) {
            $formData = $this->params()->fromPost();
            $this->changePasswordForm->setData($formData);
            if ($this->changePasswordForm->isValid()) {
                $passwordCurrent = $this->request->getPost('passwordCurrent', '');
                $passwordNew = $this->request->getPost('passwordNew', '');
                $userName = $this->params()->fromRoute('loginId', '');
                
                try {
                    $results = $client->changeLNAAPassword($sessionId, $passwordNew);    
                    if (!empty($results) && $this->request->getPost('question_1', '') 
                                                && $results->status->code == LNAAAuthService::CODE_SUCCESS) {
                        
                        $insertAnswer = [
                            [
                                'question_id' => $formData['question_1'],
                                'answer' => $formData['answer_1']
                            ],
                            [
                                'question_id' => $formData['question_2'],
                                'answer' => $formData['answer_2']
                            ],
                            [
                                'question_id' => $formData['question_3'],
                                'answer' => $formData['answer_3']
                            ],
                            [
                                'question_id' => $formData['question_4'],
                                'answer' => $formData['answer_4']
                            ]
                        ];

                        $client->userAddSecurityQuestionAnswer($sessionId, $insertAnswer);
                    }
                } catch (Exception $e) {
                    $resultMessage = $e->getMessage();
                    // @TODO: Message should be displayed in the form
                    $this->changePasswordForm->get('passwordNew')->setMessages([$resultMessage]);
                    $this->logger->log(Logger::ERR, 'Error message from LNAA: ' . $e->getMessage());
                }
            }
            
            if (empty($resultMessage) && !empty($passwordNew) && !empty($passwordCurrent)) {
                $userId = $this->serviceUser->getUserIdByUsername($userName);
                $this->serviceUser->login($userId);
                
                $freshIdentity = $this->serviceUser->getIdentityData($userName);
                $identity = (object) array_merge($freshIdentity, ['sessionId' => $sessionId]);
                $this->serviceAuth->getStorage()->write($identity);
                
                if ($identity->role == UserService::ROLE_OPERATOR) {
                    return $this->redirect()->toRoute('report-entry');
                } else {
                    $this->setRoamingCookie($sessionId);
                    return $this->redirect()->toRoute('index');
                }
            }
        }
        
        $this->addFormMessages($this->changePasswordForm);
        $this->view->form = $this->changePasswordForm;
        $this->view->loginId = $userName;
        $this->view->sessionId = $sessionId;
        return $this->view;
    }
    
    public function changeUserPasswordAction()
    {
        $this->changePasswordForm->prepare();
        $client = $this->LnaaAuthService;
        
        // Check if user has submitted the form
        if ($this->request->isPost()) {
            $formData = $this->request->getPost();
            $this->changePasswordForm->setData($formData);
            
            if ($this->changePasswordForm->isValid()) {
                $passwordCurrent = $this->request->getPost('passwordCurrent', '');
                $passwordNew = $this->request->getPost('passwordNew', '');
                $userName = $this->request->getPost('username', '');
                $resultMessage = '';
                
                $userInternalInfo = $this->serviceUser->getUserIsInternalAndDomainInfo($userName);
                $isInternal = $userInternalInfo['isInternal'];

                if ($isInternal) {
                    $resultMessage = "For Internal Users: Please contact Helpdesk to change or reset your password.";
                    $this->changePasswordForm->get('passwordNew')->setMessages([$resultMessage]);
                } else {
                    try {
                        $sessionId = $client->isValidLNAAPassword($userName, $passwordCurrent);
                        if ($sessionId) {
                            $client->changeLNAAPassword($sessionId, $passwordNew);
                        } else {
                            $resultMessage = "Invalid User ID.";
                            $this->changePasswordForm->get('passwordNew')->setMessages([$resultMessage]);
                        }
                    } catch (Exception $e) {
                        $resultMessage = $e->getMessage();
                        $this->changePasswordForm->get('passwordNew')->setMessages([$resultMessage]);
                    }

                    if (empty($resultMessage)) {
                        $userId = $this->serviceUser->getUserIdByUsername($userName);
                        $this->serviceUser->login($userId);
                        $freshIdentity = $this->serviceUser->getIdentityData($userName);
                        $identity = (object) array_merge($freshIdentity, ['sessionId' => $sessionId]);
                        $this->serviceAuth->getStorage()->write($identity);

                        if ($identity->role == UserService::ROLE_OPERATOR ) {
                            return $this->redirect()->toRoute('reportentry');
                        } else {
                            $this->setRoamingCookie($sessionId);
                            return $this->redirect()->toRoute('index');
                        }
                    }
                }
            }
        }
        $this->addFormMessages($this->changePasswordForm);
        $this->view->form = $this->changePasswordForm;
        return $this->view;
    }
    
    public function forgotPasswordAction()
    {
        $client = $this->LnaaAuthService;
        $this->forgotPasswordForm->prepare();
        $errorMessage = '';
        
        // Check if user has submitted the form
        if ($this->request->isPost()) {
            $formData = $this->request->getPost();
            $this->forgotPasswordForm->setData($formData);
            
            if ($this->forgotPasswordForm->isValid()) {
                $userName = $this->request->getPost('username', '');
                $email = $this->request->getPost('email', '');
                
                $userNameInfo = explode("@", $userName);
                $userNameWithoutDomain = $userNameInfo[0];
                $userInternalInfo = $this->serviceUser->getUserIsInternalAndDomainInfo($userName, $email);
                $isInternal = $userInternalInfo['isInternal'];
                $domain = $userInternalInfo['domain'];
                
                $sessionId = $client->authUserAdmin();
                $loginId = $userNameWithoutDomain . '@' . $domain;
                $result = $client->adminGetUserData($sessionId, $loginId); 
                if ($result->status->code == LNAAAuthService::CODE_SUCCESS) {
                    $userEmail = $result->user_data->user_info->email_address;
                    if ($email == $userEmail) {
                        if ($isInternal) {
                            $errorMessage = 'For Internal Users: Please contact Helpdesk to recover or reset your password.';
                        } else {
                            $resultData = [
                                'loginId' => $userName,
                                'session_id' => $sessionId,
                                'from' => 'forgot_password'
                            ];
                            return $this->redirect()->toRoute('security-question', $resultData);
                        }
                    } else {
                        $errorMessage = 'Invalid email address for User ID';
                    }
                } else {
                    $errorMessage = "User ID does not exist.";
                }
            }
        }

        if (!empty($errorMessage)) {
            $this->flashMessenger()->addErrorMessage($errorMessage);
        }
        
        $this->addFormMessages($this->forgotPasswordForm);
        $this->view->form = $this->forgotPasswordForm;
        return $this->view;
    }
    
    public function securityQuestionAction()
    {
        $client = $this->LnaaAuthService;
        $randomQuestionArray = $this->request->getPost('randomQuestions', '');
        if (empty($randomQuestionArray)) {
            $randomQuestionArray = $this->randomArray(0, 3, 2);
        }
        $userName = $this->params()->fromRoute('loginId');
        $sessionId = $client->authUserAdmin();
        $from = $this->params()->fromRoute('from');
        $isInternal = $this->params()->fromRoute('isInternal');
        $domain = $this->config['registration']['domain'];
        $loginId = $userName. '@' .$domain;
        $userData = $client->adminGetUserData($sessionId, $loginId);
        $authQuestion = $userData->user_data->sec_questions;
        foreach ($randomQuestionArray as $key => $value) {
            $authQuestions[] = $authQuestion[$value];
        }
        $resultMessage = '';
        $authQuestions = $this->objectToArray($authQuestions);
        
        $this->securityQuestionForm->prepare();
        // Check if user has submitted the form
        if ($this->request->isPost()) {
            $formData = $this->request->getPost();
            $this->securityQuestionForm->setData($formData);
            
            if ($this->securityQuestionForm->isValid()) {
                $searchResult = [
                    [
                        'question_id' => $authQuestions['0']['question_id'],
                        'answer' => $formData['answer_1']
                    ],
                    [
                    'question_id' => $authQuestions['1']['question_id'],
                    'answer' => $formData['answer_2']
                    ],
                ];
                $results = $client->authUserSecurityQuestion($userName, $searchResult);
                                
                if (!empty($results->session_id)) {
                    $sessionId = $results->session_id;
                    if ($from == 'forgot_password') {
                        $adminSessionId = $this->params()->fromRoute('session_id');
                        try {
                            $results = $client->adminChangeUserPassword($adminSessionId, $loginId);                            
                            if ($results->status->code == LNAAAuthService::CODE_SUCCESS) {
                                $resultMessage = "Your password has been sent to your e-mail address";
                            } else {
                                $resultMessage = "Error occured in sending mail. Contact your Administrator";
                            }
                        } catch (Exception $e) {
                            $resultMessage = "Your password has been reset, but could not be sent to your e-mail Address. Please contact the Administrator";
                        }
                    } elseif ($from == 'ip_restriction') {
                        $userId = $this->serviceUser->getUserIdByUsername($userName);
                        $this->serviceUser->login($userId);
                        
                        $freshIdentity = $this->serviceUser->getIdentityData($userName);
                        $identity = (object) array_merge( $freshIdentity, ['sessionId' => $sessionId]);
                        $this->serviceAuth->getStorage()->write($identity);
                        
                        if ($identity->role == UserService::ROLE_OPERATOR) {
                            $this->redirect()->toRoute('report-entry');
                        } else {
                            $this->setRoamingCookie($sessionId);
                            $this->redirect()->toRoute('index');
                        }
                    }
                } else {
                    $resultMessage = "Invalid security question answer.";
                }
            }
        }
        
        $this->addFormMessages($this->securityQuestionForm);
        $this->view->form = $this->securityQuestionForm;
        $this->view->username = $userName;
        $this->view->authQuestions = $authQuestions;
        $this->view->randomQuestions = $randomQuestionArray;
        $this->view->resultMessage = $resultMessage;
        return $this->view;
    }       
    
    protected function randomArray($min, $max, $count)
    {
        $randomQuestionArray = [];
        if ($count < ($max - $min)) {
            $numbers = range($min, $max);
            shuffle($numbers);
            $randomQuestionArray = array_slice($numbers, 0, $count);
        }
        return $randomQuestionArray;
    }
    
    public function objectToArray($data)
    {
        if (is_array($data) || is_object($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = $this->objectToArray($value);
            }
            return $result;
        }
        return $data;
    }
    
    public function setRoamingCookie($sessionId)
    {
        $numberOfHours = $this->config['session']['cookietimeout'];
        $cookieName = $this->config['session']['cookie'];
        $cookieValue = $sessionId;
        $dateOfExpiry = time() + 60 * 60 * $numberOfHours;
        // if this is an https connection then set cookie to secure.
        $cookieSecure = empty($_SERVER['HTTPS']) ? false : true;
        setcookie($cookieName, $cookieValue, $dateOfExpiry, "/", null, $cookieSecure, true);
    }
    
    public function checkConcurrentUserLoginAction()
    {
        if (!$this->validateCsrfToken($this->request->getPost('csrf'))) {
            $response = $this->getInvalidCSRFJsonResponse();
        } else {
            $response = ['logout' => false];
            if ($this->serviceAuth->hasIdentity()) {
                $userInfo = $this->identity();
                $userInfoFresh = $this->serviceUser->getIdentityData($userInfo->userId);
                if ($userInfo->loginCounter < $userInfoFresh['loginCounter']) {
                    $response['logout'] = true;
                }
            }
        }
        
        return $this->json->setVariables($response);
    }
    
    public function remoteAction()
    {
        $what = $this->request->getPost('w');
        switch ($what) {
            // for security standards concurrent users implementation
            case 'sessionwarning':
                $this->checkSessionOut();
                break;
            case 'resetsessiontime':
                $this->ResetSessionTimeOut();
                break;
        }
        
        exit(0);
    }
    
    /**
     * Checks if it is time to warn or log a user out after a period of inactivity
     * 
     * @see setfocus.js
     * @return null - PRINTS TO OUTPUT
     */
     public function checkSessionOut() {
        if (!$this->validateCsrfToken($this->request->getPost('csrf'))) {
            echo 'csrferror';
        } else {
            $lastActivityTime = $this->getLastActivityTime();
            if (is_null($lastActivityTime)) {
                $this->ResetSessionTimeOut();
            } else {
                $timeSinceLastActivity = time() - $lastActivityTime;
                
                $sessionTimeOut = $this->config['session']['timeout'] * 60;
                $sessionTimeOutWarning = $this->config['session']['timeoutWarning'] * 60;
                
                if ($timeSinceLastActivity > $sessionTimeOut) {
                    if ($this->serviceAuth->hasIdentity()) {
                        echo 'logout';
                    }
                } elseif (($timeSinceLastActivity >= $sessionTimeOutWarning)
                    || ($sessionTimeOutWarning > ($sessionTimeOut - $timeSinceLastActivity))) {
                    // Its time to show the warning again or timeout will happen before another warning is scheduled
                    if ($this->serviceAuth->hasIdentity()) {
                        echo 'showform';
                    }
                } else {
                    // Not time to show the form yet so do nothing.
                }
            }
        }
    }
    
    /**
     * Get the timestamp for the last activity recorded in the current session
     * 
     * @return int
     */
    public function getLastActivityTime() {
        $time = null;
        if (isset($this->session->last_activity)) {
            $time = $this->session->last_activity;
        }
        
        return $time;
    }
    
    /**
     * Update the timestamp for last activity to the current time
     * 
     * @return null
     */
    public function ResetSessionTimeOut() {
        $this->session->last_activity = time();
    }
}
