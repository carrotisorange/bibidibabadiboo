<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Log\Logger;

class FormTypeAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'form_type';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    /**
     * Select all Form types
     * 
     * @return array all form types, else return empty array; on exception of failure
     */
    public function fetchAllFormTypes()
    {   
        try {
            $select = $this->getSelect();
            
            return $this->fetchAll($select);
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        }
    }
    
    /**
     * Select all Form types only used in keying
     * 
     * @return array all form types used in keying, else return empty array; on exception of failure
     */
    public function fetchAllKeyedFormTypes()
    {
        try {
            $select = $this->getSelect();
            $select->from(['f' => 'form']);
            $select->join(['ft' => $this->table], 'ft.form_type_id = f.form_type_id', []);
            $columns = [
                'formTypeId' => $this->getDistinct('f.form_type_id'),
                'formTypeCode' => 'ft.code',
                'formTypeDescription' => 'ft.description',
                'formTypeActive' => 'ft.active',
                'formTypeAllowedIncident' => 'ft.allowed_incident'
            ];
            $select->columns($columns, false);

            return $this->fetchAll($select);
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        }
    }
}
