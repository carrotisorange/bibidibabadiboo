<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db\ReadOnly;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter as PaginationAdapter;
use Zend\Db\Sql\Select;

use Base\Service\ReportEntryService;
use Base\Adapter\Db\DbAbstract;
use Base\Service\EntryStageService;
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
    }
    
/**
     * @param string $dateStart YYYY-mm-dd
     * @param string $dateEnd YYYY-mm-dd
     * @param int|string $keyingVendorId
     * @param null|string $nameFirst
     * @param null|string $nameLast
     * @return array
     */
    public function fetchEntryAvgStatistics($dateStart, $dateEnd, $keyingVendorId, $nameFirst = null, $nameLast = null)
    {
        $result = false;
        $dateStart = $this->normalizeDate($dateStart);
        $dateEnd = $this->normalizeDate($dateEnd);
        
        if (!empty($dateStart) && !empty($dateEnd)) {
            $bind = [
                'dateStart' => $dateStart . ' 00:00:00',
                'dateEnd' => $dateEnd . ' 23:59:59',
                'editStageName' => EntryStageService::STAGE_EDIT,
            ];
            
            $where = [];
            if (!empty($keyingVendorId) && $keyingVendorId != KeyingVendorService::VENDOR_ALL) {
                $where[] = 'usr.keying_vendor_id = :keyingVendorId';
                $bind['keyingVendorId'] = $keyingVendorId;
            }
            
            if ($nameFirst != '') {
                $where[] = 'usr.name_first = :nameFirst';
                $bind['nameFirst'] = $nameFirst;
            }
            if ($nameLast != '') {
                $where[] = 'usr.name_last = :nameLast';
                $bind['nameLast'] = $nameLast;
            }

            $whereAddtSql = '';
            if ($where) {
                $whereAddtSql = 'AND ' . implode(' AND ', $where);
            }

            $sql = "
                SELECT
                    usr.name_last,
                    usr.name_first,
                    usr.username,
                    COUNT(ren.report_entry_id) AS keyedTotal,
                    ROUND(60 / (AVG(TIMEDIFF(ren.date_updated, ren.date_created)) / 60), 2) AS keyedPerHourAvg,
                    ROUND(SUM(rec.count_keyed) / COUNT(rec.report_entry_id), 2) as 'keyedFieldsAvg',
                    kv.vendor_name
                FROM report_entry AS ren
                    JOIN entry_stage AS est USING (entry_stage_id)
                    JOIN user AS usr USING (user_id) 
                    JOIN keying_vendor AS kv ON usr.keying_vendor_id = kv.keying_vendor_id
                    LEFT JOIN report_entry rec ON (
                        rec.report_entry_id = ren.report_entry_id
                        AND rec.count_keyed IS NOT NULL
                    )
                WHERE ren.date_created BETWEEN :dateStart AND :dateEnd
                    AND ren.entry_status = 'complete'
                    AND (
                        est.name_internal != :editStageName
                        OR ren.date_created != ren.date_updated
                    )
                    $whereAddtSql
                GROUP BY usr.user_id
                ORDER BY usr.name_last, usr.name_first, usr.user_id
            ";

            $result = $this->fetchAll($sql, $bind);
        }

        return $result;
    }
}
