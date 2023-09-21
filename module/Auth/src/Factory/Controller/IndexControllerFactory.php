<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Auth\Factory\Controller;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\Helper\HeadTitle;

use Base\Factory\BaseFactory;
use Auth\Controller\IndexController;
 
class IndexControllerFactory extends BaseFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return IndexController
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $config = $container->get('Config');
        
        return new IndexController(
            $this->getSession($config),
            $container->get('ViewHelperManager')->get(HeadTitle::class)
        );
    }
}
