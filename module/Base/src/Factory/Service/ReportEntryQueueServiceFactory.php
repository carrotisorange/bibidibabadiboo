<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

use Base\Factory\ServiceFactory;
use Base\Service\ReportEntryQueueService;

class ReportEntryQueueServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * ReportEntryQueue service factory to inject required parameters to ReportEntryQueue service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Base\Factory\Service\ReportEntryQueueService]
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');

        $service = new ReportEntryQueueService(
            $config,
            $container->get('Logger'),
            $this->getReportEntryQueueAdapter($config, $container),
            $this->getReportEntryQueueHistoryAdapter($config),
            $this->getReportEntryAdapter($config),
            $this->getEntryStageProcessAdapter($config)
        );

        return $service;
    }
}
