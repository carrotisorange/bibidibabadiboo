<?php
namespace Base\Service\Job;

use Zend\Log\Logger;

use Base\Service\Job\ProcessCheck\ProcessCheckInterface;
use Base\Service\ReportEntryService;

class ReportEntryCleanupService extends JobAbstract
{
    /**
     * @var Base\Service\ReportEntryService
     */
    protected $serviceReportEntry;
    
    /**
     * @var Array
     */
    protected $config;
    
    public function __construct(
        ProcessCheckInterface $jobProcess,
        ReportEntryService $serviceReportEntry,
        Array $config,
        $log) 
    {
        parent::__construct(
            $jobProcess,
            $config,
            $log
        );
        
        $this->serviceReportEntry = $serviceReportEntry;
        $this->config = $config;
        $this->logger = $log;
    }
    
    protected function runJob()
    {
        $configCleanup = $this->config['app']['keying']['cleanup'];
        $timeUnit = $configCleanup['timeUnit'];
        $timeLength = $configCleanup['timeLength'];

        $this->logCleanableRecords($timeLength, $timeUnit);
        $this->logger->log(Logger::INFO, 'ReportEntryCleanup started');

        $resultObj = $this->serviceReportEntry->cleanUp($timeLength,$timeUnit);

        $this->logger->log(Logger::INFO, 'ReportEntryCleanup finished');
    }

    protected function logCleanableRecords($timeLength, $timeUnit)
    {
        $result = $this->serviceReportEntry->logCleanableRecords($timeLength, $timeUnit);

        $output = "\n\n#### Cleanup for " . date('Y-m-d H:i:s') . " found " . count($result) . " records";

        if (count($result) > 0) {
            $output .= "\n" . implode(",", array_keys($result[0]));
            foreach ($result as $record) {
                $output .= "\n" . implode(' ,', $record);
            }
        }

        $filename = $this->config['app']['log']['path'] . '/job.ReportEntryCleanup.log';

        if (!file_exists($filename)) {
            touch($filename);
            chmod($filename, 0666);
        }

        file_put_contents($filename, $output, FILE_APPEND);
    }
}
