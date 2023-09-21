<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db\Extract;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Log\Logger;

use Base\Adapter\Db\DbAbstract;

class EnumerationValueAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'enumeration_value';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(
        Adapter $adapter,
        Logger $logger)
    {
        parent::__construct($adapter, $this->table);
        $this->logger = $logger;
        $this->adapter = $adapter;
    }
    
    /**
     *
     * @param type $formId
     * @return type
     */
    public function getEnumerationPairs($formId)
    {
        $sql = '
            SELECT
                ef.field_name AS fieldName,
                enumeration_value_vendor AS enumerationValueVendor,
                enumeration_value_id AS enumerationValueId,
                related_table AS relatedTable,
                additional_info_field_name AS additionalInfoFieldName
            FROM enumeration_value ev
            INNER JOIN enumeration_field ef USING(enumeration_field_id)
            WHERE form_id = :formId
            ORDER BY ev.field_name';

        return $this->fetchAll( $sql, ['formId' => $formId] );
    }

    /**
     * Return aggregate set of enumeration value records for 1 to N form ids
     * @param string $formIdsCsv - CSV string of form ids to aggregate enum values for.
     * @param [string] $fields (*) the fields to retrieve.
     * @param [string] $orderBy ('enumeration_value_vendor') the column to order by from enumeration values table
     * @return array
     */
    public function fetchEnumerationValues($formIdsCsv, $fields = '*', $orderBy = 'enumeration_value_vendor')
    {
        try {
            $sql = "SELECT DISTINCT {$fields} FROM {$this->table} WHERE form_id IN ( ? ) ORDER BY {$orderBy}";
            $bind = [ $formIdsCsv ];

            return $this->fetchAll( $sql, $bind );
            // @codeCoverageIgnoreStart
        } catch ( Exception $e ) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return null;
        }// @codeCoverageIgnoreEnd
    }

    /**
     * Insert a record into the enumeration value table.
     * @param array $fieldAndVals kv pairs array of strings for fields and values to insert
     * @return int number of rows inserted (should be 1). 0 on error or exception.
     */
    public function insertEnumerationValue($fieldAndVals)
    {
        try {
            $fieldsCsv = implode( ', ', array_keys( $fieldAndVals ) );
            $bindCsv = ':' . implode( ', :', array_keys( $fieldAndVals ) );
            $sql = "INSERT INTO {$this->table} ($fieldsCsv) VALUES ($bindCsv)";
            $qry = $this->adapter->createStatement($sql, $fieldAndVals)->execute();

            return $qry->getAffectedRows();
            // @codeCoverageIgnoreStart
        } catch ( Exception $e ) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);

            return 0;
        }// @codeCoverageIgnoreEnd
    }

    /**
     * Insert (or Update on dup key) a record in the enumeration value table.
     * @param array $fieldAndVals kv pairs array of strings for fields and values to insert
     * @return int number of rows inserted/updated (should be 1). 0 on error or exception.
     * 
     * Note: This differs slightly from insert so that enum repair tool (AnalyzeRepairFormData job) can update 
     * additional_info_field_name in special cases where the read query fails to find the record where the
     * additional_info_field_name field may be empty for target form id. Can use for general insert update ops.
     */
    public function insertUpdateEnumerationValue($fieldAndVals)
    {
        try {
            $fieldsCsv = implode( ', ', array_keys( $fieldAndVals ) );
            $bindCsv = ':' . implode( ', :', array_keys( $fieldAndVals ) );
            $sql = "INSERT INTO {$this->table} ($fieldsCsv) VALUES ($bindCsv) ON DUPLICATE KEY UPDATE ";
            $fieldAndVals[ 'form_id' ] = ( int ) $fieldAndVals[ 'form_id' ]; // needed to prevent exceptions
            /*
             * Setup the fields to update based on the schema unique index we are triggering update on. This should
             * result only in the `field_name` or `additional_info_field_name` fields to be updated.
             * schema unique index: form_id, enumeration_field_id, enumeration_value_vendor, enumeration_value
             * Note: We have to unset these else exception will occur on the update with invalid form_id value etc.
             */
            $updateFields = $fieldAndVals;
            unset( $updateFields[ 'form_id' ] );
            unset( $updateFields[ 'enumeration_field_id' ] );
            unset( $updateFields[ 'enumeration_value_vendor' ] );
            unset( $updateFields[ 'enumeration_value' ] );
            foreach ( $updateFields as $field => $val ) {
                $sql .= " {$field}=:{$field},";
            }
            $sql = rtrim( $sql, ',' );
            $qry = $this->adapter->createStatement($sql, $fieldAndVals)->execute();

            return $qry->getAffectedRows();
            // @codeCoverageIgnoreStart
        } catch ( Exception $e ) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);

            return 0;
        }// @codeCoverageIgnoreEnd
    }

    /**
     * Delete a record into the enumeration value table.
     * @param array $fieldAndVals kv pairs array of strings for fields and values to insert
     * @return int number of rows deleted (should be 1). 0 on error or exception.
     */
    public function deleteEnumerationValue($fieldAndVals)
    {
        try {
            $wh = [ ];
            foreach ( $fieldAndVals as $key => $val ) {
                $wh[] = "`{$key}`='{$val}'";
            }
            $where = implode( ' AND ', $wh );
            $rc = $this->delete( $this->table, $where );

            return $rc;
            // @codeCoverageIgnoreStart
        } catch ( Exception $e ) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);

            return 0;
        }// @codeCoverageIgnoreEnd
    }

    /**
     * Retrieve a record from the enumeration value table.
     * @param array $fieldAndVals kv pairs array of strings for fields and values to query against
     * @return array
     */
    public function readEnumerationValue($fieldAndVals)
    {
        try {
            $wh = [ ];
            foreach ( $fieldAndVals as $key => $val ) {
                $wh[] = "`{$key}`='{$val}'";
            }
            $where = implode( ' AND ', $wh );
            $sql = "SELECT * FROM {$this->table} WHERE $where";
            $rs = $this->fetchRow( $sql );

            return $rs;
            // @codeCoverageIgnoreStart
        } catch ( Exception $e ) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);

            return [ ];
        }// @codeCoverageIgnoreEnd
    }

}
