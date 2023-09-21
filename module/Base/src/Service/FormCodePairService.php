<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;

use Zend\Db\Sql\Select;

use Base\Adapter\Db\FormCodePairAdapter;

class FormCodePairService extends BaseService
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
     * @var Base\Adapter\Db\FormCodePairAdapter
     */
    protected $adapterForm;
    
    public function __construct(
        Array $config,
        Logger $logger,
        FormCodePairAdapter $adapterFormCodePair)
    {
        $this->config   = $config;
        $this->logger   = $logger;
        $this->adapterFormCodePair   = $adapterFormCodePair;
    } 
      

    public function insertPair($code, $value)
	{
		return $this->adapterFormCodePair->insertPair($code, $value);
	}
	
	public function updatePair($codePairId, $code, $value)
	{
		return $this->adapterFormCodePair->updatePair($codePairId, $code, $value);
	}
	
	public function deletePair($codePairId)
	{
		return $this->adapterFormCodePair->deletePair($codePairId);
	}
}
