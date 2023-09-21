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
use Base\Service\Job\IsitMessageQueuePollingService;
use Auth\Service\LNAAAuthService;
use Base\Service\UserService;
use Base\Service\IsitService;
use Base\Service\KeyingVendorService;
use Base\Helper\LnHelper;

class IsitMessageQueuePollingServiceFactory extends ServiceFactory implements FactoryInterface
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
        $jobName = 'IsitMessageQueuePolling';
        $service = new IsitMessageQueuePollingService(
            new File($jobName),
            $container->get(LNAAAuthService::class),
            $container->get(UserService::class),
            $container->get(IsitService::class),
            $container->get(KeyingVendorService::class),
            new LnHelper(),
            $config,
            $this->getJobLog($config, $jobName)
        );

        return $service;
    }

}
