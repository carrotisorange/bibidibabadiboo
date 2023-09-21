<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */
namespace Base\Service;

use Zend\Log\Logger;
use Zend\Db\Sql\Select;

use Base\Adapter\Db\ReadOnly\VinStatusAdapter as ReadOnlyVinStatusAdapter;
use Base\Service\EntryStageService;

class VinStatusService extends BaseService
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
     * @var Base\Adapter\Db\ReadOnlyVinStatusAdapter
     */
    protected $adapterReadOnlyVinStatus;
    
    public function __construct(
        Array $config,
        Logger $logger,
        ReadOnlyVinStatusAdapter $adapterReadOnlyVinStatus)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterReadOnlyVinStatus = $adapterReadOnlyVinStatus;
    }

    public function getVinStatusCount($keyingVendorId, $dateStart = null, $dateEnd = null)
    {
        $summary = $this->adapterReadOnlyVinStatus->fetchVinStatusCount($keyingVendorId, $dateStart, $dateEnd);
        $return = [];
        foreach ($summary as $row) {
            /**
             * If an 'E' is saved from anything other than the invalid vin queue
             * it should come up as an 'H', which indicates that someone sent it
             * to the invalid vin queue.
             */
            if (strcasecmp($row['status'], 'E') == 0
                && $row['stage'] != EntryStageService::STAGE_INVALID_VIN
                && $row['stage'] != EntryStageService::STAGE_ALL) {
                $row['status'] = 'H';
            }

            if (!isset($return[$row['status']])) {
                $return[$row['status']] = 0;
            }

            $return[$row['status']] += $row['count'];
        }

        return $return;
    }
    
    /**
     * Returns vin status counts by operator in a format good for the metric report
     * 
     * @param int|string $keyingVendorId keying vendor id
     * @param string $dateStart - strtotime compatible string
     * @param string $dateEnd - strtotime compatible string
     * @param string $nameFirst
     * @param string $nameLast
     * @return array
     */
    public function getVinStatusCountByOperator(
        $keyingVendorId,
        $dateStart = null,
        $dateEnd = null,
        $nameFirst = null,
        $nameLast = null)
    {
        $result = $this->adapterReadOnlyVinStatus->fetchVinStatusCountByOperator(
            $keyingVendorId,
            $dateStart,
            $dateEnd,
            $nameFirst,
            $nameLast
        );
        $return = [];
        foreach ($result as $row) {
            if (!isset($return[$row['username']])) {
                $return[$row['username']] = [
                    'userName' => $row['username'],
                    'nameFirst' => $row['name_first'],
                    'nameLast' => $row['name_last'],
                    'H' => 0,
                    'V' => 0,
                    'E' => 0,
                    'total' => 0,
                    'vendorName' => $row['vendorName']
                ];
            }
            // @see $this->getVinStatusCount for why this is done
            if (strcasecmp($row['status'], 'E') == 0
                && $row['stage'] != EntryStageService::STAGE_INVALID_VIN
                && $row['stage'] != EntryStageService::STAGE_ALL) {
                $row['status'] = 'H';
            }
            if (isset($return[$row['username']][$row['status']])) {
                $return[$row['username']][$row['status']] += $row['count'];
                $return[$row['username']]['total'] += $row['count'];
            }
        }
        
        return $return;
    }
}
