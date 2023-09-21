<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Log\Logger;
use Exception;
use InvalidArgumentException;

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
        Array $config,
        Adapter $adapter,
        Logger $logger)
    {
        parent::__construct($adapter, $this->table);
        $this->logger = $logger;
        $this->config = $config;
    }

        /**
     * Fetch an agency contributory source record based on given mbs agency contributory source id
     * 
     * @param int $mbsAgencyContributorySourceId mbs agency contrib source id 
     * @param int $agencyId ecrash_v3 agency id
     * @return array agency contributory source row selected, else empty array; on exception of failure
     */
    public function fetchByMbsAgencyContribSourceId($mbsAgencyContributorySourceId, $agencyId) {
        try {
            if (empty($mbsAgencyContributorySourceId)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; mbs agency contrib source id is empty';
                $this->logger->log(Logger::ERR, $errMsg);
                throw new InvalidArgumentException($errMsg);
            }
            if (empty($agencyId)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; agency id is empty';
                throw new InvalidArgumentException($errMsg);
            }
            $sql = "SELECT * 
                    FROM $this->table
                    WHERE mbs_agency_contributory_source_id = :mbsAgencyContributorySourceId 
                    AND agency_id = :agencyId 
                    ORDER BY effective_date DESC";
            $bind = ['mbsAgencyContributorySourceId' => $mbsAgencyContributorySourceId, 'agencyId' => $agencyId];
            return $this->fetchRow($sql, $bind);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        } // @codeCoverageIgnoreEnd
    }
    
    /**
     * Fetch an agency contributory source record based on given agency id and source
     * 
     * @param int $agencyId agency id 
     * @param char $source agency contributory source from mbs
     * @return array agency contributory source row selected, else empty array; on exception of failure
     */
    public function fetchByAgencyIdAndSource($agencyId, $source) {
        try {
            if (empty($agencyId)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; agency id is empty';
                throw new InvalidArgumentException($errMsg);
            }
            if (empty($source)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; source is empty';
                throw new InvalidArgumentException($errMsg);
            }
            $sql = "SELECT * 
                    FROM $this->table
                    WHERE agency_id = :agencyId AND source = :contribSource 
                    ORDER BY effective_date DESC";
            $bind = ['agencyId' => $agencyId, 'contribSource' => $source];
            return $this->fetchRow($sql, $bind);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        } // @codeCoverageIgnoreEnd
    }

    /**
     * Creates a new agency contributory source record
     * 
     * @param int $agencyId agency id associated with the new agency contributory source
     * @param object $mbsAgencyContribSource agency contributory source data from mbs
     * @return int primary key id of the new agency contributory source, else false; on exception of failure
     */
    public function createContribSource($agencyId, $mbsAgencyContribSource) {
        try {
            if (empty($agencyId)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; agency id is empty';
                throw new InvalidArgumentException($errMsg);
            }
            if (empty($mbsAgencyContribSource)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; mbs agency contributory source object is empty';
                throw new InvalidArgumentException($errMsg);
            }
            $mbsExpirationDate = $mbsAgencyContribSource->mbsExpirationDate;
            $mbsSource = $mbsAgencyContribSource->mbsSource;
            $mbsAgencyId = $mbsAgencyContribSource->mbsAgencyId;
            $gracePeriod = $this->fetchGracePeriod($mbsExpirationDate, $mbsAgencyId, $mbsSource); //always calculate the grace period
            $data = [
                'agency_id' => $agencyId,
                'source' => $mbsSource,
                'effective_date' => $mbsAgencyContribSource->mbsEffectiveDate,
                'expiration_date' => $mbsExpirationDate,
                'grace_period' => $gracePeriod,
                'is_deleted' => $mbsAgencyContribSource->mbsIsDeleted,
                'termination_date' => $mbsAgencyContribSource->mbsTerminationDate,
                'resale_allowed' => $mbsAgencyContribSource->mbsResaleAllowed,
                'auto_renew' => $mbsAgencyContribSource->mbsAutoRenew,
                'allow_sale_of_component_data' => $mbsAgencyContribSource->mbsAllowSaleOfComponentData,
                'allow_extract_of_vehicle_data' => $mbsAgencyContribSource->mbsAllowExtractOfVehicleData,
                'note' => $mbsAgencyContribSource->mbsNote,
                'mbs_agency_contributory_source_id' => $mbsAgencyContribSource->mbsAgencyContributorySourceId,
                'mbs_agency_id' => $mbsAgencyId,
                'mbs_date_added' => $mbsAgencyContribSource->mbsDateAdded,
                'mbs_date_changed' => $mbsAgencyContribSource->mbsDateChanged,
                'date_created' => new Expression('NOW()')
            ];
            return $this->insert($data);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return false;
        } // @codeCoverageIgnoreEnd
    }

    /**
     * Updates an agency contributory source record
     * 
     * @param int $agencyContribSourceId agency contributory source id (primary key)
     * @param object $mbsAgencyContribSource agency contributory source data from mbs
     * @return int number of rows affected by the update, else false; on exception of failure
     */
    public function updateContribSource($agencyContribSourceId, $mbsAgencyContribSource) {
        try {
            if (empty($agencyContribSourceId)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; agency contributory source id is empty';
                throw new InvalidArgumentException($errMsg);
            }
            if (empty($mbsAgencyContribSource)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; mbs agency contributory source object is empty';
                throw new InvalidArgumentException($errMsg);
            }
            $mbsAgencyId = $mbsAgencyContribSource->mbsAgencyId;
            $mbsSource = $mbsAgencyContribSource->mbsSource;
            $mbsExpirationDate = $mbsAgencyContribSource->mbsExpirationDate;
            $gracePeriod = $this->fetchGracePeriod($mbsExpirationDate, $mbsAgencyId, $mbsSource);

            $data = [
                'source' => $mbsSource,
                'effective_date' => $mbsAgencyContribSource->mbsEffectiveDate,
                'expiration_date' => $mbsExpirationDate,
                'grace_period' => $gracePeriod,
                'is_deleted' => $mbsAgencyContribSource->mbsIsDeleted,
                'termination_date' => $mbsAgencyContribSource->mbsTerminationDate,
                'resale_allowed' => $mbsAgencyContribSource->mbsResaleAllowed,
                'auto_renew' => $mbsAgencyContribSource->mbsAutoRenew,
                'allow_sale_of_component_data' => $mbsAgencyContribSource->mbsAllowSaleOfComponentData,
                'allow_extract_of_vehicle_data' => $mbsAgencyContribSource->mbsAllowExtractOfVehicleData,
                'note' => $mbsAgencyContribSource->mbsNote,
                'mbs_agency_contributory_source_id' => $mbsAgencyContribSource->mbsAgencyContributorySourceId,
                'mbs_agency_id' => $mbsAgencyId,
                'mbs_date_added' => $mbsAgencyContribSource->mbsDateAdded,
                'mbs_date_changed' => $mbsAgencyContribSource->mbsDateChanged
            ];
            $where = ['agency_contributory_source_id' => $agencyContribSourceId];
            return $this->update($data, $where);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return false;
        } // @codeCoverageIgnoreEnd
    }

    /**
     * Selects all the active agency contributory sources based on given agency id and vendor code
     * 
     * @param int $agencyId agency id to be used in selecting the appropriate sources
     * @param string $vendorCode vendor code to be used in validating the agency sources
     * @return string the active agency contributory source, else false; on exception of failure
     */
    public function fetchActiveAgencySource($agencyId, $vendorCode) {
        try {
            if (empty($agencyId)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; agency id is empty';
                throw new InvalidArgumentException($errMsg);
            }
            if (empty($vendorCode)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; vendor code is empty';
                throw new InvalidArgumentException($errMsg);
            }
            $sql = "SELECT 
                        UPPER(a.source) 
                    FROM $this->table a 
                    INNER JOIN vendor_agency_source v
                        ON LOWER(a.source) = LOWER(v.source_id) 
                    WHERE 
                        a.effective_date <= CURDATE() 
                        AND ((a.expiration_date IS NULL) OR (a.expiration_date >= CURDATE()) OR (a.grace_period >= CURDATE())) 
                        AND a.agency_id = :agencyId 
                        AND a.is_deleted = 0
                        AND v.vendor_name IS NOT NULL 
                        AND LOWER(v.vendor_name) = :vendorCode 
                        ORDER BY a.effective_date DESC LIMIT 1";
            $bind = ['agencyId' => $agencyId, 'vendorCode' => strtolower($vendorCode)];
            return $this->fetchOne($sql, $bind);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return false;
        } // @codeCoverageIgnoreEnd
    }
    
    /**
     * Selects an agency's current active contributory source based on given agency id
     * 
     * @param int $agencyId agency id to be used in selecting the appropriate sources
     * @return array the mbs agency contrib source id and the active agency contributory source, 
     * else false; on exception of failure
     */
    public function fetchAgencyActiveSource($agencyId) {
        try {
            if (empty($agencyId) || ! is_numeric( $agencyId )) {
                $errMsg = "Expected agencyId to be numeric and not empty.";
                throw new InvalidArgumentException($errMsg);
            }
            
            /*
             * Cast int fields in query.
             */
            $agencyId = is_scalar($agencyId) ? (int) $agencyId : $agencyId;
            
            $sql = "SELECT 
                        a.mbs_agency_contributory_source_id, UPPER(a.source) 
                    FROM $this->table a 
                    WHERE 
                        a.effective_date <= CURDATE() 
                        AND ((a.expiration_date IS NULL) OR (a.expiration_date >= CURDATE()) OR (a.grace_period >= CURDATE())) 
                        AND a.agency_id = :agencyId 
                        AND a.is_deleted = 0 
                        AND LOWER(a.source) IN (SELECT LOWER(v.source_id) FROM vendor_agency_source v) 
                        ORDER BY a.effective_date DESC";
            $bind = ['agencyId' => $agencyId];
            return $this->fetchRow($sql, $bind);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return false;
        } // @codeCoverageIgnoreEnd
    }

    /**
     * Select all agencies with incidents created yesterday having expired or within the grace period agency 
     * contributory sources
     * 
     * @return array list of agencies with incidents having expired or within the grace period agency contributory 
     * sources, else empty array; on exception of failure
     */
    public function fetchExpiredContribSourcesInIncidents() {
        try {
            $sql = "SELECT 
                        ac.agency_id, 
                        ac.mbs_agency_id,
                        ac.source, 
                        a.name,
                        s.name_abbr,
                        ac.expiration_date, 
                        ac.grace_period, 
                        COUNT(i.Incident_ID) as expiredIncidents 
                    FROM $this->table ac  
                    INNER JOIN agency a 
                        ON ac.agency_id = a.agency_id
                    INNER JOIN incident i 
                        ON ac.mbs_agency_id = i.Agency_ID 
                        AND i.contrib_source IS NOT NULL 
                        AND LOWER(ac.source) = LOWER(i.contrib_source) 
                        AND DATE(i.Creation_Date) > ac.expiration_date
                        AND DATE(i.Creation_Date) <= ac.grace_period
                        AND DATE(i.Creation_Date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) 
                    INNER JOIN state AS s
                        ON s.state_id = a.state_id
                    WHERE 
                        ac.is_deleted = 0
                        AND ac.expiration_date < CURDATE()
                        AND ac.grace_period >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)
                    GROUP BY ac.agency_id";
            return $this->fetchAll($sql);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        } // @codeCoverageIgnoreEnd
    }

    /**
     * Select all agencies with new/latest agency contributory sources that started yesterday or earlier and get number 
     * of incidents created yesterday having the new/latest agency contributory sources
     * 
     * @return array list of agencies with new/latest agency contributory sources that started yesterday or earlier, 
     * else empty array; on exception of failure
     */
    public function fetchAgenciesWithNewContribSources() {
        try {
            $sql = "SELECT 
                        ac.agency_id, 
                        ac.mbs_agency_id,
                        ac.source, 
                        a.name, 
                        a.agency_ori,
                        s.name_abbr,
                        ac.effective_date, 
                        ac.expiration_date,
                        COUNT(i.Incident_ID) as newSourceIncidents
                    FROM $this->table ac 
                    INNER JOIN agency a 
                        ON ac.agency_id = a.agency_id 
                    INNER JOIN state AS s
                        ON s.state_id = a.state_id
                    LEFT JOIN incident i 
                        ON ac.mbs_agency_id = i.Agency_ID 
                        AND i.contrib_source IS NOT NULL
                        AND LOWER(ac.source) = LOWER(i.contrib_source) 
                        AND DATE(i.Creation_Date) >= ac.effective_date
                        AND (CASE WHEN ac.grace_period IS NOT NULL 
                            THEN (DATE(i.Creation_Date) <= ac.grace_period) 
                            ELSE true END)
                    WHERE 
                        ac.is_deleted = 0 
                        AND a.company_status = 'A' 
                        AND (a.agency_ori IS NOT NULL 
                             AND TRIM(a.agency_ori) != '' 
                             AND SUBSTR(a.agency_ori, -6)!= '999999'
                            ) 
                        AND a.name NOT REGEXP '[[:<:]]test(s|ing|er)*([[:>:]]|[[:digit:]]+)' 
                        AND ac.effective_date < CURDATE()
                        AND ((ac.grace_period IS NULL) OR 
                            (ac.grace_period IS NOT NULL AND ac.grace_period >= DATE_SUB(CURDATE(), INTERVAL 1 DAY))) 
                    GROUP BY ac.agency_id";
            return $this->fetchAll($sql);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        } // @codeCoverageIgnoreEnd
    }

    /**
     * Get the latest mbs create and updates dates
     * 
     * @return array latest mbs activity dates, else empty array; on exception of failure
     */
    public function fetchLatestMbsContribSourceSyncDates() {
        try {
            $sql = "SELECT 
                        IFNULL(MAX(mbs_date_added), '0000-00-00 00:00:00') latestMbsDateAdded,
                        IFNULL(MAX(mbs_date_changed), '0000-00-00 00:00:00') latestMbsDateChanged
                    FROM $this->table";
            return $this->fetchRow($sql);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        } // @codeCoverageIgnoreEnd
    }
    
    /**
     * Calculate the grace period based on the given expiration date
     * 
     * @param string $expirationDate agency contrib source expiration date
     * @param int $mbsAgencyId the mbs agency id
     * @param string $mbsSource the source from MBS
     * @return string grace period date if expiration date is not null, else null
     */
    public function fetchGracePeriod($expirationDate, $mbsAgencyId, $mbsSource) {
        $m = __METHOD__.'(): ';
        try {
            if (empty($expirationDate)) { //no expiry date so no grace period
                return null;
            }
            
            $intervalDays = $this->config['agencycontribsource']['graceperiod'];
            $expirationDateTime = new \DateTime($expirationDate);
            $gracePeriodDerived = $expirationDateTime->add(new \DateInterval('P' . $intervalDays . 'D'));
            $gracePeriodDate = $gracePeriodDerived->format('Y-m-d');
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $gracePeriodDate)) {
                $logMsg = $m . " - MBS Agency Id: $mbsAgencyId, Source: $mbsSource - Invalid grace period date: '" . $gracePeriodDate . "'. "
                        . "Using expiration date: '". $expirationDate . "' as grace period date";
                $this->logger->log(Logger::DEBUG, $logMsg); 
                $gracePeriodDate = $expirationDate;
            }
            return $gracePeriodDate;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return null;
        }   // @codeCoverageIgnoreEnd
    }
    
    /**
     * Fetches all incidents affected by an update or delete of an agency contributory source
     * 
     * @param array $agencyContribSourceRow the current agency contributory source data
     * @return array the agency name and number of affected incidents, else null; on exception of failure
     */
    public function fetchIncidentsByContribSource($agencyContribSourceRow) {
        try {
            $agencyId = $agencyContribSourceRow['mbs_agency_id'];
            $contribSource = $agencyContribSourceRow['source'];
            $effectiveDate = $agencyContribSourceRow['effective_date'];
            $gracePeriod = $agencyContribSourceRow['grace_period'];
            
            /*
             * Cast int fields in query.
             */
            $agencyId = is_scalar($agencyId) ? (int) $agencyId : $agencyId;
            
            $sql = "SELECT 
                        a.name as agencyName,
                        COUNT(i.Incident_ID) as numAffected
                    FROM 
                        incident i 
                    LEFT JOIN agency a 
                        ON i.agency_id=a.mbsi_agency_id
                    WHERE
                        i.contrib_source IS NOT NULL
                        AND i.agency_id = :agencyId 
                        AND LOWER(i.contrib_source) = :contribSource 
                        AND DATE(i.Creation_Date) >= :effectiveDate";
            
            $bind = ['agencyId' => $agencyId, 'contribSource' => $contribSource, 'effectiveDate' => $effectiveDate];
            
            if (!empty($gracePeriod)) {
            
                $sql .=  " AND DATE(i.Creation_Date) <= :gracePeriod";
                $bind['gracePeriod'] = $gracePeriod;
            }
            
            return $this->fetchAll($sql, $bind);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return null;
        } // @codeCoverageIgnoreEnd
    }
}
