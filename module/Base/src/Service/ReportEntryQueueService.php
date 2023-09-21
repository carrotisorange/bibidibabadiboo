<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Zend\Db\Sql\Select;

use Base\Adapter\Db\ReportEntryQueueAdapter;
use Base\Adapter\Db\ReportEntryQueueHistoryAdapter;
use Base\Adapter\Db\ReportEntryAdapter;
use Base\Adapter\Db\EntryStageProcessAdapter;

class ReportEntryQueueService extends BaseService
{
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
     * @var Array
     */
    protected $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Adapter\Db\ReportEntryQueueAdapter
     */
    protected $adapterReportEntryQueue;

    /**
     * @var Base\Adapter\Db\ReportEntryQueueHistoryAdapter
     */
    protected $adapterReportEntryQueueHistory;
    
    /**
     * @var Base\Adapter\Db\ReportEntryAdapter
     */
    protected $adapterReportEntry;
    
    /**
     * @var Base\Adapter\Db\EntryStageProcessAdapter
     */
    protected $adapterEntryStageProcess;
    
    public function __construct(
        Array $config,
        Logger $logger,
        ReportEntryQueueAdapter $adapterReportEntryQueue,
        ReportEntryQueueHistoryAdapter $adapterReportEntryQueueHistory,
        ReportEntryAdapter $adapterReportEntry,
        EntryStageProcessAdapter $adapterEntryStageProcess)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterReportEntryQueue = $adapterReportEntryQueue;
        $this->adapterReportEntryQueueHistory = $adapterReportEntryQueueHistory;
        $this->adapterReportEntry = $adapterReportEntry;
        $this->adapterEntryStageProcess = $adapterEntryStageProcess;
    }
    
    public function getReportDetails($reportId)
    {
        return $this->adapterReportEntryQueue->getReportDetails($reportId);
    }
    
    public function checkReportAvailabilityForAssign($reportId)
    {
        return $this->adapterReportEntryQueue->checkReportAvailabilityForAssign($reportId);
    }
    
    public function getReservedReport($userId)
    {
        return $this->adapterReportEntryQueue->fetchReservedReport($userId);
    }
    
    /**
     * checks the user Entry stage permission for Report assignment.
     *
     * @param integer $userId
     * @param interger $reportId
     * @return array
    */
    public function reviewUserEntryStageForReport($userId, $reportId)
    {
        return $this->adapterReportEntryQueue->checkUserEntryStageForReport($userId, $reportId);
    }
    
    public function reserveAssignedReport($reportId, $userId)
    {
        return $this->adapterReportEntryQueue->reserveAssignedReport($reportId, $userId);
    }      
    
    /**
     * Insert the Filtered report to user.
     *
     * @param int $userId
     * @param int $reportId
     * @param int $formId
     * @return boolean
     */
    public function insertAssignedReport($userId, $reportId)
    {
        $qryResult = 0;
        if (!empty($userId) && !empty($reportId)) {
            try {
                $qryResult = $this->adapterReportEntryQueue->insertAssignedReport($userId, $reportId);
                if (!$qryResult) {
                    $this->logger->log->err('User Report :' . $reportId . ' failed to insert');
                }
            } catch (Exception $e) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L' . $e->getLine();
                $errorMessage = $origin . ' ' . $e->getMessage();
                $this->logger->log->err($errorMessage);
            }
        }
        
        return $qryResult;
    }
    
    /**
     * Adds (or replaces) a report into the entry queue with the appropriate data.
     *
     * @param integer $reportId
     */
    public function add($reportId)
    {
        $this->adapterReportEntryQueue->add($reportId);
    }

    /**
     * Removes a report from the entry queue using the specified removal reason.
     *
     * @param <type> $reportId
     * @param <type> $removalReason
     */
    public function remove($reportId, $removalReason)
    {
        $this->adapterReportEntryQueueHistory->copyFromQueue($reportId, $removalReason);
        $this->adapterReportEntryQueue->remove($reportId);
    }
    
    /**
     *
     * @param integer $userId
     * @param integer $workTypeId
     * @param <type> $orderBy
     * @param integer $keyingVendorId
     * @return integer
     */
    public function pull($userId, $workTypeId, $keyingVendorId, $rekey = 0)
    {
        $reportId = $this->adapterReportEntryQueue->pull($userId, $workTypeId, $keyingVendorId, $rekey);

        if (!empty($reportId)) {
            $this->adapterReportEntryQueueHistory->copyFromQueue($reportId, self::REMOVAL_REASON_PULLED);
        }

        return $reportId;
    }
    
    public function moveToNextStage($reportId)
    {
        //add log that report is now on the moving next stage
        $this->logger->log(Logger::DEBUG, "Moving report $reportId into the next stage in report_entry_queue");
        $queuedData = $this->getQueuedData($reportId);
        $passNumber = $this->adapterReportEntry->getMaxCompletedPass($reportId);
        $nextPassNumber = $passNumber + 1;
        $nextEntryStageId = $this->adapterEntryStageProcess->getEntryStageId($queuedData['entryStageProcessGroupId'], $nextPassNumber);
        
        if (!empty($nextEntryStageId)) {
            //add log for next stage details of the report
            $this->logger->log(Logger::DEBUG, "Moving report $reportId into the next stage with entry stage id: $nextEntryStageId under pass $nextPassNumber");
            $this->adapterReportEntryQueueHistory->copyFromQueue($reportId, self::REMOVAL_REASON_MOVED);
            if($passNumber==1) {
               $this->adapterReportEntry->revertInProgress(null, $reportId);
            }
            $this->add($reportId);

            return true;
        } else {
            $this->remove($reportId, self::REMOVAL_REASON_REMOVED);
            //remove record from user report table after pass 2 completion
            if ($passNumber == 2) {
                $this->removeUserReportRecord($reportId);
				$this->adapterReportEntryQueue->removeReportTATStatus($reportId);
            }

            return false;
        }
    }
    
    /**
     * Gets all the information about a queued report.
     *
     * @param integer $reportId
     * @return array
     */
    public function getQueuedData($reportId)
    {
        return $this->adapterReportEntryQueue->fetchRowByReportId($reportId);
    }
    
    /**
     * Removes a user from any assignments.
     *
     * @param integer $userId
     */
    public function unassignUser($userId)
    {
        $this->adapterReportEntryQueue->unassignUser($userId);
    }

    public function insertForRekey($reportId, $workTypeId, $priority, $newFormId, $agencyId = null)
    {
        return $this->adapterReportEntryQueue->insertForRekey($reportId, $workTypeId, $priority, $newFormId, $agencyId);
    }
    
    /**
     * deletes the report entry created available for user if they completed or removed the keying of report
     *
     * @param integer $reportId
     * @param interger $userId
     * @return boolean
     */
    public function removeUserReportRecord($reportId, $userId = null)
    {
        return $this->adapterReportEntryQueue->removeUserReportRecord($reportId, $userId);
    }

    public function unAssignReport($userId, $reportId)
    {
        return $this->adapterReportEntryQueue->unAssignReport($userId, $reportId);
    }

    /**
     * Populates the Queue based on distinct available
     * @param integer $queueLimit
     */
    public function populate($queueLimit)
    {
        foreach ($this->adapterReportEntryQueue->fetchEntryQueuingStatistics() as $row) {
            if ($row['countQueuable'] == 0 || $row['countQueued'] >= $queueLimit) {
                continue;
            }
            // Increase queue growth if a critically low count is found.
            // This means the initial queue count will be low, but that's okay.
            // This is primarily a stop-gap measure in case a queue is getting worked abnormally fast.
            if ($row['countQueued'] == 0) {
                $queueInsertCount = $queueLimit * 3;
            } elseif ($row['countQueued'] < $queueLimit / 10) {
                $queueInsertCount = $queueLimit * 2;
            } else {
                $queueInsertCount = $queueLimit - $row['countQueued'];
            }

            $this->adapterReportEntryQueue->queueNewReports(
                $row['workTypeId'],
                $row['formId'],
                $row['agencyId'],
                $row['entryStageId'],
                $row['orderDirection'],
                $queueInsertCount
            );
        }
    }

    /**
     * Get the date range assigned user reports 
     *
     * @param int $userId
     * @return boolean
    */
    public function getdateRangeAssignedReports($userId)
    {
        return $this->adapterReportEntryQueue->fetchDateRangeAssignedReports($userId);
    }
}
