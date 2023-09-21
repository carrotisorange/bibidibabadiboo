<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db\Mbs;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Log\Logger;
use Exception;
use InvalidArgumentException;

use Base\Adapter\Db\DbAbstract;

class AgencyContributorySourceAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'agency_contributory_source';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(
        Adapter $adapter,
        Logger $logger)
    {
        parent::__construct($adapter, $this->table);
        $this->logger = $logger;
    }

    /**
     * Selects all agency contributory source entries from mbs based on last sync dates
     * 
     * @param string $lastSyncedMbsDateAdded latest datetime a new entry in the table was added
     * @param string $lastSyncedMbsDateChanged latest datetime an entry in the table was updated
     * @return array $mbsAgencyContribSources list of agency contributory source entries for syncing to ecrash, 
     * else empty array; on exception of failure
     */
    public function fetchAgencyContributorySources($lastSyncedMbsDateAdded, $lastSyncedMbsDateChanged) 
    {
        try {
            $sql = "SELECT 
                    acs.agency_contributory_source_id mbsAgencyContributorySourceId,
                    acs.agency_id mbsAgencyId,
                    acs.source mbsSource,
                    acs.effective_date mbsEffectiveDate,
                    acs.expiration_date mbsExpirationDate,
                    acs.date_added mbsDateAdded,
                    acs.date_changed mbsDateChanged,
                    acs.IsDeleted mbsIsDeleted,
                    acs.termination_date mbsTerminationDate,
                    acs.resale_allowed mbsResaleAllowed,
                    acs.auto_renew mbsAutoRenew,
                    acs.allow_sale_of_component_data mbsAllowSaleOfComponentData,
                    acs.allow_extract_of_vehicle_data mbsAllowExtractOfVehicleData,
                    acs.note mbsNote
                FROM agency_contributory_source acs
                    JOIN agency a ON (acs.agency_id = a.agency_id)
                WHERE acs.date_added > :dateAdded
                    OR acs.date_changed > :dateChanged
                ORDER BY acs.agency_id";
                
            $bind = [
                'dateAdded' => $lastSyncedMbsDateAdded,
                'dateChanged' => $lastSyncedMbsDateChanged
            ];

            return $this->fetchAll($sql, $bind);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        } // @codeCoverageIgnoreEnd
    }
}
