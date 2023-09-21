<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

class WorkTypeAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'work_type';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    /**
     * To get all work types
     * @return array
     */
    public function getAll() {
        return $this->fetchAll();
    }

    /**
     * Get the work types that are allowed for a specific user id (Returns name_external values).
     * @param type $userId
     * @return array
     */
    public function getAllowedByUser($userId)
    {
        $select = $this->getSelect()
            ->from(['wt' => $this->table])
            ->columns([new Expression('DISTINCT wt.work_type_id AS workTypeId, name_external AS name')])
            ->join(['ufp' => 'user_form_permission'], 'ufp.work_type_id = wt.work_type_id', [])
            ->where('ufp.user_id = :user_id')
            ->order(new Expression('FIELD(wt.work_type_id, 1, 3, 2) ASC'));

        $bind = ['user_id' => $userId];
        
        return $this->fetchPairs($select, $bind);
    }

    /**
     * Get allowed work types
     *
     * @return array [entry_stage_id => name_external, ...]
     */
    public function getWorkTypeNamePairs()
    {
        $columns = [
            'work_type_id' => 'work_type_id',
            'name_external' => 'name_external',
        ];
        $select = $this->getSelect();
        $select->columns($columns);
        $select->order('work_type_id');
        
        return $this->fetchPairs($select);
    }

    /**
     * Fetch array of all work types ordered by work type id ASC by default.
     * @param [string] orderby ('work_type_id ASC') optional order by string.
     * @return array
     */
    public function fetchAllWorkTypes($orderby = 'work_type_id ASC')
    {
        try {
            $sql = "SELECT * FROM {$this->table} ORDER BY {$orderby}";
            return $this->fetchAll($sql);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '() L' . $e->getLine();
            $this->logger->log(Logger::ERR, $origin . $e->getMessage());
            return [];
        }// @codeCoverageIgnoreEnd
    }
}
