<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Factory\Controller;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\Container;
use Zend\View\Renderer\PhpRenderer;

use Base\Factory\ControllerFactory;
use Base\Service\StateService;
use Base\Service\StateConfigurationService;
use Admin\Controller\StateConfigurationController;
use Admin\Form\StateConfigurationForm;
use Base\Service\WorkTypeService;

class StateConfigurationControllerFactory extends ControllerFactory implements FactoryInterface
{
    /**
     * @param object        $container      Interop\Container\ContainerInterface
     * @param string        $requestedName  Requested controller name
     * @param null|array    $options
     * @return object       Admin\Controller\StateConfigurationController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        
        $config = $container->get('Config');

        $stateConfigurationForm = new StateConfigurationForm(
            $container->get(WorkTypeService::class)
        );
        
        return new StateConfigurationController (
            $container->get('Config'),
            $container->get('Logger'),
            $this->getSession($config),
            $stateConfigurationForm,
            $container->get(StateConfigurationService::class),
            $container->get(StateService::class)
        );
    }
}
