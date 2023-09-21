<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;

class EntryStageAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'entry_stage';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    /**
     * @return array array(entryStageId => internalName, ...)
     */
    public function getInternalNamePairs()
    {
        $columns = [
            'entry_stage_id' => 'entry_stage_id',
            'name_internal' => 'name_internal',
        ];
        $select = $this->getSelect();
        $select->columns($columns);
        $select->order('entry_stage_id');
        
        return $this->fetchPairs($select);
    }
    
    /**
     * Get allowed entry stages
     *
     * @param boolean $isPermissionableOnly
     * @return array [entry_stage_id => name_external, ...]
     */
    public function getExternalNamePairs($isPermissionableOnly = false)
    {
        $columns = [
            'entry_stage_id' => 'entry_stage_id',
            'name_external' => 'name_external',
        ];
        $select = $this->getSelect();
        $select->columns($columns);
        if ($isPermissionableOnly) {
            $select->where('is_permissionable = TRUE');
        }
        $select->order('entry_stage_id');
        
        return $this->fetchPairs($select);
    }
    
    public function getIdByInternalName($internalName)
    {
        $columns = [
            'entry_stage_id' => 'entry_stage_id'
        ];
        
        $select = $this->getSelect()
            ->columns($columns)
            ->where('name_internal = :name_internal');
        $bind = ['name_internal' => $internalName];
        
        return $this->fetchOne($select, $bind);
    }
}
