<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Exception;

use Base\Adapter\Db\FormFieldAdapter;
use Base\Service\DataTransformer\Universal;
use Base\Service\DataTransformer\Universal\Handler\ToCommon;
use Base\Service\DataTransformer\Universal\Handler\FromCommon;

class DataTransformerService extends BaseService
{    
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    public function __construct(
        Array $config,
        Logger $logger,
        FormFieldAdapter $adapterFormField)
    {
        $this->config   = $config;
        $this->logger   = $logger;
        $this->adapterFormField   = $adapterFormField;
    }

    public function getDataTransformerUniversal()
    {
        try {
            return new Universal(
                new ToCommon($this->adapterFormField),
                new FromCommon($this->adapterFormField)
            );
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '() L' . $e->getLine();
            $this->logger->log(Logger::ERR, $origin . $e->getMessage());
            return null;
        }// @codeCoverageIgnoreEnd
    }
}
