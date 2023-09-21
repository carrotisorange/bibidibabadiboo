<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Db\Sql\Select;
use Zend\Log\Logger;

use Base\Adapter\Db\ReportQueueAdapter;
use Base\Adapter\Db\ReportQueueHistoryAdapter;

class ReportQueueService extends BaseService
{
    const QUEUE_BAD_IMAGE = 'Bad Image';
    const QUEUE_DISCARDED = 'Discarded';

    /**
     * Specifies that the removal reason for a queue entry is pulled to be worked.
     */
    const REMOVAL_REASON_PULLED = 'pulled';

    /**
     * A generic catch-all for any permanent removal action.
     */
    const REMOVAL_REASON_REMOVED = 'removed';

    /**
     * Specifies that the removal reason for a queue entry is moved to another queue state.
     */
    const REMOVAL_REASON_MOVED = 'moved';

    /**
     * @var Model_DbTable_ReportQueue
     */
    protected $_reportQueueDbTable;
    
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Adapter\Db\ReportQueueAdapter
     */
    protected $adapterReportQueue;

    /**
     * @var Base\Adapter\Db\ReportQueueHistoryAdapter
     */
    protected $adapterReportQueueHistory;
    
    public function __construct(
        Array $config,
        Logger $logger,
        ReportQueueAdapter $adapterReportQueue,
        ReportQueueHistoryAdapter $adapterReportQueueHistory)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterReportQueue = $adapterReportQueue;
        $this->adapterReportQueueHistory = $adapterReportQueueHistory;
    }

    /**
     *
     * @param string $queueName
     * @param integer $reportId
     * @param string $priority
     */
    public function add($queueName, $reportId, $priority, $userId = null)
    {
        $reportQueue = $this->adapterReportQueue->insert([
            'report_id' => $reportId,
            'queue_name' => $queueName,
            'priority' => $priority,
            'user_id_queued_by' => $userId,
        ]);
    }

    /**
     *
     * @param string $queueName
     * @param integer $reportId
     * @param string $removalReason
     */
    public function remove($queueName, $reportId, $removalReason)
    {
        $this->adapterReportQueueHistory->copyReportQueueToHistory($queueName, $reportId, $removalReason);
        $this->adapterReportQueue->remove($queueName, $reportId);
    }

    /**
     *
     * @param string $queueName
     * @param integer $userId
     * @param integer $keyingVendorId
     * @return integer|boolean reportId
     */
    public function pull($queueName, $userId, $keyingVendorId)
    {
        $reportId = $this->adapterReportQueue->pull($queueName, $userId, $keyingVendorId);
        if (!empty($reportId)) {
            $this->adapterReportQueueHistory->copyReportQueueToHistory($queueName, $reportId, self::REMOVAL_REASON_PULLED);
        }

        return $reportId;
    }
    
    public function unassignUser($userId)
    {
        $this->adapterReportQueue->unassignUser($userId);
    }

    public function assign($queueName, $reportId, $userId)
    {
        return $this->adapterReportQueue->assign($queueName, $reportId, $userId);
    }

    public function selectbyQueue($queueName)
    {
        return $this->adapterReportQueue->selectbyQueue($queueName);
    }

    public function selectByStatus($status)
    {
        return $this->adapterReportQueue->selectByStatus($status);
    }

    public function addInputCriteria(Select $select, $criteria)
    {
        return $this->adapterReportQueue->addInputCriteria($select, $criteria);
    }

    public function recycle()
    {
        return $this->adapterReportQueue->recycle();
    }
}
