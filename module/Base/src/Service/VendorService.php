<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Zend\Db\Sql\Select;

use Base\Adapter\Db\VendorAdapter;

class VendorService extends BaseService
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
     * @var Base\Adapter\Db\VendorAdapter
     */
    protected $adapterAgency;
    
    public function __construct(
        Array $config,
        Logger $logger,
        VendorAdapter $adapterVendor)
    {
        $this->config   = $config;
        $this->logger   = $logger;
        $this->adapterVendor   = $adapterVendor;
    }
    
    /**
     * Fetch all active Vendor Codes and IDs.
     */
    public function fetchActiveVendorPairs()
    {
        return $this->adapterVendor->fetchActiveVendorPairs();
    }
}
