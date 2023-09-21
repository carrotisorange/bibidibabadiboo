<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;
use Zend\Log\Logger;
use Zend\Soap\Client as SoapClient;

use Base\Adapter\Db\BaseAdapter;
use Base\Service\UserAccuracyInvalidService;
use Base\Factory\ServiceFactory;

class UserAccuracyInvalidServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * UserAccuracyInvalid service factory to inject required parameters to useraccuracyinvalid model service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Base\Factory\Service\UserAccuracyInvalidService]
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');

        return new UserAccuracyInvalidService(
            $config,
            $container->get('Logger'),
            $this->getUserAccuracyInvalidAdapter($config, $container)
        );
    }
}
