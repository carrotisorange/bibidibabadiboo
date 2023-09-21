<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Exception;

use Base\Adapter\Db\IsitTicketAdapter;

class IsitTicketService extends BaseService
{
    /**
     * @var Array
     */
    protected $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Adapter\Db\IsitTicketAdapter
     */
    protected $adapterIsitTicket;
    
    public function __construct(
        Array $config,
        Logger $logger,
        IsitTicketAdapter $adapterIsitTicket)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterIsitTicket = $adapterIsitTicket;
    }
    
    /**
     * To create an ISIT ticket
     *
     * @param int $userId   User id of the keying app
     * @param int $externalIsitTicketId
     * @param int $statusId ISIT ticket status id
     * @param int $typeId   ISIT ticket type id
     * @return int  Return the id of isit ticket
     */
    public function add($userId, $externalIsitTicketId, $statusId, $typeId)
    {
        return $this->adapterIsitTicket->add($userId, $externalIsitTicketId, $statusId, $typeId);
    }
    
    /**
     * Returns isit_ticket.isit_ticket_id by the native isit ticket number
     * @param int $externalId
     * @return mixed int on success, null on failure
     */
    public function getInternalIdFromExternalId($externalId)
    {
        return $this->adapterIsitTicket->getInternalIdFromExternalId($externalId);
    }

    public function closeTicket($externalIsitTicketId, $statusId)
    {
        return $this->adapterIsitTicket->closeTicket($externalIsitTicketId, $statusId);
    }

    public function getTicketByExternalId($externalTicketId)
    {
        return $this->adapterIsitTicket->getTicketByExternalId($externalTicketId);
    }
}
