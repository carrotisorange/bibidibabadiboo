<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Auth\Service;

use Zend\Log\Logger;

use Base\Service;
use Base\Service\BaseService;
use Auth\Adapter\Soap\MaeAuthAdapter;

class MaeAuthService extends BaseService 
{
    const CODE_SUCCESS = 0;
    const CODE_CHANGE_PASSWORD = 20;
    const CHANGE_EXPIRED_PASSWORD = 21;
    /**
    * @var wsMaeAuth ws MaeAuth
    */
    protected $wsMaeAuth;

    /**
    * @var Array
    */
    protected $config;

    /**
    * @var Zend\Log\Logger
    */
    protected $log;

    /**
    * @var Auth\Adapter\Soap\MaeAuthAdapter
    */
    protected $adapterMaeAuth;
    
    public function __construct(Logger $log, Array $config, MAEAuthAdapter $adapterMaeAuth)
    {
        $this->config = $config;
        $this->log = $log;
        $this->wsMaeAuth = $adapterMaeAuth;
    }
    
    public function userGetUserSecurityQuestions($sessionId)
    {
        return $this->wsMaeAuth->userGetUserSecurityQuestions($sessionId);
    }
    
    public function adminAddUser($sessionId, $username, $firstName, $lastName, $email, $employeeId)
    {
        return $this->wsMaeAuth->adminAddUser($sessionId, $username, $firstName, $lastName, $email, $employeeId);
    }
    
    public function userOpenSessionAdmin()
    {
        return $this->wsMaeAuth->userOpenSessionAdmin();
    }
    
    public function userOpenSession($username, $password)
    {
        return $this->wsMaeAuth->userOpenSession($username, $password);
    }
    
    public function adminGetDomainSecurityQuestions($sessionId)
    {
        return $this->wsMaeAuth->adminGetDomainSecurityQuestions($sessionId);
    }
    public function userAddSecurityQuestionAnswer($sessionId, $answer)
    {
        return $this->wsMaeAuth->userAddSecurityQuestionAnswer($sessionId, $answer);
    }
    
    public function userOpenSessionSecurityQuestionAnswer($username, $searchResult)
    {
        return $this->wsMaeAuth->userOpenSessionSecurityQuestionAnswer($username, $searchResult);
    }
    
    public function adminChangeUserPassword($sessionId, $loginId, $password=null)
    {
        return $this->wsMaeAuth->adminChangeUserPassword($sessionId, $loginId, $password);
    }
    
    public function adminResetUserPassword($sessionId, $loginId)
    {
        return $this->wsMaeAuth->adminResetUserPassword($sessionId, $loginId);
    }
    
    public function adminUnlockUserAccount($sessionId, $loginId)
    {
        return $this->wsMaeAuth->adminUnlockUserAccount($sessionId, $loginId);
    }
    
    public function adminGetUserData($sessionId, $loginId)
    {
        return $this->wsMaeAuth->adminGetUserData($sessionId, $loginId);
    }
    
    public function adminSetUserPasswordForceReset($sessionId, $loginId, $status)
    {
        return $this->wsMaeAuth->adminSetUserPasswordForceReset($sessionId, $loginId, $status);
    }
    
    public function isValidMAEPassword($loginId, $password)
    {
        return $this->wsMaeAuth->isValidMAEPassword($loginId, $password);
    }
    
    public function changeMAEPassword($sessionId, $password)
    {
        return $this->wsMaeAuth->changeMAEPassword($sessionId, $password);
    }
    
    public function adminUpdateUser($sessionId, $username, $firstName, $lastName, $email, $active)
    {
        return $this->wsMaeAuth->adminUpdateUser($sessionId, $username, $firstName, $lastName, $email, $active);
    }
}
