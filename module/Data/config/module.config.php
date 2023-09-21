<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Data;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'report-entry' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/data/report-entry[/:action[/display[/:display]]]',
                    'constraints' => [
                        'action' => '[a-zA-Z0-9_-]*',
                        'display'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ReportEntryController::class,
                        'action'     => 'index',
                    ],
                ],
            ]
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\ReportEntryController::class => Factory\Controller\ReportEntryControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'data' => __DIR__ . '/../view',
        ]
    ],
];
