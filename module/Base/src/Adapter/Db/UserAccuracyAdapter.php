<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

use Base\Service\EntryStageService;

class UserAccuracyAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'user_accuracy';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    /**
     * Inserts a new user accuracy record
     *
     * @param int $userId
     * @param int $reportId
     * @param int $numKeyed
     * @return int - userAccuracyId of the inserted row
     */
    public function insertNew($userId, $reportId)
    {
        $data = [
            'date_created' => $this->getNowExpr(),
            'user_id' => $userId,
            'report_id' => $reportId
        ];

        return $this->insert($data);
    }

    /**
     * Creates where statements based on input. Requires specific alias use.
     *
     * This method will assume certain joins are present with certain aliases:
     * ren = ReportEntry
     * frm = Form
     * agn = AgencyNew
     *
     * @param Zend_Db_Select $select
     * @param string $fromDate
     * @param string $toDate
     * @param string $stateId
     * @param int $formId
     * @param int $formAgencyId
     */
    protected function addWhereToSelect(
        Select $select,
        $fromDate = null,
        $toDate = null,
        $stateId = null,
        $formId = null,
        $formAgencyId = null)
    {
        $select->where(['ens.name_internal' => EntryStageService::STAGE_ALL]);

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
     * Adds joins to common report select based on criteria
     *
     * @param array|int $userIds
     * @param string $fromDate
     * @param string $toDate
     * @param string $stateId
     * @param int $formId
     * @param int $formAgencyId
     * @return Zend_Db_Select
     */
    protected function addJoinsToSelect(
        Select $select,
        $stateId = null,
        $formId = null,
        $formAgencyId = null)
    {
        $isJoined = 0;

        if (!is_null($stateId) || !is_null($formId)) {
            $select->join(
                ['rep' => 'report'],
                'rep.report_id = ren.report_id',
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
                'rep2.report_id = ren.report_id',
                []
            );
        }
        
        return $select;
    }
    
    /**
     * Will return a select to pull userAccuracy info for the first and last name of a user
     *
     * This will not return userAccuracyInvalid data.
     * Try not to use this outside of paginators, use getAllByUsername instead.
     *
     * @param array|int $userIds
     * @param string $fromDate
     * @param string $toDate
     * @param string $stateId
     * @param int $formId
     * @param int $formAgencyId
     * @return Zend_Db_Select
     */
    public function getSelectAllByUserIds(
        array $userIds,
        $fromDate = null,
        $toDate = null,
        $stateId = null,
        $formId = null,
        $formAgencyId = null)
    {
        $select = false;

        $userIds = (is_array($userIds) || empty($userIds)) ? $userIds : [$userIds];

        if (!empty($userIds)) {
            $select = $this->getSelect();
            $select->from(['uac' => 'user_accuracy']);
            $select->join(['uai' => 'user_accuracy_invalid'], 'uai.user_accuracy_id = uac.user_accuracy_id', [], Select::JOIN_LEFT);
            $select->join(['ren' => 'report_entry'], 'ren.report_id = uac.report_id AND ren.user_id = uac.user_id', []);
            $select->join(['rep' => 'report'], 'rep.report_id = uac.report_id', []);
            $select->join(['frm' => 'form'], 'frm.form_id = rep.form_id', []);
            $select->join(['st' => 'state'], 'st.state_id = frm.state_id', []);
            $select->join(['agn' => 'agency'], 'agn.agency_id = rep.agency_id', [], Select::JOIN_LEFT);
            $select->join(['ens' => 'entry_stage'], 'ren.entry_stage_id = ens.entry_stage_id', []);
            $select->join(['u' => 'user'], 'u.user_id = uac.user_id', []);
            $select->join(['kv' => 'keying_vendor'], 'kv.keying_vendor_id = u.keying_vendor_id', []);
            $columns = [
                'countKeyed' => 'ren.count_keyed',
                'reportId' => 'uac.report_id',
                'userAccuracyId' => 'uac.user_accuracy_id',
                'countInvalid' => $this->getCount('uai.user_accuracy_invalid_id'),
                'dateKeyed' => 'ren.date_created',
                'formState' => 'st.name_abbr',
                'formName' =>'frm.name_external',
                'agencyName' => 'agn.name',
                'vendorName' => 'kv.vendor_name'
            ];
            $select->columns($columns, false);
            $select->order(['ren.date_created', 'uac.user_accuracy_id'], 'ASC');
            $select->group('uac.user_accuracy_id');
            $select->where->In('uac.user_id', $userIds);
            $select = $this->addWhereToSelect(
                $select,
                $fromDate,
                $toDate,
                $stateId,
                $formId,
                $formAgencyId
            );
        }
        
        return $this->fetchAll($select);
    }

    /**
     * Will return userAccuracy info for the first and last name of a user
     *
     * This will not return userAccuracyInvalid data.
     *
     * @param array|int $userIds
     * @param string $fromDate
     * @param string $toDate
     * @param string $stateId
     * @param int $formId
     * @param int $formAgencyId
     * @return array|FALSE
     */
    public function getAllByUserIds(
        Array $userIds,
        $fromDate = null,
        $toDate = null,
        $stateId = null,
        $formId = null,
        $formAgencyId = null)
    {
        $result = false;
        $select = $this->getSelectAllByUserIds(
            $userIds,
            $fromDate,
            $toDate,
            $stateId,
            $formId,
            $formAgencyId
        );
        
        if ($select != false) {
            $result = $this->fetchAll($select);
        }
        
        return $result;
    }

    /**
     * Gets number of fields keyed for criteria
     *
     * @param array|int $userIds
     * @param string $fromDate
     * @param string $toDate
     * @param string $stateId
     * @param int $formId
     * @param int $formAgencyIdd
     * @return int
     */
    public function getCountKeyed(
        array $userIds,
        $fromDate = null,
        $toDate = null,
        $stateId = null,
        $formId = null,
        $formAgencyId = null)
    {
        $select = $this->getSelect();
        $select->from(['ren' => 'report_entry']);
        $columns = [
            'count_keyed' => new Expression('SUM(ren.count_keyed)')
        ];
        $select->columns($columns);
        $select->join(['uac' => 'user_accuracy'],'uac.report_id = ren.report_id',[]);
        $select->join(['ens' => 'entry_stage'], 'ren.entry_stage_id = ens.entry_stage_id', []);
        $select->where(['uac.user_id' => $userIds]);
        $select = $this->addJoinsToSelect(
            $select,
            $stateId,
            $formId,
            $formAgencyId
        );
        $select = $this->addWhereToSelect(
            $select,
            $fromDate,
            $toDate,
            $stateId,
            $formId,
            $formAgencyId
        );
        
        return $this->fetchOne($select);
    }
    
    /**
     * Get the count of user accuracy records that exist for a report
     *
     * @param int $reportId
     * @return int
     */
    public function getCountRecords($reportId)
    {
        $select = $this->getSelect();
        $select->from($this->table);
        $columns = [
            'count' => $this->getCount('user_accuracy_id')
        ];
        $select->columns($columns);
        $select->where('report_id = :report_id');
        $bind = ["report_id" => $reportId];

        return $this->fetchOne($select, $bind);
    }
}
