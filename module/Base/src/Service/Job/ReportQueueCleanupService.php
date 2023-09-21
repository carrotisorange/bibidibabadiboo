<?php
namespace Base\Service\Job;

use Zend\Log\Logger;

use Base\Service\Job\ProcessCheck\ProcessCheckInterface;
use Base\Service\ReportQueueService;

class ReportQueueCleanupService extends JobAbstract
{
    /**
     * @var Base\Service\ReportQueueService 
     */
    protected $serviceReportQueue;
    
    /**
     * @var Array 
     */
    protected $config;
    
    public function __construct(
        ProcessCheckInterface $jobProcess,
        ReportQueueService $serviceReportQueue,
        Array $config,
        $log)
    {
        parent::__construct(
            $jobProcess,
            $config,
            $log
        );
        
        $this->serviceReportQueue = $serviceReportQueue;
        $this->config = $config;
        $this->logger = $log;
    }
    
    protected function runJob()
    {
        $resultObj = $this->serviceReportQueue->recycle();
    }
}
