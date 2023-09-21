<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;

use Base\Service\UserService;

class UserRoleAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'user_role';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    /**
     * Get all valid keying roles only
     * 
     * @return array User roles list
     */
    public function getValidRoles()
    {
        $columns = [
            'userRoleId' => 'user_role_id',
            'name',
            'nameExternal' => 'name_external'
        ];
        
        $select = $this->getSelect()
            ->columns($columns)
            ->where('external_system_id IS NULL')
            ->where("name != '" . UserService::ROLE_SYSTEM . "'");
        
        return $this->fetchAll($select);
    }
    
    public function getRoleByRoleId($roleId) 
    {
        $columns = [
            'name' => 'name',
            'nameExternal' => 'name_external'
        ];
        
        $select = $this->getSelect()
            ->columns($columns)
            ->where('user_role_id = :user_role_id');
        $bind = ['user_role_id' => $roleId];
        
        return $this->fetchRow($select, $bind);
    }
    
    public function getRoleIdByNameExternal($nameExternal)
    {
        $select = $this->getSelect();
        $select->where(['name_external' => $nameExternal]);
        $userRole = $this->fetchRow($select);

        return($userRole['user_role_id']);
    }
}
