<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use InvalidArgumentException;

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

    public function fetchReportCountNullOnlyAgencyOfReportEntryNull()
    {
        $result = [];
        $sql = "
            SELECT
                a.name AS name,
                COUNT(*) AS available
            FROM
                report AS r
                LEFT JOIN agency AS a ON (a.agency_id = r.agency_id)
                LEFT JOIN report_status AS rs USING (report_status_id)
                LEFT JOIN report_entry AS re ON (re.report_id = r.report_id AND re.entry_status = 'in progress')
            WHERE a.name IS NULL AND rs.name = 'keying' AND re.report_entry_id IS NULL
            GROUP BY a.name
        ";
        $result = $this->fetchAll($sql);
        foreach ($result as $row) {
            $agencyName = $row['name'];
            $return[$agencyName] = $row;
        }

        return $return;
    }

    public function fetchReportCountNullOnlyAgencyOfReportEntryNotNull()
    {
        $result = [];
        $sql = "
            SELECT
                a.name AS name,
                COUNT(*) AS inProgress
            FROM
                report AS r
                LEFT JOIN agency AS a ON (a.agency_id = r.agency_id)
                LEFT JOIN report_status AS rs USING (report_status_id)
                LEFT JOIN report_entry AS re ON (re.report_id = r.report_id AND re.entry_status = 'in progress')
            WHERE a.name IS NULL AND rs.name = 'keying' AND re.report_entry_id IS NOT NULL
            GROUP BY a.name
        ";
        $result = $this->fetchAll($sql);
       
        foreach ($result as $row) {
            $agencyName = $row['name'];
            $return[$agencyName] = $row;
        }

        return $return;
    }

    public function fetchReportCountNullOnlyAgencyOtherThanUnavailableReportStatus()
    {
        $sql = "
            SELECT
                a.name AS name,
                rs.name AS status,
                count(*) as count
            FROM
                report AS r
                LEFT JOIN agency AS a ON (a.agency_id = r.agency_id)
                LEFT JOIN report_status AS rs USING (report_status_id)
            WHERE a.name IS NULL AND rs.name != 'keying' AND rs.name != 'unavailable' AND rs.name IS NOT NULL
            GROUP BY rs.name, a.name;
        ";
        
        return $this->fetchAll($sql);
    }

    public function fetchReportCount($stateId = null, $agencyId = null)
    {
        $criteria = [];
        $bind = [];
        $return = [];
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
                LEFT JOIN report_entry AS re ON (re.report_id = r.report_id AND re.entry_status = 'in progress')
            $where
            GROUP BY a.name
        ";
        $result = $this->fetchAll($sql, $bind);
        foreach ($result as $row) {
            $agencyName = $row['name'];
            $return[$agencyName] = $row;
        }
        
        return $return;
    }
    
    public function fetchReportCountNotNullAgency()
    {
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
                LEFT JOIN report_entry AS re ON (re.report_id = r.report_id AND re.entry_status = 'in progress')
            WHERE a.name IS NOT NULL
            GROUP BY a.name
        ";
        $result = $this->fetchAll($sql);
        
        foreach ($result as $row) {
            $agencyName = $row['name'];
            $return[$agencyName] = $row;
        }
        
        return $return;
    }

    public function getIdByStatus($status)
    {
        $select = $this->getSelect()
            ->from($this->table)
            ->where(['name = :status']);

        $bind = ['status' => $status];

        $reportStatus = $this->fetchRow($select, $bind);

        if (empty($reportStatus)) {
            throw new InvalidArgumentException('Invalid report status given.');
        }

        return $reportStatus['report_status_id'];
    }

    public function getStatusByReportId($reportId)
    {
        $select = $this->getSelect();
        $select->from(['r' => 'report']);
        $select->columns(
            ['name' => new Expression('rs.name')]
        );
        $select->join(['rs' => 'report_status'], 'rs.report_status_id = r.report_status_id', []);
        $select->where('r.report_id = :report_id');
        $bind = ['report_id' => $reportId];
        
        return $this->fetchOne($select, $bind);
    }
}
