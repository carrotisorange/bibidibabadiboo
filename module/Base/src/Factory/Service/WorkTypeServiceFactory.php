<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

use Base\Factory\ServiceFactory;
use Base\Service\WorkTypeService;

class WorkTypeServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * WorkTypeService service factory to inject required parameters to work type model service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Base\Factory\Service\WorkTypeService]
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        
        return new WorkTypeService(
            $config,
            $container->get('Logger'),
            $this->getWorkTypeAdapter($config)
        );
    }
}
