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
use Base\Service\AgencyService;
use Base\Service\StateService;
use Base\Service\FormTypeService;
use Base\Service\ReportService;
use Base\Service\ReportEntryService;
use Base\View\Helper\DataRenderer\ReportMaker;
use Base\Service\ReportFlagService;
use Admin\Controller\ViewKeyedImageController;
use Admin\Form\ViewKeyedImageForm;
use Base\Service\KeyingVendorService;

class ViewKeyedImageControllerFactory extends ControllerFactory implements FactoryInterface
{
    /**
     * @param object        $container      Interop\Container\ContainerInterface
     * @param string        $requestedName  Requested controller name
     * @param null|array    $options
     * @return object       Admin\Controller\ViewKeyedImageController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        
        $config = $container->get('Config');

        $viewKeyedImageForm = new ViewKeyedImageForm (
            $container->get(AgencyService::class),
            $container->get(StateService::class),
            $container->get(FormTypeService::class),
            $container->get(KeyingVendorService::class),
            $container->get('AuthService')
        );

        $reportMaker = new ReportMaker (
            $config,
            $container->get('Zend\View\Renderer\PhpRenderer'),
            $this->getViewModel()
        );
        
        return new ViewKeyedImageController (
            $container->get('Config'),
            $container->get('Logger'),
            $this->getSession($config),
            $viewKeyedImageForm,
            $container->get(ReportService::class),
            $container->get(ReportEntryService::class),
            $reportMaker,
            $container->get(ReportFlagService::class),
            $container->get(KeyingVendorService::class)
        );
    }
}
