<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Factory\Controller;

use Zend\Session\SessionManager;
use Zend\Session\Container;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

use Base\Factory\BaseFactory;
use Base\Helper\LnHelper;
use Admin\Controller\InvalidVinController;

class InvalidVinControllerFactory extends BaseFactory implements FactoryInterface
{
    /**
     * @param object        $container      Interop\Container\ContainerInterface
     * @param string        $requestedName  Requested controller name
     * @param null|array    $options
     * @return object       Admin\Controller\InvalidVinController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        
        return new InvalidVinController(
            $config,
            $this->getSession($config),
            $container->get('Logger'),
            new LnHelper(
                $config
            )
        );
    }
}
