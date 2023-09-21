<?php
namespace Base\Service\Job;

use Zend\Log\Logger;
use Exception;

use Base\Service\Job\ProcessCheck\ProcessCheckInterface;

class ReportImageCleanupService extends JobAbstract
{
    /**
     * @var Array 
     */
    protected $config;
    
    public function __construct(
        ProcessCheckInterface $jobProcess,
        Array $config,
        $log)
    {
        parent::__construct(
            $jobProcess,
            $config,
            $log
        );

        $this->config = $config;
        $this->logger = $log;
    }
    
    protected function runJob() 
    {
        if (strcasecmp(PHP_OS, 'Linux') != 0) {
            throw new Exception('Unable to process this job on non-linux environments');
        }
        //@TODO: need to find a way to run on windows
        $reportImagePath = APPLICATION_PUBLIC_PATH . '/images/reports';
        passthru("find $reportImagePath -maxdepth 1 -type f -name '*.pdf' -mtime +1 -atime +1 -exec rm -f '{}' \;");
    }

}
