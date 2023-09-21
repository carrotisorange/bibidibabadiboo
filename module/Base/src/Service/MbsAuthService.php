<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;

use Base\Service;
use Base\Service\BaseService;
use Base\Adapter\Soap\MbsAuthAdapter;

class MbsAuthService extends BaseService
{
    const CODE_SUCCESS = 0;
    const RESULTCOUNT_SUCCESS = 1;
    const USER_TYPE = 'Internal';
    const USER_ID_TYPE = 'Permanent';
    const USER_INTERNAL_TYPE_STATUS = 'FTE_Contractor';
    
    /**
    * @var Array
    */
    protected $config;
    
    /**
    * @var Zend\Log\Logger
    */
    protected $logger;
    
    /**
    * @var Base\Adapter\Soap\MbsAuthAdapter
    */
    protected $adapterMbsAuth;
    
    public function __construct(Logger $log, Array $config, MbsAuthAdapter $adapterMbsAuth)
    {
        $this->config = $config;
        $this->logger = $log;
        $this->adapterMbsAuth = $adapterMbsAuth;
    }
    
    /**
     * Adds a user.
     * 
     * @param string $sessionId
     * @param string $employeeId
     * @param string $username
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param int $emailPassword
     * @param int $forcePasswordReset
     * @param int $isExternal
     */
    public function addMbsUser(
        $sessionId,
        $employeeId,
        $username,
        $firstName,
        $lastName,
        $email,
        $emailPassword = 1,
        $forcePasswordReset = 1,
        $isExternal = 1)
    {
        $result = [
            'result' => false,
            'message' => null
        ];
        
        try {
            $response = $this->adapterMbsAuth->addMbsUser(
                $sessionId,
                $employeeId,
                $username, 
                $emailPassword, 
                $firstName, 
                $lastName, 
                $email,
                $forcePasswordReset,
                self::USER_TYPE,
                self::USER_ID_TYPE,
                self::USER_INTERNAL_TYPE_STATUS,
                $isExternal
            );
            if ($response->AddMBSUserResult->Code == self::CODE_SUCCESS) {
                $result['result'] = true;
            } else {
                $errorMessage = 'AddMbsUser service call returned error: ' 
                    . $response->AddMBSUserResult->Message;
                $result['message'] = $errorMessage;
                $this->logger->err($errorMessage);
                
            }
        } catch(Exception $e) {
            $errorMessage = 'AddMbsUser Exception occured: ' . $e->getMessage();
            $result['message'] = $errorMessage;
            $this->logger->err($errorMessage);
        }
        
        return $result;
    }
    
    /**
     * Opens session and returns session id
     * 
     * @return string
     */
    public function openSession()
    {
        $sessionId = null;
        try {
            $sessionId = $this->adapterMbsAuth->openSession();
        } catch(Exception $e) {
            $this->logger->err('OpenSession Exception occured: ' . $e->getMessage());
        }
        
        return $sessionId;
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
            $isValid = $this->adapterMbsAuth->isValidEmployeeId($employeeId);
        } catch (Exception $e) {
            $this->logger->err('IsValidEmployeeID Exception occured: ' . $e->getMessage());
        }
        
        return $isValid;
    }
}
