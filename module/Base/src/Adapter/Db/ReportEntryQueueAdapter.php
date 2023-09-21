<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Log\Logger;
use Zend\Db\Sql\Select;
use Exception;
use UnexpectedValueException;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter as PaginationAdapter;

use Base\Service\RekeyService;
use Base\Service\EntryStageService;
use Base\Service\ReportEntryService;
use Base\Service\ReportEntryQueueService;
use Base\Service\ReportStatusService;
use Base\Service\WorkTypeService;

class ReportEntryQueueAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'report_entry_queue';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(
        Adapter $adapter,
        Logger $logger,
        Array $config)
    {
        parent::__construct($adapter, $this->table);

        $this->logger = $logger;
        $this->config = $config;
        $this->adapter = $adapter;
    }

    /**
     * Adds (or replaces!) a single report to the queue with the correct filter/priority parameters.
     *
     * @param integer $reportId
     * @return integer
     */
    public function add($reportId)
    {
        $where = "AND r.report_id = :report_id";
        $bind = ['report_id' => $reportId];

        return $this->insertOrReplace($where, $bind, 'add');
    }
    
    /**
     * Insert or replace a reportEntryQueue record based on where criteria
     * @param string $where - PDO style additional where clause beginning with "AND"
     * @param array $bind - Parameters to bind to the where clause
     * @return int - count of records inserted/replaced
     */
    protected function insertOrReplace($where, $bind, $action = null)
    {
        $m = __METHOD__.'(): ';
        $rowCount = 0;
        try {
            $select = "
                SELECT
                    r.report_id,
                    r.work_type_id,
                    r.priority,
                    r.form_id,
                    r.agency_id,
                    esp.entry_stage_id,
                    esp.entry_stage_process_group_id
                FROM report AS r
                    LEFT JOIN report_entry_queue AS req ON (req.report_id = r.report_id)
                    JOIN form AS f ON (f.form_id = IFNULL(req.form_id, r.form_id))
                    JOIN report_status AS rs USING (report_status_id)
                    LEFT JOIN report_entry AS re3 ON (
                        re3.report_id = r.report_id
                        AND re3.entry_status = '" . ReportEntryService::STATUS_IN_PROGRESS . "'
                    )
                    LEFT JOIN report_entry AS re ON (re.report_id = r.report_id)
                    LEFT JOIN report_entry AS re2 ON (
                        re2.report_id = r.report_id
                        AND re2.pass_number > re.pass_number
                    )
                    JOIN entry_stage_process AS esp ON (
                        esp.entry_stage_process_group_id = f.entry_stage_process_group_id
                        AND esp.pass_number = IFNULL(re.pass_number + 1, 1)
                    )
                WHERE re2.report_entry_id IS NULL
                    AND re3.report_entry_id IS NULL
                {$where}
            ";

            if (!empty($action)) {
                $reportQuery = "SELECT * FROM report WHERE report_id = :report_id";
                $reportData = $this->fetchRow($reportQuery, $bind);
                
                $reportEntryQueueQuery = "SELECT * FROM report_entry_queue WHERE report_id = :report_id";
                $reportEntryQueueData = $this->fetchRow($reportEntryQueueQuery, $bind);

                $reportEntryQuery = "SELECT * FROM report_entry WHERE report_id = :report_id";
                $reportEntryData = $this->fetchAll($reportEntryQuery, $bind);

                $formId = !empty($reportEntryQueueData['form_id']) ? $reportEntryQueueData['form_id'] : $reportData['form_id'];
                $formQuery = "SELECT * FROM form WHERE form_id = :form_id";
                $formBind = ['form_id' => $formId];
                $formData = $this->fetchRow($formQuery, $formBind);
            }

            $entryQueueData = $this->fetchAll($select, $bind);

            $replaceInto = "
                REPLACE INTO report_entry_queue
                    (report_id, work_type_id, priority, form_id, agency_id, entry_stage_id, entry_stage_process_group_id)
                VALUES
                    (:report_id, :work_type_id, :priority, :form_id, :agency_id, :entry_stage_id, :entry_stage_process_group_id)
            ";

            foreach ($entryQueueData as $entryQueueRow) {
                $rowCount += $this->getAdapter()->query($replaceInto, $entryQueueRow)->getAffectedRows();
				
				$reportId = $entryQueueRow ['report_id'];
				$sql = "
					REPLACE INTO report_tat_status
					(report_id, tat_hours)
					SELECT
                    r.report_id,
					r.date_created  + INTERVAL wt.tat_hours HOUR AS tat_hours                   
					FROM report AS r
					LEFT  JOIN work_type AS wt ON wt.work_type_id = r.work_type_id
					WHERE  r.report_id = :report_id   ";
				$bind = ['report_id' => $reportId];

			$this->getAdapter()->query($sql, $bind);
				
            }

            if (!empty($action)) {
                //add log to track affected rows during insert/replace
                $this->logger->log(Logger::DEBUG, $m . " - Total rows affected by insert/replace of report id: " . $bind["report_id"] . " in report_entry_queue table: $rowCount");
            }

            if (empty($rowCount)) {
                $this->logger->log(Logger::DEBUG, $m . " - replace into report_entry_queue failed");
                $this->logger->log(Logger::DEBUG, $m . " - select query: " . $select);
                $this->logger->log(Logger::DEBUG, $m . " - bind: " . print_r($bind, true));
                $this->logger->log(Logger::DEBUG, $m . " - entryQueueData: " . print_r($entryQueueData, true));
                if (!empty($action)) {
                    $this->logger->log(Logger::DEBUG, $m . " - report data before select: " . print_r($reportData, true));
                    $this->logger->log(Logger::DEBUG, $m . " - report_entry_queue data before select: " . print_r($reportEntryQueueData, true));
                    $this->logger->log(Logger::DEBUG, $m . " - report_entry data before select: " . print_r($reportEntryData, true));
                    $this->logger->log(Logger::DEBUG, $m . " - form data before select: " . print_r($formData, true));
                }
            }
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ]';
            $this->logger->log(Logger::ERR, "Exception error: " . $errMsg);
        }

        return $rowCount;
    }

    /*
     * for rekeying
     */
    public function insertForRekey($reportId, $workTypeId, $priority, $newFormId, $agencyId = null)
    {
        $select = $this->getSelect()
            ->from(['esp' => 'entry_stage_process'])
            ->join(['es' => 'entry_stage'], 'esp.entry_stage_id = es.entry_stage_id', [])
            ->join(['espg' => 'entry_stage_process_group'], 'esp.entry_stage_process_group_id = espg.entry_stage_process_group_id', [])
            ->where(["es.name_internal" => EntryStageService::STAGE_REKEY]);
        $rekeyEntryStageGroup =  $this->fetchRow($select);

        $data = [
            'report_id' => $reportId,
            'work_type_id' => $workTypeId,
            'priority' => $priority,
            'form_id' => $newFormId,
            'entry_stage_id' => $rekeyEntryStageGroup['entry_stage_id'],
            'entry_stage_process_group_id' => $rekeyEntryStageGroup['entry_stage_process_group_id']
        ];

        if (!empty($agencyId)) {
            $data['agency_id'] = $agencyId;
        }

        return $this->insert($data);
    }

    /**
     * Removes report from queue entirely.
     *
     * @param integer $reportId
     * @return integer
     */
    public function remove($reportId)
    {
        return $this->delete(['report_id' => $reportId]);
    }
	
	public function removeReportTATStatus($reportId)
    {
		return $this->delete('report_tat_status', ['report_id' => $reportId]);       
    }

    public function unassignUser($userId)
    {
        $this->update([
                'user_id_assigned_to' => null,
                'date_assigned' => null,
            ],
            [
                'user_id_assigned_to' => $userId,
            ]
        );
    }

    public function fetchRowByReportId($reportId)
    {
        $columns = [
            'reportEntryQueueId' => 'report_entry_queue_id',
            'dateQueued' => 'date_queued',
            'dateAssigned' => 'date_assigned',
            'reportId' => 'report_id',
            'workTypeId' => 'work_type_id',
            'formId' => 'form_id',
            'agencyId' => 'agency_id',
            'entryStageId' => 'entry_stage_id',
            'entryStageProcessGroupId' => 'entry_stage_process_group_id',
            'userIdAssignedTo' => 'user_id_assigned_to',
            'reportAssignedTo' => 'report_assigned_to',
            'reportAssignedDate' => 'report_assigned_date'
        ];

        $select = $this->getSelect()
            ->columns($columns)
            ->where('report_id = :report_id');

        $bind = ['report_id' => $reportId];
        return $this->fetchRow($select, $bind);
    }

    /**
     * Finds a report that is available to the specified user.
     *
     * @param string $userId
     * @param integer $workTypeId
     * @param integer $keyingVendorId
     * @param string $rekey
     * @return integer|null
     */
    public function pull($userId, $workTypeId, $keyingVendorId, $rekey)
    {
        $orderBy = $this->getWorkTypeOrderDirection($workTypeId);
        
        $retryLimit = $this->config['app']['reportEntry']['select_retry_limit'];
        for ($i = 0; $i < $retryLimit; $i++) {
            try {
                $this->logger->log(Logger::DEBUG, 'Attempting to pull a report. Try # ' . $i);
                if (isset($rekey) && $rekey == RekeyService::PAPER_ADDITIONAL_KEYING) {
                    $reportIds = $this->getAvailableReportRekeyOnly($userId, $workTypeId, RekeyService::PAPER_ADDITIONAL_KEYING, EntryStageService::STAGE_REKEY, $keyingVendorId, $orderBy);
                }
                elseif (isset($rekey) && $rekey == RekeyService::ELECTRONIC_ADDITIONAL_KEYING) {
                    $reportIds = $this->getAvailableReportRekeyOnly($userId, $workTypeId, RekeyService::ELECTRONIC_ADDITIONAL_KEYING, EntryStageService::STAGE_ELECTRONIC_REKEY, $keyingVendorId, $orderBy);
                }
                else {
                    $reportIds = $this->getAvailableReportNoRekey($userId, $workTypeId, $keyingVendorId, $orderBy);
                }
                if (empty($reportIds)) {
                    $this->logger->log(Logger::DEBUG, 'There are no reports available for this user/workType');
                    return null;
                }
                foreach ($reportIds as $reportId) {
                    $this->logger->log(Logger::DEBUG, 'Attempting to reserve report: ' . $reportId);
                    if ($this->reserveReport($reportId, $userId)) {
                        $this->logger->log(Logger::DEBUG, 'Report Reserved: ' . $reportId);
                        return $reportId;
                    }
                }
            } catch (Exception $e) {
                $this->logger->log(Logger::ERR, 'An exception occurred.' . $e);

                if (stripos($e->getMessage(), 'deadlock') !== false) {
                    usleep(100);
                    continue;
                } else {
                    throw $e;
                }
            }
        }

        throw new Exception('OMGWTFBBQ, Batman! - Robin (you tried to catch a report, but a report ran away)');
    }

    /**
     * Determines the work type direction as per the database.
     *
     * @param integer $workTypeId
     * @return string ASC | DESC
     */
    protected function getWorkTypeOrderDirection($workTypeId)
    {
        $select = $this->getSelect()
            ->from('work_type')
            ->columns(['order_direction'])
            ->where(['work_type_id = :work_type_id']);

        $bind = ['work_type_id' => $workTypeId];

        return $this->fetchCol($select, $bind)[0];
    }

    /**
     *
     * @param integer $userId
     * @param integer $workTypeId
     * @param string $orderBy ASC|DESC
     * @return integer|null
     * @throws UnexpectedValueException
     */
    protected function getAvailableReportRekeyOnly($userId, $workTypeId, $keyingType, $nameInternal, $keyingVendorId, $orderBy = 'ASC')
    {
        if ($orderBy != 'ASC' && $orderBy != 'DESC') {
            throw new UnexpectedValueException('$orderBy is an unknown value.');
        }
        //check assigned report for user in report_entry_queue table
        $select = $this->getSelect();
        $select->from(['u' => 'user']);
        $select->columns([
            'reportId' => $this->getDistinct("req.report_id")
        ]);
        $select->join(['ues' => 'user_entry_stage'], 'ues.user_id = u.user_id', []);
        $select->join(['es' => 'entry_stage'], new Expression("ues.entry_stage_id = es.entry_stage_id AND es.name_internal = '".$nameInternal."'"), []);
        $select->join(['ufp' => 'user_form_permission'], 'ufp.user_id = ues.user_id', []);
        $select->join(['req' => 'report_entry_queue'], new Expression('(req.work_type_id = ufp.work_type_id AND req.form_id = ufp.form_id AND req.entry_stage_id = ues.entry_stage_id AND req.user_id_assigned_to IS NULL)'), []);
        $select->join(['re' => 'report_entry'], '( re.report_id = req.report_id AND re.user_id = u.user_id )', [], Select::JOIN_LEFT);
        $select->join(['rpt' => 'report'], 'rpt.report_id = req.report_id', []);
        $select->join(['re2' => 'report_entry'], 're2.report_id = req.report_id', [], Select::JOIN_LEFT);
        $select->join(['u2' => 'user'], 'u2.user_id = re2.user_id', [], Select::JOIN_LEFT);
        $select->where([
            'req.report_assigned_to = :report_assigned_to',
            'req.work_type_id = :work_type_id'
        ]);
        $select->where('re.report_entry_id IS NULL');
        $select->where("req.form_id in (SELECT rufp.form_id FROM rekey_user_form_permission rufp WHERE user_id = {$userId} AND rufp.type = :keying_type)");
        $select->where('(CASE WHEN es.entry_stage_id > '. EntryStageService::STAGE_ALL_INDEX .' THEN u2.keying_vendor_id = :keying_vendor_id ELSE re2.report_entry_id IS NULL END)');
        $select->order('req.report_assigned_date ASC, req.report_id ASC')->limit(10);
        $bind = [
            'report_assigned_to' => $userId,
            'work_type_id' => $workTypeId,
            'keying_type' => $keyingType,
            'keying_vendor_id' => $keyingVendorId            
        ];
        $result = $this->fetchCol($select, $bind);

        if (empty($result)) {
            $select = $this->getSelect();
            $select->from(['ur' => 'user_report']);
            $select->columns([
                'reportId' =>$this->getDistinct("ur.report_id")
            ]);
            $select->join(['req' => 'report_entry_queue'], new Expression('ur.report_id = req.report_id AND req.user_id_assigned_to IS NULL'), []);
            $select->join(['ues' => 'user_entry_stage'], 'ues.entry_stage_id = req.entry_stage_id', []);
            $select->join(['es' => 'entry_stage'],  new Expression("ues.entry_stage_id = es.entry_stage_id AND es.name_internal = '".$nameInternal."'"), []);
            $select->join(['re' => 'report_entry'], new Expression('re.report_id = ur.report_id AND re.entry_status != "in progress"'), [], Select::JOIN_LEFT);
            $select->join(['re2' => 'report_entry'], 're2.report_id = req.report_id', [], Select::JOIN_LEFT);
            $select->join(['u2' => 'user'], 'u2.user_id = re2.user_id', [], Select::JOIN_LEFT);
            $select->where([
                'ur.user_id = :user_id',
                'req.work_type_id = :work_type_id'
            ]);
            $select->where("req.form_id in (SELECT rufp.form_id FROM rekey_user_form_permission rufp WHERE user_id = {$userId} AND rufp.type = :keying_type)");
            $select->where('(CASE WHEN es.entry_stage_id > '. EntryStageService::STAGE_ALL_INDEX .' THEN u2.keying_vendor_id = :keying_vendor_id ELSE re2.report_entry_id IS NULL END)');
            $select->order('ur.user_report_id ASC')->limit(10);
            $bind = [
                'user_id' => $userId,
                'work_type_id' => $workTypeId,
                'keying_type' => $keyingType,
                'keying_vendor_id' => $keyingVendorId
            ];
            $result = $this->fetchCol($select, $bind);
        }

        //if the assigned reports doesn't have records for user in user_report table
        if (empty($result)) {
            $select = $this->getSelect();
            $select->from(['u' => 'user']);
            $select->columns([
                'reportId' => "req.report_id"
            ], false);
            $select->join(['ues' => 'user_entry_stage'], 'ues.user_id = u.user_id', []);
            $select->join(['es' => 'entry_stage'], new Expression("ues.entry_stage_id = es.entry_stage_id AND es.name_internal = '".$nameInternal."'"), []);
            $select->join(['ufp' => 'user_form_permission'], 'ufp.user_id = ues.user_id', []);
            $select->join(['req' => 'report_entry_queue'], new Expression('(req.work_type_id = ufp.work_type_id AND req.form_id = ufp.form_id AND req.entry_stage_id = ues.entry_stage_id AND req.user_id_assigned_to IS NULL)'), []);
            $select->join(['re' => 'report_entry'], '( re.report_id = req.report_id AND re.user_id = u.user_id)', [], Select::JOIN_LEFT);
            $select->join(['rts' => 'report_tat_status'], '( rts.report_id = req.report_id)', [], Select::JOIN_LEFT);
            $select->join(['re2' => 'report_entry'], 're2.report_id = req.report_id', [], Select::JOIN_LEFT);
            $select->join(['u2' => 'user'], 'u2.user_id = re2.user_id', [], Select::JOIN_LEFT);
            $select->where([
                'u.user_id = :u_user_id',
                'req.work_type_id = :work_type_id'
            ]);
            $select->where('re.report_entry_id IS NULL');
            $select->where("req.form_id in (SELECT rufp.form_id FROM rekey_user_form_permission rufp WHERE user_id = {$userId} AND rufp.type = :keying_type)");
            $select->where('(CASE WHEN es.entry_stage_id > '. EntryStageService::STAGE_ALL_INDEX .' THEN u2.keying_vendor_id = :keying_vendor_id ELSE re2.report_entry_id IS NULL END)');
            $select->order("rts.tat_hours {$orderBy}, req.priority {$orderBy}, req.report_id {$orderBy}")->limit(10);
            $bind = [
                'u_user_id' => $userId,
                'work_type_id' => $workTypeId,
                'keying_type' => $keyingType,
                'keying_vendor_id' => $keyingVendorId
            ];

            $result = $this->fetchCol($select, $bind);
        }
        
        return $result;
    }
    
    protected function getAvailableReportNoRekey($userId, $workTypeId, $keyingVendorId, $orderBy = 'ASC')
    {
        if ($orderBy != 'ASC' && $orderBy != 'DESC') {
            throw new UnexpectedValueException('$orderBy is an unknown value.');
        }
		$result = []; 
        // Selecting reports for current user from report_cru table ordering by priority column         
        if($workTypeId == WorkTypeService::WORK_TYPE_CGF)
        {
            $select = $this->getSelect();
            $select->from(['u' => 'user']);
            $select->columns([
                'reportId' => "req.report_id"
            ], false);
            $select->join(['ues' => 'user_entry_stage'], 'ues.user_id = u.user_id', []);
            $select->join(['es' => 'entry_stage'], new Expression('ues.entry_stage_id = es.entry_stage_id AND es.name_internal != "rekey"'), []);
            $select->join(['ufp' => 'user_form_permission'], 'ufp.user_id = ues.user_id', []);
            $select->join(['req' => 'report_entry_queue'], new Expression('(req.work_type_id = ufp.work_type_id AND req.form_id = ufp.form_id AND req.entry_stage_id = ues.entry_stage_id AND req.user_id_assigned_to IS NULL AND req.report_assigned_to IS NULL)'), []);
            $select->join(['re' => 'report_entry'], '( re.report_id = req.report_id AND re.user_id = u.user_id )', [], Select::JOIN_LEFT);
             $select->join(['rc' => 'report_cru'], new Expression('( rc.report_id = req.report_id AND rc.priority IS NOT NULL)')); 
             $select->join(['ur' => 'user_report'], '( ur.report_id = req.report_id)', [], Select::JOIN_LEFT); 
            $select->where([
                'u.user_id = :user_id',
                'req.work_type_id = :work_type_id'
            ]);
            $select->where('re.report_entry_id IS NULL');
            $select->where('ur.user_report_id IS NULL');
            $select->order("rc.priority DESC, req.priority {$orderBy}, req.report_id {$orderBy}")->limit(10);
            $bind = [
                'user_id' => $userId,
                'work_type_id' => $workTypeId
            ];                        
            $result = $this->fetchCol($select, $bind);            
        }
         
        //Select reserved report for Current user from report_entry_queue table
        if(empty($result))
        {
        $select = $this->getSelect();
        $select->from(['u' => 'user']);
        $select->columns([
            'reportId' => $this->getDistinct("req.report_id")
        ]);
        $select->join(['ues' => 'user_entry_stage'], 'ues.user_id = u.user_id', []);
        $select->join(['es' => 'entry_stage'], new Expression('ues.entry_stage_id = es.entry_stage_id AND es.name_internal != "rekey"'), []);
        $select->join(['ufp' => 'user_form_permission'], 'ufp.user_id = ues.user_id', []);
        $select->join(['req' => 'report_entry_queue'], new Expression('(req.work_type_id = ufp.work_type_id AND req.form_id = ufp.form_id AND req.entry_stage_id = ues.entry_stage_id AND req.user_id_assigned_to IS NULL)'), []);
        $select->join(['re' => 'report_entry'], '(re.report_id = req.report_id AND re.user_id = u.user_id )', [], Select::JOIN_LEFT);
        $select->join(['rpt' => 'report'], 'rpt.report_id = req.report_id', []);
        $select->join(['re2' => 'report_entry'], 're2.report_id = req.report_id', [], Select::JOIN_LEFT);
        $select->join(['u2' => 'user'], 'u2.user_id = re2.user_id', [], Select::JOIN_LEFT);
        $select->where([
            'req.report_assigned_to = :report_assigned_to',
            'req.work_type_id = :work_type_id'
        ]);
        $select->where('re.report_entry_id IS NULL');
        $select->where('(CASE WHEN es.entry_stage_id > '. EntryStageService::STAGE_ALL_INDEX .' THEN u2.keying_vendor_id = :keying_vendor_id ELSE re2.report_entry_id IS NULL END)');
        $select->order('req.report_assigned_date ASC, req.report_id ASC')->limit(10);
        $bind = [
            'report_assigned_to' => $userId,
            'work_type_id' => $workTypeId,
            'keying_vendor_id' => $keyingVendorId
        ];
        $result = $this->fetchCol($select, $bind);
        }
        //Select date based reserved reports for Current user from user_report table 
        if (empty($result)) {
            $select = $this->getSelect();
            $select->from(['ur' => 'user_report']);
            $select->columns([
                'reportId' => $this->getDistinct("ur.report_id")
            ]);
            $select->join(['req' => 'report_entry_queue'], new Expression('ur.report_id = req.report_id AND req.user_id_assigned_to IS NULL'), []);
            $select->join(['ues' => 'user_entry_stage'], 'ues.entry_stage_id = req.entry_stage_id', []);
            $select->join(['es' => 'entry_stage'], new Expression('ues.entry_stage_id = es.entry_stage_id AND es.name_internal != "rekey"'), []);
            $select->join(['re' => 'report_entry'], new Expression('re.report_id = ur.report_id AND re.entry_status!= "in progress"'), [], Select::JOIN_LEFT);
            $select->join(['re2' => 'report_entry'], 're2.report_id = req.report_id', [], Select::JOIN_LEFT);
            $select->join(['u2' => 'user'], 'u2.user_id = re2.user_id', [], Select::JOIN_LEFT);
            $select->where([
                'ur.user_id = :user_id',
                'req.work_type_id = :work_type_id'
            ]);
            $select->where('(CASE WHEN es.entry_stage_id > '. EntryStageService::STAGE_ALL_INDEX .' THEN u2.keying_vendor_id = :keying_vendor_id ELSE re2.report_entry_id IS NULL END)');
            $select->order('ur.user_report_id ASC')->limit(10);
            $bind = [
                'user_id' => $userId,
                'work_type_id' => $workTypeId,
                'keying_vendor_id' => $keyingVendorId
            ];
            $result = $this->fetchCol($select, $bind);
        }

        //SLA, priority and FIFO ordered Reports
        if (empty($result)) {
            $select = $this->getSelect();
            $select->from(['u' => 'user']);
            $select->columns([
                'reportId' => "req.report_id"
            ], false);
            $select->join(['ues' => 'user_entry_stage'], 'ues.user_id = u.user_id', []);
            $select->join(['es' => 'entry_stage'], new Expression('ues.entry_stage_id = es.entry_stage_id AND es.name_internal != "rekey"'), []);
            $select->join(['ufp' => 'user_form_permission'], 'ufp.user_id = ues.user_id', []);
            $select->join(['req' => 'report_entry_queue'], new Expression('(req.work_type_id = ufp.work_type_id AND req.form_id = ufp.form_id AND req.entry_stage_id = ues.entry_stage_id AND req.user_id_assigned_to IS NULL AND req.report_assigned_to IS NULL)'), []);
            $select->join(['re' => 'report_entry'], '( re.report_id = req.report_id AND re.user_id = u.user_id )', [], Select::JOIN_LEFT);
            $select->join(['rts' => 'report_tat_status'], '( rts.report_id = req.report_id)', [], Select::JOIN_LEFT);
            $select->join(['re2' => 'report_entry'], 're2.report_id = req.report_id', [], Select::JOIN_LEFT);
            $select->join(['u2' => 'user'], 'u2.user_id = re2.user_id', [], Select::JOIN_LEFT);
            $select->join(['ur' => 'user_report'], '( ur.report_id = req.report_id)', [], Select::JOIN_LEFT); 

            $select->where([
                'u.user_id = :user_id',
                'req.work_type_id = :work_type_id'
            ]);
            $select->where('re.report_entry_id IS NULL');
            $select->where('(CASE WHEN es.entry_stage_id > '. EntryStageService::STAGE_ALL_INDEX .' THEN u2.keying_vendor_id = :keying_vendor_id ELSE re2.report_entry_id IS NULL END)');
            $select->where('ur.user_report_id IS NULL');
			$select->order("rts.tat_hours {$orderBy}, req.priority {$orderBy}, req.report_id {$orderBy}")->limit(10);

            $bind = [
                'user_id' => $userId,
                'work_type_id' => $workTypeId,
                'keying_vendor_id' => $keyingVendorId
            ];
            $result = $this->fetchCol($select, $bind);
        }
        
        return $result;
    }

    /**
     * Reserves a report, using special logic to ensure no one else has already grabbed it.
     * This also handles setting the entryStageProcessGroupId & entryStageId if not already set.
     *
     * @param integer $reportId
     * @return boolean
     */
    protected function reserveReport($reportId, $userId)
    {
        $data = ['user_id_assigned_to' => $userId, 'date_assigned' => $this->getNowExpr()];
        $where = ['report_id' => $reportId, 'user_id_assigned_to IS NULL'];
        
        return ($this->update($data, $where) > 0);
    }

    /**
    * Remove User Report record by report_id and user_id
    * @param integer $reportId
    * @param integer $userId
    * @return integer
    */
    public function removeUserReportRecord($reportId, $userId = null)
    {
        if (!empty($userId) && !empty($reportId)) {
            return $this->delete('user_report', [
                'user_id' => $userId,
                'report_id' => $reportId
            ]);
        } elseif (!empty($reportId)) {
            return $this->delete('user_report', ['report_id' => $reportId]);
        }
    }
    
    /** 
    * UnAssign the report to user after completion of pass1 or pass2
    * @param integer $userId
    * @param integer $reportId
    * @return integer
    *
    */
    public function unAssignReport($userId, $reportId)
    {
        $this->update([
                'report_assigned_to' => new Expression('NULL'),
                'report_assigned_date' => new Expression('NULL'),
            ],
            [
                'report_assigned_to' => $userId,
                'report_id' => $reportId,
            ]
        );
    }
    
    /**
    * fetch the report details with entry stage details
    * @param integer $reportId
    * @return array
    */
    public function getReportDetails($reportId)
    {
        $columns = [
            'reportId' => 'req.report_id',
            'nameInternal' => 'es.name_internal',
            'formId' => 'req.form_id',
        ];
        $select = $this->getSelect()
            ->columns($columns, false)
            ->from(['req' => 'report_entry_queue'])
            ->join(['es' => 'entry_stage'], 'es.entry_stage_id = req.entry_stage_id', [], 'Left')
            ->where('req.report_id = :report_id');
        
        return $this->fetchAll($select, ['report_id' => $reportId]);
    }
    
    /**
    * check the availability of the report before assigning to user
    * @param integer $reportId
    * @return integer
    */
    public function checkReportAvailabilityForAssign($reportId)
    {
        $select = $this->getSelect()
            ->columns(['report_id'])
            ->where('report_id = :report_id')
            ->where('user_id_assigned_to IS NULL')
            ->where('report_assigned_to IS NULL');
            
        return $this->fetchOne($select, ['report_id' => $reportId]);
    }
    
    /**
     * fetch the assigned user Reports to display it in users module
     * @param integer $userId
     * @return array
     */
    public function fetchReservedReport($userId)
    {
        $columns = [
            'reportId' => 'req.report_id',
            'assignedDate' => 'req.report_assigned_date',
            'formName' => 'f.name_external',
            'workTypeExternal' => 'wt.name_external'
        ];
        $select = $this->getSelect()
            ->columns($columns, false)
            ->from(['req' => 'report_entry_queue'])
            ->join(['wt' => 'work_type'], 'wt.work_type_id = req.work_type_id')
            ->join(['f' => 'form'], 'f.form_id = req.form_id', ["formId" => "form_id"], Select::JOIN_LEFT)
            ->join(["s" => "state"], "s.state_id = f.state_id", ["stateAbbr" => "name_abbr"], Select::JOIN_LEFT)
            ->join(["a" => "agency"], "a.agency_id = req.agency_id", ["agencyName" => "name"], Select::JOIN_LEFT)
            ->where('req.report_assigned_to = :report_assigned_to');
        
        return $this->fetchAll($select, ['report_assigned_to' => $userId]);
    }
    
    /**
     * check user has permission to view the report 
     * @param integer $userId
     * @param integer $reportId
     * @return array
     */
    public function checkUserEntryStageForReport($userId, $reportId)
    {
        $columns = [
            'reportId' => 'req.report_id',
            'entryStage' => 'ues.entry_stage_id',
            'userId' => 'ues.user_id',
        ];
        $select = $this->getSelect()
            ->columns($columns, false)
            ->from(['req' => 'report_entry_queue'])
            ->join(['ues' => 'user_entry_stage'], 'ues.entry_stage_id = req.entry_stage_id')
            ->where('req.report_id = :report_id')
            ->where('ues.user_id = :user_id');
        $bind = [
            'report_id' => $reportId,
            'user_id' => $userId
        ];
        
        return $this->fetchAll($select, $bind);
    }
    
    /**
     * Reserves a report, by assigning the report while editing the user.
     * 
     * @param integer $reportId
     * @param interger $userId
     * @return integer
     */
    public function reserveAssignedReport($reportId, $userId)
    {
        $this->update(
            [
                'report_assigned_to' => $userId,
                'report_assigned_date' => $this->getNowExpr()
            ],
            [
                'report_id' => $reportId,
                'user_id_assigned_to IS NULL',
                'report_assigned_to IS NULL'
            ]
        );
    }    
    
    /**
     * insert Assigned report to user_report  table
     * @param integer $userId
     * @param integer $reportId
     * @param integer $formId
     * @return integer
     */
    public function insertAssignedReport($userId, $reportId)
    {
        return $this->insert('user_report', [
            'user_id' => $userId,
            'report_id' => $reportId
        ]);
    }

    /**
     * fetch the assigned user Reports to display it in users module
     * @param integer $userId
     * @return array
     */
    public function fetchDateRangeAssignedReports($userId)
    {
        $columns = [
            'reportId' => 'ur.report_id',            
            'formName' => 'f.name_external',
            'workTypeExternal' => 'wt.name_external'
        ];
        $select = $this->getSelect()
            ->columns($columns, false)
            ->from(['ur' => 'user_report'])
            ->join(['req' => 'report_entry_queue'], 'req.report_id = ur.report_id')
            ->join(['wt' => 'work_type'], 'wt.work_type_id = req.work_type_id')
            ->join(['f' => 'form'], 'f.form_id = req.form_id',["formId" => "form_id"])
            ->join(["s" => "state"], "s.state_id = f.state_id", ["stateAbbr" => "name_abbr"], Select::JOIN_LEFT)
            ->join(["a" => "agency"], "a.agency_id = req.agency_id", ["agencyName" => "name"], Select::JOIN_LEFT)
            ->where('ur.user_id = :user_id');
        
        return $this->fetchAll($select, ['user_id' => $userId]);
    }

    /**
     * Counts the number of queued and queuable reports for every distinct combination of workType/form/agency/entryStage.
     *
     * @return array array(array('workTypeId' => ..., 'formId' => ..., 'agencyId' => ..., 'entryStageId' => ..., 'countQueuable' => ..., 'countQueued' => ...), ...);
     */
    public function fetchEntryQueuingStatistics()
    {
        $sql = "
            SELECT
                r.work_type_id AS workTypeId,
                IFNULL(req.form_id, r.form_id) AS formId,
                r.agency_id AS agencyId,
                esp.entry_stage_id AS entryStageId,
                wt.order_direction AS orderDirection,
                SUM(req.report_entry_queue_id IS NULL) AS countQueuable,
                SUM(req.report_entry_queue_id IS NOT NULL) AS countQueued
            FROM report AS r
                LEFT JOIN report_entry_queue AS req ON (req.report_id = r.report_id)
                JOIN form AS f ON (f.form_id = IFNULL(req.form_id, r.form_id))
                JOIN report_status AS rs USING (report_status_id)
                JOIN work_type AS wt ON (wt.work_type_id = r.work_type_id)
                LEFT JOIN report_entry AS re3 ON (
                    re3.report_id = r.report_id
                    AND re3.entry_status = '" . ReportEntryService::STATUS_IN_PROGRESS . "'
                )
                LEFT JOIN report_entry AS re ON (re.report_id = r.report_id)
                LEFT JOIN report_entry AS re2 ON (
                    re2.report_id = r.report_id
                    AND re2.pass_number > re.pass_number
                )
                JOIN entry_stage_process AS esp ON (
                    esp.entry_stage_process_group_id = f.entry_stage_process_group_id
                    AND esp.pass_number = IFNULL(re.pass_number + 1, 1)
                )
            WHERE rs.name = ?
                AND re2.report_entry_id IS NULL
                AND re3.report_entry_id IS NULL
            GROUP BY workTypeId, formId, agencyId, entryStageId
        ";
        $bind = [ReportStatusService::STATUS_KEYING];

        return $this->fetchAll($sql, $bind);
    }

    /**
     * @param integer $workTypeId
     * @param integer $formId
     * @param integer $agencyId
     * @param integer $entryStageId
     * @param integer $limit
     * @return integer
     */
    public function queueNewReports($workTypeId, $formId, $agencyId, $entryStageId, $orderBy, $limit)
    {
        if ($orderBy != 'ASC' && $orderBy != 'DESC') {
            throw new UnexpectedValueException('$orderBy is an unknown value.');
        }
        $limit = (int)$limit;
        $where = "
                AND rs.name = ?
                AND req.report_entry_queue_id IS NULL
                AND r.work_type_id = ?
                AND r.form_id = ?
                AND r.agency_id <=> ?
                AND esp.entry_stage_id = ?
            ORDER BY priority {$orderBy}, report_id {$orderBy}
            LIMIT {$limit}
        ";
        $bind = [ReportStatusService::STATUS_KEYING, $workTypeId, $formId, $agencyId, $entryStageId];
        
        return $this->insertOrReplace($where, $bind);
    }
}

