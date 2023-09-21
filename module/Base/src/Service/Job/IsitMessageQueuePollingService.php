<?php
namespace Base\Service\Job;

use Zend\Log\Logger;

use Base\Service\Job\ProcessCheck\ProcessCheckInterface;
use Auth\Service\LNAAAuthService;
use Base\Service\KeyingVendorService;
use Base\Service\UserService;
use Base\Service\IsitService;
use Base\Helper\LnHelper;

class IsitMessageQueuePollingService extends JobAbstract
{
    const DOMAIN_RISK = 'risk';
    const DOMAIN_NONE = 'no domain';
    
    /**
     * @var Auth\Service\LNAAAuthService
     */
    protected $serviceLnaaAuth;

    /**
     * @var Base\Service\UserService
     */
    protected $serviceUser;

    /**
     * @var Base\Service\IsitService
     */
    protected $serviceIsit;
    
    /**
     * @var Base\Service\KeyingVendorService
     */
    protected $serviceKeyingVendor;
    
    /**
     * @var Base\Helper\LnHelper
     */
    protected $lnHelper;
    
    /**
     * @var Array
     */
    protected $config;

    protected $errors = [];
    
    protected $allowedTicketType = array(
        IsitService::TICKET_TYPE_UPDATE,
        IsitService::TICKET_TYPE_CREATE
    );
    
    public function __construct(
        ProcessCheckInterface $jobProcess,
        LNAAAuthService $serviceLnaaAuth,
        UserService $serviceUser,
        IsitService $serviceIsit,
        KeyingVendorService $serviceKeyingVendor,
        LnHelper $lnHelper,
        Array $config,
        $log)
    {
        parent::__construct(
            $jobProcess,
            $config,
            $log
        );
        
        $this->config = $config;
        $this->logger = $log;
        $this->serviceLnaaAuth = $serviceLnaaAuth; //modelLNAAAuth
        $this->serviceUser = $serviceUser; //modelUser
        $this->serviceIsit = $serviceIsit; //modelIsit
        $this->serviceKeyingVendor = $serviceKeyingVendor; //modelKeyingVendor
        $this->lnHelper = $lnHelper;
        $this->ecrashApplicationName = $this->config['app']['webService']['messageQueue']['appName'];
    }
    
    protected function runJob()
    {
        try {
            $messages = $this->serviceIsit->getMessageQueue();

            if (empty($messages->message)) {
                return;
            }
            $currentEnv = strtolower($this->config['app']['isit']['userInfo']['environment']);
            foreach ($messages->message as $message) {
                // just doing this for 'shorthand'
                $accessRequest = $message->message_text->access_request;
                $messageEnv = strtolower($accessRequest->environment);
                if ($currentEnv == $messageEnv) {
                    $actionType = strtolower($accessRequest->action_type);

                    if (!in_array($actionType, $this->allowedTicketType)) {
                        continue;
                    }

                    $ticketId = intval($accessRequest->ticket_id);
                    
                    $logPrefix = '[ISIT#'. $ticketId .']';
                    
                    $roleDescription = '';
                    if (!empty($accessRequest->roles->role)) {
                        $roleAttributes = $accessRequest->roles->role->attributes();
                        $roleDescription = (string)$roleAttributes['description'];
                    }
                    
                    //get username, domain and email
                    $isInternal = false;
                    $isInternalDomain = false;
                    $username = $this->stripDomainFromUser((string)$accessRequest->user_id);
                    $domain = strtolower(trim($accessRequest->Domain));
                    if (!empty($domain)) {
                        $isInternalDomain = $this->serviceUser->getUserInternalInfoByDomain($domain);
                    }
                    if ($isInternalDomain) {
                        $username .= '@' . $domain;
                        $isInternal = true;
                    }
                    $userData = $this->getUserNamesAndEmailLnaa($username);
                    if (empty($userData['email'])) {
                        $userData = $this->getUserNamesAndEmailIsit($accessRequest);
                    }
                    //get user email from lnaa and target details first before getting requestor email
                    $userEmail = (!empty($userData['email'])) ? $userData['email'] : 
                        trim($accessRequest->customer_email_address); //requestor email
                    $email = strtolower($userEmail);
                    
                    $this->logger->log(Logger::INFO, $logPrefix . "Processing ". 
                            $actionType . " ISIT ticket for user: " . $username . " "
                            . "with email: ". $email . " and domain: " . $domain . "...");
                    
                    //Determine if user being created is an internal user
                    if (!$isInternalDomain) {
                        $isInternalInfo = $this->serviceUser->getUserIsInternalAndDomainInfo($username);
                        $isInternal = $isInternalInfo['isInternal'];
                        $domain = $isInternalInfo['domain'];
                    }
                                       
                    $actionSuccess = true;
                    $this->logger->log(Logger::INFO, $logPrefix . "Verifying all data...");
                    // Verify if we have everything we need for this ticket - and 2 other fields
                    if (($result = $this->verifyAllData($logPrefix, $isInternal, $username, $accessRequest)) !== true) {
                        $actionSuccess = false;
                        $this->addError($result);
                    } else {
                        // Execute the ticket command
                        if ($actionType == IsitService::TICKET_TYPE_UPDATE) {
                            $role = $accessRequest->roles->role->attributes();
                            $actionSuccess = $this->serviceUser->updateRoleById($username, (string)$role['description']);
                        } elseif ($actionType == IsitService::TICKET_TYPE_CREATE) {
                            $actionSuccess = $this->createUser(
                                $username,
                                intval($accessRequest->employee_id),
                                $email, 
                                $domain, 
                                $accessRequest, 
                                $isInternal,
                                $logPrefix
                            );
                        }
                    }

                    // replacing '.' with special symbol '{' because iSIT breaks string where the dot is
                    $usernameProcessed = str_replace('.', '{', $username);
                    $responseMessage = "Successfully processed \"$actionType\" request for user $usernameProcessed";
                    if (!$actionSuccess) {
                        $errorMessage = $this->getErrors() ? implode('; ', $this->getErrors()) : '';
                        $this->logger->log(Logger::ERR, $logPrefix . "Isit ticket: " . $ticketId . " had bad data and did not complete. $errorMessage");
                        $responseMessage = "\"$actionType\" request for user $usernameProcessed failed. $errorMessage";
                    } else {
                        $this->serviceUser->setIsPending($username, false);
                        
                        $this->logger->log(Logger::INFO, $logPrefix . "Sending acknowledgement to ISIT...");
                        //Acknowlegde to ISIT we got the ticket and write to ticket_log table
                        $this->serviceIsit->acknowledgeTicketFromMessageQueue(intval($message->id), $ticketId);

                        $this->logger->log(Logger::INFO, $logPrefix . "Sending response to ISIT: " . $responseMessage);
                        //SendResponse to ISIT (comment)
                        $this->serviceIsit->sendResponse(
                            $ticketId,
                            $responseMessage,
                            $roleDescription
                        );

                        //Close ticket status
                        $this->logger->log(Logger::INFO, $logPrefix . "Closing ticket...");
                        $this->serviceIsit->closeTicket($ticketId);
                    }
                    $this->clearErrors();
                }
            }
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '() L' . $e->getLine();		
            $errMsg = $origin . $e->getMessage();	
            $this->logger->log(Logger::ERR, $errMsg);
        }
    }

    protected function addError($error)
    {
        $this->errors[] = $error;
    }
    
    protected function getErrors()
    {
        return $this->errors;
    }
    
    protected function clearErrors()
    {
        $this->errors = [];
    }
    
    protected function verifyAllData($logPrefix, $isInternal, $username, $accessRequest = null)
    {
        $keysToVerify = [
            'disable' => [
                'user_id',
                'ticket_id',
            ],
            'update' => [
                'user_id',
                'ticket_id',
                'roles~role'
            ],
            'create' => [
                'employee_id',
                'user_id',
                'ticket_id',
                'roles~role'
            ],
            'terminate' => [
                'employee_id',
                'ticket_id',
            ]
        ];

        $objectsActionType = strtolower($accessRequest->action_type);

        // make sure we have an allowed "action_type"
        if (!array_key_exists($objectsActionType, $keysToVerify)) {
            $this->logger->log(Logger::WARN, $logPrefix . "Isit message queue has a message with an incorrect 'action_type': $objectsActionType");
            return "Field 'action_type' can not be $objectsActionType";
        }

        // Check for existing values in required fields
        foreach ($keysToVerify[$objectsActionType] as $key) {
            if (strstr($key, "~")) {
                $partKeys = explode ("~", $key);
                if (!isset($accessRequest->{$partKeys[0]}->{$partKeys[1]})
                            || $accessRequest->{$partKeys[0]}->{$partKeys[1]} == "") {
                    $this->logger->log(Logger::WARN, $logPrefix . 'Isit message queue is missing a required parameter: ' . $partKeys[1]);
                    return 'Required parameter is missing: ' . $partKeys[1];
                }
            } else {
                if (!isset($accessRequest->{$key}) or $accessRequest->{$key} == "") {
                    $this->logger->log(Logger::WARN, $logPrefix . "Isit message queue is missing a required parameter $key");
                    return 'Required parameter is missing: ' . $key;
                }
            }
        }

        // make sure this is our ticket belongs to us, via "application"
        if (strtolower($accessRequest->application) != strtolower($this->ecrashApplicationName)) {
            // we have to apply "strtolower" because we are getting ticket w/ diff case values
            return 'Ticket contains incorrect application';
        }
        
        // verifying if this ticket have been already processed
        // for internal users that has yet to be created in user table, 
        // this will return nothing as the entry in isit_ticke table is created 
        // after creating the user as user id is required
        $ticketInfo = $this->serviceIsit->getTicketByExternalId(intval($accessRequest->ticket_id));
        if (!empty($ticketInfo) && $ticketInfo['statusName'] != IsitService::STATUS_PENDING) {
            return 'This ticket was already processed';
        }
        
        $proceedUserDbCheck = true;
        //since we dont have internal user in user table yet
        if ($isInternal && $objectsActionType == IsitService::TICKET_TYPE_CREATE) {
            $proceedUserDbCheck = false;
        }
        
        if ($objectsActionType == IsitService::TICKET_TYPE_TERMINATE) {
            $userInfo = $this->serviceUser->getIdentityDataByPeoplesoftId(intval($accessRequest->employee_id));
        } else if ($proceedUserDbCheck) {
            // verifying if user, which is represented by this ticket, exists in our database
            $userInfo = $this->serviceUser->getIdentityData($username);
        }
        if ($proceedUserDbCheck && empty($userInfo)) {
            return "User $username was not created in the eCrash Keying Application. "
                . "Please create iSIT ticket after you add this user to the eCrash Keying Application.";
        }

        // verifying if Update ticket was created via eCrash Keying App.
        // When admin creates user in keying app - flag isPending will be true too.
        if ($objectsActionType == IsitService::TICKET_TYPE_UPDATE && empty($userInfo['isPending'])) {
            return ucfirst($objectsActionType) . " of user $username was not processed."
                . ucfirst($objectsActionType) . ' user through eCrash Keying Application.';
        }

        return true;
    }
    
    /**
     * Create a user in keying app
     * @param string $username
     * @param int $pplSoftId
     * @param string $email
     * @param string $domain
     * @param xml $accessRequest
     * @param boolean $isInternal
     * @return boolean
     */
    protected function createUser($username, $pplSoftId, $email, $domain, $accessRequest, $isInternal, $logPrefix)
    {
        $result = false;
        $origin = __CLASS__ . '::' . __FUNCTION__ . ' ';
        
        try {            
            if ($isInternal) {
                $this->logger->log(Logger::INFO, $logPrefix . "User: ". $username ." is internal. Email: " . $email . ". Domain: " . $domain);
                $userData = $this->getInternalUserData($accessRequest, $username, $pplSoftId, $email, $logPrefix);
                $fields = $this->setInternalUserData($userData);
                $userId = $this->serviceUser->add($fields);
                if (!empty($userId)) {
                    $result = true;
                    $externalIsitTicketId = intval($accessRequest->ticket_id);
                    //create isit log in isit_ticket table
                    $accessRequestXml = $accessRequest->asXML(); //convert to xml for logging
                    $this->serviceIsit->addInternalUser($userId, $userData['userName'], $externalIsitTicketId, $accessRequestXml, '');
                    //activate
                    $this->serviceUser->processCreateFromMessageQueue($userId, $pplSoftId, true);
                } else {
                    $errorMessage = "$origin Internal user creation error. Failed creating user " . $userData['userName'] . " in the database.";
                    $this->addError($errorMessage);
                    $this->logger->log(Logger::ERR, $logPrefix . $errorMessage);
                }
            } else {
                $this->logger->log(Logger::INFO, $logPrefix . "User: ". $username ." is not internal. Email: " . $email . ". Domain: " . $domain);
                $userInfo = $this->serviceUser->getUserRowInfoByUsername($username);

                if (!empty($userInfo)) {
                    $result = $this->serviceLnaaAuth->authUser(
                                        $this->config['lnaa']['registration']['user'],
                                        $this->config['lnaa']['registration']['password'],
                                        $this->config['lnaa']['registration']['domain']
                                    );
                    if ($result->status->code == LNAAAuthService::CODE_SUCCESS && 
                                            !empty($result->session_id)) {
                        $addUserResult = $this->serviceLnaaAuth->adminAddUser(
                            $result->session_id, 
                            (string)$pplSoftId,
                            (string)$username, 
                            $userInfo['nameFirst'], 
                            $userInfo['nameLast'], 
                            $userInfo['email']
                        );

                        if ($addUserResult['result']) {
                            $result = true;
                            $this->serviceUser->processCreateFromMessageQueue($userInfo['userId'], $pplSoftId);
                        } else {
                            $this->addError($addUserResult['message']);
                        }
                    } else {
                                $errorMessage = "$origin LNAA AuthUser failed using system credentials. Failed creating user $username.";
                                $this->addError($errorMessage);
                                $this->logger->log(Logger::ERR, $logPrefix . $errorMessage);
                            }

                } else {
                    $errorMessage = "$origin User creation error: user $username doesn't exist in keying app database";
                    $this->addError($errorMessage);
                    $this->logger->log(Logger::ERR, $logPrefix . $errorMessage);
                }   
            }
        } catch (Exception $e) {
            $this->logger->log(Logger::ERR, $logPrefix . $origin . ' L' . $e->getLine() . ' ' . $e->getMessage());
            $this->addError($e->getMessage());
        }       
        
        return $result;
    }

    protected function terminateUser ($pplSoftId, $externalIsitTicketId)
    {
        $userId = $this->serviceUser->processTerminateFromMessageQueue($pplSoftId);

        $this->serviceIsit->addTicket(
            $userId,
            $externalIsitTicketId,
            IsitService::STATUS_CLOSED,
            IsitService::TICKET_TYPE_TERMINATE
        );
    }

    protected function stripDomainFromUser ($user)
    {
        $domainPosition = strpos($user, "@");
        if ($domainPosition !== false) {
            $user = substr($user, 0, $domainPosition);
        }

        return $user;
    }
    
    
    protected function setInternalUserData($userData)
    {
        $fields = [
            'isPasswordActive' => true,
            'username' => $userData['userName'],
            'password' => $userData['password'],
            'email' => $userData['email'],
            'nameFirst' => $userData['firstname'],
            'nameLast' => $userData['lastname'],
            'userRoleId' => $userData['userRoleId'],
            'peoplesoftEmployeeId' => $userData['employeeId'],
            'keyingVendorId' => $userData['keyingVendorId']
        ];
        
        return $fields;
    }
    
    protected function getUserNamesAndEmailLnaa($userName)
    {
        $userData = [];
        $adminSessionId = $this->serviceLnaaAuth->authUserAdmin();
        $result = $this->serviceLnaaAuth->adminGetUserData($adminSessionId, $userName);
        if (!empty($result->user_data)) {
            $userData['firstname'] = $result->user_data->user_info->first_name;
            $userData['lastname'] = $result->user_data->user_info->last_name;
            $userData['email'] = strtolower($result->user_data->user_info->email_address);
        }
        return $userData;
    }
    
    protected function getUserNamesAndEmailIsit($accessRequest)
    {
        $userData = [];
        //get user first name, last name and email the hard ugly way via target user details in isit xml
        $targetUserDetails = $accessRequest->target_user_details;
        if (!empty($targetUserDetails)) {
            $targetFields = preg_split("/\r\n|\r|\n/", $targetUserDetails);
            foreach ($targetFields as $fields) {
                $userFields = explode(":", $fields);
                $index = strtolower(str_replace(' ', '', trim($userFields[0])));
                $userData[$index] = trim($userFields[1]);
            }
        }
        return $userData;
    }
    
    protected function getInternalUserNamesAndEmail($userName, $accessRequest, $requestEmail, $logPrefix) 
    {
        $userData = $this->getUserNamesAndEmailLnaa($userName);
        if (empty($userData)) {
            $logMsg = $logPrefix . "Can't retrieve user data for user " . $userName ." in";
            $this->logger->log(Logger::INFO, $logMsg . " LNAA.");
            $userData = $this->getUserNamesAndEmailIsit($accessRequest);
            if (empty($userData)) {
                $this->logger->log(Logger::INFO, $logMsg . " ISIT Target Details.");
                //worst case scenario - get user details from email
                if (!empty($requestEmail) && empty($userData['firstname'])) {
                    $emailInfo = explode('@', $requestEmail);
                    $nameInfo = explode(".", $emailInfo[0]); //Get name from internal email
                    $userData['firstname'] = $nameInfo[0];
                    $userData['lastname'] = $nameInfo[1];
                    $userData['email'] = $requestEmail;
                }
            }
        }
        return $userData;
    }
    
    
    protected function getInternalUserData($accessRequest, $userName, $pplSoftId, $requestEmail, $logPrefix) 
    {
        $userData = [];
        
        //set employee id
        $userData['employeeId'] = $pplSoftId;
        
        //get username with domain
        $userData['userName'] = strtolower($userName);
        
        //get keying vendor id
        $keyingVendorLN = $this->serviceKeyingVendor->fetchKeyingVendorByName(KeyingVendorService::VENDOR_LN);
        $userData['keyingVendorId'] = $keyingVendorLN['keying_vendor_id'];
        
        //set user password
        $generatedPassword = $this->lnHelper->generatePassword();
        $userData['password'] = md5($generatedPassword);
        
        //get user role id
        $roleName = 'Administrator'; //default for internal users
        if (!empty($accessRequest->roles->role)) {
            $roleAttributes = $accessRequest->roles->role->attributes();
            $roleName = (string)$roleAttributes['description'];
        }
        $userData['userRoleId'] = $this->serviceUser->getRoleIdByNameExternal($roleName);
        
        //get first name, last name and email
        $userNameEmailData = $this->getInternalUserNamesAndEmail($userData['userName'], $accessRequest, $requestEmail, $logPrefix);
        
        return array_merge($userData, $userNameEmailData);
    }
    
}
