<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

use Base\Factory\ServiceFactory;
use Base\Service\IsitWebService;
use Base\Service\IsitTicketService;
use Base\Service\UserService;

class IsitWebServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * State service factory to inject required parameters to state model service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Base\Factory\Service\IsitService]
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $config = $container->get('Config');
        
        return new IsitWebService(
            $config,
            $container->get('Logger'),
            $container->get(IsitTicketService::class),
            $this->getIsitTicketTypeAdapter($config),
            $this->getIsitTicketLogAdapter($config),
            $this->getIsitTicketLogTypeAdapter($config),
            $this->getIsitTicketStatusAdapter($config),
            $container->get(UserService::class),
            $this->getClientIsitCurl($config, $container),
            $container->get('AuthService')
        );
    }
}
