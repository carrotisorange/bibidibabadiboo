<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

class IsitTicketStatusAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'isit_ticket_status';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    public function getIdByStatus($status)
    {
        $select = $this->getSelect()
            ->columns(['isitTicketStatusId' => 'isit_ticket_status_id'], false)
            ->where('name = :name');
        
        $bind = ['name' => $status];
        $ticketStatus = $this->fetchRow($select, $bind);
        
        return (empty($ticketStatus['isitTicketStatusId'])) ? false : $ticketStatus['isitTicketStatusId'];
    }
}
