<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;

class UserEntryStageAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'user_entry_stage';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    public function getEntryStageByUserId($userId)
    {
        $select = $this->getSelect()
            ->where('user_id = :user_id');
        $bind = ['user_id' => $userId];
        
        return $this->fetchAll($select, $bind);
    }
}
