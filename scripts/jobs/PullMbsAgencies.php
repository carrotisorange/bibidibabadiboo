<?php
namespace scripts\jobs;

use Base\Service\Job\PullMbsAgenciesService;

require_once realpath(dirname(__FILE__) . '/../_init.php');
$application->bootstrap();

$serviceManager = $application->getMvcEvent()->getApplication()->getServiceManager();
$serviceJob = $serviceManager->get(PullMbsAgenciesService::class);
$serviceJob->run();
