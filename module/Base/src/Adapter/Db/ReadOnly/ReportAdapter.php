<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db\ReadOnly;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

use Base\Adapter\Db\DbAbstract;
use Base\Service\ReportStatusService;
use Base\Service\EntryStageService;
use Base\Service\KeyingVendorService;

class ReportAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'report';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    public function getCountWorkTypeByUsersName($keyingVendorId, $isLNUser, $dateStart = null, $dateEnd = null, $nameFirst = null, $nameLast = null) 
    {
        $bind = [];
        $select = $this->getSelect();
        $select->from(['ren' => 'report_entry']);
        $columns = [
            'average_elements' => new Expression('ROUND(SUM(rec.count_keyed) / COUNT(rec.report_entry_id), 2)'),
            'count_keyed' => $this->getCount('ren.report_entry_id'),
            'username' => 'usr.username',
            'name_first' => 'usr.name_first',
            'name_last' => 'usr.name_last',
            'agency_name' => 'agn.name',
            'work_type_name' => 'wrt.name_external'

        ];
        
        if ($isLNUser) {
            $columns['vendor_name'] = 'kv.vendor_name';
        }
        
        $select->columns($columns, false);
        $select->join(['usr' => 'user'], 'ren.user_id = usr.user_id', []);
        $select->join(['kv' => 'keying_vendor'], 'usr.keying_vendor_id = kv.keying_vendor_id', []);
        $select->join(['rep' => 'report'], 'ren.report_id = rep.report_id', []);
        $select->join(['agn' => 'agency'], 'rep.agency_id = agn.agency_id', [],Select::JOIN_LEFT);
        $select->join(['wrt' => 'work_type'], 'rep.work_type_id = wrt.work_type_id', []);
        $select->join(['rec' => 'report_entry'], new Expression('rec.report_entry_id = ren.report_entry_id AND rec.count_keyed IS NOT NULL'), [],Select::JOIN_LEFT);
        
        if (!empty($nameFirst)) {
            $select->where('usr.name_first = :nameFirst');
            $bind ['nameFirst'] = $nameFirst;
        }

        if (!empty($nameLast)) {
            $select->where('usr.name_last = :nameLast');
            $bind ['nameLast'] = $nameLast;
        }
        
        if (!empty($keyingVendorId) && $keyingVendorId != KeyingVendorService::VENDOR_ALL) {
            $select->where('usr.keying_vendor_id = :keyingVendorId');
            $bind ['keyingVendorId'] = $keyingVendorId;
        }
       
        if (!empty($dateStart) && $dateStart = $this->normalizeDate($dateStart)) {
            // Making date inclusive by time (00:00:00 -> 23:59:59) so that
            // searching from today until today shows results for today.
            $dateStart = $dateStart . ' 00:00:00';
            $select->where('ren.date_created >=  :dateStart');
            $bind ['dateStart'] = $dateStart;
            if (empty($dateEnd)) {
                $select->where('ren.date_created <=  :dateEnd');
                $bind ['dateEnd'] = $this->getNowExpr();
            } elseif ($dateEnd = $this->normalizeDate($dateEnd)) {
                $dateEnd = $dateEnd . ' 23:59:59';
                $select->where('ren.date_created <=  :dateEnd');
                $bind ['dateEnd'] = $dateEnd;
            }
        } elseif (!empty($dateEnd) && $dateEnd = $this->normalizeDate($dateEnd)) {
            $dateEnd = $dateEnd . ' 00:00:00';
            $select->where('ren.date_created <=  :dateEnd');
            $bind ['dateEnd'] = $dateEnd;
        }
        
        $select->where(['ren.entry_status' => 'complete']);
        $select->group(['usr.username', 'wrt.work_type_id', 'agn.agency_id']);
        $select->order(['usr.username']);
        return $this->fetchAll($select, $bind);
    }
    
    public function prepareAutoVsManualReportSelectByState($searchCriteria)
    {
        return $this->fetchMainQueryReportSelectByState($searchCriteria, true);
    }
    
    
    public function fetchAutoVsManualReportByState(Select $select, $searchCriteria)
    {
        $bind = $this->getAutoExtractReportParams($searchCriteria);
        return $this->fetchAll($select, $bind);
    }
    
    public function fetchAutoVsManualReportTotalRows(Select $select, $searchCriteria)
    {
        $bind = $this->getAutoExtractReportParams($searchCriteria);
        
        // To reset the group by, order, limit and offset class initialized to get particular page.
        $select->reset('group');
        $select->reset('order');
        $select->reset('limit');
        $select->reset('offset');
        $column = [
            'total_rows' => new Expression('COUNT(r.report_id)'),
        ];
        $select->columns($column);
        
        return $this->fetchOne($select, $bind);
    }
    
    public function fetchAutoVsManualColumns($columns)
    {        
        $columns['work_type'] = 'wt.name_external';
        $columns['creation_date'] = 'r.date_created';
        $columns['report_status'] = 'rs.name';
        $columns['auto_extraction_date'] = 'ae.date_created';
        $columns['pass1_username'] = 'u.username';
        $columns['pass2_username'] = 'u2.username';
        $columns['vendor_name'] = 'kv.vendor_name';
        $columns['total_duration'] = new Expression("SEC_TO_TIME(TIMESTAMPDIFF(SECOND, re.date_created, red.date_created) + IFNULL(TIMESTAMPDIFF(SECOND, re2.date_created, red2.date_created), 0))");
                
        return $columns;
    }
    
    public function fetchAutoVsManualTables(Select $select)
    {
        $select->join(['ae' => 'auto_extraction_data'], 'ae.report_id = red.report_id', [], 'Left');
        $select->join(['wt' => 'work_type'], 'wt.work_type_id = r.work_type_id', [], 'Left');
        $select->join(['rs' => 'report_status'], 'rs.report_status_id = r.report_status_id', [], 'Left');
        
        return $select;
    }
    
    public function getAutoExtractReportParams($searchCriteria, $isVolumeReport = false)
    {
        $bind = [];
        $dateStart = $this->getFormattedTimeString($this->normalizeDate($searchCriteria['fromDate']));
        $dateEnd = $this->getFormattedTimeString($this->normalizeDate($searchCriteria['toDate']). ' + 1 day');               
        
        if (strcasecmp($searchCriteria['state'], 'all') != 0) {
            $bind['state_id'] = $searchCriteria['state'];
        }
        $bind['pass_number'] = EntryStageService::STAGE_ALL_INDEX;
        $bind['entry_status'] = ReportStatusService::STATUS_COMPLETE;
        $bind['entry_stage_id'] = EntryStageService::STAGE_DYNAMIC_VERIFICATION_INDEX;

        if (!empty($dateStart) && !empty($dateEnd)) {
            $bind['start_date'] = $dateStart;
            $bind['end_date'] = $dateEnd;
        }                  
        
        if (!empty($searchCriteria['keyingVendorId']) 
                && $searchCriteria['keyingVendorId'] != KeyingVendorService::VENDOR_ALL) {
            $bind['keyingVendorId'] = $searchCriteria['keyingVendorId'];
        }
        
        return $bind;
    }

    /* select entry stage 1 and 3 data */ 
    public function prepareVolumeProductivityReportSelectByState($searchCriteria)
    {   
        return $this->fetchMainQueryReportSelectByState($searchCriteria, false);
    }
    
    /* preparing final data using entry stages data for selected dates */
    public function fetchVolumeProductivityReportByState(Select $select, $searchCriteria)
    {
        $bind = $this->getAutoExtractReportParams($searchCriteria, true); 
        return $this->fetchAll($select, $bind);
    }
    
    public function fetchMainQueryReportSelectByState($searchCriteria, $isAutovsManualReport = false)
    {
        $isKeyingVendorIncluded = false;
        if (!empty($searchCriteria['keyingVendorId']) 
                && $searchCriteria['keyingVendorId'] != KeyingVendorService::VENDOR_ALL) {
            $isKeyingVendorIncluded = true;
        }
                
        $columns = [    
            'report_id' => 'r.report_id',
            'state_abbr' => 's.name_abbr',                            
            'auto_extraction' => new Expression("r.is_auto_extracted, IF(r.is_auto_extracted = 1, 'Yes', 'No')"),
            'manually_keyed' => new Expression("IF(r.is_auto_keyed = 1, 'Auto', 'Manual')"),            
            'pass2_start_date' => 're2.date_created',
            'pass2_end_date' => 'red2.date_created',
            'pass1_start_date' => 're.date_created',
            'pass1_end_date' => 'red.date_created',
            'pass1_duration' => new Expression("time_format(timediff(red.date_created, re.date_created),'%H:%i:%s')"),
            'pass2_duration' => new Expression("time_format(timediff(red2.date_created, re2.date_created),'%H:%i:%s')"),
            'total_duration' => new Expression("((TIMESTAMPDIFF(SECOND, re.date_created, red.date_created) + IFNULL(TIMESTAMPDIFF(SECOND, re2.date_created, red2.date_created), 0)) / 60)")
        ];
        
        if ($isAutovsManualReport) {
             $columns = $this->fetchAutoVsManualColumns($columns);
        } else {
            $columns['pass2_start_date'] = new Expression('(CASE WHEN re.entry_stage_id = 1 THEN re2.date_created ELSE re.date_created END)');
            $columns['pass2_end_date'] = new Expression('(CASE WHEN re.entry_stage_id = 1 THEN red2.date_created ELSE red.date_created END)');
            $columns['pass1_start_date'] = new Expression('(CASE WHEN re.entry_stage_id = 1 THEN re.date_created ELSE re2.date_created END)');
            $columns['pass1_end_date'] = new Expression('(CASE WHEN re.entry_stage_id = 1 THEN red.date_created ELSE red2.date_created END)');
            $columns['pass1_duration'] = new Expression("(CASE WHEN re.entry_stage_id = 1 THEN time_format(timediff(red.date_created, re.date_created),'%H:%i:%s') "
                    . "ELSE time_format(timediff(red2.date_created, re2.date_created),'%H:%i:%s') END)");
            $columns['pass2_duration'] = new Expression("(CASE WHEN re.entry_stage_id = 1 THEN time_format(timediff(red2.date_created, re2.date_created),'%H:%i:%s') "
                    . "ELSE time_format(timediff(red.date_created, re.date_created),'%H:%i:%s') END)");
        }
        
        $select = $this->getSelect();
        $select->from(['re' => 'report_entry']);
        $select->columns($columns, false);
        $select->join(['r' => 'report'], 'r.report_id = re.report_id', []);
        $select->join(['red' => 'report_entry_data'], 're.report_entry_id = red.report_entry_id', []);
        $select->join(['u' => 'user'], 're.user_id = u.user_id', []); 
        
        if ($isKeyingVendorIncluded || $isAutovsManualReport) {
            $select->join(['kv' => 'keying_vendor'], 'u.keying_vendor_id = kv.keying_vendor_id', []);
        }
        
        $reportEntryPairValue = ($isAutovsManualReport) ? 're2.entry_stage_id > re.entry_stage_id' : 're2.entry_stage_id <> re.entry_stage_id';
        
        $select->join(['s' => 'state'], 's.state_id = r.state_id', [], 'Left');
        $select->join(['re2' => 'report_entry'], 'r.report_id = re2.report_id AND ' . $reportEntryPairValue, [], 'Left'); 
        $select->join(['red2' => 'report_entry_data'], 're2.report_entry_id = red2.report_entry_id', [], 'Left');
        $select->join(['u2' => 'user'], 're2.user_id = u2.user_id AND u2.keying_vendor_id = u.keying_vendor_id', [], 'Left');
        
        if ($isAutovsManualReport) {
             $select = $this->fetchAutoVsManualTables($select);
        }
        
        if (strcasecmp($searchCriteria['state'], 'all') != 0) {
            $select->where('r.state_id = :state_id');
        }
        
        if ($isKeyingVendorIncluded) {
            $select->where('u.keying_vendor_id = :keyingVendorId');
        }
                
        $select->where('(re.date_created BETWEEN :start_date AND :end_date)');
        $select->where('re.entry_status = :entry_status');
        
        if ($isAutovsManualReport) {
            $select->where('re.pass_number = :pass_number');
            $select->where('CASE WHEN re2.report_entry_id IS NOT NULL '
                    . 'THEN re2.entry_stage_id = :entry_stage_id '
                    . 'ELSE re2.report_entry_id IS NULL END');
        } else {
            $select->where('(re.pass_number = :pass_number OR re.entry_stage_id = :entry_stage_id)');
            $select->group('r.report_id');
        }

        $select->order('re.date_created');
        return $select;
    }
}
