<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */
namespace Base\Service\Mbs;

use Zend\Log\Logger;
use Zend\Db\Sql\Select;
use Exception;
use InvalidArgumentException;

use Base\Adapter\Db\Mbs\AgencyAdapter;
use Base\Service\BaseService;

class AgencyService extends BaseService
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
     * @var Base\Adapter\Db\Mbs\AgencyAdapter
     */
    protected $adapterAgency;
    
    public function __construct(
        Array $config,
        Logger $logger,
        AgencyAdapter $adapterAgency)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterAgency = $adapterAgency;
    }

    /**
     * @param string $lastSyncedMbsDateAdded
     * @param string $lastSyncedMbsDateChanged
     * @return array 
     */
    public function getAgencies($lastSyncedMbsDateAdded, $lastSyncedMbsDateChanged) 
    {
        return $this->adapterAgency->getAgencies($lastSyncedMbsDateAdded, $lastSyncedMbsDateChanged);
    }
    
}
