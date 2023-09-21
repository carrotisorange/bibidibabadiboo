<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

use Base\Service\UserService;
use Base\Service\EntryStageService;
use Auth\Adapter\REST\LNAAAuthAdapter;
use Base\Service\KeyingVendorService;

class UserAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'user';
    
    /**
     * Database adapter will be instantiated by the given table name.
     * @param object $adapter           Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter, LNAAAuthAdapter $adapterLNAAAuth)
    {
        parent::__construct($adapter, $this->table);
        $this->lnaaAuthAdapter = $adapterLNAAAuth;
    }
    
    /**
     * Update last activity of logged user
     */
    public function updateLastActivity($userId, $reset)
    {
        $where = [
            'user_id' => $userId
        ];
        $values = [
            'date_last_activity' => ($reset) ? null : $this->getNowExpr()
        ];
        return $this->update($values, $where);
    }
    
    /**
     * Retrieve a list of users by search criteria
     *
     * @param array $searchCriteria
     * @param bool  $showAdminUsers
     * @param bool  $returnSelectObj
     * @param array $userRolesToExclude
     * @param       excludeEmptyPeopleSoftId
     * @param bool $showSystemUsers (false) include users with system role.
     * @return object/array, depends on $returnSelectObj
     */
    public function getUserList($searchCriteria, $showAdminUsers, $returnSelectObj, $userRolesToExclude,
        $excludeEmptyPeopleSoftId, $showSystemUsers = false)
    {
        
        $columns = [
            'userId' => 'user_id',
            'dateCreated' => new Expression('u.date_created'),
            'username' => 'username',
            'password' => 'password',
            'email' => 'email',
            'nameFirst' => 'name_first',
            'nameLast' => 'name_last',
            'userRoleId' => 'user_role_id',
            'isActive' => 'is_active',
            'isHintRequired' => 'is_hint_required',
            'isGeneratedPassword' => 'is_generated_password',
            'entryStages' => $this->getGroupConcatExpr('es.name_external', ', '),
            'status' => new Expression('IF (u.is_password_active = 1, "Active", "In-Active")'),
            'role' => new Expression('ur.name'),
            'role_external' => new Expression('ur.name_external'),
            'peoplesoftEmployeeId' => 'peoplesoft_employee_id',
            'keyingVendorId' => new Expression('u.keying_vendor_id'),
            'vendorName' => new Expression('kv.vendor_name')
        ];
        
        $select = $this->getSelect();
        $select->from(['u' => $this->table]);
        $select->columns($columns);
        $select->join(['ur' => 'user_role'], 'ur.user_role_id = u.user_role_id', []);
        $select->join(['kv' => 'keying_vendor'], 'kv.keying_vendor_id = u.keying_vendor_id', []);
        $select->join(['ues' => 'user_entry_stage'], 'ues.user_id = u.user_id', [], 'Left');
        $select->join(['es' => 'entry_stage'], 'es.entry_stage_id = ues.entry_stage_id', [], 'Left');
        $select->where('u.is_active = 1 and ur.external_system_id IS NULL');
        
        if ( !$showSystemUsers ) {
            $select->where('ur.name <> "' . UserService::ROLE_SYSTEM . '"');
        }
        
        if (!$showAdminUsers) {
            $select->where('ur.name <> "' . UserService::ROLE_ADMIN . '"');
        }
        
        if ($excludeEmptyPeopleSoftId) {
            $select->where("u.peoplesoft_employee_id IS NOT NULL and u.peoplesoft_employee_id <> '' ");
        }
        
        if (!empty($userRolesToExclude)) {
            $select->where->NotIn('ur.name', $userRolesToExclude);
        }
        
        if (!empty($searchCriteria)) {
            foreach ($searchCriteria as $name => $value) {
                if (empty($value)) {
                    continue;
                }
                switch ($name) {
                    case 'nameFirst':
                        $select->where(["u.name_first" => $value]);
                        break;
                    case 'nameLast':
                        $select->where(["u.name_last" => $value]);
                        break;
                    case 'userRoleId':
                        $select->where(["u.user_role_id" => $value]);
                        break;
                    case 'entryStage':
                        foreach ($value as $entryStage) {
                            $select->where(['ues.entry_stage_id' => $entryStage]);
                        }
                        break;
                    case 'keyingVendorId':
                        if ($value != KeyingVendorService::VENDOR_ALL) {
                            $select->where(['u.keying_vendor_id' => $value]);
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        
        $select->group('u.user_id');
        $select->order(['name_last', 'name_first']);
        if ($returnSelectObj) {
            return $select;
        } else {
            $selectString = $this->sql->getSqlStringForSqlObject($select);
            return $this->fetchAll($selectString);
        }
    }

    /**
     * To get specified page users list from the user select object object returned from $this->getUserList().
     * @param object    $select [Zend\Db\Sql\Select] User select object with search params
     * @param int       $offset Starting record to be retrieved
     * @param int       $limit  Number of records to be fetched
     * @return array    Page users list
     */
    public function getPageUserList(Select $select, $offset, $limit) 
    {
        $select->offset($offset);
        $select->limit($limit);
        return $this->fetchAll($select);
    }
    
    /**
     * To get total number of users
     * @param object    $select User select object with search params
     * @return int      Number of users
     */
    public function getTotalRows(Select $select)
    {
        // To reset the group by, order, limit and offset class initialized to get user list of particular page.
        $select->reset('group');
        $select->reset('order');
        $select->reset('limit');
        $select->reset('offset');
        $column = [
            'total_rows' => new Expression('COUNT(DISTINCT(u.user_id))'),
        ];
        $select->columns($column);
        
        return $this->fetchCol($select)[0];
    }
    
    /**
     * Gets the userId for a username
     *
     * @param string $username
     * @param int|string $keyingVendorId
     * @return int
     */
    public function getUserIdByUsername($username, $keyingVendorId = null)
    {
        $select = $this->getSelect();
        $select->from('user', ['user_id']);
        $select->where(['username' => $username]);
        if (!empty($keyingVendorId) && $keyingVendorId != KeyingVendorService::VENDOR_ALL) {
            $select->where(['keying_vendor_id' => $keyingVendorId]);
        }
        return $this->fetchOne($select);
    }
    
    /**
     * Gets all userIds for a first & last name
     *
     * @param int|string $keyingVendorId
     * @param string $nameFirst
     * @param string $nameLast
     * @return string
     */
    public function getUserIdsByName($keyingVendorId, $nameFirst = null, $nameLast = null) 
    {
        $select = $this->getSelect();
        $select->from('user');
        $select->columns(['user_id']);
        if (!empty($keyingVendorId) && $keyingVendorId != KeyingVendorService::VENDOR_ALL) {
            $select->where(['keying_vendor_id' => $keyingVendorId]);
        }
        if (!empty($nameFirst)) {
            $select->where(['name_first' => $nameFirst]);
        }
        
        if (!empty($nameLast)) {
            $select->where(['name_last' => $nameLast]);
        }
        
        return $this->fetchCol($select);
    }
    
    public function fetchUserRowInfoByUsername($username) 
    {
        $columns = [
            'userId' => 'user_id',
            'dateCreated' => 'u.date_created',
            'dateUpdated' => 'date_updated',
            'datePasswordSet' => 'date_password_set',
            'dateLastLogin' => 'date_last_login',
            'dateLastActivity' => 'date_last_activity',
            'username' => 'username',
            'password' => 'password',
            'email' => 'email',
            'nameFirst' => 'name_first',
            'nameLast' => 'name_last',
            'userRoleId' => 'u.user_role_id',
            'isActive' => 'is_active',
            'isPending' => 'is_pending',
            'isPasswordActive' => 'is_password_active',
            'isHintRequired' => 'is_hint_required',
            'isGeneratedPassword' => 'is_generated_password',
            'loginAttemptCount' => 'login_attempt_count',
            'loginCounter' => 'login_counter',
            'peoplesoftEmployeeId' => 'peoplesoft_employee_id',
            'role' => 'ur.name',
            'role_external' => 'ur.name_external',
            'keyingVendorId' => 'u.keying_vendor_id',
            'vendorName' => 'kv.vendor_name'
        ];
        $select = new Select();
        $select->from(['u' => $this->table]);
        $select->columns($columns, false);
        $select->join(['ur' => 'user_role'], 'ur.user_role_id = u.user_role_id', []);
        $select->join(['kv' => 'keying_vendor'], 'kv.keying_vendor_id = u.keying_vendor_id', []);
        $select->where(["u.username" => $username]);
        return $this->fetchRow($select);
    }
    
    public function login($userId)
    {
        $sql = "UPDATE user
        SET date_last_login = NOW(),
            date_last_activity = NOW(),
            login_attempt_count = 0,
            login_counter = login_counter + 1
            WHERE user_id = :user_id";
        
        $bind = ['user_id' => $userId];
        
        return $this->getAdapter()->query($sql, $bind);
    }
    
    public function setPasswordInactive($username, $reasonCode, $message = null, $isInternal = false)
    {
        if (!$isInternal || ($isInternal && $reasonCode == LNAAAuthAdapter::REASON_CODE_OTHER)) {
            $this->changeLNAAStatus($username, LNAAAuthAdapter::STATUS_DISABLED, $reasonCode, $message, $isInternal);
        }
        if (!$isInternal) {
            $data['is_password_active'] = false;
            return $this->update($data, ['username = ?' => $username]);
        }
    }
    
    public function changeLNAAStatus($username, $userStatus, $reasonCode = null, $message = null, $isInternal = false)
    {
        $userInfo = $this->fetchUserRowInfoByUsername($username);
        $adminSessionId = $this->lnaaAuthAdapter->authUserAdmin();        
        if (!$isInternal && $userStatus == LNAAAuthAdapter::STATUS_ENABLED) {
            $this->lnaaAuthAdapter->adminResetUserPassword($adminSessionId, $userInfo['username']);
        }
        else {
            if (!$isInternal) {
                $this->lnaaAuthAdapter->adminUpdateUser($adminSessionId, $username, $userInfo['nameFirst'],
                        $userInfo['nameLast'], $userInfo['email']);
            }
            $this->lnaaAuthAdapter->adminUpdateUserStatus($adminSessionId, $username, $userStatus, $reasonCode, $message);
        }
    }
    
    public function updateLoginAttempt($username, $loginAttempt)
    {
        return $this->update(['login_attempt_count' => $loginAttempt], ['username = ?' => $username]);
    }
    
    public function fetchUserRowInfoByUserId($userId) 
    {
        $columns = [
            'userId' => 'user_id',
            'dateCreated' => new Expression('u.date_created'),
            'dateUpdated' => 'date_updated',
            'datePasswordSet' => 'date_password_set',
            'dateLastLogin' => 'date_last_login',
            'dateLastActivity' => 'date_last_activity',
            'username' => 'username',
            'password' => 'password',
            'email' => 'email',
            'nameFirst' => 'name_first',
            'nameLast' => 'name_last',
            'userRoleId' => 'user_role_id',
            'isActive' => 'is_active',
            'isPending' => 'is_pending',
            'isPasswordActive' => 'is_password_active',
            'isHintRequired' => 'is_hint_required',
            'isGeneratedPassword' => 'is_generated_password',
            'loginAttemptCount' => 'login_attempt_count',
            'loginCounter' => 'login_counter',
            'peoplesoftEmployeeId' => 'peoplesoft_employee_id',
            'role' => new Expression('ur.name'),
            'roleExternal' => new Expression('ur.name_external'),
            'keyingVendorId' => new Expression('u.keying_vendor_id'),
            'vendorName' => new Expression('kv.vendor_name')
        ];
        
        $select = $this->getSelect();
        $select->from(['u' => $this->table]);
        $select->columns($columns);
        $select->join(['ur' => 'user_role'], 'ur.user_role_id = u.user_role_id', []);
        $select->join(['kv' => 'keying_vendor'], 'kv.keying_vendor_id = u.keying_vendor_id', []);
        $select->where(['u.user_id' => $userId]);
        
        return $this->fetchRow($select);
    }
    
    public function isUsernameUnique($username, $excludeUserId)
    {
        $select = $this->getSelect()
            ->where('username = :username');
        
        $bind = ['username' => $username];
        
        if (!empty($excludeUserId)) {
            $select->where('user_id != :user_id');
            $bind['user_id'] = $excludeUserId;
        }
        
        $result = $this->fetchRow($select, $bind);
        return (!count($result));
    }
    
    public function fetchUsernameById($userId)
    {
        $select = $this->getSelect()
            ->columns(['username'])
            ->where('user_id = :user_id');
        $bind = ['user_id' => $userId];
        
        return $this->fetchOne($select, $bind);
    }
    
    public function setPasswordActive($userId, $refreshDateLastLogin = true, $updateLNAA = true, $isInternal = false)
    {
        if ($refreshDateLastLogin) {
            $data['date_last_login'] = $this->getNowExpr();
        }
        
        $data['date_password_set'] = $this->getNowExpr();
        if (!$isInternal) {
            $data['is_generated_password'] = 1;
        }
        $data['login_attempt_count'] = 0;
        $data['is_password_active'] = true;
        if ($updateLNAA) {
            $username = $this->fetchUsernameById($userId);
            $this->changeLNAAStatus($username, LNAAAuthAdapter::STATUS_ENABLED);
        }
        
        return $this->update($data, ['user_id' => $userId]);
    }
    
    public function setIsPending($userName, $value = true)
    {
        $this->update(['is_pending' => $value], ['username' => $userName]);
    }

    public function ifUserSetUpForRekey($userId)
    {
        $column = [
            'total_rows' => new Expression('COUNT(u.user_id)'),
        ];

        $select = $this->getSelect()
            ->from(['ues' => 'user_entry_stage'])
            ->columns($column)
            ->join(['u' => 'user'], 'ues.user_id = u.user_id', ["user_id" => "user_id"])
            ->join(['es' => 'entry_stage'], 'ues.entry_stage_id = es.entry_stage_id', [])
            ->where(["es.name_internal" => EntryStageService::STAGE_REKEY, "u.user_id" => ":user_id"]);
        $bind = ['user_id' => $userId];

        return $this->fetchOne($select, $bind);
    }
    
    public function ifUserSetUpForElectronicRekey($userId)
    {
        $column = [
            'total_rows' => $this->getCount('u.user_id'),
        ];

        $select = $this->getSelect()
            ->from(['ues' => 'user_entry_stage'])
            ->columns($column)
            ->join(['u' => 'user'], 'ues.user_id = u.user_id', ["user_id" => "user_id"])
            ->join(['es' => 'entry_stage'], 'ues.entry_stage_id = es.entry_stage_id', [])
            ->where(["es.name_internal" => EntryStageService::STAGE_ELECTRONIC_REKEY, "u.user_id" => ":user_id"]);
        $bind = ['user_id' => $userId];
        
        return $this->fetchOne($select, $bind);
    }
    
    public function deleteById($userId, $isTerminate = false)
    {
        $data = [
            'is_active' => 0,
            'is_password_active' => 0
        ];
        $userStatus = LNAAAuthAdapter::STATUS_DISABLED; 
        $reasonCode = LNAAAuthAdapter::REASON_CODE_REQ_BY_MGR;
        $message = "Deleted";
        if ($isTerminate === true) {
            $data['is_pending'] = false;
            $userStatus = LNAAAuthAdapter::STATUS_EXPIRED;
            $reasonCode = LNAAAuthAdapter::REASON_CODE_DECOMMISSIONED;
            $message = "Terminated";
        }
        $username = $this->fetchUsernameById($userId);
        $this->changeLNAAStatus($username, $userStatus, $reasonCode, $message);
        
        return $this->update($data, ['user_id' => $userId]);
    }

    public function updateUserRolebyUserName($userName, $roleId)
    {
        return $this->update([
            'user_role_id' => $roleId
            ], [
            'username' => $userName,
        ]);
    }

    public function getUserIdByPeoplesoftId($peoplesoftId)
    {
        $select = $this->getSelect();
        $select->from('user', ['user_id']);
        $select->where('peoplesoft_employee_id = :peoplesoft_employee_id');
        $bind = ['peoplesoft_employee_id', $peoplesoftId];

        return $this->fetchOne($select, $bind);
    }

        /**
     * 
     * @param Int $userId
     * @param Int $pplSoftId
     * @return Boolean
     * @throws Exception
     */
    public function processCreateFromMessageQueue($userId, $pplSoftId, $isInternal = false)
    {
        $result = false;
        try {
            $this->setPasswordActive($userId, false, false, $isInternal);
            $updateFields = [
                'peoplesoft_employee_id' => $pplSoftId,
                'is_pending' => false,
                'is_active' => true,
            ];
            $result =  $this->update($updateFields, ['user_id' => $userId]);
        } catch(Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            throw new Exception($e);
        }
        
        return $result;
    }

    public function getUserIdByUserPplSoftId($pplSoftId)
    {
        $select = $this->getSelect();
        $select->from('user', ['user_id']);
        $select->where('peoplesoft_employee_id = :peoplesoft_employee_id');
        $bind = ['peoplesoft_employee_id' => $pplSoftId];

        return $this->fetchOne($select, $bind);
    }

     public function processTerminateFromMessageQueue($pplSoftId)
     {
        $userId = $this->getUserIdByUserPplSoftId($pplSoftId);

        if ($userId) {
            $this->deleteById($userId, true);
        }

        return $userId;
    }
}
