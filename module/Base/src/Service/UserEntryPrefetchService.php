<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Base\Adapter\Db\UserEntryPrefetchAdapter;

class UserEntryPrefetchService extends BaseService
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
     * @var Base\Adapter\Db\UserEntryPrefetchAdapter
     */
    protected $adapterReport;
    
    public function __construct(
        Array $config,
        Logger $logger,
        UserEntryPrefetchAdapter $adapterUserEntryPrefetch)
    {
        $this->config   = $config;
        $this->logger   = $logger;
        $this->adapterUserEntryPrefetch   = $adapterUserEntryPrefetch;
    }

    public function removeUserReports($userId)
    {
        return $this->adapterUserEntryPrefetch->removeUserReports($userId);
    }
    
    public function fetchReportIdByUserId($userId)
    {
        return $this->adapterUserEntryPrefetch->fetchReportIdByUserId($userId);
    }
    
    public function addUserEntry($userId, $reportId)
    {
        return $this->adapterUserEntryPrefetch->addUserEntry($userId, $reportId);
    }
    
}
