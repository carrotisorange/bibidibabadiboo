<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Log\Logger;

class ReportFlagHistoryAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'report_flag_history';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(
        Adapter $adapter,
        Logger $logger)
    {
        parent::__construct($adapter, $this->table);
        $this->logger = $logger;
        $this->adapter = $adapter;
    }

    public function copyFlagToHistory($reportFlagId, $userIdRemovedBy)
    {
        $sql = "
            INSERT INTO report_flag_history
            (date_created, report_id, flag_id, user_id_flagged_by, user_id_removed_by)
            SELECT
                date_created,
                report_id,
                flag_id,
                user_id_flagged_by,
                                ?
            FROM report_flag
            WHERE report_flag_id = ?
        ";
        $bind = [$userIdRemovedBy, $reportFlagId];
        $qry = $this->adapter->createStatement($sql, $bind)->execute();
        $this->logger->log(Logger::DEBUG, "Report Flag History insert. Bind = " . print_r($bind, true) . ", "
            . "rowCount: " . $qry->getAffectedRows());
        
        return $qry->getAffectedRows();
    }
}