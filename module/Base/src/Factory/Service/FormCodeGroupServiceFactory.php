<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;
use Zend\Log\Logger;
use Base\Adapter\Db\BaseAdapter;
use Base\Service\FormCodeGroupService;
use Base\Factory\ServiceFactory;
use Zend\Soap\Client as SoapClient;

class FormCodeGroupServiceFactory extends ServiceFactory implements FactoryInterface
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
     * @return object       [Base\Factory\Service\FormService]
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        return new FormCodeGroupService(
            $config,
            $container->get('Logger'),
            $this->getFormCodeGroupAdapter($config)
        );
    }
}
