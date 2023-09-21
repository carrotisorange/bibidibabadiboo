<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

class EntryStageProcessAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'entry_stage_process';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(
        Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    /**
     * Will return the highest pass a entry process group CAN do
     * @param int $reportId
     * @return int
     */
    public function getMaxPotentialPassNumber($entryStageProcessGroupId)
    {
        $select = $this->getSelect();
        $select->columns([
            'passNumber' => $this->getMax("pass_number")
        ]);
        $select->where('entry_stage_process_group_id = :entry_stage_process_group_id');
        $bind = ["entry_stage_process_group_id" => $entryStageProcessGroupId];

        return $this->fetchOne($select, $bind);
    }

    /**
     * Fetches the entryStageId associated with a particular group and passNumber, if exists.
     * @param integer $entryStageProcessGroupId
     * @param integer $passNumber
     * @return integer|null
     */
    public function getEntryStageId($entryStageProcessGroupId, $passNumber)
    {
        $select = $this->getSelect();
        $select->columns(["entry_stage_id"]);
        $select->where([
            "entry_stage_process_group_id = :entry_stage_process_group_id",
            "pass_number = :pass_number"
        ]);
        $bind = [
            "entry_stage_process_group_id" => $entryStageProcessGroupId,
            "pass_number" => $passNumber
        ];
        
        return $this->fetchOne($select, $bind);
    }
}
