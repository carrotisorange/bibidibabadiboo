<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

class StateAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'state';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    public function fetchStates()
    {
        $select = $this->getSelect()
            ->order(['name_abbr'], 'ASC');
        
        return $this->fetchAssoc($select);
    }
    
    /**
     * To get all the states
     *
     * @return array List of states
     */
    public function fetchAllStates()
    {
        $columns = [
            'stateId' => 'state_id',
            'nameAbbr' => 'name_abbr',
            'nameFull' => 'name_full',
        ];
        
        $select = $this->getSelect()
            ->columns($columns);
        
        return $this->fetchAll($select);
    }
    
    public function fetchStateIdNamePairs($stateId = null)
    {
        $columns = [
            'stateId' => 'state_id',
            'stateName' => 'name_full'
        ];
        
        $select = $this->getSelect()
            ->columns($columns)
            ->order($columns['stateName']);
        
        $bind = [];
        if (!empty($stateId)) {
            $select->where(['state_id = :state_id']);
            $bind['state_id'] = $stateId;
        }
        
        return $this->fetchPairs($select, $bind);
    }
    
    /**
     * Select all active states
     * 
     * @return array all states, else return empty array; on exception of failure
     */
    public function fetchAlltoArray()
    {   
        $columns =  [
            'stateId' => 'state_id',
            'nameAbbr' => 'name_abbr',
            'nameFull' => 'name_full'
        ];
        $select = $this->getSelect();
        $select->from($this->table)
            ->columns($columns)
            ->order($columns['stateId']);
        return $rows = $this->fetchAll($select);
    }

    public function fetchStateConfigurationList()
    {
        $sql = "
            SELECT s.state_id as stateId,
                s.name_abbr as nameAbbr,
                s.name_full as nameFull,
                sc.auto_extraction as autoExtraction,
                sc.work_type_id_list as WorkTypeIds,
                (SELECT GROUP_CONCAT(b.name_external ORDER BY b.work_type_id) FROM state_configuration a 
                    INNER JOIN work_type b ON FIND_IN_SET(b.work_type_id, a.work_type_id_list) > 0 where sc.work_type_id_list != '' and a.state_id = sc.state_id
                    GROUP BY a.state_configuration_id) as WorkTypes 
                FROM  state s
                LEFT JOIN state_configuration sc On s.state_id = sc.state_id WHERE s.state_id > 0 ORDER BY s.state_id ASC";

        return $this->fetchAll($sql);
    }
    
    public function fetchStateIdAbbrPairs()
    {
        $sql = "
            SELECT
                state_id,
                name_abbr
            FROM state
        ";

        return $this->fetchPairs($sql);
    }
    
    public function fetchAutoExtractionEnabledStates()
    {
        $columns = [
            'stateId' => 's.state_id',
            'nameAbbr' => 's.name_abbr',
            'nameFull' => 's.name_full',
        ];

        $select = $this->getSelect()
            ->columns($columns, false)
            ->from(['s' => $this->table])
            ->join(["sc" => "state_configuration"], new Expression("s.state_id = sc.state_id AND sc.auto_extraction = 1"), [])
            ->order(['s.name_abbr'], 'ASC');
        
        return $this->fetchAll($select);
    }

    public function fetchStateAbbrById($stateId = null)
    {
        if (empty($stateId)) {
            return;
        }
        
        $columns = [
            'nameAbbr' => 'name_abbr',
        ];
        
        $select = $this->getSelect()
            ->columns($columns);
        
        $select->where(['state_id = :state_id']);
        $bind = [];
        $bind['state_id'] = $stateId;
        
        return $this->fetchOne($select, $bind);
    }
}
