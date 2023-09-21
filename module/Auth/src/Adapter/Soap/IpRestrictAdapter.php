<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Auth\Adapter\Soap;

use Zend\Soap\Client;
use SoapFault;
use Exception;

use Base\Service\UserService;

class IpRestrictAdapter
{
    protected $soapClient;
    protected $applicationIdentifier = '';
    protected $bypass = true;

    public function __construct($soapClient, $applicationIdentifier)
    {
        $this->soapClient = $soapClient;
        $this->applicationIdentifier = $applicationIdentifier;
    }

    public function allowIfServiceDown($bypass = true)
    {
        $this->bypass = $bypass;
    }

    public function checkLogin($username, $ipAddress)
    {
        $params = [
            'applicationId' => $this->applicationIdentifier,
            'locationIds' => '',
            'userId' => $username,
            'userIpAddress' => $ipAddress,
            'userName' => '',
            'browserInfo' => '',
            'cookieValue' => '',
            'securityAnswer' => '',
            'trustedComputer' => '',
            'securityQuestionValidated' => '',
        ];
        
        try {
            $result = $this->soapClient->requestIprLogin($params);
        } catch (SoapFault $e) {
            return $this->bypass;
        } catch (Exception $e) {
            return $this->bypass;
        }
        
        $action = strtoupper($result->return->action);
        if ($action == UserService::IPRESTRICT_ACTION_ALLOW) {
            return UserService::IPRESTRICT_ALLOW;
        } elseif ($action == UserService::IPRESTRICT_ACTION_DENY
            && ($this->bypass && $result->return->actionReasonCode == UserService::IPRESTRICT_ROAMING_CODE)) {
            
            return UserService::IPRESTRICT_ROAMING;
        } else {
            return UserService::IPRESTRICT_DENY;
        }
    }
}
