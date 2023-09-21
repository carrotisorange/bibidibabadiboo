<?php
namespace Base\Factory\Helper;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

use Base\View\Helper\UrlSimple;

class UrlFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $router = $container->get('Application')->getMvcEvent()->getRouter();
        // Instantiate the helper.
        return new UrlSimple($router);
    }
}
