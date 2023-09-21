<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;

use Base\Adapter\Db\FormCodeMapAdapter;

class FormCodeMapService extends BaseService
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
     * @var Base\Adapter\Db\FormCodeMapAdapter
     */
    protected $adapterFormCodeMap;
    
    public function __construct(
        Array $config,
        Logger $logger,
        FormCodeMapAdapter $adapterFormCodeMap)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterFormCodeMap = $adapterFormCodeMap;
    }

    public function getAllFormCodePairs($formCodeGroupId, $codeMapName = null)
    {
        return $this->adapterFormCodeMap->getAllFormCodePairs($formCodeGroupId, $codeMapName);
    }
}
