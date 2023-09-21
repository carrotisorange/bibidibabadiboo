<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Auth\Adapter\Soap;

use Zend\Soap\Client;
use SoapFault;
use RuntimeException;
use Exception;

use Auth\Service\MaeAuthService;

class MaeAuthAdapter
{
    const GEN_PW_EMAIL_TO_USER = 1;
    const GENERATE_PW_EMAIL_TO_CUSTOMER = 1;
    const ENABLED = 1;
    const USER_TYPE = 'Internal';
    const ID_TYPE = 'Permanent';
    const FORCE_USER_PW_RESET = 1;
    const WRONG_PW_COUNT = 0;
    const INTERNAL_TYPE_STATUS = 'FTE_Contractor';
    
    /**
     * @var Zend\Soap\Client 
     */
    protected $soapClient;
    
    /**
     * @var Array
     */
    protected $config;
    
    public function __construct(
        Array $config,
        Client $soapClient)
    {
        $this->ws = $soapClient;
        $this->config = $config;
    }
    
    /**
     * Returns instance of Open Session Result
     *
     * @return result
     */
    public function userOpenSessionAdmin()
    {
        try {
            $result = $this->ws->UserOpenSession([
                'domain' => $this->config['registration']['domain'],
                'login_id' => $this->config['registration']['user'],
                'password' => $this->config['registration']['password']
            ]);
            
            $result = $result->UserOpenSessionResult->session_id;
        } catch (SoapFault $e) {
            return false;
        } catch (Exception $e) {
            return false;
        } catch (RuntimeException $e) {
            return false;
        }
        
        return $result;
    }
    
    public function userOpenSession($userName, $password)
    {
        try {
            $result = $this->ws->UserOpenSession([
                'domain' => $this->config['registration']['domain'],
                'login_id' => $userName,
                'password' => $password
            ]);
        } catch (SoapFault $e) {
            return false;
        } catch (Exception $e) {
            return false;
        } catch (RuntimeException $e) {
            return false;
        }
        
        return $result;
    }
    
    public function userGetUserSecurityQuestions($sessionId)
    {
        try {
            $result = $this->ws->UserGetUserSecurityQuestions(['session_id' => $sessionId]);
        } catch (Exception $e) {
            return false;
        }
        
        return $result;
    }
    
    public function adminGetDomainSecurityQuestions($sessionId)
    {
        try {
            $result = $this->ws->AdminGetDomainSecurityQuestions([
                'session_id' => $sessionId,
                'domain' => $this->config['registration']['domain'],
            ]);
        } catch (SoapFault $e) {
            $result = $this->config['ws_response']; 
        } catch (Exception $e) {
            return false;
        }
        return $result;
    }
    
    public function userAddSecurityQuestionAnswer($sessionId, $answer)
    {
        try {
            $result = $this->ws->UserAddSecurityQuestionAnswer([
                'session_id' => $sessionId,
                'question_id_answer' => $answer
            ]);
        } catch (Exception $e) {
            return false;
        }
        
        return $result;
    }
    
    public function userOpenSessionSecurityQuestionAnswer($userName, $searchResult)
    {
        try {
            $result = $this->ws->UserOpenSessionSecurityQuestionAnswer([
                'domain' => $this->config['registration']['domain'],
                'login_id' => $userName,
                'user_sec_question_answer' => $searchResult
            ]);
        } catch (SoapFault $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
        
        return $result;
    }

    public function adminChangeUserPassword($sessionId, $loginId, $password)
    {
        if (empty($password)) {
            $generateEmail = 1;
        } else {
            $generateEmail = 0;
        }
        
        try {
            $request = [
                'session_id' => $sessionId,
                'login_id' => $loginId,
                'generate_pw_email_to_customer' => $generateEmail,
                'password' => $password
            ];
            $result = $this->ws->AdminChangeUserPassword($request);
        } catch (Exception $e) {
            return false;
        }
        
        return $result;
    }

    public function adminResetUserPassword($sessionId, $userId)
    {
        try {
            $domain = $this->config['registration']['domain'];
            $loginId = $userId . '@' . $domain;
            $request = [
                'session_id' => $sessionId,
                'login_id' => $loginId,
                'generate_pw_email_to_customer' => self::GENERATE_PW_EMAIL_TO_CUSTOMER,
                'password' => '',
                'force_user_pw_reset' => self::FORCE_USER_PW_RESET
            ];
            $result = $this->ws->AdminChangeUserPassword($request);
        } catch (SoapFault $e) {
            if ($this->config['app']['env'] == 'local') {
                // @TODO: Removed in future
                // Mock-up response for local environment
                $result = $this->config['ws_response'];
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        
        return $result;
    }
    
    public function adminUnlockUserAccount($sessionId, $userId)
    {
        try {
            $domain = $this->config['registration']['domain'];
            $loginId = $userId . '@' . $domain;
            $result = $this->ws->AdminUnlockUserAccount([
                'session_id' => $sessionId,
                'login_id' => $loginId
            ]);
        } catch (SoapFault $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
        
        return $result;
    }
    
    public function adminGetUserData($sessionId, $loginId)
    {
        try {
            $result = $this->ws->AdminGetUserData([
                'session_id' => $sessionId,
                'login_id' => $loginId
            ]);
        } catch (SoapFault $e) {
            if ($this->config['app']['env'] == 'local') {
                // @TODO: Removed in future
                // Mock-up response for local environment
                $result = $this->config['ws_response'];
            } else {
                return null;
            }
        } catch (Exception $e) {
            return null;
        } catch (RuntimeException $e) {
           return null;
        }
        return $result;
    }
    
    public function adminSetUserPasswordForceReset($sessionId, $userName, $status)
    {
        try {
            $domain = $this->config['registration']['domain'];
            $domain = $this->config['registration']['domain'];
            $loginId = $userName . '@' . $domain;
            $result = $this->ws->AdminSetUserPasswordForceReset([
                'session_id' => $sessionId,
                'login_id' => $loginId,
                'enabled' => $status
            ]);
            $result = $result->AdminSetUserPasswordForceResetResult;
        } catch (Exception $e) {
            return false;
        }
        
        return $result;
    }
    
    public function isValidMAEPassword($loginId, $password)
    {
        $sessionId = null;
        try {
            $result = $this->ws->UserOpenSession([
                'domain' => $this->config['registration']['domain'],
                'login_id' => $loginId,
                'password' => $password
            ]);
            
            $results = $result->UserOpenSessionResult;
            if ($results->code == MaeAuthService::CODE_SUCCESS
                || $results->code == MaeAuthService::CODE_CHANGE_PASSWORD
                || $results->code == MaeAuthService::CHANGE_EXPIRED_PASSWORD) {
                
                $sessionId = $results->session_id;
            } else {
                throw new Exception('UserOpenSession returned: ' . $results->message);
            }
        } catch (SoapFault $e) {
            if ($this->config['app']['env'] == 'local') {
                // @TODO: Removed in future
                // Mock-up response for the local environment
                return 'SPI';
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
        
        return $sessionId;
    }
    
    public function changeMAEPassword($sessionId, $password)
    {
        $request = [
            'session_id' => $sessionId,
            'password' => $password
        ];
        $result = $this->ws->UserChangeUserPassword($request);
        $results = $result->UserChangeUserPasswordResult;
        if ($results->code != MaeAuthService::CODE_SUCCESS) {
            throw new Exception('Password not changed. ' . $results->message);
        }
        
        if ($this->config['app']['env'] == 'local') {
            // @TODO: Removed in future
            // Mock-up response for the local environment
            $results = $this->config['ws_response'];
        }
        return $results;
    }
    
    public function adminUpdateUser($sessionId, $userName, $firstName, $lastName, $email, $active)
    {
        try {
            $domain = $this->config['registration']['domain'];
            $loginId = $userName . '@' . $domain;
            $request = [
                'session_id' => $sessionId,
                'login_id' => $loginId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email_address' => $email,
                'enabled' => $active
            ];
            $result = $this->ws->AdminUpdateUser($request);
        } catch (Exception $e) {
            if ($this->config['app']['env'] == 'local') {
                // @TODO: Removed in future
                // Mock-up response for the local environment
                return $this->config['ws_response'];
            }
            
            return false;
        }
        
        return $result;
    }
}
