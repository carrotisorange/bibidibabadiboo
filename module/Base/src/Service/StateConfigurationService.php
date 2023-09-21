<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Exception;

use Base\Adapter\Db\StateConfigurationAdapter;

class StateConfigurationService extends BaseService
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
     * @var Base\Adapter\Db\StateConfigurationAdapter
     */
    protected $adapterStateConfiguration;

    public function __construct(
        Array $config,
        Logger $logger,
        StateConfigurationAdapter $adapterStateConfiguration)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterStateConfiguration = $adapterStateConfiguration;
    }

    public function isRollOutState($reportStateId, $reportWorkTypeId)
    {
        return $this->adapterStateConfiguration->isRollOutState($reportStateId, $reportWorkTypeId);
    }

    public function getWorkTypeIDPerState($report_state_id)
    {
        return $this->adapterStateConfiguration->getWorkTypeIDPerState($report_state_id);
    }

    public function getAutoExtractionValue($stateId)
    {
        return $this->adapterStateConfiguration->getAutoExtractionValue($stateId);
    }

    public function insertOrUpdateSetting($stateId, $autoExtractionvalue, Array $workTypeValues)
    {
        return $this->adapterStateConfiguration->insertOrUpdateSetting($stateId, $autoExtractionvalue, $workTypeValues);
    }
}
