<?php
/**
 * @copyright (c) 2016 LexisNexis. All rights reserved.
 */
namespace Base\Factory\Service\Job;

use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

use Base\Factory\ServiceFactory;
use Base\Service\Job\ProcessCheck\File;
use Base\Service\Job\ReportEntryCleanupService;
use Base\Service\ReportEntryService;

class ReportEntryCleanupServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * DON'T REMOVE THIS, FactoryInterface will not like it !!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }

    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $config = $container->get('Config');
        $jobName = 'ReportEntryCleanup';
        
        $service = new ReportEntryCleanupService(
            new File($jobName),
            $container->get(ReportEntryService::class),
            $config,
            $this->getJobLog($config, $jobName)
        );

        return $service;
    }

}
