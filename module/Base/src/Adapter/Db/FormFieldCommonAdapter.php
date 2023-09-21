<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;

class FormFieldCommonAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'form_field_common';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    public function fetchAllValueFields() {
        $select = $this->getSelect();
        $select->where('is_value_field = 1');
        
        return $this->fetchAssoc($select);
    }
}
