<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Factory\Service\Cdi;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;
use Zend\Log\Logger;

use Base\Adapter\Db\BaseAdapter;
use Base\Service\Cdi\EnumeratorService;
use Base\Factory\ServiceFactory;
use Zend\Soap\Client as SoapClient;

class EnumeratorServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * EnumeratorServicefactory to inject required parameters to agency model service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Base\Service\Mbs\EnumeratorService]
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        
        return new EnumeratorService(
            $config,
            $container->get('Logger'),
            $this->getEnumerationValueAdapter($config, $container),
            $this->getEnumerationMapAdapter($config),
            $this->getEnumerationFieldAdapter($config)
        );
    }
}
