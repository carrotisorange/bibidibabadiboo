<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Auth\Service;

use Zend\Log\Logger;
use Zend\Authentication\Result;
use Zend\Authentication\Adapter\AdapterInterface;
use SoapFault;
use Exception;
use RunTimeException;

use Base\Service\BaseService;
use Auth\Service\MaeAuthService;
use Auth\Adapter\Soap\MaeAuthAdapter;

class MAEAdapterService extends BaseService implements AdapterInterface
{
    protected $identity   = null;
    protected $credential = null;
    
    /**
     * @var Auth\Adapter\Soap\MaeAuthAdapter 
     */
    protected $adapterMaeAuth;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $log;
    
    /**
     * @var Array
     */
    protected $config;
    
    /**
     * @param string $identity Same as username
     * @param string $credential Same as password
     */
    public function __construct(
        Logger $log,
        Array $config,
        MaeAuthAdapter $adapterMaeAuth
    ) {
        $this->adapterMaeAuth = $adapterMaeAuth;
        $this->log = $log;
        $this->config = $config;
    }
    
    /**
     * @param string $identity Same as username
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
        return $this;
    }
    
    /**
     * @param string $credential Same as password
     */
    public function setCredential($credential)
    {
        $this->credential = $credential;
        return $this;
    }
    
    /**
     * Initailization function of MAEAdapter,Used For User Authentication
     */
    public function initMAEAdapter($identity = null, $credential = null)
    {
        if (null != $identity) {
            $this->setIdentity($identity);
        }
        if (null != $credential) {
            $this->setCredential($credential);
        }
    }
    
    public function authenticate()
    {
        try {
            $response = $this->adapterMaeAuth->userOpenSession($this->identity, $this->credential);
            $result   = $response->UserOpenSessionResult;
            $roleName = $this->config['registration']['mbsrole'];
            
            if ($result->code == MaeAuthService::CODE_SUCCESS) {
                $data = [
                    'username' => (!empty($result)) ? $result->user_data_response->user_data->email_address : null,
                    'first_name' => (!empty($result)) ? $result->user_data_response->user_data->first_name : null,
                    'last_name' => (!empty($result)) ? $result->user_data_response->user_data->last_name : null,
                    'email_address' => (!empty($result)) ? $result->user_data_response->user_data->email_address : null,
                    'mbs_login_id' => (!empty($result)) ? $result->user_data_response->user_data->login_id : null,
                    'mbs_role_name' => $roleName,
                    'session_id' => (!empty($result)) ? $result->session_id : null,
                    'code' => (!empty($result)) ? $result->code : null
                ];
                $result = new Result(
                    Result::SUCCESS,
                    $data,
                    ['Authentication successful.']
                );
            } elseif ($result->code == MaeAuthService::CODE_CHANGE_PASSWORD || $result->code == MaeAuthService::CHANGE_EXPIRED_PASSWORD) {
                $data = [
                    'loginId' => $this->identity,
                    'session_id' => $result->session_id,
                    'code' => $result->code
                ];
                $result = new Result(
                    Result::SUCCESS,
                    $data,
                    ['Password needs to be reset.']
                );
            } else {
                // Replace the ecrkeyin user name with the more friendly email address entered.
                $message = preg_replace("/[a-z0-9]*@ecrkeyin/i", $this->identity, $result->message);
                $result = new Result(
                    $this->identity,
                    [$message]
                );
            }
        } catch (SoapFault $e) {
            /**
             * @TODO:
             * The If block alone is not required, added for development purpose. It will be removed later.
             * Need to update the return value of exception handling.
             */
            if (strtolower($this->config['app']['env']) == 'local') {
                $data = ['code' => '0', 'sessionId' => '138337673676326468478245506665', 'loginId' => $this->identity];
            } else {
                $data = [];
            }
            
            return $result = new Result(Result::SUCCESS, $data, ['Authentication failed, bypassed the authentication.']);
        } catch (Exception $e) {
           return null;
        } catch (RunTimeException $e) {
            $result = '';
            throw new RunTimeException($e->getMessage());
        }
        return $result;
    }
}
