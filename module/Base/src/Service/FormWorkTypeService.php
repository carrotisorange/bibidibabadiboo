<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Zend\Db\Sql\Select;
use Exception;

use Base\Adapter\Db\FormWorkTypeAdapter;
use Base\Adapter\Db\WorkTypeAdapter;
use Base\Service\EcrashUtilsArrayService;

class FormWorkTypeService extends BaseService
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
     * @var Base\Adapter\Db\FormWorkTypeAdapter
     */
    protected $adapterFormWorkType;
    
    /**
     * @var Base\Adapter\Db\WorkTypeAdapter
     */
    protected $adapterWorkType;
    
    /**
     * @var Base\Service\EcrashUtilsArrayService
     */
    protected $serviceEcrashUtilsArray;

    public function __construct(
        Array $config,
        Logger $logger,
        FormWorkTypeAdapter $adapterFormWorkType,
        WorkTypeAdapter $adapterWorkType,
        EcrashUtilsArrayService $serviceEcrashUtilsArray)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterFormWorkType = $adapterFormWorkType;
        $this->adapterWorkType = $adapterWorkType;
        $this->serviceEcrashUtilsArray = $serviceEcrashUtilsArray;
    }
    
    /**
     * Fetch an array of all work types for the given form id.
     * @param int|numeric $formId
     * @return array
     */
    public function fetchWorkTypesByFormId($formId)
    {
        try {
            if (!is_numeric($formId) || empty($formId)) {
                throw new Exception('Form id must be numeric and not empty.');
            }
            
            return $this->adapterFormWorkType->fetchWorkTypesByFormId($formId);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at ' . $e->getFile() . ' on line ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        }// @codeCoverageIgnoreStart
    }
    
    /**
     * Gets all work types and all of their information.
     *
     * @return array
     */
    public function fetchWorkTypes()
    {
        try {
            $workTypes = $this->adapterWorkType->fetchAllWorkTypes();

            if (!is_array($workTypes) || empty($workTypes)) {
                // @codeCoverageIgnoreStart
                throw new Exception('Error retrieving work types array or work_type table is empty.');
                // @codeCoverageIgnoreEnd
            }
            return $workTypes;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '() L' . $e->getLine();
            $this->logger->log(Logger::ERR, $origin . $e->getMessage());
            return [];
        }// @codeCoverageIgnoreEnd
    }

    /**
     * Add a new record for the form_work_type table for form id and work type id.
     * @param int|numeric $formId
     * @param int|numeric $workTypeId
     * @return bool
     */
    public function insertFormWorkType($formId, $workTypeId)
    {
        try {
            if (!is_numeric($formId) || empty($formId)) {
                throw new Exception('Form id must be numeric.');
            }
            if (!is_numeric($workTypeId) || empty($workTypeId)) {
                throw new Exception('Work type id must be numeric.');
            }
            $workTypes = $this->adapterWorkType->fetchAllWorkTypes();
            $workTypeIds = explode(',', $this->serviceEcrashUtilsArray->implodeAlt(',', $workTypes, 'work_type_id'));
            if (!in_array($workTypeId, $workTypeIds)) {
                throw new Exception('Invalid work type id.');
            }
            
            return $this->adapterFormWorkType->insertFormWorkType($formId, $workTypeId);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);

            return false;
        }// @codeCoverageIgnoreEnd
    }
}
