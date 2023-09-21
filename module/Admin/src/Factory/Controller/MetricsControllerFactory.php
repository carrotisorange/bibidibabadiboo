<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Factory\Controller;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Validator\Date;
use Zend\View\Renderer\PhpRenderer;
use Zend\Session\Container;

use Base\Service\AgencyService;
use Base\Service\StateService;
use Base\Service\ReportService;
use Base\View\Helper\DataRenderer\ReportMaker;
use Base\Service\ReportStatusService;
use Base\Service\FormService;
use Base\Service\UserAccuracyService;
use Base\Service\UserAccuracyInvalidService;
use Base\Service\UserService;
use Base\Service\ReportEntryService;
use Base\Service\WorkTypeService;
use Base\Service\VinStatusService;
use Base\Service\KeyingVendorService;
use Base\Factory\ControllerFactory;
use Admin\Form\Metric\VinStatusByOperatorForm;
use Admin\Controller\MetricsController;
use Admin\Form\Metric\ImageStatusByAgencyForm;
use Admin\Form\Metric\OperatorByAgencyStatsForm;
use Admin\Form\Metric\OperatorKeyingAccuracyForm;
use Admin\Form\Metric\OperatorSummaryStatsForm;
use Admin\Form\Metric\VinStatusSummaryForm;
use Admin\Form\Metric\SlaStatusSummaryForm;

class MetricsControllerFactory extends ControllerFactory implements FactoryInterface
{
    /**
     * @param object        $container      Interop\Container\ContainerInterface
     * @param string        $requestedName  Requested controller name
     * @param null|array    $options
     * @return object       Admin\Controller\MetricsController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $imageStatusByAgencyForm = new ImageStatusByAgencyForm(
            $container->get(AgencyService::class),
            $container->get(StateService::class),
            $container->get('AuthService'),
            $container->get(KeyingVendorService::class)
        );
        
        $operatorByAgencyStatsForm = new OperatorByAgencyStatsForm(
            $container->get('AuthService'),
            $container->get(KeyingVendorService::class)
        );
        
        $operatorKeyingAccuracyForm = new OperatorKeyingAccuracyForm(
            $container->get(AgencyService::class),
            $container->get(StateService::class),
            $container->get(FormService::class),
            $container->get('AuthService'),
            $container->get(KeyingVendorService::class)
        );
        
        $operatorSumaryStatsForm = new OperatorSummaryStatsForm(
            $container->get('AuthService'),
            $container->get(KeyingVendorService::class)
        );
        
        $vinStatusByOperatorForm = new VinStatusByOperatorForm(
            $container->get('AuthService'),
            $container->get(KeyingVendorService::class)
        );
        
        $vinStatusSummaryForm = new VinStatusSummaryForm(
            $container->get('AuthService'),
            $container->get(KeyingVendorService::class)    
        );
        
        $slaStatusSummaryForm = new SlaStatusSummaryForm(
            $container->get(StateService::class),
            $container->get('AuthService'),
            $container->get(KeyingVendorService::class),
            $container->get(WorkTypeService::class)
        );

        $reportMaker = new ReportMaker(
            $config,
            $container->get('Zend\View\Renderer\PhpRenderer'),
            $this->getViewModel()
        );

        return new MetricsController(
            $container->get('Config'),
            $container->get('Logger'),
            $this->getSession($config),
            $imageStatusByAgencyForm,
            $operatorByAgencyStatsForm,
            $operatorKeyingAccuracyForm,
            $operatorSumaryStatsForm,
            $vinStatusSummaryForm,
            $vinStatusByOperatorForm,
            $slaStatusSummaryForm,
            $container->get(ReportService::class),
            $reportMaker,
            $container->get(AgencyService::class),
            $container->get(ReportStatusService::class),
            new Date(),
            $container->get(UserAccuracyService::class),
            $container->get(UserAccuracyInvalidService::class),
            $container->get(UserService::class),
            $container->get(ReportEntryService::class),
            $container->get(VinStatusService::class),
            $container->get(WorkTypeService::class),
            $container->get(KeyingVendorService::class)
        );
    }
}
