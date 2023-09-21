<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class FormNoteAdapter extends DbAbstract
{
    /**
     * Table name
     * @var string Table name
     */
    protected $table = 'form_note';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    public function insertNote($formId, $userId, $note)
    {
        $now = date('Y-m-d H:i:s');

        return $this->insert('form_note', [
            'date_created' => $now,
            'date_updated' => $now,
            'form_id' => $formId,
            'user_id' => $userId,
            'note' => $note
        ]);
    }

    public function fetchNotes($formId)
    {
        $select = $this->getSelect();
        $select->where('form_id = :form_id');
        $bind = ['form_id' => $formId];
        
        return $this->fetchAll($select, $bind);
    }

}
