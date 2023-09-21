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
use Base\Service\AgencyContributorySourceService;
use Base\Factory\ServiceFactory;
use Base\Service\AgencyService;

class AgencyContributorySourceServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * AgencyContributorySourceService service factory to inject required parameters to agency model service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Base\Factory\Service\Mbs\AgencyContributorySourceService]
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');

        return new AgencyContributorySourceService(
            $config,
            $container->get('Logger'),
            $this->getAgencyContributorySourceAdapter($config, $container),
            $container->get(AgencyService::class)
        );

    }
}
