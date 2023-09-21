<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

class IsitTicketTypeAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'isit_ticket_type';
    
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
            ->columns(['isitTicketTypeId' => 'isit_ticket_type_id'])
            ->where('name = :name');
        
        $bind = ['name' => $type];
        $ticketType = $this->fetchRow($select, $bind);
        
        return (empty($ticketType['isitTicketTypeId'])) ? false : $ticketType['isitTicketTypeId'];
    }
}
