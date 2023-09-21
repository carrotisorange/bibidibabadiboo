<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

use Base\Service\IsitTicketService;

class IsitTicketAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'isit_ticket';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    public function add($userId, $externalIsitTicketId, $statusId, $typeId)
    {
        return $this->insert([
            'date_created' => $this->getNowExpr(),
            'user_id' => $userId,
            'external_isit_ticket_id' => $externalIsitTicketId,
            'isit_ticket_type_id' => $typeId,
            'isit_ticket_status_id' => $statusId
        ]);
    }

    /**
     * Returns isit_ticket.isit_ticket_id by the native isit ticket number 
     * @param int $externalId
     * @return mixed int on success, null on failure
     */
    public function getInternalIdFromExternalId($externalId)
    {
        $isitTicketId = null;
        
        try {
            $integerExternalId = (int) $externalId; //externalId is an object

            $sql = "
                SELECT isit_ticket_id
                FROM isit_ticket
                WHERE external_isit_ticket_id = :ticket
                LIMIT 1
            ";

            $bind = [
                'ticket' => $integerExternalId
            ];

            $resp = $this->fetchRow($sql, $bind);
            $isitTicketId = empty($resp['isit_ticket_id']) ? null : $resp['isit_ticket_id'];
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
        }
        
        return $isitTicketId;
    }

    public function closeTicket($externalIsitTicketId, $statusId)
    {
        $this->update([
            'isit_ticket_status_id' => $statusId
        ], [
            'external_isit_ticket_id' => $externalIsitTicketId,
        ]);

        return true;
    }

    public function getTicketByExternalId($externalTicketId)
    {
        $sql = "
            SELECT
                it.isit_ticket_id AS isitTicketId,
                it.date_created AS dateCreated,
                it.date_updated AS dateUpdated,
                it.user_id AS userId,
                it.external_isit_ticket_id AS externalIsitTicketId,
                it.isit_ticket_type_id AS typeId,
                it.isit_ticket_status_id AS statusId,
                its.name AS statusName,
                itt.name AS typeName
            FROM isit_ticket AS it
            INNER JOIN isit_ticket_status AS its USING(isit_ticket_status_id)
            INNER JOIN isit_ticket_type AS itt USING(isit_ticket_type_id)
            WHERE it.external_isit_ticket_id = :external_isit_ticket_id
        ";
        $bind = ['external_isit_ticket_id' => $externalTicketId];

        return $this->fetchRow($sql, $bind);
    }
}
