<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Base\Service\KeyingVendorService;

class AutoExtractionAccuracyAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'auto_extraction_accuracy';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    /**
     * Inserts a new invalid field record
     *
     * @param int $userAccuracyId - id of the associated user accuracy record
     * @param string $formAttributeName
     * @param string $validValue
     * @param string $userValue
     * @return int - Id of the inserted row
     */
    public function insertNew($userId, $reportID, $diff)
    {
        $data = [
            'date_created' => $this->getNowExpr(),
            'user_id' => $userId,
            'report_id' => $reportID,
            'accuracy_details' => $diff
        ];

        return $this->insert($data);
    }

    /**
     * @TODO: this table will be removed in future by replacing existing proper table if it's feasible. Consider the newApp column while removing this table.
     * Gets all of the Accuracy data records for Date Range
     *
     * @param string $fromDate
     * @param string $toDate
     * @return array
     */
    public function getAutoExtractionAccuracyData($searchCriteria)
    {
        $fromDate = $this->normalizeDate($searchCriteria['fromDate']);
        $toDate = $this->normalizeDate($searchCriteria['toDate']);

        $select = $this->getSelect();
        
        $columns = [
            'userId' => 'user_id',
            'reportId' => 'report_id',
            'accuracy_details' => 'accuracy_details',
            'dateKeyed' => 'date_created',
            'vendorName' => new Expression('kv.vendor_name')
        ];
        
        $select->columns($columns)
        ->from(['aea' => $this->table])
        ->join(['r' => 'report'], 'r.report_id = aea.report_id', [])
        ->join(['u' => 'user'], 'u.user_id = aea.user_id', [])
        ->join(['kv' => 'keying_vendor'], 'u.keying_vendor_id = kv.keying_vendor_id', []);

        $select->where('aea.date_created >= "' . $this->getFormattedTimeString($fromDate) . '"');
        $select->where('aea.date_created < "' . $this->getFormattedTimeString($toDate . ' + 1 day') . '"');

        if (!empty($searchCriteria['state'])) {
            $select->where(["r.state_id" => $searchCriteria['state']]);
        }

        if (!empty($searchCriteria['reportID'])) {
            $select->where(["aea.report_id" => $searchCriteria['reportID']]);
        }

        if (!empty($searchCriteria['workType'])) {
            $select->where(["r.work_type_id" => $searchCriteria['workType']]);
        }

        if (!empty($searchCriteria['agencyId'])) {
            $select->where(["r.agency_id" => $searchCriteria['agencyId']]);
        }

        if (!empty($searchCriteria['userID'])) {
            $select->where(["u.username" => $searchCriteria['userID']]);
        }

        if (!empty($searchCriteria['lastName'])) {
            $select->where(["u.name_last" => $searchCriteria['lastName']]);
        }

        if (!empty($searchCriteria['firstName'])) {
            $select->where(["u.name_first" => $searchCriteria['firstName']]);
        }
        
        $keyingVendorId = $searchCriteria['keyingVendorId'];
        if (!empty($keyingVendorId) 
                && $keyingVendorId != KeyingVendorService::VENDOR_ALL) {
            $select->where(["u.keying_vendor_id" => $keyingVendorId]);
        }

        return $this->fetchAll($select);
    }
    
    /**
     * Get the count of Auto Extarction Accuracy record for a report
     *
     * @param int $reportId
     * @return int
     */
    public function hasAutoExtractAccuracyCalculated($reportId)
    {
        $select = $this->getSelect();
        $select->from($this->table);
        $columns = [
            'autoExtractionAccuracyId' => 'auto_extraction_accuracy_id'
        ];
        $select->columns($columns);
        $select->where('report_id = :report_id');
        $bind = ["report_id" => $reportId];

        return $this->fetchOne($select, $bind);
    }
    
}
