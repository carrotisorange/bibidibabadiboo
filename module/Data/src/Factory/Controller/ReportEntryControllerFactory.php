<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Data\Factory\Controller;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

use Base\Factory\BaseFactory;
use Base\Service\UserService;
use Base\Service\WorkTypeService;
use Base\Service\ReportEntryService;
use Base\Service\ReportEntryQueueService;
use Base\Service\EntryStageService;
use Base\Service\ReportService;
use Base\Service\FormService;
use Base\Service\FormCodeGroupConfigurationService;
use Base\Service\FormCodeMapService;
use Base\Service\AgencyService;
use Base\Service\FormFieldAttributeService;
use Base\Service\ReportCruService;
use Base\Service\RekeyService;
use Base\Service\ReportQueueService;
use Base\Service\UserEntryPrefetchService;
use Base\Service\ReportFlagService;
use Base\Service\ReportStatusService;
use Base\Service\ReportNoteService;
use Base\Service\ImageServerService;
use Base\Service\FormFieldService;
use Base\Service\UserAccuracyService;
use Base\Service\DataTransformerService;
use Base\View\Helper\DataRenderer\ReportMaker;
use Base\Service\AutoExtractionService;
use Base\Service\AutoExtractionAccuracyService;
use Data\Controller\ReportEntryController;
use Data\Form\WorkTypeSelectionForm;

class ReportEntryControllerFactory extends BaseFactory implements FactoryInterface
{
    /**
     * @param object        $container      Interop\Container\ContainerInterface
     * @param string        $requestedName  Requested controller name
     * @param null|array    $options
     * @return object       Data\Controller\ReportEntryController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');

        $reportMaker = new ReportMaker (
            $config,
            $container->get('Zend\View\Renderer\PhpRenderer'),
            $this->getViewModel()
        );

        // Invoke controller
        return new ReportEntryController(
            $config,
            $this->getSession($config),
            $container->get('Logger'),
            $container->get('ViewRenderer'),
            new WorkTypeSelectionForm(),
            $container->get(UserService::class),
            $container->get(WorkTypeService::class),
            $container->get(ReportEntryService::class),
            $container->get(ReportEntryQueueService::class),
            $container->get(ReportService::class),
            $container->get(EntryStageService::class),
            $container->get(FormService::class),
            $container->get(FormCodeGroupConfigurationService::class),
            $container->get(FormCodeMapService::class),
            $container->get(AgencyService::class),
            $container->get(RekeyService::class),
            $container->get(FormFieldAttributeService::class),
            $container->get(ReportCruService::class),
            $container->get(ReportQueueService::class),
            $container->get(UserEntryPrefetchService::class),
            $container->get(ReportFlagService::class),
            $container->get(ReportStatusService::class),
            $container->get(ReportNoteService::class),
            $container->get(ImageServerService::class),
            $container->get(FormFieldService::class),
            $container->get(UserAccuracyService::class),
            $container->get(AutoExtractionService::class),
            $container->get(DataTransformerService::class),
            $container->get(AutoExtractionAccuracyService::class),
            $reportMaker,
            $container->get('Zend\View\Renderer\PhpRenderer')
        );
    }
}