<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;

use Zend\Db\Sql\Select;

use Base\Adapter\Db\FormCodeListPairMapAdapter;

class FormCodeListPairMapService extends BaseService
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
     * @var Base\Adapter\Db\FormListPairMapAdapter
     */
    protected $adapterFormCodeListPairMap;
    
    public function __construct(
        Array $config,
        Logger $logger,
        FormCodeListPairMapAdapter $adapterFormCodeListPairMap)
    {
        $this->config   = $config;
        $this->logger   = $logger;
        $this->adapterFormCodeListPairMap   = $adapterFormCodeListPairMap;
    } 
  
	public function insertAssoc($listId, array $codePairIds)
	{
		return $this->adapterFormCodeListPairMap->insertAssoc($listId, $codePairIds);
	}
}
