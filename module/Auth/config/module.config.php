<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Auth;

use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'login' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'logout',
                    ],
                ],
            ],
            'index' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/index',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ], 
           'forgot-password' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/forgot-password',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'forgotPassword',
                    ],
                ],
            ],
            'change-user-password' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/change-user-password',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'changeUserPassword',
                    ],
                ],
            ],
            'change-password' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/change-password[/:loginId][/:session_id][/:from]',
                    'constraints' => [
                        'loginId' => '[a-zA-Z0-9_-]*',
                        'session_id'  => '[0-9]+',
                        'from' => '[a-zA-Z_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'changePassword',
                    ],
                ],
            ],
            'security-question' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/security-question[/:loginId][/:session_id][/:from]',
                    'constraints' => [
                        'loginId' => '[a-zA-Z0-9_-]*',
                        'session_id'  => '[0-9]+',
                        'from' => '[a-zA-Z_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'security-question',
                    ],
                ],
            ],
            'check-concurrent-user-login' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/check-concurrent-user-login',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'checkConcurrentUserLogin',
                    ],
                ],
            ],
            'remote' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/remote',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'remote',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\AuthController::class => Factory\Controller\AuthControllerFactory::class,
            Controller\IndexController::class => Factory\Controller\IndexControllerFactory::class,
        ],
    ],    
    'service_manager' => [
        'aliases' => [
            'Zend\Authentication\AuthenticationService' => 'AuthService',
        ],
        'factories' => [
            'AuthService' => Factory\Service\AuthenticationServiceFactory::class,
            // @TODO: remove the MAE related service files in all places, 
            /*Service\MAEAdapterService::class => Factory\Service\MAEAdapterServiceFactory::class,
            Service\MaeAuthService::class => Factory\Service\MaeAuthServiceFactory::class,*/
            Service\LNAAAdapterService::class => Factory\Service\LNAAAdapterServiceFactory::class,
            Service\LNAAAuthService::class => Factory\Service\LNAAAuthServiceFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ]
    ],
];
