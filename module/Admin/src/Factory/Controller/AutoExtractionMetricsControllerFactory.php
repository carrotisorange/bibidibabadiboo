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
use Base\Service\DataTransformerService;
use Base\Service\EntryStageService;
use Base\Service\UserService;
use Base\Service\KeyingVendorService;
use Base\View\Helper\DataRenderer\ReportMaker;
use Admin\Controller\AutoExtractionMetricsController;
use Admin\Form\AutoExtractionMetric\AutoExtractionReportForm;
use Admin\Form\AutoExtractionMetric\AutoExtractionAccuracyForm;
use Admin\Form\AutoExtractionMetric\VolumeProductivityReportForm;

class AutoExtractionMetricsControllerFactory extends ControllerFactory implements FactoryInterface
{
    /**
     * @param object        $container      Interop\Container\ContainerInterface
     * @param string        $requestedName  Requested controller name
     * @param null|array    $options
     * @return object       Admin\Controller\AutoExtractionMetricsController
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $config = $container->get('Config');
        $request = $container->get('Request');
        $queryParams = $request->getPost();
        $queryParams = (!empty($queryParams)) ? (array) $queryParams : [];

        $autoExtractionReportForm = new AutoExtractionReportForm(
            $container->get(StateService::class),
            $container->get('AuthService'),
            $container->get(KeyingVendorService::class),
            $queryParams
        );

        $autoExtractionAccuracyReportForm = new AutoExtractionAccuracyForm(
            $container->get(StateService::class),
            $container->get(WorkTypeService::class),
            $container->get(AgencyService::class),
            $container->get('AuthService'),
            $container->get(KeyingVendorService::class)
        );

        $volumeProductivityReportForm = new VolumeProductivityReportForm(
            $container->get(StateService::class),
            $container->get('AuthService'),
            $container->get(KeyingVendorService::class),    
            $queryParams
        );

        $reportMaker = new ReportMaker(
            $config,
            $container->get('Zend\View\Renderer\PhpRenderer'),
            $this->getViewModel()
        );
        
        return new AutoExtractionMetricsController (
            $container->get('Config'),
            $container->get('Logger'),
            $this->getSession($config),
            $autoExtractionReportForm,
            $autoExtractionAccuracyReportForm,
            $volumeProductivityReportForm,
            $container->get(ReportService::class),
            $container->get(AutoExtractionAccuracyService::class),
            $reportMaker,
            $container->get(StateService::class),
            $container->get(ReportEntryService::class),
            $container->get(EntryStageService::class),
            $container->get(UserService::class),
            $container->get('Zend\View\Renderer\PhpRenderer'),
            $container->get(KeyingVendorService::class)    
        );
    }
}
