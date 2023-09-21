<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;

class UserFormPermissionAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'user_form_permission';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    public function getPermissionByUserId($userId, $groupByFormId = false)
    {
        $columns = [
            'formId' => 'ufp.form_id',
            'agencyId' => 'f.agency_id',
            'stateId' => 'f.state_id',
            'userId' => 'ufp.user_id',
            'workTypeId' => 'ufp.work_type_id'
        ];
        
        $select = $this->getSelect()
            ->from(['ufp' => 'user_form_permission'], [])
            ->join(['f' => 'form'], 'f.form_id = ufp.form_id', [])
            ->where('ufp.user_id = :user_id');
        
        $bind = ['user_id' => $userId];
        
        if (!empty($groupByFormId)) {
            $columns['workTypeId'] = $this->getGroupConcatExpr('ufp.work_type_id', ' ');
            $select->group('ufp.form_id');
            $select->columns($columns, false);
            $result = $this->fetchAssoc($select, $bind);
        } else {
            $select->columns($columns, false);
            $result = $this->fetchAll($select, $bind);
        }
        
        return $result;
    }
    
    /**
    * select record if user has data for the formId
    * @param integer $userId
    * @param integer $formId
    * @return boolean
    */
    public function getReportFormPermissionByFormId($userId, $formId)
    {
        $columns = [
            'formId' => 'ufp.form_id',
            'userId' => 'ufp.user_id',
            'workTypeId' => 'ufp.work_type_id'
        ];
        
        $select = $this->getSelect()
            ->columns($columns, false)
            ->from(['ufp' => 'user_form_permission'])
            ->where('ufp.form_id = :form_id')
            ->where('ufp.user_id = :user_id');
        
        $bind = [
            'form_id' => $formId,
            'user_id' => $userId,
        ];
        $result = $this->fetchAll($select, $bind);
        
        return count($result);
    }
}
