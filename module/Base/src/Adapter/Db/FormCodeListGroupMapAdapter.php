<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class FormCodeListGroupMapAdapter extends DbAbstract
{
    /**
     * Table name
     * @var string Table name
     */
    protected $table = 'form_code_list_group_map';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    public function insertAssoc($groupId, array $listIds)
    {
        foreach ($listIds as $listId) {
            $this->insert([
                'form_code_group_id' => $groupId,
                'form_code_list_id' => $listId
            ]);
        }
    }
    
    public function getAssocListIds($groupId)
    {
        $sql = "
            select form_code_list_id as listId
              from ([$this->table])
              where form_code_group_id = :form_code_group_id
        ";
        
        $bind = ['form_code_group_id' => $groupId ];
        
        return $this->fetchCol($sql, $bind);
    }
    
    public function getAssocLists($groupId)
    {
        $sql = "
            select l.form_code_list_id as listId, l.name as name
                from ([$this->table]) lgm
                    join form_code_list l using (form_code_list_id)
                where lgm.form_code_group_id = :form_code_group_id            
        ";
        $bind = ['form_code_group_id' => $groupId];
        
        return $this->fetchPairs($sql, $bind);
    }
    
    public function removeAssoc($groupId, $listIds)
    {
        $deletedCount = 0;
        
        foreach ($listIds as $listId) {
            $deletedCount += $this->delete([
                'form_code_group_id = ?' => $groupId,
                'form_code_list_id = ?' => $listId
            ]);
        }
        return $deletedCount;
    }
}
