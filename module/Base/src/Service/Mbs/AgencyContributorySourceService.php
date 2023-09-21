<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */
namespace Base\Service\Mbs;

use Zend\Log\Logger;
use Zend\Db\Sql\Select;
use Exception;
use InvalidArgumentException;

use Base\Service\BaseService;
use Base\Adapter\Db\Mbs\AgencyContributorySourceAdapter as MbsAgencyContributorySourceAdapter;

class AgencyContributorySourceService extends BaseService
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
     * @var Base\Adapter\Db\Mbs\AgencyContributorySourceAdapter
     */
    protected $adaptergencyContributorySource;
    
    public function __construct(
        Array $config,
        Logger $logger,
        MbsAgencyContributorySourceAdapter $adaptergencyContributorySource)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adaptergencyContributorySource = $adaptergencyContributorySource;
    }

    /**
     * Passthru in getting agency contributory source entries from mbs based on last sync dates
     * 
     * @param string $lastSyncedMbsDateAdded latest datetime a new entry in the table was added
     * @param string $lastSyncedMbsDateChanged latest datetime an entry in the table was updated
     * @return array list of agency contributory source entries for syncing to ecrash, 
     * else empty array; on exception of failure
     */
    public function getAgencyContributorySources($lastSyncedMbsDateAdded, $lastSyncedMbsDateChanged) {
        try {
            if (!empty($lastSyncedMbsDateAdded)) {
                return $this->adaptergencyContributorySource->fetchAgencyContributorySources($lastSyncedMbsDateAdded, $lastSyncedMbsDateChanged);
            } else {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
                $this->logger->log(Logger::ERR, $errMsg);
            }
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        } // @codeCoverageIgnoreEnd
    }
}
