<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Auth\Service;

use Zend\Log\Logger;
use Zend\Authentication\Result;
use Zend\Authentication\Adapter\AdapterInterface;
use Exception;

use Base\Service\BaseService;
use Auth\Service\LNAAAuthService;
use Auth\Adapter\REST\LNAAAuthAdapter;

class LNAAAdapterService extends BaseService implements AdapterInterface
{
    protected $identity   = null;
    protected $credential = null;
    protected $domain = null;
    
    /**
     * @var Auth\Adapter\REST\LNAAAuthAdapter 
     */
    protected $adapterLNAAAuth;
    
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
        LNAAAuthAdapter $adapterLNAAAuth
    ) {
        $this->adapterLNAAAuth = $adapterLNAAAuth;
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
     * @param string $domain Same as domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * Initialization function of LNAAAdapter,Used For User Authentication
     */
    public function initLNAAAdapter($identity = null, $credential = null, $domain = null)
    {
        if (null != $identity) {
            $this->setIdentity($identity);
        }
        if (null != $credential) {
            $this->setCredential($credential);
        }
        if (null != $domain) {
            $this->setDomain($domain);
        }
    }

    /**
     * Authenticate user with LNAA auth.
     *
     * @throws Zend_Auth_Exception
     * @return Zend_Auth_Result
     */
    
    public function authenticate()
    {
        try {
            $result = $this->adapterLNAAAuth->authUser($this->identity, $this->credential, $this->domain);
            $roleName = $this->config['registration']['mbsrole'];
            if ($result->status->code == LNAAAuthService::CODE_SUCCESS) {
                $domain = $result->user_data->user_info->domain;
                $mbsLoginId = $result->user_data->user_info->login_id;
                if (empty($domain)) {
                    $loginIdInfo = explode('@', $mbsLoginId);
                    $domain = (!empty($loginIdInfo[1])) ? $loginIdInfo[1] : 
                        $this->config['registration']['domain'];
                }
                $data = [
                        'username' => $result->user_data->user_info->email_address,
                        'first_name' => $result->user_data->user_info->first_name,
                        'last_name' => $result->user_data->user_info->last_name,
                        'email_address' => $result->user_data->user_info->email_address,
                        'mbs_login_id' => $mbsLoginId,
                        'mbs_role_name' => $roleName,
                        'session_id' => $result->session_id,
                        'code' => $result->status->code,
                        'domain' => $domain
                ];
                $result = new Result(
                    Result::SUCCESS,
                    $data,
                    ['Authentication successful.']
                );
            } elseif ($result->status->code == LNAAAuthService::PASSWORD_RESET_REQUIRED || $result->status->code == LNAAAuthService::PASSWORD_EXPIRED) {
                $data = [
                        'loginId' => $this->identity,
                        'session_id' => $result->session_id,
                        'code' => $result->status->code
                ];
                $result = new Result(
                        Result::SUCCESS,
                        $data,
                        ['Password needs to be reset.']
                );
            } elseif ($result->status->code == LNAAAuthService::ACCOUNT_DISABLED) {
                    $data = [
                        'loginId' => $this->identity,
                        'session_id' => $result->session_id,
                        'code' => $result->status->code
                    ];
                $result = new Result(
                        Result::FAILURE,
                        $data,
                        ['Account is disabled.']
                                );
                        } else { 
                // Replace the ecrkeyin user name with the more friendly email address entered.
                $message = preg_replace("/[a-z0-9]*@ecrkeyin/i", $this->identity, $result->status->message);
                $result = new Result(
                    $this->identity,
                    [$message]
                );
            }
        } catch (Exception $e) {
           $result = '';
           throw new Exception($e->getMessage());
        } 
        return $result;
    }
}
