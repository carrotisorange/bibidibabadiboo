<?php
namespace Base\Factory\Service\Menu;

use Interop\Container\ContainerInterface;

use Base\Factory\ServiceFactory;
use Base\Service\NavManagerService;

/**
 * This is the factory class for NavManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class NavManagerServiceFactory extends ServiceFactory
{
    /**
     * This method creates the NavManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $viewHelperManager = $container->get('ViewHelperManager');
        $urlHelper = $viewHelperManager->get('url');
        
        return new NavManagerService($urlHelper);
    }
}
