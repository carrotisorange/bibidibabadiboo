<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;

class FormsToRekeyAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'form_to_rekey';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    /**
     * Select all forms to rekey
     * 
     * @return array
     */
    public function fetchAllFormsToRekey()
    {
        return $this->fetchAll();
    }
}
