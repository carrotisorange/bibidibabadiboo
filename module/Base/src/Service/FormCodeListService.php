<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;

use Zend\Db\Sql\Select;

use Base\Adapter\Db\FormCodeListAdapter;

class FormCodeListService extends BaseService
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
     * @var Base\Adapter\Db\FormCodeListAdapter
     */
    protected $adapterForm;
    
    public function __construct(
        Array $config,
        Logger $logger,
        FormCodeListAdapter $adapterFormCodeList)
    {
        $this->config   = $config;
        $this->logger   = $logger;
        $this->adapterFormCodeList   = $adapterFormCodeList;
    } 
      

    public function getCodePairs($listId)
	{
		return $this->adapterFormCodeList->getCodePairs($listId);
	}
	
	public function insertList($name)
	{
		return $this->adapterFormCodeList->insertList($name);
	}
	
	public function updateList($updListId, $updListName)
	{
		return $this->adapterFormCodeList->updateList($updListId, $updListName);
	}
}
