<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Auth\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\AuthenticationService;
use Interop\Container\ContainerInterface;

use Base\Service;
use Base\Factory\ServiceFactory;

/**
 * The factory is responsible for creating of authentication service.
 */
class AuthenticationServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * This method creates the Zend\Authentication\AuthenticationService and returns its instance. 
     *
     * @param object        $container      Interop\Container\ContainerInterface
     * @param string        $requestedName  Requested controller name
     * @param null|array    $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        return new AuthenticationService();
    }
}
