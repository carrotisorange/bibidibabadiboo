<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

use Base\Factory\ServiceFactory;
use Base\Service\ReportService;
use Base\Service\StateConfigurationService;
use Base\Service\ReportEntryService;

class ReportServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * Report service factory to inject required parameters to report model service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Base\Factory\Service\ReportService]
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $config = $container->get('Config');
        
        return new ReportService(
            $config,
            $container->get('Logger'),
            $this->getReportAdapter($config),
            $this->getReadOnlyReportAdapter($config),
            $this->getReportNoteAdapter($config),
            $this->getReportCruAdapter($config),
            $container->get(StateConfigurationService::class),
            $container->get(ReportEntryService::class),
            $this->getReportEntryDataAdapter($config)
        );
    }
}
