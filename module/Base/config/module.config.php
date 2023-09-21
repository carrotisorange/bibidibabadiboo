<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */
namespace Base;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'service_manager' => [
        'factories' => [
            'Logger' => Factory\LoggerFactory::class,
            Service\NavManagerService::class => Factory\Service\Menu\NavManagerServiceFactory::class,
            Service\UserService::class => Factory\Service\UserServiceFactory::class,
            Service\UserRoleService::class => Factory\Service\UserRoleServiceFactory::class,
            Service\EntryStageService::class => Factory\Service\EntryStageServiceFactory::class,
            Service\StateService::class => Factory\Service\StateServiceFactory::class,
            Service\UserAccuracyService::class => Factory\Service\UserAccuracyServiceFactory::class,
            Service\UserAccuracyInvalidService::class => Factory\Service\UserAccuracyInvalidServiceFactory::class,
            Service\VinStatusService::class => Factory\Service\VinStatusServiceFactory::class,
            Service\AgencyService::class => Factory\Service\AgencyServiceFactory::class,
            Service\FormService::class => Factory\Service\FormServiceFactory::class,
            Service\WorkTypeService::class => Factory\Service\WorkTypeServiceFactory::class,
            Service\FormWorkTypeService::class => Factory\Service\FormWorkTypeServiceFactory::class,
            Service\UserEntryStageService::class => Factory\Service\UserEntryStageServiceFactory::class,
            Service\UserNoteService::class => Factory\Service\UserNoteServiceFactory::class,
            Service\UserFormPermissionService::class => Factory\Service\UserFormPermissionServiceFactory::class,
            Service\ReportStatusService::class => Factory\Service\ReportStatusServiceFactory::class,
            Service\IsitService::class => Factory\Service\IsitServiceFactory::class,
            Service\IsitWebService::class => Factory\Service\IsitWebServiceFactory::class,
            Service\IsitTicketService::class => Factory\Service\IsitTicketServiceFactory::class,
            Service\MbsAuthService::class => Factory\Service\MbsAuthServiceFactory::class,
            Service\ReportEntryService::class => Factory\Service\ReportEntryServiceFactory::class,
            Service\ReportEntryQueueService::class => Factory\Service\ReportEntryQueueServiceFactory::class,
            Service\FormCodeGroupConfigurationService::class => Factory\Service\FormCodeGroupConfigurationServiceFactory::class,
            Service\FormCodeMapService::class => Factory\Service\FormCodeMapServiceFactory::class,
            Service\RekeyService::class => Factory\Service\RekeyServiceFactory::class,
            Service\FormFieldAttributeService::class => Factory\Service\FormFieldAttributeServiceFactory::class,
            Service\ReportCruService::class => Factory\Service\ReportCruServiceFactory::class,
            Service\ReportQueueService::class => Factory\Service\ReportQueueServiceFactory::class,
            Service\UserEntryPrefetchService::class => Factory\Service\UserEntryPrefetchServiceFactory::class,
            Service\ReportFlagService::class => Factory\Service\ReportFlagServiceFactory::class,
            Service\DataTransformerService::class => Factory\Service\DataTransformerServiceFactory::class,
            Service\ReportNoteService::class => Factory\Service\ReportNoteServiceFactory::class,
            Service\ImageServerService::class => Factory\Service\ImageServerServiceFactory::class,
            Service\ReportService::class => Factory\Service\ReportServiceFactory::class,
            Service\FormFieldService::class => Factory\Service\FormFieldServiceFactory::class,
            Service\FormNoteService::class => Factory\Service\FormNoteServiceFactory::class,
            Service\FormTypeService::class => Factory\Service\FormTypeServiceFactory::class,
            Service\VendorService::class => Factory\Service\VendorServiceFactory::class,
            Service\Job\ReportImageCleanupService::class => Factory\Service\Job\ReportImageCleanupServiceFactory::class,
            Service\Job\ReportEntryCleanupService::class => Factory\Service\Job\ReportEntryCleanupServiceFactory::class,
            Service\Job\ReportQueueCleanupService::class => Factory\Service\Job\ReportQueueCleanupServiceFactory::class,
            Service\Job\PopulateEntryQueueService::class => Factory\Service\Job\PopulateEntryQueueServiceFactory::class,
            Service\UsersExportToAuditHelperService::class => Factory\Service\UsersExportToAuditHelperServiceFactory::class,
            Service\Job\UsersExportToAuditService::class => Factory\Service\Job\UsersExportToAuditServiceFactory::class,
            Service\Mbs\AgencyService::class => Factory\Service\Mbs\AgencyServiceFactory::class,
            Service\Mbs\AgencyContributorySourceService::class => Factory\Service\Mbs\AgencyContributorySourceServiceFactory::class,
            Service\AgencyContributorySourceService::class => Factory\Service\AgencyContributorySourceServiceFactory::class,
            Service\MailerService::class => Factory\Service\MailerServiceFactory::class,
            Service\Cdi\EnumeratorService::class => Factory\Service\Cdi\EnumeratorServiceFactory::class,
            Service\WebService\CrashLogicAgencyUpdateService::class => Factory\Service\WebService\CrashLogicAgencyUpdateServiceFactory::class,
            Service\EcrashUtilsArrayService::class => Factory\Service\EcrashUtilsArrayServiceFactory::class,
            Service\Job\PullMbsAgenciesService::class => Factory\Service\Job\PullMbsAgenciesServiceFactory::class,
            Service\Job\IsitMessageQueuePollingService::class => Factory\Service\Job\IsitMessageQueuePollingServiceFactory::class,
            Service\FormCodeGroupService::class => Factory\Service\FormCodeGroupServiceFactory::class,
            Service\FormCodeListGroupMapService::class => Factory\Service\FormCodeListGroupMapServiceFactory::class,
            Service\FormCodeListService::class => Factory\Service\FormCodeListServiceFactory::class,
            Service\FormCodeListPairMapService::class => Factory\Service\FormCodeListPairMapServiceFactory::class,
            Service\FormCodePairService::class => Factory\Service\FormCodePairServiceFactory::class,
            Service\StateConfigurationService::class => Factory\Service\StateConfigurationServiceFactory::class,
            Service\AutoExtractionService::class => Factory\Service\AutoExtractionServiceFactory::class,
            Service\AutoExtractionAccuracyService::class => Factory\Service\AutoExtractionAccuracyServiceFactory::class,
            Service\KeyingVendorService::class => Factory\Service\KeyingVendorServiceFactory::class,
            Service\QualityControlRemarkService::class => Factory\Service\QualityControlRemarkServiceFactory::class
        ],
    ],
    
    'session_containers' => [
        'ECR'
    ],
    
    'view_helpers' => [
        'factories' => [
            View\Helper\Menu::class => View\Helper\Factory\MenuFactory::class,
            View\Helper\UrlSimple::class => Factory\Helper\UrlFactory::class,
            Helper\LnHelper::class => InvokableFactory::class,
        ],
        'aliases' => [
           'mainMenu' => View\Helper\Menu::class,
           'LnHelper' => Helper\LnHelper::class,
           'urlHelper' => View\Helper\UrlSimple::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'            => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map'       => [
            'layout/layout'    => __DIR__ . '/../view/layout/layout.phtml',
            'base/index/index' => __DIR__ . '/../view/base/index/index.phtml',
            'error/404'        => __DIR__ . '/../view/error/404.phtml',
            'error/index'      => __DIR__ . '/../view/error/index.phtml',
            'partial/index'      => __DIR__ . '/../view/partial/Messages.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'template_map' => [
            'reportview' => __DIR__ . '/../../Base/view/partial/default-report-view.phtml',
            'paginator' => __DIR__ . '/../../Base/view/partial/paginator_generic.phtml',
            'reportheader' => __DIR__ . '/../../Base/view/partial/default-report-header.phtml',
            'agencycontribsourceincidents' => __DIR__ . '/../../Base/view/templates/agencycontribsourceincidents.phtml',
        ],
    ],
];
