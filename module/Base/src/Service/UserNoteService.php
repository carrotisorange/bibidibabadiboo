<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Db\Sql\Select;

use Base\Adapter\Db\UserNoteAdapter;

class UserNoteService extends BaseService
{
    /**
     * @var Base\Adapter\Db\UserNoteAdapter
     */
    protected $adapterUserNote;
    
    public function __construct(UserNoteAdapter $adapterUserNote)
    {
        $this->adapterUserNote = $adapterUserNote;
    }
    
    public function add($userId, $note)
    {
        return $this->adapterUserNote->insert(['user_id' => $userId, 'note' => $note]);
    }

    public function fetchNoteHistory($userId)
    {
        return $this->adapterUserNote->fetchNoteHistory($userId);
    }
}
