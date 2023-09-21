<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Base\Service\QualityControlRemarkService;
use Base\Service\ReportEntryService;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class QualityControlRemarkAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'qc_remark';
    const NO_ISSUE_FIELD = 'no_issue';

    public $qcRemarks = [];
    public $data = [];

    public function __construct($adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    /**
     * @param
     * reportData :: General Information
     * fields with remark values
     */
    public function addOrUpdate($reportData = [], $fields = [])
    {
        $retVal = false;
        /**
         * get report for 
         * additional details needed for saving fields
         */
        $report = $this->getReport($reportData['reportId'], QualityControlRemarkService::FETCH_DEFAULT);

        if (!$report) {
            return false;
        }

        //get report remarks
        $this->qcRemarks = $this->getRemarks([
            'reportId' => $reportData['reportId']
        ]);
        $qcRemarkFields = [];
        /**
         * Store Qc fields if already exists
         * use this to check wheter to insert ot udpate the field
         */
        if ($this->qcRemarks) {
            foreach ($this->qcRemarks as $key => $row) {
                $qcRemarkFields[$row['field_name']] = $row;
            }
        }
        $affectedRows = [];
        
        foreach ($fields as $key => $row) {
            $criticality = trim($row['criticality']);
            $fieldName = trim($row['key']);
            $remarkValue = trim($row['value']);
            $passValue = trim($row['passValue']);
            $data = [
                'report_id' => $report['report_id'],
                'form_id' => $report['form_type_id'],
                'state_id' => $report['state_id'],
                'field_name' => $fieldName,
                'criticality' => $criticality,
                'pass_value' => $passValue,
                'remark_value' => $remarkValue,
                'report_entry_date_created' => $report['date_created'],
                'created_by'  => $reportData['userId']
            ];
            
            if (!empty($qcRemarkFields)) {
                //check if report is previously tagged as no issue if so then delete that row
                if (in_array('no_issue', array_keys($qcRemarkFields))) {
                    $this->delete([
                        'field_name' => 'no_issue',
                        'report_id'  => $report['report_id']
                    ]);
                }
                //update if field name already exists
                if (in_array($fieldName, array_keys($qcRemarkFields))) {
                    $retVal = $this->update($data, [
                        'field_name' => $fieldName,
                        'report_id'  => $report['report_id']
                    ]); 
                } else {
                    $retVal = $this->insert($data);
                }
            } else {
                $retVal = $this->insert($data);
            }
            
            if ($retVal) {
                array_push($affectedRows, $data);
            }
        }
        $this->data['fieldRemarks'] = $affectedRows;

        return true;
    }
    
    public function noissue($reportId, $userId)
    {
        $report = $this->getReport($reportId);

        if (!$report) {
            return false;
        }
        
        return $this->insert([
            'report_id' => $reportId,
            'form_id'   => $report['form_type_id'],
            'state_id'  => $report['state_id'],
            'field_name' => self::NO_ISSUE_FIELD,
            'criticality' => self::NO_ISSUE_FIELD,
            'pass_value'  => '',
            'remark_value' => '',
            'report_entry_date_created' => $report['date_created'],
            'created_by' => $userId
        ]);    
    }
    
    public function getRemarks($params = [])
    {
        /**
         * get remarks by report order by report_id
         */
        $select = $this->getSelect();
        $select->from(['qc' => $this->table])
        ->join(['u' => 'user'], 'qc.created_by = u.user_id', ['name_first','name_last' , 'username'], Select::JOIN_LEFT)
        ->join(['r' => 'report'], 'qc.report_id = r.report_id', ['workType' => 'work_type_id'], Select::JOIN_LEFT)
        ->join(['re' => 'report_entry'], 'qc.report_id = re.report_id', ['keyerId' => 'user_id'], Select::JOIN_LEFT)
        ->join(['keyer' => 'user'], 're.user_id = keyer.user_id', ['keyerUsername' => 'username'], Select::JOIN_LEFT)
        ->join(['state' => 'state'], 'state.state_id = r.state_id', ['stateAbbr' => 'name_abbr'], Select::JOIN_LEFT)
        ->join(['work_type' => 'work_type'], 'r.work_type_id = work_type.work_type_id', ['workTypeName' => 'name_external'], Select::JOIN_LEFT);

        $filter = $this->getRemarkFilter($params);
        $bind = null;
        
        if (!empty($filter['conditions'])) {
            $conditions = $filter['conditions'];
            $bind = $filter['bind'];
            foreach ($conditions as $key => $row) {
                $select->where($row);
            }
        }

        $select->where('re.pass_number = :pass_number');
        $bind['pass_number'] = 2;
        $select->group('qc.id');

        if (isset($params['order'])) {
            $select->order($params['order']);
        }
        
        return $this->fetchAll($select, $bind);
    }


    public function getReportOpenedByUser($userId)
    {
        $select = $this->getSelect();
        $select->from('qc_report_queue');
        $select->where('user_id = :userId');

        return $this->fetchRow($select, [
            'userId' => $userId
        ]);
    }
    
    public function getReport($reportId, $fetchOption = QualityControlRemarkService::FETCH_DEFAULT)
    {
        $retVal = null;
        $report = $this->getReports([
            'conditions' => [
                'reportId' => $reportId
            ]
        ], $fetchOption);

        if (!$report) {
            return false;
        }
        
        //fetch report from qc
        $select = $this->getSelect();
        $select->from( $this->table )
        ->where('report_id = :reportId');
        $report_qc = $this->fetchRow($select, ['reportId' => $reportId]);

        switch ($fetchOption) {
            case QualityControlRemarkService::FETCH_DEFAULT:
                $retVal = $report;
            break;
            case QualityControlRemarkService::FETCH_NONE_AUDITED:
                /**
                 * return false because the report is already audited
                 */
                if ($report_qc) {
                    $retVal = false;
                }
            break;
            case QualityControlRemarkService::FETCH_AUDITED:
                /**
                 * Do not show if report is not yet audited
                 */
                if ($report_qc) {
                    $retVal = false;
                }
            break;
        }
        return is_bool($retVal) ? $retVal : $retVal[0];
    }

    public function getReports($params = [], $fetchOption = QualityControlRemarkService::FETCH_DEFAULT) {
        $select = $this->getSelect();
        $select->from(['re' => 'report_entry'], '*')
        ->join(['red' => 'report_entry_data'], 'red.report_id = re.report_id and red.report_entry_id = re.report_entry_id')
        ->join('form', 're.form_id = form.form_id', ['form_type_id'], SELECT::JOIN_LEFT)
        ->join('form_type', 'form_type.form_type_id = form.form_type_id', ['description'], SELECT::JOIN_LEFT)
        ->join('state','form.state_id = state.state_id', ['state' => 'name_abbr', 'state_full' => 'name_full', 'state_id'], SELECT::JOIN_LEFT)
        ->join(['req' => 'report_entry_queue'], 're.report_id = req.report_id', [], SELECT::JOIN_LEFT)
        ->join(['r' => 'report'], 're.report_id = r.report_id', ['work_type_id'], SELECT::JOIN_LEFT)
        ->join(['wt' => 'work_type'], 'r.work_type_id = wt.work_type_id', ['name_external'], SELECT::JOIN_LEFT)
        ->join('qc_remark', 're.report_id = qc_remark.report_id',['pass_value','remark_value','criticality'], SELECT::JOIN_LEFT);

        /**
         * if qc_remark.report_id is null because
         * we are fetching reports not yet qcied
         */
        if ($fetchOption != QualityControlRemarkService::FETCH_DEFAULT) {
            if ($fetchOption == QualityControlRemarkService::FETCH_AUDITED) {
                $select->where('qc_remark.report_id is not null');
            } else {
                $select->where('qc_remark.report_id is null');
            }
        }
        
        if (!empty($params['conditions'])) {
            $conditions = $params['conditions'];

            if (!empty($conditions['reportId'])) {
                $select->where("re.report_id = '{$conditions['reportId']}'");
                $bind['reportId'] = $conditions['reportId'];
            }

            if (isset($conditions['fromDate'])) {
                $select->where("re.date_created >= '{$conditions['fromDate']}'");
            }
            if (isset($conditions['toDate'])) {
                $select->where("re.date_created <= '{$conditions['toDate']}'");
            }
            if (!empty($conditions['workType'])) {
                $select->where("r.work_type_id = '{$conditions['workType']}'");
            }
            if (!empty($conditions['state'])) {
                $select->where("r.state_id = '{$conditions['state']}'");
            }
        }
        

        if (isset($params['order'])) {
            if($params['order'] == 'random') {
                $select->order(new Expression('RAND()'));
            }else{
                $select->order($params['order']);
            }
        }
        
        $select->where('re.pass_number = 2');
        if (isset($params['limit'])) {
            $select->limit($params['limit']);
        }
        return $this->fetchAll($select);
    }
    
    /**
     * for qc queue
     * @param
     * valid values type load/unload
     */
    public function loadOrUnloadReport($reportId, $userId, $type = 'load')
    {
        $retVal = false;
        $table = 'qc_report_queue';
        $report = $this->getReportFromQueue($reportId);

        if ($type == 'load') {
            if (!$report) {
                $this->insert($table, [
                    'report_id' => $reportId,
                    'user_id' => $userId,
                ]);
                $retVal = true;
            } else if ($report && ($report['user_id'] != $userId)){
                $retVal = false;
            } else {
                $retVal = true;
            }   
        } else if ($type == 'unload') {
            if ($report) {
                $this->delete($table, [
                    'report_id' => $reportId
                ]);
                $retVal = true;
            }
        }

        return $retVal;
    }

    public function unloadUserReport($userId)
    {
        $table = 'qc_report_queue';
        $select = $this->getSelect();
        $select->from($table);
        $select->where('user_id = :userId');
        $reports = $this->fetchAll($select, [
            'userId' => $userId
        ]);

        if(!$reports) {
            return false;
        }

        return $this->delete($table, [
            'user_id' => $userId
        ]);
    }

    public function getRemarkFilter($condition = [])
    {
        $where = [
            'conditions' => [],
            'bind'   => []
        ];

        if (!empty($condition['fromDate']) && !empty($condition['toDate'])) {
            $where = [
                'conditions' => [
                    'report_entry_date_created >= :fromDate',
                    'report_entry_date_created <= :toDate',
                ],
                'bind' => [
                    'fromDate' => $condition['fromDate'],
                    'toDate' => $condition['toDate'],
                ]
            ];
        }
        
        if (!empty($condition['reportId'])) {
            array_push($where['conditions'], "qc.report_id = :reportId");
            $where['bind']['reportId'] = $condition['reportId'];
        }

        if (!empty($condition['workType'])) {
            array_push($where['conditions'], "r.work_type_id = :workType");
            $where['bind']['workType'] = $condition['workType'];
        }

        if (!empty($condition['state'])) {
            array_push($where['conditions'], "r.state_id = :state");
            $where['bind']['state'] = $condition['state'];
        }

        return $where;
    }

    public function getReportFilter($condition = [], $fetchOption = QualityControlRemarkService::FETCH_DEFAULT)
    {
        $where = [
            'conditions' => [],
            'bind' => []
        ];

        if (!empty($condition['reportId'])) {
            array_push($where['conditions'], "re.report_id = :reportId");
            $where['bind']['reportId'] = $condition['reportId'];
        }

        if (!empty($condition['fromDate'])) {
            array_push($where['conditions'], "re.date_created >= DATE(:fromDate)");
            $where['bind']['fromDate'] = $condition['fromDate'];
        }

        if (!empty($condition['toDate'])) {
            array_push($where['conditions'], "re.date_created <= DATE(:toDate)");
            $where['bind']['toDate'] = $condition['toDate'];
        }

        if (!empty($condition['state'])) {
            array_push($where['conditions'], "form.state_id = :state");
            $where['bind']['state'] = $condition['state'];
        }

        if (!empty($condition['workType'])) {
            array_push($where['conditions'], "r.work_type_id = :workType");
            $where['bind']['workType'] = $condition['workType'];
        }

        if (!empty($condition['reportType'])) {
            array_push($where['conditions'], "form_type.form_type_id = :reportType");
            $where['bind']['reportType'] = $condition['reportType'];
        }
        array_push($where['conditions'], "re.pass_number = :passNumber");
        $where['bind']['passNumber'] = '2';
        array_push($where['conditions'], "re.entry_status = :entryStatus");
        $where['bind']['entryStatus'] =  ReportEntryService::STATUS_COMPLETE;
        
        return [
            'where' => $where,
            'fetchOption' => $fetchOption
        ];
    }

    /**
     * checks if report is already qcied
     */
    public function isReportAudit($reportId)
    {
        $select = $this->getSelect();
        $select->from($this->table);
        $select->where("report_id = :reportId");
        $bind = ['reportId' =>  $reportId];

        return $this->fetchRow($select, $bind);
    }

    /**
     * check if user can open
     * this report
     */
    public function userOpenReport($reportId, $userId)
    {   
        $report = $this->getReportFromQueue($reportId);

        if ($report && $report['user_id'] != $userId) {
            return false;
        }   

        return true;
    }

    public function getReportFromQueue($reportId)
    {
        $select = $this->getSelect();
        $table = 'qc_report_queue';
        $select->from($table)
        ->where('report_id = :reportId');
        $report = $this->fetchRow($select,[
            'reportId' => $reportId
        ]);

        return $report;
    }
}
