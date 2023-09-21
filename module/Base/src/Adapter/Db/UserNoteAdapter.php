<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

class UserNoteAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'user_note';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    public function fetchNoteHistory($userId)
    {
        $select = $this->getSelect()
            ->where('user_id = :user_id')
            ->order(['date_created DESC']);
        
        $bind = ['user_id' => $userId];
        
        return $this->fetchAll($select, $bind);
    }
}
