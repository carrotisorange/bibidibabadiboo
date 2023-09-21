<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

class FormWorkTypeAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'form_work_type';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
        $this->adapter = $adapter;
    }
    
    /**
     * Fetch an array of all work types for the given form id.
     * @param int|numeric $formId
     * @return array
     */
    public function fetchWorkTypesByFormId($formId)
    {
        $sql = 'SELECT wt.*
            FROM form_work_type AS fwt
            INNER JOIN work_type AS wt ON wt.work_type_id = fwt.work_type_id
            WHERE fwt.form_id = :form_id';
        $bind = ['form_id' => $formId];

        return $this->fetchAll($sql, $bind);
    }

    /**
     * Add a new record for the form_work_type table for form id and work type id.
     * @param int|numeric $formId
     * @param int|numeric  $workTypeId
     * @return bool
     */
    public function insertFormWorkType($formId, $workTypeId)
    {
        try {
            if (!is_numeric($formId) || empty($formId)) {
                throw new Exception('Invalid $formId passed to fn: ' . __CLASS__ . '::' . __FUNCTION__ . '. Form id must be numeric and not empty.');
            }
            if (!is_numeric($workTypeId) || empty($workTypeId)) {
                throw new Exception('Invalid $workTypeId passed to fn: ' . __CLASS__ . '::' . __FUNCTION__ . '. Work type id must be numeric and not empty.');
            }
            $sql = "INSERT INTO {$this->table} (form_id, work_type_id) VALUES(:formId, :workTypeId)";
            $bind = [
                'formId' => $formId,
                'workTypeId' => $workTypeId
            ];
            $pdo = $this->adapter->query($sql, $bind);
            $tf = $this->pdoQueryOperationStatus($pdo);
            return( $tf );
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return false;
        }// @codeCoverageIgnoreEnd
    }

}
