<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Authentication\AuthenticationService;
use Zend\Log\Logger;
use Exception;

use Base\Service\IsitService;
use Base\Service\IsitTicketService;
use Base\Adapter\Db\IsitTicketTypeAdapter;
use Base\Adapter\Db\IsitTicketLogAdapter;
use Base\Adapter\Db\IsitTicketLogTypeAdapter;
use Base\Adapter\Db\IsitTicketStatusAdapter;
use Base\Adapter\REST\Isit\CurlAdapter As IsitCurlAdapter;

class IsitWebService extends BaseService
{
    const LOG_TYPE_MESSAGE = 'message';
    const LOG_TYPE_NEW_TICKET = 'new_ticket';
    const LOG_TYPE_ACKNOWLEDGE = 'acknowledge';
    const LOG_TYPE_RESULT = 'result';
    
    /**
     * @var Array
     */
    protected $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /*
     * @var Base\Service\IsitTicket
     */
    protected $serviceIsitTicket;
    
    /*
     * @var Base\Adapter\Db\IsitTicketTypeAdapter
     */
    protected $adapterIsitTicketType;
    
    /*
     * @var Base\Adapter\Db\IsitTicketLogAdapter
     */
    protected $adapterIsitTicketLog;
    
    /*
     * @var Base\Adapter\Db\IsitTicketLogTypeAdapter
     */
    protected $adapterIsitTicketLogType;
    
    /*
     * @var Base\Adapter\Db\IsitTicketStatusAdapter
     */
    protected $adapterIsitTicketStatus;
    
    /*
     * @var Base\Service\UserService
     */
    protected $serviceUser;
    
    /*
     * @var Base\Adapter\REST\Isit\CurlAdapter;
     */
    protected $transportClient;
    
    /*
     * @var Zend\Authentication\AuthenticationService
     */
    protected $serviceAuth;

    /*
     * @var hostname/ip for the Message Queue
     */
    protected $messageQueueDomain;

    /*
     * @var isit Message Queue username
     */
    protected $isitUsername;

    /*
     * @var isit Message Queue password
     */
    protected $isitPassword;

    /*
     * @var isit Message Queue password
     */
    protected $ecrashIsitId;
    
    public function __construct(
        Array $config,
        Logger $logger,
        IsitTicketService $serviceIsitTicket,
        IsitTicketTypeAdapter $adapterIsitTicketType,
        IsitTicketLogAdapter $adapterIsitTicketLog,
        IsitTicketLogTypeAdapter $adapterIsitTicketLogType,
        IsitTicketStatusAdapter $adapterIsitTicketStatus,
        UserService $serviceUser,
        IsitCurlAdapter $adapterIsitCurl,
        AuthenticationService $serviceAuth)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->serviceIsitTicket = $serviceIsitTicket;
        $this->adapterIsitTicketType = $adapterIsitTicketType;
        $this->adapterIsitTicketLog = $adapterIsitTicketLog;
        $this->adapterIsitTicketLogType = $adapterIsitTicketLogType;
        $this->adapterIsitTicketStatus = $adapterIsitTicketStatus;
        $this->serviceUser = $serviceUser;
        $this->transportClient = $adapterIsitCurl;
        $this->serviceAuth = $serviceAuth;

        $this->messageQueueDomain = $this->config['app']['webService']['messageQueue']['domain'];

        $this->isitUsername = $this->config['app']['webService']['messageQueue']['isitLogin'];
        $this->isitPassword = $this->config['app']['webService']['messageQueue']['isitPassword'];
        $this->ecrashIsitId = $this->config['app']['webService']['messageQueue']['ecrashAppId'];

        if ($this->config['app']['webService']['messageQueue']['httpSecure']) {
            $this->messageQueueProtocol = "https";
        } else {
            $this->messageQueueProtocol = "http";
        }
    }
    
    public function createAddTicket($employeeId, $userName, $userId, $roleName, $userConfig)
    {
        if (empty($employeeId) || !is_numeric($employeeId) || empty($userName)
            || empty($userId) || !is_numeric($userId) || empty($roleName)) {
            return false;
        }
        
        $actionType = IsitService::TICKET_TYPE_CREATE;
        $subject = 'New User Access';
        
        $roles = [
            'role' => [
                '@attributes' => ['description' => $roleName],
                '@value' => $roleName
            ]
        ];
        
        return $this->createTicket($actionType, $subject, $employeeId, $userName, $userId, $roles, $userConfig);
    }
    
    public function createUpdateTicket($employeeId, $userName, $userId, $roleName, $userConfig)
    {
        if (empty($employeeId) || !is_numeric($employeeId) || empty($userName)
            || empty($userId) || !is_numeric($userId) || empty($roleName)) {
            return false;
        }
        
        $actionType = IsitService::TICKET_TYPE_UPDATE;
        $subject = 'Update user role';
        
        $roles = [
            'role' => [
                '@attributes' => ['description' => $roleName],
                '@value' => $roleName
            ]
        ];
        
        return $this->createTicket($actionType, $subject, $employeeId, $userName, $userId, $roles, $userConfig);
    }
    
    public function createDeleteTicket($employeeId, $userId, $userName, $userConfig)
    {
        if (empty($employeeId) || !is_numeric($employeeId)
            || empty($userId) || !is_numeric($userId) || empty($userName)) {
            return false;
        }
        
        $actionType = IsitService::TICKET_TYPE_DISABLE;
        $subject = 'Disable user';
        
        return $this->createTicket($actionType, $subject, $employeeId, $userName, $userId, null, $userConfig);
    }
    
    public function createTicket(
        $actionType,
        $subject,
        $employeeId,
        $userName,
        $userId,
        Array $roles = null,
        $userConfig = null)
    {
        if (empty($actionType) || empty($subject) || empty($employeeId) || empty($userName) || empty($userId)) {
            return false;
        }
        
        if (($actionType == IsitService::TICKET_TYPE_CREATE || $actionType == IsitService::TICKET_TYPE_UPDATE)
            && empty($roles)) {
            return false;
        }
        
        $actionTypes = [
            IsitService::TICKET_TYPE_UPDATE,
            IsitService::TICKET_TYPE_CREATE,
            IsitService::TICKET_TYPE_DISABLE
        ];
        
        if (!in_array($actionType, $actionTypes)) {
            return false;
        }
        
        //obtain the credentials of the person that is logged in.
        if ($this->serviceAuth->hasIdentity()) {
            $userInfo = $this->serviceAuth->getIdentity();
        }
        
        $forApprovals = 'Yes';
        if ($actionType == IsitService::TICKET_TYPE_DISABLE) {
            $forApprovals = 'No';
        }
        
        $defaultFields = [
            'application' => 'eCrash Keying',
            'requestor_email_address' => $userInfo->email,
            'for_approvals' => $forApprovals,
            'for_log_only' => 'No'
        ];
        
        $fields = [
            'action_type' => ucfirst($actionType),
            'subject' => $subject,
            'employee_id' => $employeeId,
            'user_id' => $userName
        ];
        
        if (!empty($roles)) {
            $fields['roles'] = $roles;
        }
        
        if (!empty($userConfig)) {
            foreach($userConfig as $key => $value) {
                $fields[$key] = $value;
            }
            $fields['requestor_employee_number'] = $userInfo->peoplesoftEmployeeId;
        }
        
        $request = array_merge($defaultFields, $fields);
        $requestXml = $this->arrayToXml($request, new \SimpleXMLElement('<create_ticket/>'));
        $transportException = false;
        $externalIsitTicketId = null;
        $status = IsitService::STATUS_FAILED;
        
        try {
            $response = $this->transportClient->createTicket($requestXml);
        }
        catch (Exception $e) {
            $response = $e->getMessage();
            $transportException = true;
            $this->logger->log(Logger::ERR, 'Caught exception while creating Isit ticket: ' . $response);
        }
        
        if (!$transportException) {
            $resultXmlObj = simplexml_load_string($response);
            if (!empty($resultXmlObj->ticket_id)) {
                $externalIsitTicketId = $resultXmlObj->ticket_id;
                if ($actionType == IsitService::TICKET_TYPE_DISABLE) {
                    //disable ticket is being processed on the spot so setting status to Closed right away
                    $status = IsitService::STATUS_CLOSED;
                } else {
                    $status = IsitService::STATUS_PENDING;
                }
            }
        }
        
        $this->saveTicket($userId, $externalIsitTicketId, $status, $actionType, $requestXml, $response);
        $result = (!empty($externalIsitTicketId));
        if ($result) {
            $this->serviceUser->setIsPending($userName);
        }
        
        return $result;
    }
    
    public function saveTicket($userId, $externalIsitTicketId, $status, $type, $request, $response)
    {
        $ticketId = $this->addTicket($userId, $externalIsitTicketId, $status, $type);
        if (empty($ticketId)) {
            return false;
        }
        $ticketLogType = self::LOG_TYPE_NEW_TICKET;
        
        return $this->saveTicketLog($ticketId, $ticketLogType, $request, $response);
    }
    
    protected function saveTicketLog($ticketId, $type, $request, $response)
    {
        if (!is_numeric($type)) {
            $typeId = $this->adapterIsitTicketLogType->getIdByType($type);
            if (empty($typeId)) return false;
        } else {
            $typeId = $type;
        }
        
        return $this->adapterIsitTicketLog->add($ticketId, $typeId, $request, $response);
    }
    
    public function addTicket($userId, $externalIsitTicketId, $status, $type)
    {
        if (!is_numeric($status)) {
            $statusId = $this->adapterIsitTicketStatus->getIdByStatus($status);
            if (empty($statusId)) return false;
        } else {
            $statusId = $status;
        }
        
        if (!is_numeric($type)) {
            $typeId = $this->adapterIsitTicketType->getIdByType($type);
            if (empty($typeId)) return false;
        } else {
            $typeId = $type;
        }
        
        return $this->serviceIsitTicket->add($userId, $externalIsitTicketId, $statusId, $typeId);
    }
    
    protected function addTicketLogWithExternalId($externalTicketId, $type, $request, $response)
    {
        $ticketId = $this->serviceIsitTicket->getInternalIdFromExternalId($externalTicketId);

        if ( $ticketId == "" || !isset($ticketId) ) {
            return(false);
        }

        return $this->saveTicketLog($ticketId, $type, $request, $response);
    }

    protected function arrayToXml(array $array, \SimpleXMLElement $xml)
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                if ($k === '@attributes') {
                    foreach ($v as $name => $value) {
                        $xml->addAttribute($name, htmlspecialchars($value));
                    }
                } elseif (!empty($v['@value'])) {
                    $value = $v['@value'];
                    unset($v['@value']);
                    $this->arrayToXml($v, $xml->addChild($k, htmlspecialchars($value)));
                } else {
                    if (!is_string($k)) {
                        $this->arrayToXml($v, $xml);
                    } else {
                        $this->arrayToXml($v, $xml->addChild($k));
                    }
                }
            } else {
                $xml->addChild($k, htmlspecialchars($v));
            }
        }
        return $xml->asXML();
    }

    public function getMessageQueue()
    {
        try {
            $xml = $this->transportClient->getMessageQueue();
        }
        catch (Exception $e) {
            $this->logger->log(Logger::ERR, 'Caught exception while reading ISIT message queue: ' . $e->getMessage());
            return false;
        }

        $xmlObject = simplexml_load_string($xml,null,LIBXML_DTDATTR);

        return $xmlObject;
    }

    public function acknowledgeTicketFromMessageQueue($messageId, $externalIsitTicketId)
    {
        try {
            $response = $this->transportClient->acknowledgeTicket($messageId);
        }
        catch (Exception $e) {
            $response = $e->getMessage();
        }
        $response = trim($response);

        if (!empty($externalIsitTicketId)) {
            $this->addTicketLogWithExternalId(
                $externalIsitTicketId,
                self::LOG_TYPE_ACKNOWLEDGE,
                $this->transportClient->getLastUrl(),
                $response
            );
        }

        return $response;
    }

    public function sendResponse($externalIsitTicketId, $message, $role, $success)
    {
        $arrayToTransform = [
            'ticket_id' => $externalIsitTicketId,
            'success' => $success,
            'comment' => $message,
            'error' => '',
            'applications' => [
                'application' => [
                    'application_id' => $this->ecrashIsitId,
                    'application_name' => 'eCrash Keying',
                    'roles' => [
                        'role' => [
                            [
                                'role_code' => $role,
                                'role_description' => $role,
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $sendXml = $this->arrayToXml($arrayToTransform, new \SimpleXMLElement('<access_response/>'));

        try {
            $response = $this->transportClient->sendResponse($sendXml);
        }
        catch (Exception $e) {
            $response = $e->getMessage();
        }
        $response = trim($response);

        $this->addTicketLogWithExternalId(
            $externalIsitTicketId,
            self::LOG_TYPE_RESULT,
            $sendXml,
            $response
        );

        return $response;
    }
}
