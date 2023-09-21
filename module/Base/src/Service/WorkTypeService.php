<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Exception;

use Base\Adapter\Db\WorkTypeAdapter;

class WorkTypeService extends BaseService
{
    /**
     * WORK TYPE id for eCrash
     */
    const WORK_TYPE_ECRASH = 1; 

    /**
     * WORK TYPE id for CRU GoForward
     */
    const WORK_TYPE_CGF = 3;
	
	/**
     * @var Array
     */
    protected $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Adapter\Db\WorkTypeAdapter
     */
    protected $adapterWorkType;
    
    public function __construct(
        Array $config,
        Logger $logger,
        WorkTypeAdapter $adapterWorkType)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterWorkType = $adapterWorkType;
    }
    
    /**
     * Fetch an array of all work types.
     * @return array    Work types
     */
    public function getAll()
    {
        try {
            return $this->adapterWorkType->getAll();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at ' . $e->getFile() . ' on line ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        }// @codeCoverageIgnoreStart
    }

    /**
     * Get the work types that are allowed for a specific user id (Returns name_external values).
     * @param type $userId
     * @return array
     */
    public function getAllowedByUser($userId)
    {
        try {
            if (!is_numeric($userId)) {
                // @codeCoverageIgnoreStart
                throw new Exception('User id must be numeric.');
                // @codeCoverageIgnoreEnd
            }
            return $this->adapterWorkType->getAllowedByUser($userId);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at ' . $e->getFile() . ' on line ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        }// @codeCoverageIgnoreStart
    }

    /**
     * Get allowed entry stages
     *
     * @return array    Allowed work types array
     */
    public function getWorkTypeNamePairs()
    {
        return $this->adapterWorkType->getWorkTypeNamePairs();
    }
}
