<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;

class StateConfigurationAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'state_configuration';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    public function isRollOutState($reportStateId, $reportWorkTypeId)
    {
        $sql = "SELECT count(*) from $this->table where state_id = :stateId and FIND_IN_SET($reportWorkTypeId,work_type_id_list)";
        $bind = ['stateId' => $reportStateId];

        return $this->fetchOne($sql, $bind);
    }

    public function getWorkTypeIDPerState($report_state_id)
    {
        $sql = "SELECT work_type_id_list FROM $this->table WHERE state_id = :report_state_id";
        $bind = ['report_state_id' => $report_state_id];
        $result = $this->fetchOne($sql, $bind);

        return (!empty($result) ? $result : '');
    }

    public function getAutoExtractionValue($stateId)
    {
        $sql = "SELECT auto_extraction FROM $this->table WHERE state_id = :stateId";
        $bind = ['stateId' => $stateId];
        $result = $this->fetchOne($sql, $bind);

        return (!empty($result)) ? $result : 0;
    }

    public function insertOrUpdateSetting($stateId, $autoExtractionvalue, Array $workTypeValues)
    {
        $select = $this->getSelect();
        $columns = [
            'count' => $this->getCount('state_id')
        ];
        $select->columns($columns);

        $select->where('state_id = :stateId');
        $bind = ['stateId' => $stateId];
        $count = $this->fetchOne($select, $bind);

        if($count == 0) {
            $this->insert([
                'state_id' => $stateId,
                'auto_extraction' => $autoExtractionvalue,
                'work_type_id_list' => implode(',', $workTypeValues)
            ]);
        } else {
            $this->update(
                ['auto_extraction' => $autoExtractionvalue, 'work_type_id_list' => implode(',', $workTypeValues)], ['state_id' => $stateId]
            );
        }
        
        
    }
}
