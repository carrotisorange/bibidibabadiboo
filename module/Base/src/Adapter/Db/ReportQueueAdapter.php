<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter as PaginationAdapter;

use Base\Service\ReportEntryQueueService;
use Base\Adapter\Db\ReportQueueHistoryAdapter;
use Base\Service\EntryStageService;
use Base\Adapter\Db\EntryStageAdapter;
use Base\Service\KeyingVendorService;

class ReportQueueAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'report_queue';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter, 
        ReportQueueHistoryAdapter $adapterReportQueueHistory,
        EntryStageAdapter $adapterEntryStage)
    {
        parent::__construct($adapter, $this->table);
        $this->adapter = $adapter;
        $this->adapterReportQueueHistory = $adapterReportQueueHistory;
        $this->adapterEntryStage = $adapterEntryStage;
    }
    
    public function selectAllByQueue($queueName)
    {
        $select = $this->getSelect();
        $select->from(['rq' => 'report_queue']);
        $select->where(['rq.queue_name' => $queueName]);
        $select->order(['rq.priority']);

        return $select;
    }

    public function pull($queueName, $userId, $keyingVendorId)
    {
        $sql = "
            UPDATE report_queue rq 
            INNER JOIN
            (
              SELECT rq2.report_queue_id 
              FROM report_queue AS rq2
              JOIN user AS u ON u.user_id=rq2.user_id_queued_by 
              WHERE rq2.queue_name = :queue_name
              AND rq2.user_id_assigned_to IS NULL 
              AND u.keying_vendor_id = :keying_vendor_id
              ORDER BY rq2.priority ASC 
              LIMIT 1
            ) AS rq3
            ON rq.report_queue_id=rq3.report_queue_id 
            SET rq.user_id_assigned_to = :user_id,
                rq.date_assigned = NOW()
        ";
        $bind = ['queue_name' => $queueName, 'keying_vendor_id' => $keyingVendorId, 
                 'user_id' => $userId];
        $result = $this->adapter->query($sql, $bind);

        if ($result->getAffectedRows() > 0) {
            return $this->getSelectedReportId($queueName, $userId, $keyingVendorId);
        } else {
            return false;
        }
    }
    
    public function remove($queueName, $reportId)
    {
        return $this->delete([
            'report_id' => $reportId,
            'queue_name' => $queueName,
        ]);
    }
    
    protected function getSelectedReportId($queueName, $userId, $keyingVendorId)
    {
        $select = $this->getSelect()
            ->from(['rq' => $this->table])
            ->join(['u' => 'user'], 'u.user_id = rq.user_id_assigned_to', [])
            ->columns(['rq.report_id' => 'report_id'])
            ->where(["rq.queue_name = :queue_name", "rq.user_id_assigned_to = :user_id", 
                "u.keying_vendor_id = :keying_vendor_id"])
            ->limit(1);
        $bind = ['queue_name' => $queueName, 'user_id' => $userId, 
            'keying_vendor_id' => $keyingVendorId];

        return $this->fetchOne($select, $bind);
    }

    /**
     *
     * @param string $queueName
     * @param integer $reportId
     * @param integer $userId
     */
    public function assign($queueName, $reportId, $userId)
    {
        $data['user_id_assigned_to'] = $userId;
        $data['date_assigned'] = $this->getNowExpr();

        $this->update($data, [
            'queue_name' => $queueName,
            'report_id' => $reportId,
        ]);
        
        $this->adapterReportQueueHistory->copyReportQueueToHistory($queueName, $reportId, ReportEntryQueueService::REMOVAL_REASON_PULLED);
    }

    /**
     * Gets basic information about a report (form, type, status, etc)
     *
     * @param integer $reportId
     * @return array
     */
    public function getRelatedInfo($reportId)
    {
        $select = $this->getSelect()
            ->from(["r" => $this->table])
            ->columns([
                "reportId" => "report_id",
                "isObsolete" => "is_obsolete",
                "reportIdObsoletedBy" => "report_id_obsoleted_by",
                "priority" => "priority",
                "newFormId" => "form_id"
            ])
            ->join(["s" => "state"], "s.state_id = r.state_id", ["stateId" => "state_id", "stateAbbr" => "name_abbr", "stateFull" => "name_full"], Select::JOIN_LEFT)
            ->join(["wt" => "work_type"], "wt.work_type_id = r.work_type_id", ["workTypeId" => "work_type_id", "workTypeInternal" => "name_internal", "workTypeExternal" => "name_external"])
            ->join(["a" => "agency"], "a.agency_id = r.agency_id", ["agencyId" => "agency_id", "agencyName" => "name"], Select::JOIN_LEFT)
            ->join(["ft" => "form_type"], "ft.form_type_id = r.form_type_id", ["formTypeId" => "form_type_id", "formTypeCode" => "code", "formTypeDescription" => "description"])
            ->join(["f" => "form"], "f.form_id = r.form_id", ["formId" => "form_id", "formName" => "name_external", "entryStageProcessGroupId" => "entry_stage_process_group_id"])
            ->join(["ftmpl" => "form_template"], "ftmpl.form_template_id = f.form_template_id", [])
            ->join(["fs" => "form_system"], "fs.form_system_id = ftmpl.form_system_id", ["formSystem" => "name_internal"])
            ->join(["re" => "report_entry"], new Expression("r.report_id = re.report_id AND re.entry_status = 'complete'"), ["oldFormId" => "form_id"], Select::JOIN_LEFT)
            ->join(["re2" => "report_entry"], new Expression("re2.report_id = re.report_id AND RE2.date_updated > RE.date_updated AND RE2.entry_status = 'complete'"), [], Select::JOIN_LEFT)
            ->where("r.report_id = :report_id")
            ->where('( re.report_entry_id IS NULL OR re2.report_entry_id IS NULL )');

        $bind = ['report_id' => $reportId];
        $info = $this->fetchRow($select, $bind);

        $select_flag = $this->getSelect()
            ->from(["rf" => "report_flag"])
            ->columns([])
            ->join(["f" => "flag"], "rf.flag_id = f.flag_id", ["flag" => "name"])
            ->where("rf.report_id = :report_id");

        $bind = ['report_id' => $reportId];
        $flags = $this->fetchAll($select_flag, $bind);
        if (count($flags) > 0) {
            foreach($flags as $flag) {
                $info['flags'][] = $flag['flag'];
            }
        } else {
            $info['flags'] = [];
        }
        
        return $info;
    }

    public function selectbyQueue($queueName)
    {
        $select = $this->selectAllByQueue($queueName);
        $select->join(['r' => 'report'], 'r.report_id = rq.report_id', []);
        $select->join(['rs' => 'report_status'], 'rs.report_status_id = r.report_status_id', []);
        $select->join(['rm' => 'report_metadata'], 'rm.report_id = rq.report_id', [], Select::JOIN_LEFT);
        $select->join(['v' => 'vendor'], 'v.vendor_id = rm.vendor_id', [], Select::JOIN_LEFT);
        $select->join(['u' => 'user'], 'u.user_id = rq.user_id_queued_by', [], Select::JOIN_LEFT);

        return $this->addResultCols(
            $select
        );
    }

    public function selectByStatus($status)
    {
        $select = $this->getSelect();
        $select->from(['r' => 'report']);
        $select->join(['rs' => 'report_status'], 'rs.report_status_id = r.report_status_id', []);
        $select->join(['rm' => 'report_metadata'], 'rm.report_id = r.report_id', [], Select::JOIN_LEFT);
        $select->join(['v' => 'vendor'], 'v.vendor_id = rm.vendor_id', [], Select::JOIN_LEFT);
        $select->join(['u' => 'user'], 'u.user_id = r.user_id_status_set_by', [], Select::JOIN_LEFT);
        $select->where(['rs.name' => $status]);

        return $this->addResultCols(
            $select
        );
    }

    public function addInputCriteria(Select $select, $criteria)
    {
        $bind = [];
        foreach ($criteria as $key => $value) {
            if (empty($value)) {
                continue;
            }

            switch ($key) {
                case 'operatorFirstName':
                    $select->where('u.name_first = :u_name_first');
                    $bind['u_name_first'] = $value;
                    break;

                case 'operatorLastName':
                    $select->where('u.name_last = :u_name_last');
                    $bind['u_name_last'] = $value;
                    break;
                
                case 'keyingVendorId':
                    if ($value != KeyingVendorService::VENDOR_ALL) {
                        $select->where('kv.keying_vendor_id = :kv_keying_vendor_id');
                        $select->where('(CASE WHEN esp.entry_stage_id > '. EntryStageService::STAGE_ALL_INDEX .' THEN u2.keying_vendor_id = :kv_keying_vendor_id ELSE re3.report_entry_id IS NULL END)');
                        $bind['kv_keying_vendor_id'] = $value;
                    }
                    break;
                
                case 'reportId':
                    $select->where('r.report_id = :r_report_id');
                    $bind['r_report_id'] = $value;
                    break;

                case 'vendorCode':
                    $select->where('v.vendor_id = :v_vendor_id');
                    $bind['v_vendor_id'] = $value;
                    break;
                
                case 'entryStage':
                    $value = (array) $value;
                    $entryStageIdRekey = $this->adapterEntryStage->getIdByInternalName(EntryStageService::STAGE_REKEY);                    
                    $nestSelect = $select->where->nest->In("esp.entry_stage_id", $value);
                    if (in_array($entryStageIdRekey, $value)) {
                        $nestSelect->where->or                               
                            ->NotequalTo(new Expression('re.form_id'), new Expression('r.form_id'))
                            ->unnest;
                    }                                        
                    break;
            }
        }
        $result = $this->fetchAll($select, $bind);
        $paginator = new Paginator(new PaginationAdapter\ArrayAdapter($result));

        return $paginator;
    }

    public function addResultCols(Select $select)
    {
        $select->join(['re' => 'report_entry'], 're.report_id = r.report_id', [], Select::JOIN_LEFT);
        $select->join(['re2' => 'report_entry'], 're2.report_id = r.report_id AND re2.date_created > re.date_created', [],  Select::JOIN_LEFT);
        $select->join(['rsh' => 'report_status_history'], 'rsh.report_id = r.report_id', [], Select::JOIN_LEFT);
        $select->join(['rsh2' => 'report_status_history'], new Expression('rsh2.report_id = r.report_id AND rsh2.report_status_history_id > rsh.report_status_history_id'), [], Select::JOIN_LEFT);
        $select->join(['f' => 'form'], 'f.form_id = r.form_id', []);
        $select->join(['esp' => 'entry_stage_process'], new Expression('esp.pass_number = IFNULL(re.pass_number, 0) + 1 AND esp.entry_stage_process_group_id = f.entry_stage_process_group_id'), [], Select::JOIN_LEFT);
        $select->join(['a' => 'agency'], 'a.agency_id = r.agency_id', [], Select::JOIN_LEFT);
        $select->join(['s' => 'state'], 's.state_id = r.state_id', [], Select::JOIN_LEFT);
        $select->join(['kv' => 'keying_vendor'], 'u.keying_vendor_id = kv.keying_vendor_id', []);
        $select->join(['re3' => 'report_entry'], 're3.report_id = r.report_id', [],  Select::JOIN_LEFT);
        $select->join(['u2' => 'user'], 'u2.user_id = re3.user_id', [], Select::JOIN_LEFT);
        $select->where('re2.report_entry_id IS NULL');
        $select->where('rsh2.report_status_history_id IS NULL');
        $select->columns([
            'reportId' => 'r.report_id',
            'status' => 'rs.name',
            'lastName' => 'u.name_last',
            'firstName' => 'u.name_first',
            'dateEntered' => new Expression('DATE(rsh.date_created)'),
            'passGroup' => new Expression('IFNULL(re.pass_number, 0) + 1'),
            'agencyId' => 'a.agency_id',
            'agencyName' => new Expression('COALESCE(a.name, rm.agency_ori, "n/a")'),
            'stateId' => 's.state_id',
            'stateAbbr' => new Expression('IFNULL(s.name_abbr, "n/a")'),
            'filename' => new Expression('IFNULL(rm.filename, "n/a")'),
            'vendorName' => new Expression('IFNULL(kv.vendor_name, "n/a")'),
        ], false);

        return $select;
    }

    public function recycle()
    {
        $sql = "
            UPDATE report_queue AS rq
            SET rq.user_id_assigned_to = NULL,
                date_assigned = NULL
            WHERE user_id_assigned_to IS NOT NULL
                AND date_assigned < NOW() - INTERVAL 1 HOUR
        ";
        
        $this->adapter->createStatement($sql)->execute();
    }
    
    public function unassignUser($userId)
    {
        $this->update(
            [
                'user_id_assigned_to' => null,
                'date_assigned' => null,
            ],
            [
                'user_id_assigned_to' => $userId,
            ]
        );
    }
}