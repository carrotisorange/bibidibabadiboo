<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'users' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/users[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\UsersController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'view-keyed-image' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/view-keyed-image[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ViewKeyedImageController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'assign-form-code-values' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/assign-form-code-values[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AssignFormCodeValuesController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'form-code-lists-json' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/form-code-lists-json[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AssignFormCodeValuesController::class,
                        'action'     => 'formCodeListsJson',
                    ],
                ],
            ],
            'list-code-pairs-json' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/list-code-pairs-json[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AssignFormCodeValuesController::class,
                        'action'     => 'listCodePairsJson',
                    ],
                ],
            ],
            'assign-data-elements' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/assign-data-elements[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AssignDataElementsController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'show-notes' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/assign-data-elements/show-notes/formId[/:formId]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AssignDataElementsController::class,
                        'action'     => 'showNotes',
                    ],
                ],
            ],
            'metrics' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/metrics[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\MetricsController::class,
                        'action'     => 'imageStatusByAgency',
                    ],
                ],
            ],
            'image-status-by-agency' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/metrics/image-status-by-agency',
                    'defaults' => [
                        'controller' => Controller\MetricsController::class,
                        'action'     => 'imageStatusByAgency',
                    ],
                ],
            ],
            'operator-by-agency-stats' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/metrics/operator-by-agency-stats',
                    'defaults' => [
                        'controller' => Controller\MetricsController::class,
                        'action'     => 'operatorByAgencyStats',
                    ],
                ],
            ],
            'operator-keying-accuracy' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/metrics/operator-keying-accuracy',
                    'defaults' => [
                        'controller' => Controller\MetricsController::class,
                        'action'     => 'operatorKeyingAccuracy',
                    ],
                ],
            ],
            'operator-summary-stats' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/metrics/operator-summary-stats',
                    'defaults' => [
                        'controller' => Controller\MetricsController::class,
                        'action'     => 'operatorSummaryStats',
                    ],
                ],
            ],
            'vin-status-summary' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/metrics/vin-status-summary',
                    'defaults' => [
                        'controller' => Controller\MetricsController::class,
                        'action'     => 'vinStatusSummary',
                    ],
                ],
            ],
            'vin-status-by-operator' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/metrics/vin-status-by-operator',
                    'defaults' => [
                        'controller' => Controller\MetricsController::class,
                        'action'     => 'vinStatusByOperator',
                    ],
                ],
            ],            
            'bad-image' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/bad-image[/:action[/:param[/:id]]]',
                     'defaults' => [
                        'controller' => Controller\BadImageController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'report-entry' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/bad-image/report-entry[/:action[/:param[/:id]]]',
                     'defaults' => [
                        'controller' => Controller\BadImageController::class,
                        'action'     => 'reportEntry',
                    ],
                ],
            ],
            'get-user-interface-info' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/bad-image/get-user-interface-info[/:action[/:param[/:id]]]',
                     'defaults' => [
                        'controller' => Controller\BadImageController::class,
                        'action'     => 'getUiInfo',
                    ],
                ],
            ],
            'invalid-vin' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/invalid-vin[/:action]',
                    'defaults' => [
                        'controller' => Controller\InvalidVinController::class,
                    ],
                ],
            ],
            'state-configuration' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/state-configuration[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\StateConfigurationController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'auto-extraction-metrics' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/auto-extraction-metrics[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AutoExtractionMetricsController::class,
                        'action'     => 'autoExtractionReport',
                    ],
                ],
            ],
            'auto-extraction-report' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/auto-extraction-metrics/auto-extraction-report[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AutoExtractionMetricsController::class,
                        'action'     => 'autoExtractionReport',
                    ],
                ],
            ],
            'unload-user-report' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/quality-control/unload-user-report',
                    'defaults' => [
                        'controller' => Controller\QualityControlController::class,
                        'action'     => 'unload-user-report',
                    ],
                ],
            ],
            'auto-extraction-accuracy' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/auto-extraction-metrics/auto-extraction-accuracy',
                    'defaults' => [
                        'controller' => Controller\AutoExtractionMetricsController::class,
                        'action'     => 'autoExtractionAccuracy',
                    ],
                ],
            ],

            'quality-control' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/quality-control[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'params' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\QualityControlController::class,
                        'action'     => 'index',
                    ],
                ],
            ],

            'select-filter' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/select-filter[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'params' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\QualityControlController::class,
                        'action'     => 'selectFilter',
                    ],
                ],
            ],
            
            'qc-reports' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/qc-reports[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'params' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\QualityControlController::class,
                        'action'     => 'report-summary',
                    ],
                ],
            ],

            'report-and-image' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/quality-control/report-and-image[/:id]',
                    'constraints' => [
                        'id'     => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => Controller\QualityControlController::class,
                        'action'     => 'report-and-image',
                    ],
                ],
            ],

            'remark-entry' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/quality-control/remark-entry[/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\QualityControlController::class,
                        'action'     => 'remark-entry',
                    ],
                ],
            ],
            'no-issue' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/quality-control/no-issue',
                    'defaults' => [
                        'controller' => Controller\QualityControlController::class,
                        'action'     => 'noissue',
                    ],
                ],
            ],
            'report-quality-remarks' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/quality-control/report-quality-remarks[/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\QualityControlController::class,
                        'action'     => 'report-quality-remarks',
                    ],
                ],
            ],

            'apply-remark' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/quality-control/apply-remark[/:reportId]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'reportId'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\QualityControlController::class,
                        'action'     => 'apply-remark',
                    ],
                ],
            ],


            'volume-productivity-report' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/auto-extraction-metrics/volume-productivity-report',
                    'defaults' => [
                        'controller' => Controller\AutoExtractionMetricsController::class,
                        'action'     => 'volumeProductivityReport',
                    ],
                ],
            ],
            'update-configuration' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/state-configuration/update-configuration/stateId[/:stateId]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\StateConfigurationController::class,
                        'action'     => 'updateConfiguration',
                    ],
                ],
            ],
            'configure-timeout' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/admin/users/configure-timeout',
                    'defaults' => [
                        'controller' => Controller\UsersController::class,
                        'action'     => 'configureTimeout',
                    ],
                ],
            ],
			'sla-status-summary' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/admin/metrics/sla-status-summary',
                    'defaults' => [
                        'controller' => Controller\MetricsController::class,
                        'action'     => 'slaStatusSummary',						
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\UsersController::class => Factory\Controller\UsersControllerFactory::class,
            Controller\MetricsController::class => Factory\Controller\MetricsControllerFactory::class,
            Controller\InvalidVinController::class => Factory\Controller\InvalidVinControllerFactory::class,
            Controller\AssignDataElementsController::class => Factory\Controller\AssignDataElementsControllerFactory::class,
            Controller\ViewKeyedImageController::class => Factory\Controller\ViewKeyedImageControllerFactory::class,
            Controller\BadImageController::class => Factory\Controller\BadImageControllerFactory::class,
            Controller\AssignFormCodeValuesController::class => Factory\Controller\AssignFormCodeValuesControllerFactory::class,
            Controller\StateConfigurationController::class => Factory\Controller\StateConfigurationControllerFactory::class,
            Controller\AutoExtractionMetricsController::class => Factory\Controller\AutoExtractionMetricsControllerFactory::class,
            Controller\QualityControlController::class => Factory\Controller\QualityControlControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'users' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ]
    ]
];
