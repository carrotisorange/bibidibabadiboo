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
use Base\Service\ReportService;
use Base\Service\WorkTypeService;
use Base\Service\AgencyService;
use Base\Service\AutoExtractionAccuracyService;
use Base\Service\ReportEntryService;
use Base\Service\EntryStageService;
use Base\Service\UserService;
use Base\View\Helper\DataRenderer\ReportMaker;
use Admin\Controller\QualityControlController;
use Admin\Form\QualityControlForm;
use Base\Service\FormFieldService;
use Base\Service\QualityControlRemarkService;

class QualityControlControllerFactory extends ControllerFactory implements FactoryInterface
{
    /**
     * @param object        $container      Interop\Container\ContainerInterface
     * @param string        $requestedName  Requested controller name
     * @param null|array    $options
     * @return object       Admin\Controller\QualityControlController
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $config = $container->get('Config');
        $request = $container->get('Request');
        $queryParams = $request->getPost();
        $queryParams = (!empty($queryParams)) ? (array) $queryParams : [];

   
        $qualityControlForm = new QualityControlForm(
            $container->get(StateService::class),
            $container->get(WorkTypeService::class),
            $container->get(AgencyService::class)
        );

        $reportMaker = new ReportMaker(
            $config,
            $container->get('Zend\View\Renderer\PhpRenderer'),
            $this->getViewModel()
        );
        
        return new  QualityControlController (
            $container->get('Config'),
            $container->get('Logger'),
            $container->get('AuthService'),
            $this->getSession($config),
            $qualityControlForm,
            $container->get(ReportService::class),
            $container->get(AutoExtractionAccuracyService::class),
            $reportMaker,
            $container->get(StateService::class),
            $container->get(WorkTypeService::class),
            $container->get(ReportEntryService::class),
            $container->get(EntryStageService::class),
            $container->get(UserService::class),
            $container->get(QualityControlRemarkService::class),
            $container->get(FormFieldService::class),
            $container->get('Zend\View\Renderer\PhpRenderer')
        );
    }
}
