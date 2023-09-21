<?php

namespace Base\Service\Job\ProcessCheck;

interface ProcessCheckInterface
{
    public function isRunning();
    public function start();
    public function startOrAbort();
    public function stop();
}
