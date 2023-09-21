<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Log\Logger;

class ReportEntryQueueHistoryAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'report_entry_queue_history';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(
        Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
        $this->adapter = $adapter;
    }

    /**
     * Copies a row from the Queue and adds audit information for removal date and reason.
     *
     * @param integer $reportId
     * @param string $removalReason
     */
    public function copyFromQueue($reportId, $removalReason)
    {
        $sql = "
            INSERT INTO report_entry_queue_history
            (
                date_queued,
                date_removed,
                report_id,
                work_type_id,
                priority,
                form_id,
                agency_id,
                entry_stage_id,
                entry_stage_process_group_id,
                user_id_assigned_to,
                removal_reason
            )
            SELECT
                req.date_queued,
                NOW() AS date_removed,
                req.report_id,
                req.work_type_id,
                req.priority,
                req.form_id,
                req.agency_id,
                req.entry_stage_id,
                req.entry_stage_process_group_id,
                req.user_id_assigned_to,
                :removal_reason AS removal_reason
            FROM report_entry_queue AS req
            WHERE req.report_id = :report_id
            LIMIT 1
        ";
        $bind = ['removal_reason' => $removalReason, 'report_id' => $reportId];
        $statement = $this->adapter->createStatement($sql, $bind);
        $statement->execute();
    }
    
}