<?php
/**
 * @copyright (c) 2016 LexisNexis. All rights reserved.
 */
namespace Base\Factory\Service\Job;

use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;

use Base\Factory\ServiceFactory;
use Base\Service\Job\ProcessCheck\File;
use Base\Service\Job\PullMbsAgenciesService;
use Base\Service\Mbs\AgencyService as MbsAgencyService;
use Base\Service\AgencyService;
use Base\Service\Mbs\AgencyContributorySourceService as MbsAgencyContributorySourceService;
use Base\Service\AgencyContributorySourceService;
use Base\Service\StateService;
use Base\Service\MailerService;
use Base\Service\FormService;
use Base\Service\Cdi\EnumeratorService;
use Base\Service\FormWorkTypeService;
use Base\Service\WebService\CrashLogicAgencyUpdateService;
use Base\Service\EcrashUtilsArrayService;

class PullMbsAgenciesServiceFactory extends ServiceFactory implements FactoryInterface
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
        $jobName = 'PullMbsAgencies';

        $service = new PullMbsAgenciesService(
            new File($jobName),
            $container->get(MbsAgencyService::class),
            $container->get(AgencyService::class),
            $container->get(MbsAgencyContributorySourceService::class),
            $container->get(AgencyContributorySourceService::class),
            $container->get(StateService::class),
            $container->get(MailerService::class),
            $container->get(FormService::class),
            $container->get(EnumeratorService::class),
            $container->get(FormWorkTypeService::class),
            $container->get(CrashLogicAgencyUpdateService::class),
            $this->getAdapter($config),
            $config,
            $this->getJobLog($config, $jobName),
            $container->get(EcrashUtilsArrayService::class)
        );

        return $service;
    }

}
