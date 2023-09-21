<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\TableIdentifier;

use Base\Service\ReportStatusService;
use Base\Service\ReportEntryService;
use Base\Service\KeyingVendorService;

class ReportAdapter extends DbAbstract
{
    
    const SCHEMA_ECRASH = 'ecrash';
    
    /**
     * @var string
     * Table name
     */
    protected $table = 'report';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    /**
    * select reports if user has data for the formId and selected date range with report status keying & in progress
    * @param integer $formId
    * @param date $startDate
    * @param date $endDate
    * @return array
    */
    public function getReportByFormId($formId, $startDate, $endDate)
    {        
        $fromDate = date('Y-m-d H:i:s', strtotime($startDate));
        $toDate = date('Y-m-d H:i:s', strtotime($endDate));        
        $columns = [
            'reportId' => 'r.report_id'
        ];
        
        // To get the not completed reports
        $where = $this->getWhere();
        $where->in('rs.name', [ReportStatusService::STATUS_KEYING, ReportEntryService::STATUS_IN_PROGRESS]);
        
        $select = $this->getSelect()
            ->columns($columns, false)
            ->from(['r' => $this->table])
            ->join(['rs' => 'report_status'], 'rs.report_status_id = r.report_status_id', [])
            ->join(['rts' => 'report_tat_status'], 'rts.report_id = r.report_id', [])            
            ->join(['req' => 'report_entry_queue'], new Expression('(req.report_id = r.report_id AND req.user_id_assigned_to IS NULL AND req.report_assigned_to IS NULL)'), [])
            ->join(['ur' => 'user_report'], 'ur.report_id  = r.report_id', [], SELECT::JOIN_LEFT)
            ->where($where)
            ->where('ur.user_report_id IS NULL')
            ->where('r.date_created >= :start_date')
            ->where('r.date_created <= :end_date')
            ->where('r.form_id = :form_id');
        
        $bind = [
            'start_date' => $fromDate,
            'end_date' => $toDate,
            'form_id' => $formId
        ];       
        
        return $this->fetchAll($select, $bind);
    }
    
    public function getReport($reportId)
    {
        $select = $this->getSelect()
            ->where('report_id = :report_id');
        
        return $this->fetchAll($select, ['report_id' => $reportId]);
    }
    
    /**
     * Gets basic information about a report (form, type, status, etc)
     *
     * @param integer $reportId
     * @return array
     */
    public function getRelatedInfo($reportId)
    {
        $select = $this->getSelect();
        $select->from(["r" => $this->table]);
        $select->columns([
            "reportId" => "report_id",
            "isObsolete" => "is_obsolete",
            "reportIdObsoletedBy" => "report_id_obsoleted_by",
            "priority" => "priority",
            "newFormId" => "form_id"
        ]);
        $select->join(["s" => "state"], "s.state_id = r.state_id", ["stateId" => "state_id", "stateAbbr" => "name_abbr", "stateFull" => "name_full"], Select::JOIN_LEFT);
        $select->join(["wt" => "work_type"], "wt.work_type_id = r.work_type_id", ["workTypeId" => "work_type_id", "workTypeInternal" => "name_internal", "workTypeExternal" => "name_external"]);
        $select->join(["a" => "agency"], "a.agency_id = r.agency_id", ["agencyId" => "agency_id", "agencyName" => "name"], Select::JOIN_LEFT);
        $select->join(["ft" => "form_type"], "ft.form_type_id = r.form_type_id", ["formTypeId" => "form_type_id", "formTypeCode" => "code", "formTypeDescription" => "description"]);
        $select->join(["f" => "form"], "f.form_id = r.form_id", ["formId" => "form_id", "formName" => "name_external", "entryStageProcessGroupId" => "entry_stage_process_group_id"]);
        $select->join(["ftmpl" => "form_template"], "ftmpl.form_template_id = f.form_template_id", []);
        $select->join(["fs" => "form_system"], "fs.form_system_id = ftmpl.form_system_id", ["formSystem" => "name_internal", "formSystemId" => "form_system_id"]);
        $select->join(["re" => "report_entry"], new Expression("r.report_id = re.report_id AND re.entry_status = 'complete'"), ["oldFormId" => "form_id"], Select::JOIN_LEFT);
        $select->join(["re2" => "report_entry"], new Expression("re2.report_id = re.report_id AND RE2.date_updated > RE.date_updated AND RE2.entry_status = 'complete'"), [], Select::JOIN_LEFT);
        $select->where("r.report_id = :report_id");
        $select->where('( re.report_entry_id IS NULL OR re2.report_entry_id IS NULL )');
        $bind = ['report_id' => $reportId];

        $info = $this->fetchRow($select, $bind);

        $selectFlag = $this->getSelect();
        $selectFlag->columns([
            "flag" => "f.name"
        ], false);
        $selectFlag->from(["rf" => "report_flag"]);
        $selectFlag->join(["f" => "flag"], "rf.flag_id = f.flag_id", []);
        $selectFlag->where("rf.report_id = :rf_report_id");
        $bind = ['rf_report_id' => $reportId];
        $flags = $this->fetchAll($selectFlag, $bind);

        if (count($flags) > 0) {
            foreach($flags as $flag) {
                $info['flags'][] = $flag['flag'];
            }
        } else {
            $info['flags'] = [];
        }
        
        return $info;

    }

    public function setStatus($reportId, $reportStatusId, $userId)
    {
        $this->update([
                'report_status_id' => $reportStatusId,
                'user_id_status_set_by' => $userId,
            ],
            [
                'report_id' => $reportId,
            ]
        );
    }

    public function setDateReordered($reportId, $dateReordered)
    {
        $this->update([
                'date_reordered' => $dateReordered,
            ],
            [
                'report_id' => $reportId,
            ]
        );
    }

    public function getHashKey($reportId)
    {
        $select = $this->getSelect();
        $select->columns([
            "hash_key",
            "work_type_id"
        ]);
        $select->where('report_id = :report_id');
        $bind = ["report_id" => $reportId];

        return $this->fetchRow($select, $bind);
    }

    public function fetchKeyedImages($searchParams)
    {
        $bind = [];

        if (empty($searchParams)) {
            return false;
        }
        $reportEntrySubQuery = $this->getSelect();
        $reportEntrySubQuery->from(['RE' =>'report_entry']);
        $reportEntrySubQuery->columns([
            'report_id',
            'date_updated',
            'form_id'
        ]);
        $reportEntrySubQuery->join(['RE2' => 'report_entry'], new Expression('RE2.report_id = RE.report_id AND RE2.date_updated > RE.date_updated AND RE2.entry_status = "complete"'), [], Select::JOIN_LEFT);
        $reportEntrySubQuery->where('RE2.report_entry_id IS NULL AND RE.entry_status = "complete"');
        
        /*We need to do this to specifically use the ecrash schema*/
        $tblIncident = $this->getEcrashSchemaTable('incident');
        $tblVehicle = $this->getEcrashSchemaTable('vehicle');
        $tblPerson = $this->getEcrashSchemaTable('person');
        
        $select = $this->getSelect();
        $select->from(['I' => $tblIncident]);
        $select->join(['R' => 'report'], 'R.report_id = I.Report_ID', []);
        $select->join(['RC' => 'report_cru'], 'RC.report_id = R.report_id', [], Select::JOIN_LEFT);
        $select->join(['F' => 'form'], 'F.form_id = R.form_id', []);
        $select->join(['S' => 'state'], 'S.state_id = F.state_id', []);
        $select->join(['A' => 'agency'], 'R.agency_id = A.agency_id', [], Select::JOIN_LEFT);
        $select->join(['P' => $tblPerson], 'P.Incident_ID = I.Incident_ID', [], Select::JOIN_LEFT);
        $select->join(['V' => $tblVehicle], 'V.Incident_ID = I.Incident_ID', [], Select::JOIN_LEFT);
        $select->join(['RE' => 'report_entry'], 'RE.report_id = I.Report_ID', []); 
        $select->join(['RE2' => $reportEntrySubQuery], 'RE2.report_id = I.Report_ID', []);
        $select->join(['U' => 'user'], 'U.user_id = RE.user_id', [],  Select::JOIN_LEFT);
        $select->join(['KV' => 'keying_vendor'], 'KV.keying_vendor_id = U.keying_vendor_id', []);
        $columns = [
            'reportId' => 'I.Report_ID',
            'caseIdentifier' => 'I.Case_Identifier',
            'stateName' => 'S.name_full',
            'agencyName' => 'A.name',
            'vin' => new Expression('GROUP_CONCAT(DISTINCT CONCAT_WS("; ", V.VIN, V.Other_Unit_VIN) SEPARATOR "; ")'),
            'crashDate' => 'I.Crash_Date',
            'cru_order_id' => 'I.CRU_Order_ID',
            'oldFormId' => 'RE2.form_id',
            'newFormId' => 'R.form_id',
            'vendorName' => 'KV.vendor_name'
        ];
        foreach ($searchParams as $name => $value) {
            if (empty($value)) {
                continue;
            }
            
            switch ($name) {
                case 'reportId':
                    $reportEntrySubQuery->where("RE.report_id = :re_report_id");
                    $bind['re_report_id'] = $value;
                    break;
                case 'cruOrderId':
                    $select->where("RC.cru_order_id = :rc_cru_order_id");
                    $bind['rc_cru_order_id'] = $value;
                    break;
                case 'agencyId':
                    $select->where("R.agency_id = :r_agency_id");
                    $bind['r_agency_id'] = $value;
                    break;
                case 'caseIdentifier':
                    $select->where->like('I.Case_Identifier', '%' . $value . '%');
                    break;
                case 'stateId':
                    $select->where("S.state_id = :s_state_id");
                    $bind['s_state_id'] = $value;
                    break;
                case 'licensePlate':
                    $select->where->like('V.License_Plate', '%' . $value . '%');
                    break;
                case 'registrationState':
                    $select->where("V.Registration_State = :v_registration_state");
                    $bind['v_registration_state'] = $value;
                    break;
                case 'vin':
                    $select->where->nest()->like('V.Vin', '%' . $value . '%')->or->like('V.Other_Unit_VIN', '%' . $value . '%');
                    $columns['vin'] = new Expression('GROUP_CONCAT(DISTINCT CONCAT_WS("; ", V2.VIN, V2.Other_Unit_VIN) SEPARATOR "; ")');
                    break;
                case 'partyFirstName':
                    $select->where->like('P.First_Name', '%' . $value . '%');
                    break;
                case 'partyLastName':
                    $select->where->like('P.Last_Name', '%' . $value . '%');
                    break;
                case 'operatorFirstName':
                    $select->where->like('U.name_first', '%' . $value . '%');
                    break;
                case 'operatorLastName':
                    $select->where->like('U.name_last', '%' . $value . '%');
                    break;
                case 'processingStartTime':
                    $reportEntrySubQuery->where("RE.date_updated >= :re_date_updated_start_time");
                    $bind['re_date_updated_start_time'] = $value . ' 00:00:00';
                    break;
                case 'processingEndTime':
                    $reportEntrySubQuery->where("RE.date_updated <= :re_date_updated_end_time");
                    $bind['re_date_updated_end_time'] = $value . ' 23:59:59';
                    break;
                case 'crashDate':
                    $select->where("I.Crash_Date = :i_crash_date");
                    $bind['i_crash_date'] = $value;
                    break;
                case 'reportType':
                    $select->where("F.form_type_id = :f_form_type_id");
                    $bind['f_form_type_id'] = $value;
                    break;
                case 'keyingVendorId':
                    if ($value != KeyingVendorService::VENDOR_ALL) {
                        $select->where("KV.keying_vendor_id = :keying_vendor_id");
                        $bind['keying_vendor_id'] = $value;
                    }
                    break;
            }
        }

        $tableAliases = ['person' => 'P', 'user' => 'U'];
        if (!empty($searchParams['operatorFirstName']) || !empty($searchParams['operatorLastName'])) {
            $tableAliases['user'] = 'U2';
            $select->join(['RE3' => 'report_entry'], 'RE3.report_id = I.Report_ID', []);
            $select->join([$tableAliases['user'] => 'user'], "{$tableAliases['user']}.user_id = RE3.user_id", []);
        }
        if (!empty($searchParams['partyFirstName']) || !empty($searchParams['partyLastName'])) {
            $tableAliases['person'] = 'P2';
            $select->join([$tableAliases['person'] => 'person'], "{$tableAliases['person']}.Incident_ID = I.Incident_ID", []);
        }
        if (!empty($searchParams['licensePlate']) || !empty($searchParams['registrationState']) || !empty($searchParams['vin'])) {
            $select->join(['V2' => 'vehicle'], 'V2.Incident_ID = I.Incident_ID', []);
        }

        $columns['driverName'] = new Expression(
            "GROUP_CONCAT(DISTINCT CONCAT_WS(', ', {$tableAliases['person']}.Last_Name, {$tableAliases['person']}.First_Name)
            ORDER BY {$tableAliases['person']}.Person_ID SEPARATOR '; ')"
        );
        $columns['operatorName'] = new Expression(
            "GROUP_CONCAT(DISTINCT CONCAT_WS(', ', {$tableAliases['user']}.name_last, {$tableAliases['user']}.name_first)
            ORDER BY {$tableAliases['user']}.name_last, {$tableAliases['user']}.name_first SEPARATOR '; ')");

        $select->columns($columns, false);
        $select->group('I.Report_ID');
        $select->order(['S.name_full', 'A.name', 'R.report_id', 'operatorName'], 'ASC');
        
        return $this->fetchAll($select, $bind);
    }

    public function getReportStateId($reportId)
    {
        $select = $this->getSelect()
        ->columns(['state_id'])
        ->where('report_id = :report_id');
        
        return $this->fetchOne($select, ['report_id' => $reportId]);
    }

    public function getReportWorkTypeId($reportId)
    {
        $select = $this->getSelect()
        ->columns(['work_type_id'])
        ->where('report_id = :report_id');
        
        return $this->fetchOne($select, ['report_id' => $reportId]);
    }
    
    public function updateForm($formId, $reportId)
    {
        return $this->update(['form_id' => $formId], ['report_id' => $reportId]);
    }

    public function updateReportKeyingType($reportId, $hasAutoExtracted = 0, $hasAutoKeyed = 0)
    {
        return $this->update(['is_auto_extracted' => $hasAutoExtracted, 'is_auto_keyed' => $hasAutoKeyed], ['report_id' => $reportId]);
    }

    public function isAutoKeyed($reportId)
    {
        $select = $this->getSelect()
        ->columns(['is_auto_keyed'])
        ->where('report_id = :report_id');

        return $this->fetchOne($select, ['report_id' => $reportId]);
    }
    
    /**
     * Get table from the ecrash schema
     *
     * @param string $table the table to use under ecrash schema
     * @return string table name under the ecrash schema
     */
    public function getEcrashSchemaTable($table)
    {
        $eCrashTable = new TableIdentifier($table, self::SCHEMA_ECRASH);
        return $eCrashTable;
    }
}
