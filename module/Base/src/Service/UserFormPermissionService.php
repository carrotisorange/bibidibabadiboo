<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Zend\Db\Sql\Select;

use Base\Adapter\Db\UserFormPermissionAdapter;

class UserFormPermissionService extends BaseService
{
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Adapter\Db\UserFormPermissionAdapter
     */
    protected $adapterUserFormPermission;
    
    public function __construct(
        Array $config,
        Logger $logger,
        UserFormPermissionAdapter $adapterUserFormPermission)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterForm = $adapterUserFormPermission;
    }
    
    public function add($fields)
    {
        return $this->adapterForm->insert($fields);
    }
    
    public function getPermissionByUserId($userId, $groupByFormId)
    {
        return $this->adapterForm->getPermissionByUserId($userId, $groupByFormId);
    }
    
    public function removePermissionByUserId($userId)
    {
        if (empty($userId)) {
            return false;
        }
        
        return $this->adapterForm->delete(['user_id' => $userId]);
    }
    
    /**
    * check for the Form permission to the user
    * @param integer $userId
    * @param integer $formId
    * @return boolean
    */
    public function getReportFormPermissionByFormId($userId, $formId)
    {
        return $this->adapterForm->getReportFormPermissionByFormId($userId, $formId);
    }
}
