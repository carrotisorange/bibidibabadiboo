<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Auth\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

use Base\Factory\ServiceFactory;
use Auth\Service\MAEAdapterService;

/**
 * The factory is responsible for creating of MAEAdapterService.
 */
class MAEAdapterServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * @param object        $container      Interop\Container\ContainerInterface
     * @param string        $requestedName  Requested controller name
     * @param null|array    $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        
        return new MAEAdapterService(
            $container->get('Logger'),
            $config,
            $this->getMaeAuthAdapter($config)
        );
    }
}
