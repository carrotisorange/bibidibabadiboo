<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Log\Logger;

use Base\Service\ReportEntryService;
use Base\Service\EntryStageService;

class ReportEntryDataAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'report_entry_data';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
        $this->adapter = $adapter;
    }

    /**
     * Insert or update a row in report entry data
     *
     * Note: invalid vin queue will call this directly to bypass the count
     * done in the model since it updates entry data in-place. It would be nice
     * if someday it just created a new record.
     *
     * @param int $reportId
     * @param int $reportEntryId
     * @param string $entryData - alredy compressed
     * @return int - row count
     */
    public function insertOrUpdateRow($reportId, $reportEntryId, $entryData)
    {
        $sql = "
            INSERT INTO report_entry_data
            (date_created, report_id, report_entry_id, entry_data, new_app) VALUES
            (NOW(), :report_id, :report_entry_id, :entry_data, :new_app)
            ON DUPLICATE KEY UPDATE entry_data = VALUES(entry_data)
        ";
        $bind = [
            'report_id' => $reportId,
            'report_entry_id' => $reportEntryId,
            'entry_data' => $entryData,
            'new_app' => 1
        ];

        $qry = $this->adapter->createStatement($sql, $bind)->execute();

        return $qry->getAffectedRows();
    }

    /**
     * Get ALL report entry data for a report
     *
     * @param int $reportId
     * @return array|FALSE - FALSE if no records found
     */
    public function fetchAllReportEntryData($reportId, $reportEntryId = null)
    {
        $select = $this->getSelect();
        $select->from(['red' => 'report_entry_data'], []);
        $select->join(['ren' => 'report_entry'], 'ren.report_entry_id = red.report_entry_id', []);
        $select->join(['est' => 'entry_stage'], 'est.entry_stage_id = ren.entry_stage_id', []);
        $select->join(['usr' => 'user'], 'ren.user_id = usr.user_id', []);
        $select->columns([
            'reportEntryId' => 'red.report_entry_id',
            'entryData' => 'red.entry_data',
            'formId' => 'ren.form_id',
            'entryStage' => 'est.name_internal',
            'userId' => 'usr.user_id',
            'dateUpdated' => 'ren.date_updated',
            'entryStageId' => 'ren.entry_stage_id'
        ], false);

        $select->where(['red.report_id' => $reportId]);
        $select->where(['ren.entry_status' => ReportEntryService::STATUS_COMPLETE]);

        if ($reportEntryId != null) {
            $select->where(['ren.report_entry_id' => $reportEntryId]);
        }

        $result = $this->fetchAll($select);
        return $result;
    }
    
    /*
     * Get Misc info for a particular report entry.
     *
     * @param int $reportId
     * @param int $reportEntryId
     * @return array
     */
    function fetchReportEntryInfo($reportEntryId)
    {
        $select = $this->getSelect();
        $select->from(['ren' => 'report_entry']);
        $select->join(['est' => 'entry_stage'], 'est.entry_stage_id = ren.entry_stage_id', []);
        $select->join(['usr' => 'user'], 'ren.user_id = usr.user_id', []);
        $select->columns([
            'reportId' => 'ren.report_id',
            'reportEntryId' => 'ren.report_entry_id',
            'passName' => 'est.name_external',
            'entryStage' => 'est.name_internal',
            'userId' => 'usr.user_id',
            'userName' => 'usr.username',
            'nameFirst' => 'usr.name_first',
            'nameLast' => 'usr.name_last',
            'dateUpdated' => 'ren.date_updated',
            'entryStageId' => 'ren.entry_stage_id',
            'formId' => 'ren.form_id'
        ], false);
        $select->where([
            'ren.entry_status = :entry_status',
            'ren.report_entry_id = :report_entry_id'
        ]);
        $bind = [
            'entry_status' => ReportEntryService::STATUS_COMPLETE,
            'report_entry_id' => $reportEntryId
        ];
        
        return $this->fetchRow($select, $bind);
    }
    
    /**
     * Fetch the specific report entry data row
     *
     * @param int $reportId
     * @param int $reportEntryId
     * @return array
     */
    public function fetchByEntryId($reportId, $reportEntryId)
    {
        $select = $this->getSelect();
        $select->columns(['entryData' => 'entry_data']);
        $select->where('report_id = :report_id');
        $select->where('report_entry_id = :report_entry_id');

        return $this->fetchRow($select, [
            'report_id' => $reportId,
            'report_entry_id' => $reportEntryId,
        ]);
    }

    public function getLastReportEntryDataByReportId($reportId)
    {
         $sql = '
            SELECT 
                new_app as newApp
            FROM report_entry_data
            WHERE report_id = :reportId
            ORDER BY report_entry_data_id DESC
            LIMIT 1
        ';
        
        return $this->fetchRow($sql, ['reportId' => $reportId]);
    }
    
    public function fetchAutoextractionData($reportId)
    {
       $sql = "
            SELECT 
                entry_data AS entryData, date_created, '' as entryStage, '' as reportEntryId
            FROM auto_extraction_data
            WHERE report_id = :reportId
        ";

        return $this->fetchRow($sql, ['reportId' => $reportId]);
    }


    public function fetchAccuracyReportEntryData($reportId, $reportEntryId = null)
    {
        $select = $this->getSelect();
        $select->from(['red' => 'report_entry_data'], []);
        $select->join(['ren' => 'report_entry'], 'ren.report_entry_id = red.report_entry_id', []);
        $select->join(['est' => 'entry_stage'], 'est.entry_stage_id = ren.entry_stage_id', []);
        $select->join(['usr' => 'user'], 'ren.user_id = usr.user_id', []);
        $select->columns([
            'reportEntryId' => 'red.report_entry_id',
            'entryData' => 'red.entry_data',
            'formId' => 'ren.form_id',
            'entryStage' => 'est.name_internal',
            'userId' => 'usr.user_id',
            'dateUpdated' => 'ren.date_updated',
            'entryStageId' => 'ren.entry_stage_id'
        ], false);

        $select->where(['red.report_id' => $reportId]);
        $select->where(['ren.entry_status' => ReportEntryService::STATUS_COMPLETE]);
        $select->where->In('est.name_internal', [EntryStageService::STAGE_ALL, EntryStageService::STAGE_DYNAMIC_VERIFICATION]);

        if ($reportEntryId != null) {
            $select->where(['ren.report_entry_id' => $reportEntryId]);
        }

        $result = $this->fetchAll($select);
        return $result;
    }
    
}
