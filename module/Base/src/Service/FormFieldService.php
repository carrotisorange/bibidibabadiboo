<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;

use Base\Adapter\Db\FormFieldAdapter;

class FormFieldService extends BaseService
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
     * @var Base\Adapter\Db\FormAdapter
     */
    protected $adapterForm;
    
    public function __construct(
        Array $config,
        Logger $logger,
        FormFieldAdapter $formFieldAdapter)
    {
        $this->config   = $config;
        $this->logger   = $logger;
        $this->formFieldAdapter   = $formFieldAdapter;
    }
    
    public function getCodeDescriptionPairFieldsByFormSystemId($formSystemId) {
        return $this->formFieldAdapter->getCodeDescriptionPairFieldsByFormSystemId($formSystemId);
    }
}
