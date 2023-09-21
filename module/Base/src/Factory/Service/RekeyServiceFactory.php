<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

use Base\Factory\ServiceFactory;
use Base\Service\RekeyService;
use Base\Service\ReportService;
use Base\Service\ReportStatusService;
use Base\Service\ReportEntryQueueService;
use Base\Service\EntryStageService;
use Base\Service\FormService;
use Base\Service\ReportEntryService;

class RekeyServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * Rekey service factory to inject required parameters to Rekey model service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Base\Service\RekeyService]
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        
        $service = new RekeyService(
            $config,
            $container->get('Logger'),
            $this->getReportEntryAdapter($config),
            $this->getFormAdapter($config),
            $this->getReportAdapter($config),
            $this->getFormsToRekeyAdapter($config),
            $container->get(ReportService::class),
            $container->get(ReportStatusService::class),
            $container->get(ReportEntryQueueService::class),
            $this->getRekeyUserFormPermissionAdapter($config),
            $container->get(EntryStageService::class),
            $container->get(FormService::class),
            $container->get(ReportEntryService::class)
        );
        
        return $service;
    }
}
