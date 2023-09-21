<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;

use Base\Adapter\Db\ReportNoteAdapter;

class ReportNoteService extends BaseService
{
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;

    /**
     * @var Base\Adapter\Db\ReportNoteAdapter
     */
    protected $adapterReportNote;
    
    public function __construct(
        Array $config,
        Logger $logger,
        ReportNoteAdapter $adapterReportNote)
    {
        $this->config   = $config;
        $this->logger   = $logger;
        $this->adapterReportNote   = $adapterReportNote;
    }
    
    public function add($reportId, $userId, $note, $area)
    {
        $this->adapterReportNote->add($reportId, $userId, $note, $area);
    }

    public function getReportNotesWithUsers($reportId)
    {
        return $this->adapterReportNote->getReportNotesWithUsers($reportId);
    }
}
