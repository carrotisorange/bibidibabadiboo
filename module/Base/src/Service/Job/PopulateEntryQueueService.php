<?php
namespace Base\Service\Job;

use Zend\Log\Logger;

use Base\Service\Job\ProcessCheck\ProcessCheckInterface;
use Base\Service\ReportEntryQueueService;

class PopulateEntryQueueService extends JobAbstract
{
    /**
     * @var Base\Service\ReportEntryQueueService 
     */
    protected $serviceReportEntryQueue;
        
    /**
     * @var Array 
     */
    protected $config;
    
    public function __construct(
        ProcessCheckInterface $jobProcess,
        ReportEntryQueueService $serviceReportEntryQueue,
        Array $config,
        $log)
    {
        parent::__construct(
            $jobProcess,
            $config,
            $log
        );
        
        $this->serviceReportEntryQueue = $serviceReportEntryQueue;
        $this->config = $config;
        $this->logger = $log;
    }
    
    protected function runJob() 
    {
        $resultObj = $this->serviceReportEntryQueue->populate($this->config['app']['reportEntry']['queue_distinct_limit']);
    }

}
