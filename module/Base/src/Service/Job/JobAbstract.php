<?php
namespace Base\Service\Job;

use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Log\Logger;

use Base\Service\Job\ProcessCheck\ProcessCheckInterface;

abstract class JobAbstract
{
    /**
     * Code returned after job was successfully finished
     */
    const RETURN_CODE_SUCCESS = 0;

    /**
     * Code returned after job was failed
     */
    const RETURN_CODE_FAILED = 1;

    /**
     * @var Base\Service\Job\ProcessCheck\ProcessCheckInterface
     */
    protected $jobProcess;

    /**
     *
     * @var Array
     */
    protected $config;

    /*
     * @var Zend\Http\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var Zend\Log\Logger
     */
    protected $log;
    
    /**
     * @param Base_JobProcess $jobProcess
     */
    public function __construct(
        ProcessCheckInterface $jobProcess,
        Array $config,
        Logger $log,
        RemoteAddress $remoteAddress = null)
    {
        ini_set('memory_limit', -1);

        $this->jobProcess = $jobProcess;
        $this->config = $config;
        $this->log = $log;
        $this->remoteAddress = is_null($remoteAddress) ? new RemoteAddress() : $remoteAddress;
    }

    public function run()
    {
        $this->jobProcess->startOrAbort();
        $startTime = microtime(true);
        $result = null;

        try {
            $result = $this->runJob();
        } catch (\Exception $e) {
            echo 'An exception has occurred and the process has terminated abnormally.', "\n";
            echo $e->getMessage(), "\n";
            echo $e->getTraceAsString(), "\n";
        }
        
        $elapsedTime = microtime(true) - $startTime;
        $this->log->info("Total running time: $elapsedTime seconds");
        $this->jobProcess->stop();
        if ($result != null) {
            exit($result);
        }
    }

    abstract protected function runJob();

    /**
     * Moves file from $sourceFilePath to $targetFilePath (appends a timestamp if $appendProcessedTimestamp),
     * and logs a message with the passed in $title.
     *
     * @param string $sourceFilePath
     * @param string $targetFilePath
     * @param string $title
     * @param boolean $appendProcessedTimestamp Default true
     */
    protected function moveFile($sourceFilePath, $targetFilePath, $title = '', $appendProcessedTimestamp = true)
    {
        $sourceFilePath = realpath($sourceFilePath);
        $result = true;

        if ($appendProcessedTimestamp) {
            $targetFilePath .= '.' . date('YmdHis');
        }

        if (rename($sourceFilePath, $targetFilePath)) {
            $targetFilePath = realpath($targetFilePath); //only after rename succeeds
            $this->log->info("Moved $sourceFilePath to $targetFilePath. Reason: $title");
        } else {
            $result = false;
            $this->log->err("Failed to move $sourceFilePath to $targetFilePath. " . print_r(error_get_last(), true));
        }

        return $result;
    }

    /**
     * [getClientIp description]
     * @param  boolean $useProxy [description]
     * @return [type]            [description]
     */
    protected function getClientIp($useProxy = true)
    {
        if ($useProxy) {
            $this->remoteAddress->setUseProxy($useProxy);
        }
        
        return $this->remoteAddress->getIpAddress();
    }
}
