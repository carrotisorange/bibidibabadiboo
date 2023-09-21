<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Exception;

use Base\Service\IsitTicketService;
use Base\Service\IsitWebService;
use Base\Service\UserService;
use Base\Adapter\Db\IsitTicketStatusAdapter;

class IsitService extends BaseService
{
    const TICKET_TYPE_CREATE = 'create';
    const TICKET_TYPE_UPDATE = 'update';
    const TICKET_TYPE_DISABLE = 'disable';
    const TICKET_TYPE_TERMINATE = 'terminate';
    
    /*
     *  We tried to resolve this ticket but were not able to for some reason
     *  @var string
     */
    const STATUS_FAILED = 'failed';
    
    /*
     * Ticket is created, opened, and awaiting resolution
     * @var string
     */
    const STATUS_PENDING = 'pending';
    
    /*
     * Ticket is successfully resolved
     * @var string
     */
    const STATUS_CLOSED = 'closed';
    
    /**
     * @var Array
     */
    protected $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Service\IsitTicketService
     */
    protected $serviceIsitTicket;
    
    /**
     * @var Base\Service\IsitWebService
     */
    protected $serviceIsitWeb;
    
    /**
     * @var Base\Adapter\Db\IsitTicketStatusAdapter
     */
    protected $adapterIsitTicketStatus;
    
    /**
     * @var Base\Service\UserService
     */
    protected $serviceUser;
    
    public function __construct(
        Array $config,
        Logger $logger,
        IsitTicketService $serviceIsitTicket,
        IsitTicketStatusAdapter $adapterIsitTicketStatus,
        IsitWebService $serviceIsitWeb,
        UserService $serviceUser)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->serviceIsitTicket = $serviceIsitTicket;
        $this->adapterIsitTicketStatus = $adapterIsitTicketStatus;
        $this->serviceIsitWeb = $serviceIsitWeb;
        $this->serviceUser = $serviceUser;
    }
    
    public function addUser($userId)
    {
        $userInfo = $this->serviceUser->getIdentityData($userId);
        if (empty($userInfo)) {
            return false;
        }
        
        $employeeId = $userInfo['peoplesoftEmployeeId'];
        $userName = $userInfo['username'];
        $roleName = $userInfo['roleExternal'];
        $userConfig = $this->getUserConfig();
        
        return $this->serviceIsitWeb->createAddTicket($employeeId, $userName, $userId, $roleName, $userConfig);
    }
    
    public function addInternalUser($userId, $userName, $externalIsitTicketId, $requestXml, $response)
    {   
        $actionType = self::TICKET_TYPE_CREATE;
        $status = self::STATUS_PENDING;
        
        //we will use accessRequest xml as requestXml
        $this->serviceIsitWeb->saveTicket($userId, $externalIsitTicketId, $status, $actionType, $requestXml, $response);
        $result = (!empty($externalIsitTicketId));
        if ($result) {
            $this->serviceUser->setIsPending($userName);
        }
        
        return $result;
    }
    
    public function changeUserRole($userId, $roleId, $userDomain = null)
    {
        $userInfo = $this->serviceUser->getIdentityData($userId);
        if (empty($userInfo) || empty($roleId)) {
            return false;
        }
        
        $roleInfo = $this->serviceUser->getRoleById($roleId);
        $roleName = $roleInfo['nameExternal'];
        $employeeId = $userInfo['peoplesoftEmployeeId'];
        $userName = $userInfo['username'];
        $userConfig = ['environment' => $this->config['app']['isit']['userInfo']['environment']];
        if (!empty($userDomain)) {
            $userConfig['domain'] = strtoupper($userDomain);
        }
        return $this->serviceIsitWeb->createUpdateTicket($employeeId, $userName, $userId, $roleName, $userConfig);
    }
    
    public function deleteUser($userId)
    {
        $userInfo = $this->serviceUser->getIdentityData($userId);
        if (empty($userInfo)) {
            return false;
        }
        
        $employeeId = $userInfo['peoplesoftEmployeeId'];
        $userName = $userInfo['username'];
        $userConfig = ['environment' => $this->config['app']['isit']['userInfo']['environment']];
        return $this->serviceIsitWeb->createDeleteTicket($employeeId, $userId, $userName, $userConfig);
    }

    public function closeTicket($externalIsitTicketId)
    {
        $statusId = $this->adapterIsitTicketStatus->getIdByStatus(self::STATUS_CLOSED);

        return $this->serviceIsitTicket->closeTicket($externalIsitTicketId, $statusId);
    }

    public function getMessageQueue()
    {
        return $this->serviceIsitWeb->getMessageQueue();
    }

    public function acknowledgeTicketFromMessageQueue($messageId, $externalIsitTicketId = null)
    {
        if (empty($messageId)) return false;
        
        return $this->serviceIsitWeb->acknowledgeTicketFromMessageQueue($messageId, $externalIsitTicketId);
    }

    public function sendResponse($externalIsitTicketId = '', $message = '', $role = '', $success = 'true')
    {
        return $this->serviceIsitWeb->sendResponse($externalIsitTicketId, $message, $role, $success);
    }

    public function addTicket($userId, $externalIsitTicketId, $status, $type)
    {
        return $this->serviceIsitWeb->addTicket($userId, $externalIsitTicketId, $status, $type);
    }

    public function getTicketByExternalId($externalTicketId)
    {
        if (empty($externalTicketId)) {
            return false;
        }
        return $this->serviceIsitTicket->getTicketByExternalId($externalTicketId);
    }
    
    public function getUserConfig() {
        $isitUserInfo = $this->config['app']['isit']['userInfo'];
        
        $userConfig = [];
        $userConfig['user_type'] = $isitUserInfo['userType'];
        $userConfig['environment'] = $isitUserInfo['environment'];
        $userConfig['domain'] = $isitUserInfo['domain'];
        $userConfig['is_this_request_for_you'] = $isitUserInfo['isThisRequestForYou'];
        $userConfig['clone_existing_user'] = $isitUserInfo['cloneExistingUser'];
        $userConfig['user_id_type'] = $isitUserInfo['userIdType'];
        $userConfig['internal_user_status'] = $isitUserInfo['internalUserStatus'];
        $userConfig['audience'] = $isitUserInfo['audience'];
        $userConfig['app_location'] = $isitUserInfo['appLocation'];
        
        return $userConfig;
    }
}
