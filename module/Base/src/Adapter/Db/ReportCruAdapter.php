<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Log\Logger;

class ReportCruAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'report_cru';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    /**
     * Gets basic information about a report cru
     *
     * @param integer $reportId
     * @return array
     */
    public function getCruData($reportId)
    {
        $select = $this->getSelect();
        $select->where('report_id = :report_id');
        $bind = ['report_id' => $reportId];

        return $this->fetchRow($select, $bind);
    }
}
