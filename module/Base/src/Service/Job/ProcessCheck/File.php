<?php

namespace Base\Service\Job\ProcessCheck;

class File implements ProcessCheckInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name 
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Only start if not already running, else exit everything.
     */
    public function startOrAbort()
    {
        if ($this->isRunning()) {
            echo 'Process is already running. Aborting.', "\n";
            exit;
        }

        $this->start();
    }

    /**
     * Start and log the process.
     */
    public function start()
    {
        $pid = (function_exists('posix_getpid')) ? posix_getpid() : $this->getPidViaHack();
        file_put_contents(
            $this->getPidFile(),
            $pid
        );
    }

    /**
     * Check if the process is already running.
     *
     * @return boolean
     */
    public function isRunning()
    {
        $pidFile = $this->getPidFile();
        if (file_exists($pidFile)) {
            if (strcasecmp(PHP_OS, 'Linux') == 0) {
                $pid = trim(file_get_contents($pidFile));
                $pArgv = explode(' ', trim(`ps -o command= -p $pid`));
                if (!empty($pArgv) && basename($pArgv[0]) == 'php' && basename($pArgv[1], '.php') == $this->name) {
                    return true;
                } else {
                    $this->stop();
                    return false;
                }
            } else {
                echo 'Process may be running already; unable to determine.', "\n";
                return true;
            }
        }

        return false;
    }

    /**
     * Log that the process is no longer running.
     */
    public function stop()
    {
        unlink($this->getPidFile());
    }

    /**
     * Specify the Process Id file.
     *
     * @return string
     */
    protected function getPidFile()
    {
        return sys_get_temp_dir() . '/phpJob_' . $this->name . '_' . md5(APPLICATION_PATH) . '.pid';
    }

    protected function getPidViaHack()
    {
        if ( in_array(PHP_OS, ['WIN32', 'WINNT', 'Windows']) ) {
            return 1; //Don't care - dev machine
        }
        else {
            $ps = explode(' ', trim(`ps -o pid,comm | grep php | sort -nr | tail -n 1`));
            return $ps[0];
        }
    }
}
