<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db\Extract;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

use Base\Adapter\Db\DbAbstract;

class EnumerationMapAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'enumeration_map';
    
    protected $primary = 'enumeration_map_id';
    
    protected $hasTransformColumns = false;

    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

}
