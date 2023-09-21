<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

use Base\Factory\ServiceFactory;
use Base\Service\ReportCruService;

class ReportCruServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * User service factory to inject required parameters to user model service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Base\Service\ReportCruService]
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $config = $container->get('Config');
        
        return new ReportCruService(
            $config,
            $container->get('Logger'),
            $this->getReportCruAdapter($config, $container)
        );
    }
}
