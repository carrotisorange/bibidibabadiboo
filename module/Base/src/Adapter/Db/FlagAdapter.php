<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Log\Logger;

class FlagAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'flag';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    /**
     * Gets basic information about a report flag
     *
     * @param string $flagName
     * @return array
     */
    public function getFlag($flagName)
    {
        $select = $this->getSelect();
        $select->where('name = :name');
        $bind = ['name' => $flagName];

        return $this->fetchRow($select, $bind);
    }
}
