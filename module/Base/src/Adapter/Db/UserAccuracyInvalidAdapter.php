<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class UserAccuracyInvalidAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'user_accuracy_invalid';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    /**
     * Inserts a new invalid field record
     *
     * @param int $userAccuracyId - id of the associated user accuracy record
     * @param string $formAttributeName
     * @param string $validValue
     * @param string $userValue
     * @return int - Id of the inserted row
     */
    public function insertNew($userAccuracyId, $formAttributeName, $validValue, $userValue)
    {
        $data = [
            'date_created' => $this->getNowExpr(),
            'user_accuracy_id' => $userAccuracyId,
            'form_attribute_name' => $formAttributeName,
            'value_valid' => $validValue,
            'value_keyed' => $userValue
        ];

        return $this->insert($data);
    }
    
    /**
     * Gets the count of invalid keyed fields by criteria
     *
     * @param array|int $userIds
     * @param string $fromDate
     * @param string $toDate
     * @param string $stateId
     * @param int $formId
     * @param int $formAgencyIdd
     * @return int
     * @return int
     */
    public function getCountInvalid(
        Array $userIds,
        $fromDate = null,
        $toDate = null,
        $stateId = null,
        $formId = null,
        $formAgencyId = null)
    {
        $select = $this->getSelect();
        $select->from(['uai' => 'user_accuracy_invalid']);
        $columns = [
            'countinvalid' => $this->getCount('uai.user_accuracy_invalid_id')
        ];
        $select->columns($columns);
        $select->join(['uac' => 'user_accuracy'], 'uai.user_accuracy_id = uac.user_accuracy_id', []);
        $select->where(['uac.user_id' => $userIds]);
        $select = $this->addJoinsToSelect(
            $select,
            $fromDate,
            $toDate,
            $stateId,
            $formId,
            $formAgencyId);
        $select = $this->addWhereToSelect(
            $select,
            $fromDate,
            $toDate,
            $stateId,
            $formId,
            $formAgencyId);

        return $this->fetchOne($select);
    }

    /**
     * Gets all of the invalid data records for a user accuracy id
     *
     * @param int $userAccuracyId
     * @return array
     */
    function getInvalid($userAccuracyId)
    {
        $select = $this->getSelect();
        $columns = [
            'formAttributeName' => 'form_attribute_name',
            'valueValid' => 'value_valid',
            'valueKeyed' => 'value_keyed',
            'UserAccuracyId' => 'user_accuracy_id',
            'DateCreated' => 'date_created',
            'UserAccuracyInvalidId' => 'user_accuracy_invalid_id'  
        ];
        $select->columns($columns);
        $select->where('user_accuracy_id = :userAccuracyId');
        $bind = ['userAccuracyId' => $userAccuracyId];

        return $this->fetchAll($select, $bind);
    }
    
    /**
     * Adds where clause to common report select based on criteria
     *
     * @param Zend_Db_Select $select
     * @param string $fromDate
     * @param string $toDate
     * @param string $stateId
     * @param int $formId
     * @param int $formAgencyIdd
     * @return Zend_Db_Select
     */
    protected function addWhereToSelect(
        Select $select,
        $fromDate = null,
        $toDate = null,
        $stateId = null,
        $formId = null,
        $formAgencyId = null)
    {
        if (!empty($fromDate)) {
            $select->where('ren.date_created >=  "' . $this->getFormattedTimeString($fromDate) . '"');
        }
        if (!empty($toDate)) {
            $select->where('ren.date_created <  "' . $this->getFormattedTimeString($toDate . ' + 1 day') . '"');
        }
        if (!empty($stateId)) {
            $select->where(['frm.state_id' => $stateId]);
        }
        if (!empty($formId)) {
            $select->where(['frm.form_id' => $formId]);
        }
        if (!empty($formAgencyId)) {
            $select->where(['rep.agency_id' => $formAgencyId]);
        }
        
        return $select;
    }

    /**
     * Add joins to common report select
     *
     * @param Zend_Db_Select $select
     * @param string $fromDate
     * @param string $toDate
     * @param string $formState
     * @param int $formId
     * @param int $formAgencyIdd
     * @return Zend_Db_Select
     */
    protected function addJoinsToSelect(
        Select $select,
        $fromDate = null,
        $toDate = null,
        $formState = null,
        $formId = null,
        $formAgencyId = null)
    {
        $isJoined = 0;

        if (!is_null($fromDate) || !is_null($toDate)) {
            $select->join(
                ['ren' => 'report_entry'],
                'ren.report_id = uac.report_id AND ren.user_id = uac.user_id',
                []
            );
        }
        if (!is_null($formState) || !is_null($formId)) {
            $select->join(
                ['rep' => 'report'],
                'rep.report_id = uac.report_id',
                []
            );

            $select->join(
                ['frm' => 'form'],
                'frm.form_id = rep.form_id',
                []
            );

            $isJoined = 1;
        }
        if (!is_null($formAgencyId) && $isJoined == 0) {
            $select->join(
                ['rep2' => 'report'],
                'rep2.report_id = uac.report_id',
                []
            );
        }

        return $select;
    }
}
