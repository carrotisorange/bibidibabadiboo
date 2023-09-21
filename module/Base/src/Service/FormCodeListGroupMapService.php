<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;

use Zend\Db\Sql\Select;

use Base\Adapter\Db\FormCodeListGroupMapAdapter;

class FormCodeListGroupMapService extends BaseService
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
     * @var Base\Adapter\Db\FormCodeListGroupMapAdapter
     */
    protected $adapterFormCodeListGroupMap;
    
    public function __construct(
        Array $config,
        Logger $logger,
        FormCodeListGroupMapAdapter $adapterFormCodeListGroupMap)
    {
        $this->config   = $config;
        $this->logger   = $logger;
        $this->adapterFormCodeListGroupMap   = $adapterFormCodeListGroupMap;
    } 
    
    public function insertAssoc($groupId, array $listIds)
	{
		return $this->adapterFormCodeListGroupMap->insertAssoc($groupId, $listIds);
	}
	
	public function getAssocListIds($groupId)
	{
		return $this->adapterFormCodeListGroupMap->getAssocListIds($groupId);
	}
	
	public function getAssocLists($groupId)
	{
		return $this->adapterFormCodeListGroupMap->getAssocLists($groupId);
	}
	
	public function removeAssoc($groupId, $listIds)
	{
		return $this->adapterFormCodeListGroupMap->removeAssoc($groupId, $listIds);
	}

    
}
