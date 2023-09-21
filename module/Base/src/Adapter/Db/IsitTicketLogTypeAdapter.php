<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

class IsitTicketLogTypeAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'isit_ticket_log_type';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    public function getIdByType($type)
    {
        $select = $this->getSelect()
            ->columns(['isitTicketLogTypeId' => 'isit_ticket_log_type_id'], false)
            ->where('name = :name');
        
        $bind = ['name' => $type];
        $ticketLogType = $this->fetchRow($select, $bind);
        
        return (empty($ticketLogType['isitTicketLogTypeId'])) ? false : $ticketLogType['isitTicketLogTypeId'];
    }
}
