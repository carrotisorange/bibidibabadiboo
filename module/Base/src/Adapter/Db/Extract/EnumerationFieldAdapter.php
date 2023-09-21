<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db\Extract;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

use Base\Adapter\Db\DbAbstract;

class EnumerationFieldAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'enumeration_field';
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    public function getAllEnumArray(){
        $select = $this->getSelect();
        $select->from(['EF' =>'enumeration_field'], ['EF.field_name']);
        
        return $this->fetchAll($select);
    }

}
