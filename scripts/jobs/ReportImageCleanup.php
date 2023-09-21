<?php
namespace scripts\jobs;

use Base\Service\Job\ReportImageCleanupService;

require_once realpath(dirname(__FILE__) . '/../_init.php');
$application->bootstrap();

$serviceManager = $application->getMvcEvent()->getApplication()->getServiceManager();
$serviceJob = $serviceManager->get(ReportImageCleanupService::class);
$serviceJob->run();
