<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db\ReadOnly;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

use Base\Adapter\Db\DbAbstract;

class AgencyAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'agency';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    public function fetchAgenciesWithReports()
    {
        $select = $this->getSelect();
        $select->from(['agn' => 'agency']);
        $select->columns(['agency_id' => $this->getDistinct('agn.agency_id'), 'name' => 'agn.name' ], false);
        $select->join(['rep' => 'report'], 'agn.agency_id = rep.agency_id', []);
        
        return $this->fetchAll($select);
    }
    
}
