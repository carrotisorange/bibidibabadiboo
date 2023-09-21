<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Zend\Db\Sql\Select;
use Base\Adapter\Db\UserAccuracyInvalidAdapter;

class UserAccuracyInvalidService extends BaseService
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
     * @var Base\Adapter\Db\UserAccuracyInvalidAdapter
     */
    protected $adapterUserAccuracyInvalid;
    
    public function __construct(
        Array $config,
        Logger $logger,
        UserAccuracyInvalidAdapter $adapterUserAccuracyInvalid)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterUserAccuracyInvalid = $adapterUserAccuracyInvalid;
    }

    /**
     * Gets the count of invalid keyed fields by criteria
     *
     * @param array|int $userIds
     * @param string $fromDate
     * @param string $toDate
     * @param string $stateId
     * @param int $formId
     * @param int $formAgencyIdd
     * @return int
     * @return int
     */
    public function getCountInvalid(
        Array $userIds,
        $fromDate = null,
        $toDate = null,
        $stateId = null,
        $formId = null,
        $formAgencyId = null)
    {
        return $this->adapterUserAccuracyInvalid->getCountInvalid($userIds, $fromDate, $toDate, $stateId, $formId, $formAgencyId);
    }
    
    /**
     * Gets all of the invalid data records for a user accuracy id
     *
     * @param int $userAccuracyId
     * @return array
     */
    function getInvalid($userAccuracyId)
    {
        return $this->adapterUserAccuracyInvalid->getInvalid($userAccuracyId);
    }
    
}
