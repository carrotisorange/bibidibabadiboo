<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Log\Logger;
use Zend\Db\Sql\Select;

class FormCodeListPairMapAdapter extends DbAbstract
{
    /**
     * Table name
     * @var string Table name
     */
    protected $table = 'form_code_list_pair_map';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(
        Adapter $adapter,
        Logger $logger)
    {
        parent::__construct($adapter, $this->table);

        $this->logger = $logger;
    }
 
  public function insertAssoc($listId, Array $codePairIds)
	{
		foreach ($codePairIds as $codePairId) {
			$this->insert([
				'form_code_list_id' => $listId,
				'form_code_pair_id' => $codePairId
            ]);
		}
	}
    
    

}
