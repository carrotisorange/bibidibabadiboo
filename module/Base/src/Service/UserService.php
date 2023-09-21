<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Zend\Db\Sql\Select;
use Base\Adapter\Db\UserAdapter;
use Base\Adapter\Db\UserRoleAdapter;
use Auth\Adapter\Soap\IpRestrictAdapter;
use Base\Service\KeyingVendorService;
use Base\Adapter\Db\ReportEntryAdapter;
use Base\Adapter\Db\ReportQueueAdapter;
use Base\Adapter\Db\ReportEntryQueueAdapter;
use Base\Adapter\Db\UserEntryPrefetchAdapter;


class UserService extends BaseService
{
    const ROLE_GUEST    = 'guest';
    const ROLE_OPERATOR = 'operator';
    const ROLE_SUPER_OPERATOR   = 'superoperator';
    const ROLE_SUPERVISOR       = 'supervisor';
    const ROLE_ADMIN    = 'admin';
    const ROLE_SYSTEM   = 'system';
    const USER_SYSTEM   = 'SYSTEM';
    
    const IPRESTRICT_REASON_CODE_DOWN = 'B13';
    const IPRESTRICT_ROAMING_CODE = 'B9';
    
    const IPRESTRICT_ALLOW = 1;
    const IPRESTRICT_ROAMING = 2;
    const IPRESTRICT_DENY = 0;
    
    const IPRESTRICT_ACTION_ALLOW = 'ALLOW';
    const IPRESTRICT_ACTION_DENY = 'DENY';
    const REPORT_SPECIFIC = 'reportspecific';
    const DATERANGE_REPORTS = 'daterangereports';     
    
    const INTERNAL_DOMAIN_RISK = 'risk';
    const INTERNAL_DOMAIN_LEXISNEXIS = 'lexisnexis';
    
    /**
    * @var Array List of columns to be stored in the user table.
    */
    protected $fields;
    
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Adapter\Db\UserAdapter
     */
    protected $adapterUser;
    
    /**
     * @var Base\Adapter\Db\UserRoleAdapter
     */
    protected $adapterUserRole;
    
    /**
    * @var Auth\Adapter\Soap\IpRestrictAdapter
    */
    public $adapterIpRestrict;

    /**
     * @var Base\Adapter\Db\ReportEntryAdapter
     */
    protected $adapterReportEntry;

    /**
     * @var Base\Adapter\Db\ReportQueueAdapter
     */
    protected $adapterReportQueue;

    /**
     * @var Base\Adapter\Db\ReportEntryQueueAdapter
     */
    protected $adapterReportEntryQueue;

    /**
     * @var Base\Adapter\Db\UserEntryPrefetchAdapter
     */
    protected $adapterUserEntryPrefetch;
    
    public function __construct(
        Array $config,
        Logger $logger,
        UserAdapter $adapterUser,
        UserRoleAdapter $adapterUserRole,
        IpRestrictAdapter $adapterIpRestrict,
        ReportEntryAdapter $adapterReportEntry,
        ReportQueueAdapter $adapterReportQueue,
        ReportEntryQueueAdapter $adapterReportEntryQueue,
        UserEntryPrefetchAdapter $adapterUserEntryPrefetch)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterUser = $adapterUser;
        $this->adapterUserRole = $adapterUserRole;
        $this->adapterIpRestrict = $adapterIpRestrict;
        $this->adapterReportEntry = $adapterReportEntry;
        $this->adapterReportQueue = $adapterReportQueue;
        $this->adapterReportEntryQueue = $adapterReportEntryQueue;
        $this->adapterUserEntryPrefetch = $adapterUserEntryPrefetch;
        
        // List of fields to be stored in the user table.
        $this->fields = [
            'user_id' => ['alias' => 'userId'],
            'is_password_active' => ['alias' => 'isPasswordActive'],
            'date_created' => ['alias' => 'dateCreated', 'defaultValue' => $this->adapterUser->getNowExpr()],
            'date_password_set' => ['alias' => 'datePasswordSet', 'defaultValue' => $this->adapterUser->getNowExpr()],
            'username' => ['alias' => 'username', 'isRequired' => true],
            'password' => ['alias' => 'password', 'isRequired' => true],
            'email' => ['alias' => 'email', 'isRequired' => true],
            'name_first' => ['alias' => 'nameFirst', 'isRequired' => true],
            'name_last' => ['alias' => 'nameLast', 'isRequired' => true],
            'user_role_id' => ['alias' => 'userRoleId', 'isRequired' => true],
            'peoplesoft_employee_id' => ['alias' => 'peoplesoftEmployeeId', 'isRequired' => true],
            'keying_vendor_id' => ['alias' => 'keyingVendorId']
        ];
    }
    
    /**
     * Return select object - a list of users that match search criteria
     * @param array $searchCriteria ([])
     * @param bool  $showAdminUsers (false) include users with admin role
     * @param bool  $returnSelectObj (true)
     * @param array $userRolesToExclude ([])
     * @param bool  $excludeEmptyPeopleSoftId (false)
     * @param bool  $showSystemUsers (false) include users with system role
     * @return object/array, Depends on $returnSelectObj
     */
    public function getUserList(
        Array $searchCriteria = [],
        $showAdminUsers = false,
        $returnSelectObj = true,
        Array $userRolesToExclude = [],
        $excludeEmptyPeopleSoftId = false,
        $showSystemUsers = false)
    {
        return $this->adapterUser->getUserList($searchCriteria, $showAdminUsers, $returnSelectObj, $userRolesToExclude,
            $excludeEmptyPeopleSoftId, $showSystemUsers);
    }
    
    /**
     * To get total number of users
     * @param object    $select [Zend\Db\Sql\Select] User select object with search params
     * @return int      Number of users
     */
    public function getTotalRows($select = null)
    {
        return (empty($select)) ? 0 : $this->adapterUser->getTotalRows($select);
    }
    
    /**
     * To get specified page users list from the user select object returned from $this->getUserList().
     * @param object    $select [Zend\Db\Sql\Select] User select object with search params
     * @param array     $searchCriteria ([])
     * @return array    Page users list
     */
    public function getPageUserList(Select $select, Array $searchCriteria = [])
    {
        $offset = (!empty($searchCriteria['offset'])) ? $searchCriteria['offset'] : 0;
        $limit = (!empty($searchCriteria['limit'])) ? $searchCriteria['limit'] : $this->config['pagination']['perpage'];
        
        return $this->adapterUser->getPageUserList($select, $offset, $limit);
    }
    
    /**
     * Gets the userId for a username
     *
     * @param string $username
     * @return int
     */
    public function getUserIdByUsername($username, $keyingVendorId = null) 
    {
        return $this->adapterUser->getUserIdByUsername($username, $keyingVendorId);
    }

    /**
     * Gets all userIds for a first & last name
     
     * @param int $keyingVendorId
     * @param string $nameFirst
     * @param string $nameLast
     * @return string
     */
    public function getUserIdsByName($keyingVendorId, $nameFirst = null, $nameLast = null) {
        return $this->adapterUser->getUserIdsByName($keyingVendorId, $nameFirst, $nameLast);
    }
    
    public function getUserRowInfoByUsername($username)
    {
        return $this->adapterUser->fetchUserRowInfoByUsername($username);
    }
    
    public function login($userId)
    {
        return $this->adapterUser->login($userId);
    }
    
    public function updateLastActivity($userId, $reset = false)
    {
        return $this->adapterUser->updateLastActivity($userId, $reset);
    }
    
    public function checkLastLoginTime($user, $maxIdleDays)
    {        
        $intervalDays = abs(strtotime(date('Y-m-d H:i:s')) - strtotime($user->dateLastLogin)) / (60 * 60 * 24);
        return ($intervalDays < $maxIdleDays);
    }
    
    public function getUsernameById($userId)
    {
        return $this->adapterUser->fetchUsernameById($userId);
    }
    
    public function setPasswordInactive($username, $reasonCode, $message = null, $isInternal = false)
    {
        return $this->adapterUser->setPasswordInactive($username, $reasonCode, $message, $isInternal);
    }
    
    public function increaseLoginAttempt($username, $increment = 1)
    {
        $userInfo = $this->getIdentityData($username);
        if (empty($userInfo)) {
            return false;
        }
        
        $loginAttempt = $userInfo['loginAttemptCount'] + abs($increment);
        $this->adapterUser->updateLoginAttempt($username, $loginAttempt);
        return $loginAttempt;
    }
    
    /**
     * Fetch data good to use to identify a user.
     *
     * @param integer/string $userId ($userId has to be a number)
     * @return array
     */
    public function getIdentityData($userId)
    {
        $userId = (is_numeric($userId)) ? $userId : $this->getUserIdByUsername($userId);
        $user = $this->adapterUser->fetchUserRowInfoByUserId($userId);
        if (empty($user)) {
            return false;
        }
        
        $role = $this->adapterUserRole->getRoleByRoleId($user['userRoleId']);
        $user['role'] = $role['name'];
        $user['roleExternal'] = $role['nameExternal'];
        return $user;
    }
    
    /**
     * Get all valid keying roles only
     *
     * @return array User roles list
     */
    public function getValidRoles()
    {
        return $this->adapterUserRole->getValidRoles();
    }
    
    public function IpRestrictCheck($username, $ipAddress)
    {
        return $this->adapterIpRestrict->checkLogin($username, $ipAddress);
    }
    
    public function add($fields)
    {
        $data = [];
        foreach ($this->fields as $field => $value) {
            if (!isset($fields[$value['alias']])) {
                if (isset($value['defaultValue'])) {
                    $data[$field] = $value['defaultValue'];
                } elseif (!empty($value['isRequired'])) {
                    return false;
                }
                continue;
            } else {
                $data[$field] = $fields[$value['alias']];
            }
        }
        
        return $this->adapterUser->insert($data);
    }

    public function edit($userId, $fields)
    {
        $data = [];
        foreach ($this->fields as $field => $value) {
            if (isset($fields[$value['alias']])) {
                $data[$field] = $fields[$value['alias']];
            }
        }
        
        return $this->adapterUser->update($data, ['user_id' => $userId]);
    }
    
    public function save($fields)
    {
        if (!empty($fields['userId'])) {
            return $this->edit($fields['userId'], $fields);
        } else {
            return $this->add($fields);
        }
    }
    
    public function isUsernameUnique($username, $excludeUserId = null)
    {
        return $this->adapterUser->isUsernameUnique($username, $excludeUserId);
    }
    
    public function setPasswordActive($userId, $refreshDateLastLogin = true, $updateLNAA = true, $isInternal = false)
    {
        return $this->adapterUser->setPasswordActive($userId, $refreshDateLastLogin, $updateLNAA, $isInternal);
    }
    
    public function setIsPending($userName, $value = true)
    {
        return $this->adapterUser->setIsPending($userName, $value);
    }
    
    public function getRoleById($roleId)
    {
        return $this->adapterUserRole->getRoleByRoleId($roleId);
    }
    
    public function getRoleIdByNameExternal($roleName)
    {
        return $this->adapterUserRole->getRoleIdByNameExternal($roleName);
    }
    
    public function deleteById($userId)
    {
        return $this->adapterUser->deleteById($userId);
    }

    public function updateRoleById($userName, $roleName)
    {
        $roleId = $this->adapterUserRole->getRoleIdByNameExternal($roleName);
        
        return empty($roleId) ? false : $this->adapterUser->updateUserRolebyUserName($userName, $roleId);
    }

     public function getUserIdByPeoplesoftId($peoplesoftId) {
        return $this->adapterUser->getUserIdByPeoplesoftId($peoplesoftId);
    }

    public function getIdentityDataByPeoplesoftId($peoplesoftId)
    {
        $userId = $this->getUserIdByPeoplesoftId($peoplesoftId);

        return $this->getIdentityData($userId);
    }

    /**
     * Do all the things necessary when creating a new user from the message queue
     * example: set the 'peopleSoftId, reset the proper flags, etc....
     * @param string $username the username of the user
     * @param int $pplSoftId the 'people soft id' of the user
     * @param bool $isInternal if user is internal or not
     * @return int 'user_id' of the user
     * @return string the 'email' of the user
     */
    public function processCreateFromMessageQueue($userId, $pplSoftId, $isInternal = false)
    {
        return $this->adapterUser->processCreateFromMessageQueue($userId, $pplSoftId, $isInternal);
    }
    
    public function processTerminateFromMessageQueue($pplSoftId)
    {
        return $this->adapterUser->processTerminateFromMessageQueue($pplSoftId);
    }
    
    public function ifUserSetUpForRekey($userId)
    {
        return $this->adapterUser->ifUserSetUpForRekey($userId);
    }
    
    public function ifUserSetUpForElectronicRekey($userId)
    {
        return $this->adapterUser->ifUserSetUpForElectronicRekey($userId);
    }
    
    /**
     * Return keying vendor id of logged in user
     * @param int $userId
     * @return int $keyingVendorId
     */
    public function getKeyingVendorIdByUserId($userId) 
    {
        if (empty($userId)) {
            return;
        }
        $userInfo = $this->getIdentityData($userId);
        return $userInfo['keyingVendorId'];
    }
    
    /*
     * Check if user is internal by checking the domain included in his login user id
     * @param string $loginDomain domain included in the login user id
     * @return array $isInternal isInternal flag
     */   
    public function getUserInternalInfoByDomain($loginDomain)
    {
        $isInternal = false;
        if (in_array($loginDomain, $this->config['internalDomains'])) {
            $isInternal = true;
        }
        return $isInternal;
    }
    
    /**
     * Return if user is internal and user domain based on user email
     * @param string $email email address of user
     * @return array $userInternalInfo isInternal flag and email domain
     */
    public function getUserInternalInfoByEmail($email) 
    {
        $domain = $this->config['registration']['domain'];
        $userInternalInfo = ['isInternal' => false, 'domain' => $domain];
        if (preg_match('/(?<=@)(\w+)/i', strtolower($email), $matches)) { //get the main domain (risk, noam, relx, etc)*/
            $domain = $matches[0];
            $isInternal = $this->getUserInternalInfoByDomain($domain);
            if ($isInternal) {
                if (stristr($domain, self::INTERNAL_DOMAIN_LEXISNEXIS) !== FALSE) {
                    $domain = self::INTERNAL_DOMAIN_RISK;
                }
            } else {
                $domain = $this->config['registration']['domain'];
            }
            $userInternalInfo['isInternal'] = $isInternal; 
            $userInternalInfo['domain'] = $domain;
        }
        return $userInternalInfo;
    }
    
    /**
     * Return if user is internal and user domain based on username or email
     * @param string $userName username of user used in login
     * @param string $email email address of user
     * @return array $userInternalInfo isInternal flag and email domain
     */
    public function getUserIsInternalAndDomainInfo($userName, $email = null)
    {
        if (!empty($userName)) { 
            $userNameInfo = explode("@", $userName);
        }
        $domain = $this->config['registration']['domain'];
        $userInternalInfo = ['isInternal' => false, 'domain' => $domain];
        if (!empty($userName) && !empty($userNameInfo[1])) { //domain
            $domain = $userNameInfo[1];
            $isInternal = $this->getUserInternalInfoByDomain($domain);
            $userInternalInfo['isInternal'] = $isInternal; 
            $userInternalInfo['domain'] = $domain;
        }
        if (!$userInternalInfo['isInternal']) {
            $userInfo = $this->getUserRowInfoByUsername($userName);
            if (!empty($userInfo)) {
                if ($userInfo['vendorName'] != KeyingVendorService::VENDOR_LN) {
                    $userInternalInfo['isInternal'] = false;
                    $userInternalInfo['domain'] = $this->config['registration']['domain']; //default domain - ecrkeyin
                } else {
                    $userInternalInfo  = $this->getUserInternalInfoByEmail($userInfo['email']);
                }
            } else if (!empty($email)) {
                $userInternalInfo = $this->getUserInternalInfoByEmail($email);
            } else {
                $userInternalInfo['domain'] = $domain; //default
            }
        }
        return $userInternalInfo;
    }

    public function resetUser($userId)
    {
        // Double verify all parts set to the user have been reset.
        $this->adapterReportEntry->revertInProgress($userId);
        $this->adapterUserEntryPrefetch->removeUserReports($userId);
        $this->adapterReportQueue->unassignUser($userId);
        $this->adapterReportEntryQueue->unassignUser($userId);
    }
}
