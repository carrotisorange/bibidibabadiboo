<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Factory\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Base\Factory\ServiceFactory;
use Base\Service\UserService;

class UserServiceFactory extends ServiceFactory implements FactoryInterface
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
     * @return object       [Base\Service\UserService]
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $config = $container->get('Config');
        
        return new UserService(
            $config,
            $container->get('Logger'),
            $this->getUserAdapter($config, $container->get('Logger')),
            $this->getUserRoleAdapter($config),
            $this->getIpRestrictAdapter($config),
            $this->getReportEntryAdapter($config),
            $this->getReportQueueAdapter($config),
            $this->getReportEntryQueueAdapter($config, $container),
            $this->getUserEntryPrefetchAdapter($config)
        );
    }
}
