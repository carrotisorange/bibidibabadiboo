<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Exception;

use Base\Adapter\Db\StateAdapter;
use Base\Adapter\Db\ReadOnly\StateAdapter as ReadOnlyStateAdapter;

class StateService extends BaseService
{
    /**
     * @var Array
     */
    protected $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Adapter\Db\StateAdapter
     */
    protected $adapterState;
    
    /**
     * @var Base\Adapter\Db\ReadOnlyStateAdapter
     */
    protected $adapterReadOnlyState;

    public function __construct(
        Array $config,
        Logger $logger,
        StateAdapter $adapterState,
        ReadOnlyStateAdapter $adapterReadOnlyState)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterState = $adapterState;
        $this->adapterReadOnlyState = $adapterReadOnlyState;
    }

    public function getStates()
    {
        try {
            return $this->adapterState->fetchStates();
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return( [] );
        }
    }
    
    public function getAllStates() 
    {
        try {
            return $this->adapterState->fetchAllStates();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at ' . $e->getFile() . ' on line ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        }// @codeCoverageIgnoreStart
    }
    
    public function fetchStateIdNamePairs($stateId = null)
    {
        try {
            return $this->adapterState->fetchStateIdNamePairs($stateId);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at ' . $e->getFile() . ' on line ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        }// @codeCoverageIgnoreStart
    }
    
    public function getStatesWithReports()
    {
        return $this->adapterReadOnlyState->fetchStatesWithReports();
    }

    /**
     * Select all active states
     * 
     * @return array all states, else return empty array; on exception of failure
     */
    public function fetchAlltoArray()
    {
        try {
            return $this->adapterState->fetchAlltoArray();
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        }
    }

    /**
     * Select all states with its stete level configuration
     * 
     * @return array all states, else return empty array; on exception of failure
     */
    public function getStateConfigurationList()
    {
        try {
            return $this->adapterState->fetchStateConfigurationList();
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        }
    }

    public function getStateIdAbbrPairs()
    {
        return $this->adapterState->fetchStateIdAbbrPairs();
    }

    public function getStateAbbrIdPairs()
    {
        return array_flip($this->getStateIdAbbrPairs());
    }

    /**
     * Select all autoextraction enabled states
     * 
     * @return array all autoextraction states, else return empty array; on exception of failure
     */
    public function getAutoExtractionEnabledStates()
    {
        try {
            return $this->adapterState->fetchAutoExtractionEnabledStates();
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return( [] );
        }
    }

    public function getStateAbbrById($stateId = null)
    {
        try {
            return $this->adapterState->fetchStateAbbrById($stateId);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at ' . $e->getFile() . ' on line ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        }// @codeCoverageIgnoreStart
    }
}
