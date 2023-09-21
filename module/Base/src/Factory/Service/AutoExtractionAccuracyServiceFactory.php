<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

use Base\Factory\ServiceFactory;
use Base\Service\AutoExtractionAccuracyService;
use Base\Service\AutoExtractionService;
use Base\Service\ReportEntryService;
use Base\Service\UserAccuracyService;

class AutoExtractionAccuracyServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * UserAccuracy service factory to inject required parameters to useraccuracy model service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Base\Service\AutoExtractionAccuracyService]
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $config = $container->get('Config');
        
        return new AutoExtractionAccuracyService(
            $config,
            $this->getAutoExtractionAccuracyAdapter($config),
            $container->get(ReportEntryService::class),
            $container->get(AutoExtractionService::class),
            $container->get(UserAccuracyService::class),
            $this->getReportEntryDataAdapter($config),
            $this->getFormFieldAdapter($config, $container),
            $this->getReportAdapter($config),
        );
    }
}
