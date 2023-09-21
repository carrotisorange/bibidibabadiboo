<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

use Base\Factory\ServiceFactory;
use Base\Service\UserEntryStageService;

class UserEntryStageServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * EntryStage service factory to inject required parameters to entrystage model service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Base\Factory\Service\UserEntryStageService]
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        return new UserEntryStageService(
            $this->getUserEntryStageAdapter(
                $container->get('Config')
            )
        );
    }
}
