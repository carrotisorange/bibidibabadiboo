<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

class IsitTicketLogAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'isit_ticket_log';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    public function add($ticketId, $type, $request, $response)
    {
        return $this->insert([
            'isit_ticket_id' => $ticketId,
            'isit_ticket_log_type_id' => $type,
            'request' => $request,
            'response' => $response
        ]);
    }
}
