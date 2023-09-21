<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class FormCodeListAdapter extends DbAbstract
{
    /**
     * Table name
     * @var string Table name
     */
    protected $table = 'form_code_list';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    public function getCodePairs($listId)
    {
        $sql = "
            select cp.form_code_pair_id as id, cp.code as code, cp.description as value 
              from form_code_pair cp
              join form_code_list_pair_map lcpm using (form_code_pair_id)           
              where lcpm.form_code_list_id = :list_id
        ";
        
        $bind = [
            'list_id' => $listId,
        ];
        
        return $this->fetchAll($sql, $bind);
    }
    
    public function insertList($name)
    {
        return $this->insert([
            'name' => $name
        ]);
    }
    
    public function updateList($updListId, $updListName)
    {
        return $this->update(
            ['name' => $updListName],
            ['form_code_list_id = ?' => $updListId]
        );
    }
}
