<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Exception;
use InvalidArgumentException;

use Base\Adapter\Db\AgencyAdapter;
use Base\Adapter\Db\ReadOnly\AgencyAdapter as ReadOnlyAgencyAdapter;

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
     * @var Base\Adapter\Db\AgencyAdapter
     */
    protected $adapterAgency;
    
    /**
     * @var Base\Adapter\Db\ReadOnly\AgencyAdapter
     */
    protected $adapterReadOnlyAgency;

    public function __construct(
        Array $config,
        Logger $logger,
        AgencyAdapter $adapterAgency,
        ReadOnlyAgencyAdapter $adapterReadOnlyAgency)
    {
        $this->config   = $config;
        $this->logger   = $logger;
        $this->adapterAgency   = $adapterAgency;
        $this->adapterReadOnlyAgency = $adapterReadOnlyAgency;
    }
    
    /**
     * Select all active agencies
     * 
     * @return array all active agencies, else return empty array; on exception of failure
     */
    public function fetchActive()
    {
        try {
            return $this->adapterAgency->fetchActive();
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        }
    }
    
    /**
     * Select all active agencies under a given state
     * 
     * @param int $stateId
     * @return array all active agencies under specified state, else return empty array; on exception of failure
     */
    public function fetchActiveByState($stateId)
    {
        try {
            if (empty($stateId)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; state id is empty';
                throw new InvalidArgumentException($errMsg);
            }
            
            return $this->adapterAgency->fetchActiveByState((int) $stateId);
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        }
    }
    
    /**
     * Get array of agency id => agency names by state id ordered by name
     * @param int $stateId
     * @return array 
     */
    public function fetchActiveAgencyIdNamePairs($stateId)
    {
        try {
            if (empty($stateId)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; state id is empty';
                throw new InvalidArgumentException($errMsg);
            }
            
            return $this->adapterAgency->fetchActiveAgencyIdNamePairs((int) $stateId);
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        }
    }
    
    /**
     * Gets all agencies in a state
     *
     * @param int $stateId
     * @return array
    */
    public function getAllByState($stateId = null)
    {
        try {
            if (empty($stateId)) {
                return [];
            } else {
                $stateId = (int) $stateId;
            }
            
            return $this->adapterAgency->fetchAllByState($stateId);
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        }
    }
    
    public function getAgenciesWithReports()
    {
        return $this->adapterReadOnlyAgency->fetchAgenciesWithReports();
    }
    
    public function fetchActiveByFormId($formId)
    {
        try {
            if (empty($formId)) {
                throw new InvalidArgumentException('form id is empty');
            }
            
            return $this->adapterAgency->fetchActiveByFormId($formId);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at ' . $e->getFile() . ' on line ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        }// @codeCoverageIgnoreStart
    }
    
    /**
     * Fetch a single row for one agency by agency id.
     * @param int $agencyId
     * @return array
     */
    public function getAgencyByAgencyId($agencyId)
    {
        return $this->adapterAgency->getAgencyByAgencyId($agencyId);
    }
    
    /**
     * Passthru function for getting latest mbs agency activity dates
     * 
     * @return array latest mbs agency activity dates
     */
    public function getLatestMbsAgencySyncDates()
    {
        try {
            return $this->adapterAgency->fetchLatestMbsAgencySyncDates();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] '
                . 'while getting latest mbs agency activity dates';
            $this->logger->log(Logger::ERR, $errMsg);

            return [];
        }// @codeCoverageIgnoreEnd
    }

    /**
     * Passthru function to get the agency information using the mbs agency id equivalent
     * 
     * @param int $mbsAgencyId the mbs agency id equivalent
     * @return array agency object, else empty array; on exception of failure
     */
    public function getAgencyByMbsAgencyId($mbsAgencyId)
    {
        try {
            if (!empty($mbsAgencyId)) {
                return $this->adapterAgency->fetchAgencyByMbsAgencyId($mbsAgencyId);
            } else {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; mbs agency id is empty';
                throw new InvalidArgumentException($errMsg);
            }
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] '
                . 'while getting agency by mbs agency id';
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        }
    }

    /**
     * Creates or updates an eCrash Agency
     * @param object $mbsAgency
     * @return boolean true on insert or create success, false on failure or invalid primary key of mbsAgencyId
     */
    public function createOrUpdateAgency($mbsAgency, $crashLogicSuccess = true)
    {
        return $this->adapterAgency->createOrUpdateAgency($mbsAgency, $crashLogicSuccess);
    }
}
