<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;

class ReportQueueHistoryAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'report_queue_history';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
        $this->adapter = $adapter;
    }

    public function copyReportQueueToHistory($queueName, $reportId, $removalReason)
    {
        $sql = "
            INSERT INTO report_queue_history
            (date_queued, date_removed, report_id, queue_name, priority, user_id_queued_by, removal_reason, user_id_removed_by)
            SELECT
                rq.date_queued,
                NOW() AS date_removed,
                rq.report_id,
                rq.queue_name,
                rq.priority,
                rq.user_id_queued_by,
                :removal_reason AS removal_reason,
                rq.user_id_assigned_to
            FROM report_queue AS rq
            WHERE rq.queue_name = :queue_name
                AND rq.report_id = :report_id
            LIMIT 1
        ";
        $bind = ['removal_reason' => $removalReason, 'queue_name' => $queueName, 'report_id' => $reportId];

        $this->adapter->query($sql, $bind);
    }
}