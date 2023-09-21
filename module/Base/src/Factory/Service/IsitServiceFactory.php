<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

use Base\Factory\ServiceFactory;
use Base\Service\IsitService;
use Base\Service\IsitTicketService;
use Base\Service\IsitWebService;
use Base\Service\UserService;

class IsitServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * Isit service factory to inject required parameters to Isit model service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Base\Service\IsitService]
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $config = $container->get('Config');
        
        return new IsitService(
            $config,
            $container->get('Logger'),
            $container->get(IsitTicketService::class),
            $this->getIsitTicketStatusAdapter($config),
            $container->get(IsitWebService::class),
            $container->get(UserService::class)
        );
    }
}
