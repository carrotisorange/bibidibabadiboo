<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

use Base\Factory\ServiceFactory;
use Base\Service\ImageServerService;

class ImageServerServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * Form service factory to inject required parameters to form model service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Base\Service\ImageServerService]
     */

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $configSoap = $config['app']['soap'];
        
        return new ImageServerService(
            $config,
            $container->get('Logger'),
            $this->getReportAdapter($config),
            $this->getSoapClient($configSoap, $config['imageWSDL']['url'], $config['imageWSDL']['user'], $config['imageWSDL']['password'])
        );
    }
}
