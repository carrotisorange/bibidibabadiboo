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
use Base\Service\VinStatusService;
use Base\Factory\ServiceFactory;

class VinStatusServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * VinStatus service factory to inject required parameters to vin status model service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Base\Factory\Service\StateService]
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        
        return new VinStatusService(
            $config,
            $container->get('Logger'),
            $this->getReadOnlyVinStatusAdapter($config, $container)
        );
    }
}
