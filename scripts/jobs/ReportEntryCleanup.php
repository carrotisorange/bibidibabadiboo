<?php
namespace scripts\jobs;

use Base\Service\Job\ReportEntryCleanupService;

require_once realpath(dirname(__FILE__) . '/../_init.php');
$application->bootstrap();

$serviceManager = $application->getMvcEvent()->getApplication()->getServiceManager();
$serviceJob = $serviceManager->get(ReportEntryCleanupService::class);
$serviceJob->run();
