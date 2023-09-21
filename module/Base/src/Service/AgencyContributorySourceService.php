<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Zend\Db\Sql\Select;
use Exception;
use InvalidArgumentException;

use Base\Service\AgencyService;
use Base\Adapter\Db\AgencyContributorySourceAdapter;

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
     * @var Base\Adapter\Db\AgencyContributorySourceAdapter
     */
    protected $adapterAgencyContributorySource;
    /**
     * @var Base\Service\AgencyService
     */
    protected $serviceAgency;

    protected $affectedContribIncidents;
    
    public function __construct(
        Array $config,
        Logger $logger,
        AgencyContributorySourceAdapter $adapterAgencyContributorySource,
        AgencyService $serviceAgency)
    {
        $this->config   = $config;
        $this->logger   = $logger;
        $this->adapterAgencyContributorySource   = $adapterAgencyContributorySource;
        $this->serviceAgency = $serviceAgency;
        $this->affectedContribIncidents = [];
    }
        /**
     * Creates or updates an eCrash Agency Contributory Source entry based from MBS Agency Contributory Source data
     * 
     * @param object $mbsAgencyContribSource agency contributory source data from mbs
     * @return bool if insert/update of agency contributory source in ecrash was successful or not, 
     * else return false; on exception of failure
     */
    public function createOrUpdateAgencyContribSource($mbsAgencyContribSource) {
        try {
            $mbsAgencyId = $mbsAgencyContribSource->mbsAgencyId;
            if (empty($mbsAgencyId)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
                $this->logger->log(Logger::ERR, $errMsg);
                throw new InvalidArgumentException($errMsg);
            }
            $pk = null;
            //Get agency based on mbs agency id
            $agency = $this->serviceAgency->getAgencyByMbsAgencyId($mbsAgencyId);
            if (!empty($agency)) {
                $agencyId = $agency['agency_id']; //Get agency id to fetch agency contributory source
                $mbsAgencyContributorySourceId = $mbsAgencyContribSource->mbsAgencyContributorySourceId;
                $agencyContribSourceRow = $this->adapterAgencyContributorySource->fetchByMbsAgencyContribSourceId($mbsAgencyContributorySourceId, $agencyId);
                
                if (!empty($agencyContribSourceRow)) {
                    $affectedIncidents = $this->checkUpdateDeleteContribIncidents($mbsAgencyContribSource, $agencyContribSourceRow);
                    if (!empty($affectedIncidents)) {
                        $this->affectedContribIncidents[] = $affectedIncidents;
                    }
                    $agencyContribSourceId = $agencyContribSourceRow['agency_contributory_source_id']; //primary key
                    $pk = $this->adapterAgencyContributorySource->updateContribSource($agencyContribSourceId, $mbsAgencyContribSource);
                } else {
                    $pk = $this->adapterAgencyContributorySource->createContribSource($agencyId, $mbsAgencyContribSource);
                }
            } else {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; No agency with mbs agency id: ' . $mbsAgencyId;
                $this->logger->log(Logger::ERR, $errMsg);
            }
            return !empty($pk);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] '
                . 'while creating or updating agency contributory source.';
            $this->logger->log(Logger::ERR, $errMsg);
            return false;
        } // @codeCoverageIgnoreEnd
    }

    /**
     * Retrieves the active contributory source based on given agency and vendor code
     * 
     * @param int $agencyId agency id from the report
     * @param string $vendorCode vendor code from the cdi source
     * @return string the active agency contributory source based on agency id and vendor code, 
     * else return false; on exception of failure
     */
    public function getActiveAgencySource($agencyId, $vendorCode) {
        try {
            if (!empty($agencyId) && !empty($vendorCode)) {
                return $this->adapterAgencyContributorySource->fetchActiveAgencySource($agencyId, $vendorCode);
            } else {
                if (empty($agencyId)) {
                    $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                    $errMsg = 'Origin: ' . $origin . '; agency id is empty';
                    $this->logger->log(Logger::ERR, $errMsg);
                    throw new InvalidArgumentException($errMsg);
                }
                if (empty($vendorCode)) {
                    $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                    $errMsg = 'Origin: ' . $origin . '; vendor code is empty';
                    $this->logger->log(Logger::ERR, $errMsg);
                    throw new InvalidArgumentException($errMsg);
                }
            }
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] '
                . 'while getting active agency contributory sources';
            $this->logger->log(Logger::ERR, $errMsg);
            return false;
        } // @codeCoverageIgnoreEnd
    }
    
    /**
     * Retrieves the active contributory source based on given agency id
     * 
     * @param int $agencyId agency id from the report
     * @return string the active agency contributory source based on agency id 
     * else return false; on exception of failure
     */
    public function getAgencyActiveSource($agencyId) {
        try {
            if (empty($agencyId) || ! is_numeric( $agencyId )) {
                $errMsg = "Expected agencyId to be numeric and not empty.";
                throw new InvalidArgumentException($errMsg);
            }
            return $this->adapterAgencyContributorySource->fetchAgencyActiveSource($agencyId);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] '
                . "while getting agency's active contributory source";
            $this->logger->log(Logger::ERR, $errMsg);
            return false;
        } // @codeCoverageIgnoreEnd
    }

    /**
     * Get all agencies having incidents with expired agency contributory sources
     *  
     * @return array list of agencies with incidents having expired agency contributory sources, 
     * else return empty array; on exception of failure
     */
    public function getExpiredContribSourcesInIncidents() {
        try {
            return $this->adapterAgencyContributorySource->fetchExpiredContribSourcesInIncidents();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] '
                . 'while getting agencies with expired active agency source incidents.';
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        } // @codeCoverageIgnoreEnd
    }

    /**
     * Get all agencies with new contributory sources
     * 
     * @return array list of agencies with new contributory sources incidents, 
     * else return empty array; on exception of failure
     */
    public function getAgenciesWithNewContribSources() {
        try {
            return $this->adapterAgencyContributorySource->fetchAgenciesWithNewContribSources();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] '
                . 'while getting agencies with new active agency source incidents.';
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        } // @codeCoverageIgnoreEnd
    }

    /**
     * Get the latest datest on when the agency_contributory_source table was last syncedw with mbs
     * 
     * @return array latest mbs sync dates (create and update dates), else return empty array; on exception of failure
     */
    public function getLatestMbsContribSourceSyncDates() {
        try {
            return $this->adapterAgencyContributorySource->fetchLatestMbsContribSourceSyncDates();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] '
                . 'while getting latest mbs agency contributory source sync dates.';
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        } // @codeCoverageIgnoreEnd
    }
    
    /**
     * Check if an agency contributory source is deleted or updated and whether there are incidents affected
     * 
     * @param obj $mbsAgencyContribSource the updated agency contributory source data from MBS
     * @param array $agencyContribSourceRow the current agency contributory source data in ecrash_v3
     * @return array array of agency contrib source data including number of affected incidents
     */
    public function checkUpdateDeleteContribIncidents($mbsAgencyContribSource, $agencyContribSourceRow) {
        try {
            $currentDelFlag = $agencyContribSourceRow['is_deleted'];
            $newDelFlag = $mbsAgencyContribSource->mbsIsDeleted;

            $mbsSource = $mbsAgencyContribSource->mbsSource;
            $currentSource = strtolower($agencyContribSourceRow['source']);
            $newSource = strtolower($mbsSource);

            $currentEffective = $agencyContribSourceRow['effective_date'];
            $newEffective = $mbsAgencyContribSource->mbsEffectiveDate;

            $currentExpiration = $agencyContribSourceRow['expiration_date'];
            $newExpiration = $mbsAgencyContribSource->mbsExpirationDate;

            if ($currentExpiration != $newExpiration) {
                $gracePeriod = null;
                if (!empty($newExpiration)) {
                    $mbsAgencyId = $mbsAgencyContribSource->mbsAgencyId;
                    $gracePeriod = $this->adapterAgencyContributorySource->fetchGracePeriod($newExpiration, $mbsAgencyId, $mbsSource);
                }
                $mbsAgencyContribSource->mbsGracePeriod = $gracePeriod;
            }

            $isUpdateSource = (($currentSource != $newSource) || ($currentEffective != $newEffective) || 
                                ($currentExpiration != $newExpiration));
            $isDeleteSource = (empty($currentDelFlag) && !empty($newDelFlag));

            $affectedIncidents = [];
            if ($isDeleteSource || $isUpdateSource) { //go further if it is an update on source or delete flag          
                $incidents = $this->adapterAgencyContributorySource->fetchIncidentsByContribSource($agencyContribSourceRow);
                if (!empty($incidents) && $incidents[0]['numAffected'] > 0) {
                    $affectedIncidents['mbsAgencyId'] = $agencyContribSourceRow['mbs_agency_id'];
                    $affectedIncidents['agencyName'] = $incidents[0]['agencyName'];
                    $affectedIncidents['numIncidents'] = $incidents[0]['numAffected'];
                    $affectedIncidents['isUpdateSource'] = $isUpdateSource;
                    $affectedIncidents['isDeleteSource'] = $isDeleteSource;
                    $affectedIncidents['currentData'] = $agencyContribSourceRow;
                    $affectedIncidents['newData'] = (array) $mbsAgencyContribSource;
                }
            }
            return $affectedIncidents;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] '
                . "while checking affected incidents of ageny contrib source update or delete";
            $this->logger->log(Logger::ERR, $errMsg);
            return false;
        } // @codeCoverageIgnoreEnd
    }
    
    /**
     * Return the data of affected incidents
     * 
     * @return array all the data of affected incidents
     */
    public function getAffectedContribIncidents() {
        return $this->affectedContribIncidents;
    }

}
