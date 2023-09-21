<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Auth\Service;

use Zend\Log\Logger;

use Base\Service;
use Base\Service\BaseService;
use Auth\Adapter\REST\LNAAAuthAdapter;

class LNAAAuthService extends BaseService 
{
    const CODE_SUCCESS = 0;
    const PASSWORD_RESET_REQUIRED = 9;
    const PASSWORD_EXPIRED = 10;
    const ACCOUNT_DISABLED = 11;
    
    /**
    * @var wsLNAAAuth ws LNAAAuth
    */
    protected $wsLNAAAuth;

    /**
    * @var Array
    */
    protected $config;

    /**
    * @var Zend\Log\Logger
    */
    protected $log;

    /**
    * @var Auth\Adapter\REST\LNAAAuthAdapter
    */
    protected $adapterLNAAAuth;
    
    public function __construct(Logger $log, Array $config, LNAAAuthAdapter $adapterLNAAAuth)
    {
        $this->config = $config;
        $this->log = $log;
        $this->wsLNAAAuth = $adapterLNAAAuth;
    }
    
    public function userGetUserSecurityQuestions($sessionId)
    {
        return $this->wsLNAAAuth->userGetUserSecurityQuestions($sessionId);
    }
    
    public function adminAddUser($sessionId, $employeeId, $userName, $firstName, $lastName, $email)
    {
        return $this->wsLNAAAuth->adminAddUser($sessionId, $employeeId, $userName, $firstName, $lastName, $email);
    }
    
    public function authUserAdmin()
    {
        return $this->wsLNAAAuth->authUserAdmin();
    }
    
    public function authUser($userName, $password, $domain = null)
    {
        return $this->wsLNAAAuth->authUser($userName, $password, $domain);
    }
    
    public function adminGetDomainSecurityQuestions($sessionId)
    {
        return $this->wsLNAAAuth->adminGetDomainSecurityQuestions($sessionId);
    }
    public function userAddSecurityQuestionAnswer($sessionId, $answer)
    {
        return $this->wsLNAAAuth->userAddSecurityQuestionAnswer($sessionId, $answer);
    }
    
    public function authUserSecurityQuestion($userName, $searchResult)
    {
        return $this->wsLNAAAuth->authUserSecurityQuestion($userName, $searchResult);
    }
    
    public function adminChangeUserPassword($sessionId, $loginId, $password=null)
    {
        return $this->wsLNAAAuth->adminChangeUserPassword($sessionId, $loginId, $password);
    }
    
    public function adminResetUserPassword($sessionId, $loginId)
    {
        return $this->wsLNAAAuth->adminResetUserPassword($sessionId, $loginId);
    }
    
    public function adminUnlockUserAccount($sessionId, $loginId)
    {
        return $this->wsLNAAAuth->adminUnlockUserAccount($sessionId, $loginId);
    }
    
    public function adminGetUserData($sessionId, $loginId)
    {
        return $this->wsLNAAAuth->adminGetUserData($sessionId, $loginId);
    }
    
    public function isValidLNAAPassword($loginId, $password)
    {
        return $this->wsLNAAAuth->isValidLNAAPassword($loginId, $password);
    }
    
    public  function changeLNAAPassword($sessionId, $password)
    {
        return $this->wsLNAAAuth->changeLNAAPassword($sessionId, $password);
    }
    
    public  function adminUpdateUser($sessionId, $userName, $firstName, $lastName, $email)
    {
        return $this->wsLNAAAuth->adminUpdateUser($sessionId, $userName, $firstName, $lastName, $email);
    }
        
        public  function adminUpdateUserStatus($sessionId, $userName, $userStatus, $reasonCode = null, $message = null)
    {
        return $this->wsLNAAAuth->adminUpdateUserStatus($sessionId, $userName, $userStatus, $reasonCode, $message);
    }
        
        public function isValidEmployeeId($employeeId)
    {
        return $this->wsLNAAAuth->isValidEmployeeId($employeeId);
    }
}
