<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;

use Base\Adapter\Db\ReportAdapter;
use Base\Adapter\Db\ReportStatusAdapter;
use Base\Adapter\Db\ReadOnly\ReportStatusAdapter as ReadOnlyReportStatusAdapter;

class ReportStatusService extends BaseService
{
    /**
     * The report is in the keying process.
     */
    const STATUS_KEYING = 'keying';
    /**
     * The report has been fully processed
     */
    const STATUS_COMPLETE = 'complete';
    /**
     * Report entry data has been translated to extract tables
     */
    const STATUS_TRANSLATED = 'translated';
    /**
     * Report has been marked as a bad image.
     */
    const STATUS_BAD_IMAGE = 'bad image';
    /**
     * Report has been marked as discarded.
     */
    const STATUS_DISCARDED = 'discarded';
    /**
     * Report has been marked for reordering.
     */
    const STATUS_REORDERED = 'reordered';
    /**
     * Report has been discarded and not reordered; it's not going anywhere.
     */
    const STATUS_DEAD = 'dead';
    
    /**
     * @var Array
     */
    protected $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;

    /**
     * @var Base\Adapter\Db\ReportAdapter
     */
    protected $adapterReport;
    
    /**
     * @var Base\Adapter\Db\ReportStatusAdapter
     */
    protected $adapterReportStatus;

    /**
     * @var Base\Adapter\Db\ReadOnly\ReportStatusAdapter
     */
    protected $adapterReadOnlyReportStatus;
    
    public function __construct(
        Array $config,
        Logger $logger,
        ReportStatusAdapter $adapterReportStatus,
        ReadOnlyReportStatusAdapter $adapterReadOnlyReportStatus,
        ReportAdapter $adapterReport)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterReportStatus = $adapterReportStatus;
        $this->adapterReadOnlyReportStatus = $adapterReadOnlyReportStatus;
        $this->adapterReport = $adapterReport;
    }

    public function getReportCount($keyingVendorId, $stateId = null, $agencyId = null)
    {
        $result = [];
        if (strtolower($stateId) == 'all' && strtolower($agencyId) == 'all') {
            // why the heck are we doing this (4 SQL calls if 'state' && 'agency' == all)
            // and simply not using the method below (under the "else") ?????
            // Because the method below is too slow to retrieve all records from
            // a big database (like in production).
            $resultA = [];
            $resultB = [];
            $resultC = [];
            $resultD = [];
            $resultE = [];
            
            $resultA = $this->adapterReadOnlyReportStatus->fetchReportCountNullOnlyAgencyOfReportEntryNull($keyingVendorId);
            $resultB = $this->adapterReadOnlyReportStatus->fetchReportCountNullOnlyAgencyOfReportEntryNotNull($keyingVendorId);
            $resultC = $this->adapterReadOnlyReportStatus->fetchReportCountNullOnlyAgencyOtherThanUnavailableReportStatus($keyingVendorId);
            
            //set array with zero values for each status
            if (empty($resultA)) $resultA[''] = ['name' => null, 'available' => 0];
            if (empty($resultB)) $resultB[''] = ['name' => null, 'inProgress' => 0];
            
            if (!empty($resultC)) {
                foreach($resultC as $row) {
                    if ($row['status'] == "bad image") {
                        $row['status'] = "bad";
                    }
                    $resultD[''][$row['status']] = $row['count'];
                }
                //complete status is not included by default
                if (!isset($resultD['']['complete'])) $resultD['']['complete'] = 0;
            } else {
                //set array with zero values for each status
                $resultD[''] = ['bad' => 0, 'discarded' => 0, 'reordered' => 0, 'dead' => 0, 'complete' => 0];
            }
            
            $resultE[''] = array_merge($resultA[''], $resultB[''], $resultD['']);
            $resultA = $this->adapterReadOnlyReportStatus->fetchReportCountNotNullAgency($keyingVendorId);
            $result = array_merge($resultE, $resultA);
        } else {
            $result = $this->adapterReadOnlyReportStatus->fetchReportCount($keyingVendorId, $stateId, $agencyId);
        }

        return $result;
    }

    public function set($reportId, $status, $userId = null)
    {
        $this->adapterReport->setStatus(
            $reportId,
            $this->getStatusId($status),
            $userId
        );
    }
    
    public function getStatusId($status)
    {
        return $this->adapterReportStatus->getIdByStatus($status);
    }
    
    public function getStatusByReportId($reportId)
    {
        return $this->adapterReportStatus->getStatusByReportId($reportId);
    }
}
