<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Auth\Adapter\REST;

use Zend\Log\Logger;
use Zend\Http\Client;
use Zend\Http\Request;
use Exception;

use Auth\Service\LNAAAuthService;

class LNAAAuthAdapter
{
    const GEN_PW_EMAIL_TO_USER = 1;
    const GENERATE_PW_EMAIL_TO_CUSTOMER = 1;
    const ENABLED = 1;
    const USER_TYPE = 'Internal';
    const USER_ID_TYPE = 'Permanent';
    const FORCE_USER_PW_RESET = 1;
    const WRONG_PW_COUNT = 0;
    const USER_INTERNAL_TYPE_STATUS = 'FTE_Contractor';
        
    /*User Status*/
    const STATUS_ENABLED = 'Enabled';
    const STATUS_DISABLED = 'Disabled';
    const STATUS_EXPIRED = 'Expired';
    
    /*Disable/Expire User Reason Codes*/
    const REASON_CODE_DECOMMISSIONED = 'DECOMMISSIONED';
    const REASON_CODE_KEYING_ERR = 'KEYING_ERR';
    const REASON_CODE_LEFT_COMPANY = 'LEFT_COMPANY';
    const REASON_CODE_NOACT_15DAYS = 'NOACT_15DAYS';
    const REASON_CODE_NOACT_365DAYS = 'NOACT_365DAYS';
    const REASON_CODE_NOACT_90DAYS = 'NOACT_90DAYS';
    const REASON_CODE_NOT_COMPLIANT = 'NOT_COMPLIANT';
    const REASON_CODE_OTHER = 'OTHER';
    const REASON_CODE_REQ_BY_AGENT = 'REQ_BY_AGENT';
    const REASON_CODE_REQ_BY_AUD = 'REQ_BY_AUD';
    const REASON_CODE_REQ_BY_CAR = 'REQ_BY_CAR';
    const REASON_CODE_REQ_BY_LEGAL= 'REQ_BY_LEGAL';
    const REASON_CODE_REQ_BY_MGR= 'REQ_BY_MGR';
    
    /**
     * @var Zend\Http\Client 
     */
    protected $lnaaHttpClient;
    
    /**
     * @var Array
     */
    protected $config;

    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    public function __construct(
        Logger $logger,
        Array $config,
        Client $lnaaHttpClient)
    {
        $this->ws = $lnaaHttpClient;
        $this->config = $config;
        $this->logger = $logger;
        $this->ws->setMethod(Request::METHOD_POST);
        $this->ws->setHeaders(['Content-Type: application/json']);
        $this->ws->setEncType('application/json');
    }
    
    /**
     * Returns instance of AuthUser result
     *
     * @return result
     */
    public function authUserAdmin()
    {
        try {
            $result = $this->authUser(
                    $this->config['registration']['user'], 
                    $this->config['registration']['password']
            );
            return $result->session_id;
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
            $errorMessage = 'Exception occured: ' . $e->getMessage();
            $this->logger->log(Logger::ERR, $origin . $errorMessage);
            return false;
        }
    }

    public function authUser($userName, $password, $domain = null)
    {
        try {
            $domain = !empty ($domain) ? $domain : 
                    $this->config['registration']['domain'];
            $groupCode = $this->getGroupCode($domain);

            $params = json_encode([
                'domain' => $domain,
                'login_id' => $userName,
                'password' => $password,
                'group_code' => $groupCode,
                'create_session' => '1',
                'return_user_data' => '1',
                'return_user_data_group_filter' => $groupCode
            ]);
            return $this->sendRequest($params, 'AuthUser');
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
            $errorMessage = 'Exception occured: ' . $e->getMessage();
            $this->logger->log(Logger::ERR, $origin . $errorMessage);
            return false;
        }
    }

    public function userGetUserSecurityQuestions($sessionId)
    {
        try {
            $params = json_encode(['session' => ['session_id' => $sessionId]]);
            return $this->sendRequest($params, 'UserGetUserSecurityQuestions');
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
            $errorMessage = 'Exception occured: ' . $e->getMessage();
            $this->logger->log(Logger::ERR, $origin . $errorMessage);
            return false;
        }
    }

    public function adminGetDomainSecurityQuestions($sessionId)
    {
        try {                        
            $params = json_encode([
                'session' => ['session_id' => $sessionId],
                'data' => ['domain' => $this->config['registration']['domain']]
            ]);
            return $this->sendRequest($params, 'AdminGetDomainSecurityQuestions');
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
            $errorMessage = 'Exception occured: ' . $e->getMessage();
            $this->logger->log(Logger::ERR, $origin . $errorMessage);
            return false;
        }
    }

    public function userAddSecurityQuestionAnswer($sessionId, $answer)
    {
        try {                        
            $params = json_encode([
                'session' => ['session_id' => $sessionId],
                'data' => ['security_question_answers' => $answer]
            ]);
            return $this->sendRequest($params, 'UserAddSecurityQuestionAnswer');
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
            $errorMessage = 'Exception occured: ' . $e->getMessage();
            $this->logger->log(Logger::ERR, $origin . $errorMessage);
            return false;
        }
    }

    public function authUserSecurityQuestion($userName, $searchResult)
    {
        try {
            $params = json_encode([
                    'domain' => $this->config['registration']['domain'],
                    'login_id' => $userName,
                    'group_code' => $this->config['registration']['domain'], 
                    'create_session' => '1',
                    'return_user_data' => '1',
                    'return_user_data_group_filter' => $this->config['registration']['domain'],
                    'answers' => $searchResult
            ]);
            return $this->sendRequest($params, 'AuthUserSecurityQuestion');
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
            $errorMessage = 'Exception occured: ' . $e->getMessage();
            $this->logger->log(Logger::ERR, $origin . $errorMessage);
            return false;
        }
    }

    public function adminChangeUserPassword($sessionId, $loginId, $password)
    {
        if(empty($password)) {
            $generateEmail = 1;
        } else {
            $generateEmail = 0;
        }
        try {
            $params = json_encode([
                'session' => ['session_id' => $sessionId],
                'data' => [
                    'login_id' => $loginId,
                    'generate_pw_email_to_customer' => $generateEmail,
                    'password' => $password,
                    'force_user_pw_reset' => $generateEmail
                ]
            ]);
            return $this->sendRequest($params, 'AdminChangeUserPassword');
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
            $errorMessage = 'Exception occured: ' . $e->getMessage();
            $this->logger->log(Logger::ERR, $origin . $errorMessage);
            return false;
        }
    }

    public function adminResetUserPassword($sessionId, $userName)
    {
        try {
            $domain = $this->config['registration']['domain'];
            $loginId = $userName . '@' . $domain;
            $params = json_encode([
                'session' => ['session_id' => $sessionId],
                'data' => [
                    'login_id' => $loginId,
                    'generate_pw_email_to_customer' => self::GENERATE_PW_EMAIL_TO_CUSTOMER,
                    'password' => '', /*password is a required field*/
                    'force_user_pw_reset' => self::FORCE_USER_PW_RESET
                ]
            ]);
            return $this->sendRequest($params, 'AdminChangeUserPassword');
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
            $errorMessage = 'Exception occured: ' . $e->getMessage();
            $this->logger->log(Logger::ERR, $origin . $errorMessage);
            return false;
        }
    }
    
    public function adminUnlockUserAccount($sessionId, $userId)
    {
        try {
            $domain = $this->config['registration']['domain'];
            $loginId = $userId . '@' . $domain;
            $params = json_encode([
                    'session' => ['session_id' => $sessionId],
                    'data' => ['login_id' => $loginId]
            ]);
            return $this->sendRequest($params, 'AdminUnlockUser');
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
            $errorMessage = 'Exception occured: ' . $e->getMessage();
            $this->logger->log(Logger::ERR, $origin . $errorMessage);
            return false;
        }
    }

    public function adminGetUserData($sessionId, $loginId)
    {
        try {                        
            $params = json_encode([
                'session' => ['session_id' => $sessionId],
                'data' => ['login_id' => $loginId]
            ]);
            return $this->sendRequest($params, 'AdminGetUserData'); 
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
            $errorMessage = 'Exception occured: ' . $e->getMessage();
            $this->logger->log(Logger::ERR, $origin . $errorMessage);
            return false;
        }
    }

    public function isValidLNAAPassword($loginId, $password)
    {
        $sessionId = null;
        $domain = null;
        $userNameInfo = explode("@", $loginId);
        if (!empty($userNameInfo[1])) {
            $loginId = $userNameInfo[0];
            $domain = $userNameInfo[1];
        }
        $results = $this->authUser($loginId, $password, $domain);
        if ($results->status->code == LNAAAuthService::CODE_SUCCESS
                || $results->status->code == LNAAAuthService::PASSWORD_RESET_REQUIRED
                || $results->status->code == LNAAAuthService::PASSWORD_EXPIRED) {

                $sessionId = $results->session_id;
        } else {
                throw new Exception('AuthUser returned: ' . $results->status->message);
        }
        return $sessionId;
    }

    public function changeLNAAPassword($sessionId, $password)
    {
        $params = json_encode([
                'session' => ['session_id' => $sessionId],
                'data' => ['password' => $password]
        ]);
        $results = $this->sendRequest($params, 'UserChangeUserPassword');
        if ($results->status->code != LNAAAuthService::CODE_SUCCESS) {
            throw new Exception('Password not changed. ' . $results->status->message);
        }
        return $results;
    }
        
        /**
     * Adds a user
     * 
     * @param string $sessionId
     * @param string $employeeId
     * @param string $userName
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @return type
     */
    public function adminAddUser(
        $sessionId, 
        $employeeId,
        $userName,  
        $firstName, 
        $lastName, 
        $email) 
    {                
            $results = [
                    'result' => false,
                    'message' => null
                ];

            try {
                    $params = json_encode([
                        'session' => ['session_id' => $sessionId],
                        'data' => [
                            'domain' => $this->config['registration']['domain'],
                            'employee_id' => $employeeId,
                            'login_id' => $userName,
                            'gen_password_email_to_user' => self::GEN_PW_EMAIL_TO_USER,
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'email_address' => $email,
                            'force_user_pw_reset' => self::FORCE_USER_PW_RESET,
                            'user_type' => self::USER_TYPE,
                            'id_type' => self::USER_ID_TYPE,
                            'internal_type_status' => self::USER_INTERNAL_TYPE_STATUS
                        ]
                    ]);
                    $response = $this->sendRequest($params, 'AdminAddUser');
                    if ($response->status->code == LNAAAuthService::CODE_SUCCESS) {
                            $results['result'] = true;
                    } else {
                            $errorMessage = "Failed adding user $userName. AdminAddUser service call returned error: " 
                                    . $response->status->message;
                            $results['message'] = $errorMessage;
                            $this->logger->log(Logger::ERR, $errorMessage);
                    }
            } catch (Exception $e) {
                    $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
                    $errorMessage = 'Exception occured: ' . $e->getMessage();
                    $results['message'] = $errorMessage;
                    $this->logger->log(Logger::ERR, $origin . $errorMessage);
            }       

            return $results;
    }

    public function adminUpdateUser($sessionId, $userName, $firstName, $lastName, $email)
    {
        try {
                $domain = $this->config['registration']['domain'];
                $loginId = $userName . '@' . $domain;
                $params = json_encode([
                    'session' => ['session_id' => $sessionId],
                    'data' => [
                        'login_id' => $loginId,
                        'update_values' => [
                            ['field' => 'first_name', 'value' => $firstName],
                            ['field' => 'last_name', 'value' => $lastName],
                            ['field' => 'email_address', 'value' => $email]
                        ]
                    ]
                ]);
                return $this->sendRequest($params, 'AdminUpdateUser');
        } catch (Exception $e) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
                $errorMessage = 'Exception occured: ' . $e->getMessage();
                $this->logger->log(Logger::ERR, $origin . $errorMessage);
                return false;
        }
    }
        
        public function adminUpdateUserStatus($sessionId, $userName, $userStatus, $reasonCode = null, $message = null)
        {
            try {
                    $userNameInfo = explode("@", $userName);
                    $domain = (!empty($userNameInfo[1])) ? $userNameInfo[1] : 
                        $this->config['registration']['domain'];
                    $loginId = $userNameInfo[0] . '@' . $domain;
                    $data = [
                            'login_id' => $loginId,
                            'status' => $userStatus
                    ];
                    if (!empty($message)) {
                        $data['message'] = $message;
                    }
                    if ($userStatus != self::STATUS_ENABLED)
                        if (!empty($reasonCode)) {
                            $data['reason_code'] = $reasonCode;
                        } else {
                            $errorMessage = "Failed updating status of user $userName. "
                                    . "Reason code is required if you are disabling or expiring a user." ;
                            $this->logger->log(Logger::ERR, $errorMessage);
                            return false;
                    }
                    if ($userStatus == self::STATUS_EXPIRED) {
                        $data['delete_roles'] = '1';
                    }
                    $params = json_encode([
                        'session' => ['session_id' => $sessionId],
                        'data' => $data
                    ]);
                    return $this->sendRequest($params, 'AdminUpdateUserStatus');
            } catch (Exception $e) {
                    $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
                    $errorMessage = 'Exception occured: ' . $e->getMessage();
                    $this->logger->log(Logger::ERR, $origin . $errorMessage);
                    return false;
            }
        }
        
        /**
     * Validates an employee id
     * 
     * @param int $employeeId the employee id to validate
     * @return boolean $isValid whether employee id is valid
     */
    public function isValidEmployeeId($employeeId) 
        {
            $isValid = false;
            try {
                    $result = $this->authUser(
                        $this->config['lnaa']['registration']['user'],
                        $this->config['lnaa']['registration']['password'],
                        $this->config['lnaa']['registration']['domain']
                    );
                    $sessionId = $result->session_id;
                    $params = json_encode([
                        'session' => ['session_id' => $sessionId],
                        'data' => ['employee_id' => $employeeId]
                    ]);
                    $results = $this->sendRequest($params, 'AdminGetEmployeeAuthInfo');
                    if ($results->status->code == LNAAAuthService::CODE_SUCCESS) {
                        $isValid = true;
                    }
            } catch (Exception $e) {
                    $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
                    $errorMessage = 'Exception occured: ' . $e->getMessage();
                    $this->logger->log(Logger::ERR, $origin . $errorMessage);
            }
            return $isValid;
    }
        
        public function sendRequest($params, $method)
        {
           try{
               $this->ws->setUri($this->config['app']['lnaaAuthUrl'] . $method);
                $response = $this->ws->setRawBody($params)->setEncType('application/json')->send();
                $result = json_decode($response->getBody());
                if ($result->status->code != LNAAAuthService::CODE_SUCCESS) {
                    $errorMessage = $method . ' service call returned error: ' 
                            . $result->status->message;
                    $this->logger->log(Logger::ERR, $errorMessage);
                    $lastRequest = preg_replace('/"password":"(\S+?)"/i', '"password":"***"', $this->ws->getLastRawRequest());
                    $lastResponse = $this->ws->getLastRawResponse();
                    $this->logger->log(Logger::ERR, 'Last Request from ' . $method . ': ' . var_export($lastRequest, true));
                   $this->logger->log(Logger::ERR, 'Last Response from ' . $method . ': ' . var_export($lastResponse, true));
                }
                return $result;
            } catch (Exception $e) {                
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
                $errorMessage = 'Exception occured : ['.$method.'] ' . $e->getMessage();
                $this->logger->log(Logger::ERR, $origin . $errorMessage);
                if ($this->config['app']['env'] == 'local') {
                    // @TODO: Removed in future
                    // Mock-up response for the local environment                    
                    return $this->config['ws_response']->$method;
                }
                return ;
            }
    }
    
    /*
     * Determine the group code for the user based on the domain
     * @param string $domain the domain of the user (risk, noam, or nothing)
     * @return string $groupCode the group code
     */
    protected function getGroupCode($domain)
    {
        $groupCode = $domain;
        if (in_array($domain, $this->config['internalDomains'])) {
            $groupCode = $this->config['registration']['domain'];
        }
        return $groupCode;
    }
}
