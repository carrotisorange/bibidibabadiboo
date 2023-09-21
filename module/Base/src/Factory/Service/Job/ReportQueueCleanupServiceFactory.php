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
use Base\Service\Job\ReportQueueCleanupService;
use Base\Service\ReportQueueService;

class ReportQueueCleanupServiceFactory extends ServiceFactory implements FactoryInterface
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
        $jobName = 'ReportQueueCleanup';
        
        $service = new ReportQueueCleanupService(
            new File($jobName),
            $container->get(ReportQueueService::class),
            $config,
            $this->getJobLog($config, $jobName)
        );

        return $service;
    }

}
