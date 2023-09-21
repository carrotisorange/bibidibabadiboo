<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use InvalidArgumentException;

use Base\Adapter\Db\FlagAdapter;
use Base\Adapter\Db\ReportFlagAdapter;
use Base\Adapter\Db\ReportFlagHistoryAdapter;

class ReportFlagService extends BaseService
{
    const FLAG_COMMAND_CENTER_EDITED = 'command center edited';
    const FLAG_FORM_MISMATCH = 'form mismatch';
    const FLAG_REORDERED = 'reordered';
    const FLAG_TRANSLATED = 'translated';
    const FLAG_UPDATED = 'updated';
    
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;

    /**
     * @var Base\Adapter\Db\FlagAdapter
     */
    protected $adapterFlag;

    /**
     * @var Base\Adapter\Db\ReportFlagAdapter
     */
    protected $adapterReportFlag;

    /**
     * @var Base\Adapter\Db\ReportFlagHistoryAdapter
     */
    protected $adapterReportFlagHistory;
    
    public function __construct(
        Array $config,
        Logger $logger,
        FlagAdapter $adapterFlag,
        ReportFlagAdapter $adapterReportFlag,
        ReportFlagHistoryAdapter $adapterReportFlagHistory)
    {
        $this->config   = $config;
        $this->logger   = $logger;
        $this->adapterFlag   = $adapterFlag;
        $this->adapterReportFlag   = $adapterReportFlag;
        $this->adapterReportFlagHistory   = $adapterReportFlagHistory;
    }

    public function add($reportId, $flagName, $userId)
    {
        $flag = $this->fetchFlag($flagName);
        $this->adapterReportFlag->insertIgnore($reportId, $flag['flag_id'], $userId);
    }

    public function remove($reportId, $flagName, $userId)
    {
        $flag = $this->fetchFlag($flagName);
        $reportFlag = $this->adapterReportFlag->getReportFlag($reportId, $flag['flag_id']);

        if (!empty($reportFlag)) {
            $copiedFlagHistory = $this->adapterReportFlagHistory->copyFlagToHistory($reportFlag['report_flag_id'], $userId);
            
            if ($copiedFlagHistory == 0) {//put a log if insert fails and no rows are affected
                $this->logger->log(Logger::DEBUG, "No rows affected from reportFlagHistory insert "
                . "with report_id: $reportId, flag_id: " . $flag['flag_id'] . ", user_id: $userId");
            }

            $this->adapterReportFlag->delete([
                'report_id' => $reportId,
                'flag_id' => $flag['flag_id'],
            ]);
        }
    }
    
    protected function fetchFlag($flagName)
    {
        $flag = $this->adapterFlag->getFlag($flagName);

        if (empty($flag)) {
            throw new InvalidArgumentException('Invalid report flag given.');
        }

        return $flag;
    }

    public function getCountWithFlag($reportId, $flagName)
    {
        $flagCount = $this->adapterReportFlag->getCountWithFlag($reportId, $flagName);

        return $flagCount;
    }
}
