<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Exception;

use Base\Adapter\Db\FormCodeGroupConfigurationAdapter;

class FormCodeGroupConfigurationService extends BaseService
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
     * @var Base\Adapter\Db\FormCodeGroupConfigurationAdapter
     */
    protected $adapterFCGC;
    
    public function __construct(
        Array $config,
        Logger $logger,
        FormCodeGroupConfigurationAdapter $adapterFCGC)
    {
        $this->config   = $config;
        $this->logger   = $logger;
        $this->adapterFCGC   = $adapterFCGC;
    }
    
    /**
     * Derive the form code configuration record(s) from db. Default is single row based on formTemplateId with 
     * other optional params in order or precendence.
     * @param int|string $formTemplateId
     * @param [int|string] $stateId (null)
     * @param [int|string] $agencyId (null)
     * @return array or false on exception.
     */
    public function fetchFormCodeConfiguration($formTemplateId, $stateId = null, $agencyId = null)
    {
        try {
            return $this->adapterFCGC->fetchFormCodeConfiguration($formTemplateId, $stateId, $agencyId);
            //@codeCoverageIgnoreStart
        } catch ( Exception $e ) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return false;
        }//@codeCoverageIgnoreEnd
    }
}
