<?php
namespace Base\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

use Base\View\Helper\Menu;
use Base\Service\NavManagerService;
use Base\Acl\Acl;

/**
 * This is the factory for Menu view helper. Its purpose is to instantiate the
 * helper and init menu items.
 */
class MenuFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $navManagerService = $container->get(NavManagerService::class);
        
        // Get menu items.
        $menus = $navManagerService->getMenus();
        
        // Instantiate the Menu helper.
        return new Menu($menus, $container->get('AuthService'), new Acl());
    }
}