<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Log\Logger;

class FormSystemAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'form_system';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(
        Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    /**
     * Gets basic information about a form system
     *
     * @param integer $formSystemId
     * @return array
     */
    public function getFormSystemName($formSystemId)
    {
        $select = $this->getSelect();
        $select->where('form_system_id = :form_system_id');
        $bind = ['form_system_id' => $formSystemId];

        return $this->fetchRow($select, $bind);
    }
    
}
