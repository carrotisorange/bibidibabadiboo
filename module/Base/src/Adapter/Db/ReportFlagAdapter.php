<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Log\Logger;

class ReportFlagAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'report_flag';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
        $this->adapter = $adapter;
    }

    /**
     * Gets basic information about a report flag
     *
     * @param integer $reportId
     * @param integer $flagId
     * @return array
     */
    public function getReportFlag($reportId, $flagId)
    {
        $select = $this->getSelect();
        $select->where(['report_id = :report_id', 'flag_id = :flag_id']);
        $bind = ['report_id' => $reportId, 'flag_id' => $flagId];

        return $this->fetchRow($select, $bind);
    }

    public function insertIgnore($reportId, $flagId, $userId)
    {
        $sql = "
            INSERT IGNORE INTO `report_flag`
            (`report_id`, `flag_id`, `user_id_flagged_by`) VALUES
            (:report_id, :flag_id, :user_id)
        ";
        $bind = ['report_id' => $reportId, 'flag_id' => $flagId, 'user_id' => $userId];
        $qry = $this->adapter->createStatement($sql, $bind)->execute();

        return $qry->getAffectedRows();
    }
    
    public function getCountWithFlag($reportId, $flagName)
    {
        $select = $this->getSelect();
        $select->columns(['count' => $this->getCount()], false);
        $select->from(['rf' => 'report_flag'])
            ->join(['f' => 'flag'], 'rf.flag_id = f.flag_id')
            ->where(['rf.report_id = :report_id', 'f.name = :name']);
        $bind = [
            'report_id' => $reportId,
            'name' => $flagName
        ];
        
        return $this->fetchOne($select, $bind);
    }
}
