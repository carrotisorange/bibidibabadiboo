<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Factory\Controller;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Helper\HeadTitle;

use Base\Factory\BaseFactory;
use Base\Service\FormService;
use Base\Service\FormCodeGroupService;
use Base\Service\FormCodeListGroupMapService;
use Base\Service\FormCodeListService;
use Base\Service\FormCodeListPairMapService;
use Base\Service\FormCodePairService;
use Admin\Controller\AssignFormCodeValuesController;


class AssignFormCodeValuesControllerFactory extends BaseFactory implements FactoryInterface
{
    /**
     * @param object        $container      Interop\Container\ContainerInterface
     * @param string        $requestedName  Requested controller name
     * @param null|array    $options
     * @return object       Admin\Controller\AssignFormCodeValuesController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');  
        return new  AssignFormCodeValuesController(
            $container->get('Config'),
            $container->get('Logger'),
            $this->getSession($config),
            $container->get(FormService::class),
            $container->get(FormCodeGroupService::class),
            $container->get(FormCodeListService::class),
            $container->get(FormCodeListGroupMapService::class),
            $container->get(FormCodeListPairMapService::class),
            $container->get(FormCodePairService::class),
            $container->get('ViewHelperManager')->get(HeadTitle::class)
            
        );
    }
}
