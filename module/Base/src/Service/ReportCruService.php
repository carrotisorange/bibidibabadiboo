<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;

use Base\Adapter\Db\ReportCruAdapter;

class ReportCruService extends BaseService
{
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;

    /**
     * @var Base\Adapter\Db\ReportCruAdapter
     */
    protected $adapterReport;
    
    public function __construct(
        Array $config,
        Logger $logger,
        ReportCruAdapter $adapterReportCru)
    {
        $this->config   = $config;
        $this->logger   = $logger;
        $this->adapterReportCru   = $adapterReportCru;
    }

    /**
     * Gets cru result
     *
     * @param integer $reportId
     * @return array
     */
    public function getCruData($reportId)
    {
        return $this->adapterReportCru->getCruData($reportId);
    }
}
