<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Base\Service\ReportEntryService;
use Base\Service\KeyingVendorService;

class ReportEntryAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'report_entry';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
        $this->adapter = $adapter;
    }
    
    public function add($reportId, $formId, $userId, $entryStageId, $passNumber, $status)
    {
        $data = [
            'date_created' => $this->getNowExpr(),
            'report_id' => $reportId,
            'form_id' => $formId,
            'user_id' => $userId,
            'entry_stage_id' => $entryStageId,
            'entry_status' => $status,
            'pass_number' => $passNumber
        ];

        return $this->insert($data);
    }

    /**
     * Marks the report entry completed for a user in the DB
     *
     * @param integer $reportEntryId
     * @return bool - If any rows were updated
     */
    public function completeReportEntry($reportEntryId)
    {   
        return ($this->update(
                ['entry_status' => ReportEntryService::STATUS_COMPLETE],
                ['report_entry_id' => $reportEntryId]
            ) > 0
        );
    }

    public function revertInProgress($userId = null, $reportId = null)
    {
        if(empty($userId) && empty($reportId))
        {
            return;
        }
        $where = [            
            'entry_status' => ReportEntryService::STATUS_IN_PROGRESS,
        ];
        if (!empty($userId)) {
            $where['user_id'] = $userId;
        }        
        if (!empty($reportId)) {
            $where['report_id'] = $reportId;
        }

        $this->delete($this->table, $where);
    }
    
    /**
     * Gets form system (id and internal name) by entry id
     *
     * @param type $reportEntryId
     * @return [id => internal_name]
     */
    public function getFormSystemByEntryId($reportEntryId) {
        $select = $this->getSelect();
        $select->from(['ren' => $this->table]);
        $select->columns([]);
        $select->join(['frm' => 'form'], 'ren.form_id = frm.form_id', []);
        $select->join(['frt' => 'form_template'], 'frm.form_template_id = frt.form_template_id', []);
        $select->join(['frs' => 'form_system'], 'frt.form_system_id = frs.form_system_id', ["form_system_id", "name_internal"]);
        $select->where('ren.report_entry_id = :report_entry_id');
        $bind = ["report_entry_id" => $reportEntryId];

        return $this->fetchPairs($select, $bind);
    }

    /**
     * Update count of keyed elements for a report entry
     *
     * @param int $reportEntryId
     * @param int $countKeyed
     */
    public function updateCountKeyed($reportEntryId, $countKeyed)
    {
        $this->update(["count_keyed" => $countKeyed], ['report_entry_id' => $reportEntryId]);
    }
    
    /**
     * Get report entry data for last pass based on report id
     *
     * @param int $reportId
     * @param bool $isOldReport if true then uncompress the data in the fetch
     * @return array
     */
    public function fetchLastPassByReportId($reportId, $isOldReport = false)
    {
        $select = $this->getSelect();
        $select->from(['ren' => $this->table]);
        $select->columns(['formId' => 'form_id', 'reportEntryId' => 'report_entry_id']);
        $select->join(['red' => 'report_entry_data'], 'ren.report_entry_id = red.report_entry_id', 
                ['entryData' => ($isOldReport) ? new Expression('uncompress(entry_data)') : 'entry_data']);
        $select->where('ren.report_id = :report_id');
        $select->order('ren.report_entry_id DESC');
        $bind = ["report_id" => $reportId];

        return $this->fetchRow($select, $bind);
    }
    
    public function fetchPassTwoByReportId($reportId)
    {
        $select = $this->getSelect();
        $select->from(['ren' => $this->table]);
        $select->join(['red' => 'report_entry_data'], 'ren.report_entry_id = red.report_entry_id',['entryData' => 'entry_data'], 
        SELECT::JOIN_LEFT);
        $select->join(['est' => 'entry_stage'] , 'est.entry_stage_id = ren.entry_stage_id', []);
        
        $select->columns([
            'formId' => 'form_id', 
            'reportEntryId' => 'ren.report_entry_id' , 
            'userId' => 'user_id' ,
            'dateCreated' => 'ren.date_created', 
            'dateUpdated' => 'ren.date_updated', 
            'pass_number',
            'entryStageId' => 'est.entry_stage_id',
            'passName' => 'est.name_external',
            'entryStage' => 'est.name_internal'
        ],false);


        $select->where('ren.pass_number = :pass_number');
        $select->where('ren.report_id = :report_id');

        $bind = [
            'report_id' => $reportId,
            'pass_number' => 2
        ];

        return $this->fetchRow($select, $bind);
    }
    

    
    /**
     * Count how many edit passes the report has
     * (optional) - give us the entryId and we count up to that pass
     *
     * @param int report_id
     * @param int reportEntryId (optional)
     */
    public function countEditPasses($reportId, $reportEntryId = null)
    {
        $select = $this->getSelect();
        $select->columns(['count' => $this->getCount('report_entry_id')]);
        $select->where([
            'report_id = :report_id',
            'entry_status = :entry_status',
            'report_entry_id <= :report_entry_id',
            'entry_stage_id = 4'
        ]);

        $bind = [
            'report_id' => $reportId,
            'entry_status' => ReportEntryService::STATUS_COMPLETE,
            'report_entry_id' => $reportEntryId
        ];
        
        return $this->fetchOne($select, $bind);
    }

    /**
     * Determine the last reportEntryId for a specific reportId and userId
     *
     * @param integer $reportId
     * @param integer $userId
     * @return integer|null
     */
    public function getLastIdByReportAndUser($reportId, $userId)
    {
        $select = $this->getSelect();
        $select->columns(['report_entry_id']);
        $select->where([
            'report_id = :report_id',
            'user_id = :user_id'
        ]);
        $select->order('report_entry_id DESC')->limit(1);
        $bind = [
            'report_id' => $reportId,
            'user_id' => $userId
        ];

        return $this->fetchOne($select, $bind);
    }

    /**
     * Gets the maximum (most recent) pass number for a report entry
     *
     * @param int $reportId - Id of the report entry to check passes for
     * @return int|FALSE - The number of the last pass or FALSE if no records
     */
    public function getMaxCompletedPass($reportId)
    {
        $select = $this->getSelect();
        $select->columns(['passNumber' => $this->getMax("pass_number")]);
        $select->where([
            'entry_status = :entry_status',
            'report_id = :report_id'
        ]);
        $bind = [
            'entry_status' => ReportEntryService::STATUS_COMPLETE,
            'report_id' => $reportId
        ];

        return $this->fetchOne($select, $bind);
    }
    
    /**
     * Gets the most recent authoritive (completed) report entry id.
     *
     * @param integer $reportId
     * @return integer
     */
    public function getMaxCompletedId($reportId)
    {
        $select = $this->getSelect();
        $select->columns(['report_entry_id']);
        $select->where('report_id = :report_id');
        $select->where(['entry_status' => ReportEntryService::STATUS_COMPLETE]);
        $select->order('date_updated DESC');
        $select->limit(1);
        $bind = ['report_id' => $reportId];

        return $this->fetchOne($select, $bind);
    }
    
    /**
     * Gets the form Id and entry id of the maximum completed entry stage
     *
     * @param integer $reportId
     * @return two integers form_id and report_entry_id
     *
     */
    public function getMaxCompletedFormAndEntryId($reportId)
    {
        $select = $this->getSelect();
        $select->columns([
            'form_id', 'report_entry_id'
        ]);
        $select->where([
            'entry_status = :entry_status',
            'report_id = :report_id'
        ]);
        $select->order('date_updated DESC');
        $select->limit(1);
        $bind = [
            'entry_status' => ReportEntryService::STATUS_COMPLETE,
            'report_id' => $reportId
        ];

        return $this->fetchRow($select, $bind);
    }

    public function fetchFormSystemId($reportEntryId) {
        $select = $this->getSelect();
        $select->from(['ren' => $this->table]);
        $select->columns([
            "form_system_id" => "frt.form_system_id"
        ], false);
        $select->join(['frm' => 'form'], 'ren.form_id = frm.form_id', []);
        $select->join(['frt' => 'form_template'], 'frm.form_template_id = frt.form_template_id', []);
        $select->where("ren.report_entry_id = :report_entry_id");
        $bind = ["report_entry_id" => $reportEntryId];
        $formSystemId = $this->fetchRow($select, $bind);

        return (!empty($formSystemId)) ? $formSystemId['form_system_id'] : '';
    }

    public function fetchLastByReportId($reportId)
    {
        $sql = '
            SELECT report_entry_id AS reportEntryId,
                report_id AS reportId,
                form_id AS formId,
                user_id AS userId,
                entry_stage_id AS entryStageId,
                entry_status AS entryStatus,
                pass_number AS passNumber
            FROM report_entry 
            WHERE report_id = :reportId
            ORDER BY report_entry_id DESC
            LIMIT 1
        ';
        
        return $this->fetchRow($sql, ['reportId' => $reportId]);
    }

    public function cleanUp($timeLength, $timeUnit)
    {
        $sql = "
            DELETE re
            FROM report_entry AS re
            WHERE re.date_created < NOW() - INTERVAL {$timeLength} {$timeUnit}
                AND re.entry_status = 'in progress'
        ";

        $this->adapter->createStatement($sql)->execute();

        $sql = "
            DELETE uep
            FROM user_entry_prefetch AS uep
            WHERE uep.date_created < NOW() - INTERVAL {$timeLength} {$timeUnit}
        ";
      
        $this->adapter->createStatement($sql)->execute();

        $sql = "
            UPDATE report_entry_queue AS req
            SET req.user_id_assigned_to = NULL,
                req.date_assigned = NULL
            WHERE req.date_assigned < NOW() - INTERVAL {$timeLength} {$timeUnit}
        ";
        
        $this->adapter->createStatement($sql)->execute();
    }

    public function logCleanableRecords($timeLength, $timeUnit)
    {
        $sql = "
            SELECT
                ren.report_id,
                ren.report_entry_id,
                ren.date_created,
                usr.username,
                ren.entry_stage_id,
                ren.pass_number,
                red.report_entry_data_id
            FROM report_entry AS ren
                LEFT JOIN user AS usr ON (ren.user_id = usr.user_id)
                LEFT JOIN report_entry_data AS red ON (red.report_entry_id = ren.report_entry_id)
            WHERE ren.date_created < NOW() - INTERVAL {$timeLength} {$timeUnit}
                AND ren.entry_status = 'in progress'
        ";

        return $result = $this->fetchAll($sql);
    }
    
    public function updateFormIdByReportId($formId, $reportId)
    {
        return $this->update(['form_id' => $formId], ['report_id' => $reportId]);
    }
    
    public function getSlaStatusSummarybyState($searchCriteria)
    {	
        $columns = [
            'reportId' => 'r.report_id',
            'stateAbbr' => 's.name_abbr',
            'dateCreated' => 'r.date_created',		
            'agencyName' => 'agn.name',
            'workType' => 'wt.name_external',
			'dueDate' => 'rts.tat_hours',
			'wtTatHours' => 'wt.tat_hours',
		    'stage' => 'est.name_external',
			'userId' => 'usr.username',
			'agencyName' => 'agn.name',
			'reportStatus' => 'rs.name',
			'entryStatus' => 're.entry_status',
            'vendorName' => 'kv.vendor_name',
            'flag' => new Expression('CASE WHEN rc.priority IS NULL THEN "No" ELSE "Yes" END'),
            'formTypeDescription' => 'ft.description'                   
        ];
        
        $select = $this->getSelect();
        $select->from(['req' => 'report_entry_queue']);
        $select->columns($columns, false);
        $select->join(['r' => 'report'], 'r.report_id = req.report_id', []);
		$select->join(['s' => 'state'], 's.state_id = r.state_id', []);
		$select->join(['est' => 'entry_stage'], 'est.entry_stage_id = req.entry_stage_id', []);
		$select->join(['re' => 'report_entry'], new Expression('re.report_id = r.report_id AND re.entry_status != "complete"'), [], Select::JOIN_LEFT);
		$select->join(['wt' => 'work_type'], 'wt.work_type_id = r.work_type_id', []);
		$select->join(['agn' => 'agency'], 'agn.agency_id = req.agency_id', [],Select::JOIN_LEFT);
        $select->join(['ft' => 'form_type'], 'ft.form_type_id = r.form_type_id', [],Select::JOIN_LEFT);        
		$select->join(['rts' => 'report_tat_status'], 'rts.report_id = r.report_id', []);
        $select->join(['ur' => 'user_report'], '(ur.report_id = r.report_id)', [], Select::JOIN_LEFT);
		$select->join(['usr' => 'user'], new Expression('usr.user_id = CASE WHEN ur.user_report_id IS NOT NULL THEN ur.user_id WHEN req.report_assigned_to IS NOT NULL THEN req.report_assigned_to ELSE req.user_id_assigned_to END'), [], Select::JOIN_LEFT);
        $select->join(['kv' => 'keying_vendor'], 'usr.keying_vendor_id = kv.keying_vendor_id', [],Select::JOIN_LEFT);
		$select->join(['rs' => 'report_status'], 'rs.report_status_id = r.report_status_id', [],Select::JOIN_LEFT);
        $select->join(['rc' => 'report_cru'], 'rc.report_id = r.report_id', [],Select::JOIN_LEFT);
		       
	    if (strcasecmp($searchCriteria['state'], 'all') != 0) {

            $select->where(["r.state_id" => $searchCriteria['state']]);			
        }
        
        if (strcasecmp($searchCriteria['workType'], 'all') != 0) {
            $select->where(["wt.work_type_id" => $searchCriteria['workType']]);         
        }

        if ($searchCriteria['priority'] == 1) {
           $select->where('rc.priority IS NOT NULL');        
        }

        if ($searchCriteria['keyingVendorId'] != KeyingVendorService::VENDOR_ALL) {
            $select->where("(CASE WHEN req.user_id_assigned_to IS NOT NULL "
                    . "THEN usr.keying_vendor_id = " . $searchCriteria['keyingVendorId'] . " "
                    . "ELSE req.user_id_assigned_to IS NULL END)");
        }
		
	    $select->where('r.report_status_id = 1');	
        $select->order('dueDate');
        $select->order('wt.work_type_id');
        $select->order('reportId');

        return $this->fetchAll($select);
    }
    
    public function getSlaStatusSummaryTotal($searchCriteria,$workType)
    {
        $where = '';
        $where .= "WHERE r.work_type_id = '".$workType."' AND r.report_status_id = 1 ";     

        if (strcasecmp($searchCriteria['state'], 'all') != 0) {
            $where .= "AND r.state_id  = '". $searchCriteria['state']. "'  ";
        }       
        
        $isKeyingVendorIncluded = false;
        if (!empty($searchCriteria['keyingVendorId']) && 
                $searchCriteria['keyingVendorId'] != KeyingVendorService::VENDOR_ALL) {
            $where .= "AND (CASE WHEN req.user_id_assigned_to IS NOT NULL "
                    . "THEN u.keying_vendor_id = " . $searchCriteria['keyingVendorId'] . " "
                    . "ELSE req.user_id_assigned_to IS NULL END)";
            $isKeyingVendorIncluded = true;
        }
        
        $sql = "
            SELECT
                r.report_id AS reportId,
                SUM(rts.tat_hours>now() and req.entry_stage_id = 1) AS ec1,
                SUM(rts.tat_hours<now() and req.entry_stage_id = 1) AS ec2,
                SUM(rts.tat_hours>now() and req.entry_stage_id = 3) AS ec3,
                SUM(rts.tat_hours<now() and req.entry_stage_id = 3) AS ec4 
            FROM report_entry_queue AS req
            INNER JOIN report AS r ON r.report_id = req.report_id
            INNER JOIN state AS s ON s.state_id = r.state_id
            INNER JOIN entry_stage AS est ON est.entry_stage_id = req.entry_stage_id
            INNER JOIN work_type AS wt ON wt.work_type_id = r.work_type_id
            INNER JOIN report_tat_status AS rts ON rts.report_id = r.report_id ";
            
            if ($isKeyingVendorIncluded) {
                $sql .= "LEFT JOIN user AS u ON u.user_id = req.user_id_assigned_to ";
            }
                    
            $sql .= $where;      
        return $this->fetchRow($sql); 
    }

    /**
     * Check in progress entries for a specific reportId and entryStageId
     *
     * @param integer $reportId
     * @param integer $entryStageId
     * @return integer|null
     */
    public function checkInprogressReport($reportId, $entryStageId)
    {
        $select = $this->getSelect();
        $select->columns(['report_entry_id']);
        $select->where([
            'report_id = :report_id',
            'entry_stage_id = :entry_stage_id',
            'entry_status = :entry_status'
        ]);
        $select->order('report_entry_id DESC')->limit(1);
        $bind = [
            'report_id' => $reportId,
            'entry_stage_id' => $entryStageId,
            'entry_status' => ReportEntryService::STATUS_IN_PROGRESS
        ];

        return $this->fetchOne($select, $bind);
    }

}
