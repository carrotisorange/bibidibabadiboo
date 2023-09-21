<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db\ReadOnly;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use InvalidArgumentException;

use Base\Adapter\Db\DbAbstract;
use Base\Service\KeyingVendorService;

class ReportStatusAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'report_status';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    public function fetchReportCountNullOnlyAgencyOfReportEntryNull($keyingVendorId)
    {
        $return = [];
        $bind = [];
        $isKeyingVendorIncluded = false;
        if (!empty($keyingVendorId) && $keyingVendorId != KeyingVendorService::VENDOR_ALL) {
            $isKeyingVendorIncluded = true;
        }
        
        $sql = "
            SELECT
                a.name AS name,
                COUNT(*) AS available
            FROM
                report AS r
                LEFT JOIN agency AS a ON (a.agency_id = r.agency_id)
                LEFT JOIN report_status AS rs USING (report_status_id)
                LEFT JOIN report_entry AS re ON (re.report_id = r.report_id 
                AND re.entry_status = 'in progress') ";
        
        if ($isKeyingVendorIncluded) {
            $sql .= "LEFT JOIN user AS u ON (u.user_id = re.user_id 
                     AND u.keying_vendor_id = :keying_vendor_id) ";
            $bind = ['keying_vendor_id' => $keyingVendorId];
        }
            
        $sql .= "WHERE a.name IS NULL AND rs.name = 'keying' AND re.report_entry_id IS NULL 
                 GROUP BY a.name";
        $result = $this->fetchAll($sql, $bind);
        foreach ($result as $row) {
            $agencyName = $row['name'];
            $return[$agencyName] = $row;
        }

        return $return;
    }

    public function fetchReportCountNullOnlyAgencyOfReportEntryNotNull($keyingVendorId)
    {
        $return = [];
        $bind = [];
        $isKeyingVendorIncluded = false;
        if (!empty($keyingVendorId) && $keyingVendorId != KeyingVendorService::VENDOR_ALL) {
            $isKeyingVendorIncluded = true;
        }
        $sql = "
            SELECT
                a.name AS name,
                COUNT(*) AS inProgress
            FROM
                report AS r
                LEFT JOIN agency AS a ON (a.agency_id = r.agency_id)
                LEFT JOIN report_status AS rs USING (report_status_id)
                LEFT JOIN report_entry AS re ON (re.report_id = r.report_id 
                AND re.entry_status = 'in progress') ";
        
        if ($isKeyingVendorIncluded) { 
            $sql .= "LEFT JOIN user AS u ON (u.user_id = re.user_id 
                     AND u.keying_vendor_id = :keying_vendor_id) ";
            $bind = ['keying_vendor_id' => $keyingVendorId];
        }
                    
        $sql .= "WHERE a.name IS NULL AND rs.name = 'keying' AND re.report_entry_id IS NOT NULL 
                 GROUP BY a.name";
        $result = $this->fetchAll($sql, $bind);
       
        foreach ($result as $row) {
            $agencyName = $row['name'];
            $return[$agencyName] = $row;
        }

        return $return;
    }

    public function fetchReportCountNullOnlyAgencyOtherThanUnavailableReportStatus($keyingVendorId)
    {
        $bind = [];
        $isKeyingVendorIncluded = false;
        if (!empty($keyingVendorId) && $keyingVendorId != KeyingVendorService::VENDOR_ALL) {
            $isKeyingVendorIncluded = true;
        }
        $sql = "
            SELECT
                a.name AS name,
                rs.name AS status,
                count(*) as count
            FROM
                report AS r
                LEFT JOIN agency AS a ON (a.agency_id = r.agency_id)
                LEFT JOIN report_status AS rs USING (report_status_id) ";
        
        if ($isKeyingVendorIncluded) {
            $sql .= "LEFT JOIN report_entry AS re ON (re.report_id = r.report_id) 
                     LEFT JOIN user AS u ON (re.user_id=u.user_id 
                     AND u.keying_vendor_id = :keying_vendor_id) ";
            $bind = ['keying_vendor_id' => $keyingVendorId];
        }
                
        $sql .= "WHERE a.name IS NULL AND rs.name != 'keying' 
                 AND rs.name != 'unavailable' AND rs.name IS NOT NULL 
                 GROUP BY rs.name, a.name";
        return $this->fetchAll($sql, $bind);
    }


    public function fetchReportCount($keyingVendorId, $stateId = null, $agencyId = null)
    {
        $criteria = [];
        $bind = [];
        $return = [];
        $isKeyingVendorIncluded = false;
        if (!empty($keyingVendorId) && $keyingVendorId != KeyingVendorService::VENDOR_ALL) {
            $isKeyingVendorIncluded = true;
        }
        if (!empty($agencyId) && strtolower($agencyId) != "all") {
            $bind['agencyId'] = $agencyId;
            $criteria[] = "a.agency_id = :agencyId";
        }
        if (!empty($stateId) && strtolower($stateId) != "all") {
            $bind['stateId'] = $stateId;
            $criteria[] = "r.state_id = :stateId";
        }
        $where = '';
        if (count($criteria) > 0) {
            $where = "WHERE " . join(" AND ", $criteria);
        }
        $sql = "
            SELECT
                a.name AS name,
                SUM(IF(rs.name = 'keying' AND re.report_entry_id IS NULL , 1, 0)) AS available,
                SUM(IF(rs.name = 'keying' AND re.report_entry_id IS NOT NULL , 1, 0)) AS inProgress,
                SUM(IF(rs.name = 'discarded', 1, 0)) AS discarded,
                SUM(IF(rs.name = 'complete', 1, 0)) AS complete,
                SUM(IF(rs.name = 'translated', 1, 0)) AS translated,
                SUM(IF(rs.name = 'dead', 1, 0)) AS dead,
                SUM(IF(rs.name = 'reordered', 1, 0)) AS reordered,
                SUM(IF(rs.name = 'bad image', 1, 0)) AS bad
            FROM
                report AS r
                LEFT JOIN agency AS a ON (a.agency_id = r.agency_id)
                LEFT JOIN report_status AS rs USING (report_status_id)
                LEFT JOIN report_entry AS re ON (re.report_id = r.report_id AND re.entry_status = 'in progress') ";
        
        if ($isKeyingVendorIncluded) { 
            $sql .= "LEFT JOIN user AS u ON (u.user_id = re.user_id 
                     AND u.keying_vendor_id = :keyingVendorId) ";
            $bind['keyingVendorId'] = $keyingVendorId;
        }
        
        $sql .= $where . " GROUP BY a.name";
            
        $result = $this->fetchAll($sql, $bind);
        foreach ($result as $row) {
            $agencyName = $row['name'];
            $return[$agencyName] = $row;
        }
        
        return $return;
    }
    
    public function fetchReportCountNotNullAgency($keyingVendorId)
    {
        $return = [];
        $bind = [];
        $isKeyingVendorIncluded = false;
        if (!empty($keyingVendorId) && $keyingVendorId != KeyingVendorService::VENDOR_ALL) {
            $isKeyingVendorIncluded = true;
        }
        $sql = "
            SELECT
                a.name AS name,
                SUM(IF(rs.name = 'keying' AND re.report_entry_id IS NULL , 1, 0)) AS available,
                SUM(IF(rs.name = 'keying' AND re.report_entry_id IS NOT NULL , 1, 0)) AS inProgress,
                SUM(IF(rs.name = 'discarded', 1, 0)) AS discarded,
                SUM(IF(rs.name = 'complete', 1, 0)) AS complete,
                SUM(IF(rs.name = 'translated', 1, 0)) AS translated,
                SUM(IF(rs.name = 'dead', 1, 0)) AS dead,
                SUM(IF(rs.name = 'reordered', 1, 0)) AS reordered,
                SUM(IF(rs.name = 'bad image', 1, 0)) AS bad
            FROM
                report AS r
                LEFT JOIN agency AS a ON (a.agency_id = r.agency_id)
                LEFT JOIN report_status AS rs USING (report_status_id)
                LEFT JOIN report_entry AS re ON (re.report_id = r.report_id AND re.entry_status = 'in progress') ";
        
        if ($isKeyingVendorIncluded) {
            $sql .= "LEFT JOIN user AS u ON (u.user_id = re.user_id 
                     AND u.keying_vendor_id = :keying_vendor_id) ";
            $bind = ['keying_vendor_id' => $keyingVendorId];
        }
            
        $sql .= "WHERE a.name IS NOT NULL 
                 GROUP BY a.name";
        $result = $this->fetchAll($sql, $bind);
        
        foreach ($result as $row) {
            $agencyName = $row['name'];
            $return[$agencyName] = $row;
        }
        
        return $return;
    }
}
