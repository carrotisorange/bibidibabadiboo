<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Adapter\Soap;

use Zend\Log\Logger;
use Zend\Soap\Client;
use SoapFault;
use RuntimeException;
use Exception;

use Base\Service\MbsAuthService;

class MbsAuthAdapter
{
    /**
     * @var Zend\Soap\Client 
     */
    protected $client;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Array
     */
    protected $config;
    
    public function __construct(
        Logger $logger,
        Array $config,
        Client $client)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->config = $config;
    }
    
    /**
     * Adds a user
     * 
     * @param string $sessionId
     * @param string $employeeId
     * @param string $username
     * @param int $emailPassword
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param int $forcePasswordReset
     * @param string $userType
     * @param string $idType
     * @param string $internalTypeStatus
     * @param int $isExternal
     * @return type
     */
    public function addMbsUser($sessionId, $employeeId, $username, $emailPassword, $firstName, $lastName,
        $email, $forcePasswordReset, $userType, $idType, $internalTypeStatus, $isExternal)
    {
        $response = null;
        try {
            $request = [
                'session_id' => $sessionId,
                'domain' => $this->config['registration']['domain'],
                'employee_id' => $employeeId,
                'login_id' => $username,
                'send_pw_gen_email' => $emailPassword,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email_address' => $email,
                'force_password_reset' => $forcePasswordReset,
                'user_type' => $userType,
                'id_type' => $idType,
                'internal_type_status' => $internalTypeStatus,
                'external' => $isExternal
            ];
            $response = $this->client->AddMBSUser($request);
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
            $this->logger->err($origin . $e->getMessage());
        }
        
        return $response;
    }
    
    /**
     * Opens session and returns session id
     * 
     * @return string empty on failure
     */
    public function openSession()
    {
        $sessionId = '';
        try {
            $request = [
                'domain' => $this->config['mbs']['registration']['domain'],
                'login' => $this->config['mbs']['registration']['user'],
                'password' => $this->config['mbs']['registration']['password']
            ];
            $result = $this->client->OpenSession($request);
            
            if ($result->OpenSessionResult->Code == MbsAuthService::CODE_SUCCESS && isset($result->OpenSessionResult->Data->any)) {
                $data = simplexml_load_string($result->OpenSessionResult->Data->any);
                $sessionId = (string) $data->session_id;
            }
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
            $this->logger->err($origin . $e->getMessage());
        }
        
        return $sessionId;
    }
    
    /**
     * Validates an employee id in ldap
     * 
     * @param int $employeeId the employee id to validate in ldap
     * @return boolean $isValid whether employee id is existing in ldap
     */
    public function isValidEmployeeId($employeeId)
    {
        $isValid = false;
        try {
            $sessionId = $this->openSession();
            $request = [
                'session_id' => $sessionId,
                'employee_id' => $employeeId,
            ];
            $result = $this->client->IsValidEmployeeID($request);
            if ($result->IsValidEmployeeIDResult->Code == MbsAuthService::CODE_SUCCESS 
                && $result->IsValidEmployeeIDResult->ResultCount == MbsAuthService::RESULTCOUNT_SUCCESS 
                && isset($result->IsValidEmployeeIDResult->Data->any)) {
                
                $data = simplexml_load_string($result->IsValidEmployeeIDResult->Data->any);
                if (!empty($data->valid_employee_id)) {
                    $isValid = true;
                }
            }
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
            $this->logger->err($origin . $e->getMessage());
        }
        
        return $isValid;
    }
}
