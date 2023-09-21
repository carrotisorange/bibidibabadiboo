<?php
namespace Base\Service;

use Base\Service;
use Zend\View\Helper\Url;

/**
 * This service is responsible for determining which items should be in the main menu.
 * The items may be different depending on whether the user is authenticated or not.
 */
class NavManagerService
{
    /**
     * Url view helper.
     * @var Zend\View\Helper\Url
     */
    private $urlHelper;
    
    /**
     * Constructs the service.
     */
    public function __construct(Url $urlHelper) 
    {
        $this->urlHelper = $urlHelper;
    }
    
    /**
     * This method returns menu items depending on whether user has logged in or not and by UserrOle.
     */
    public function getMenus()
    {
        $url = $this->urlHelper;
        
        return [
            [
                'label' => 'Manage Users',
                'rel' => 'dropmenu1',
                'controller' => 'UsersController',
                'sub_menus' => [
                    [
                        'label' => 'Add user',
                        'link' => $url('users', ['action'=>'add'])
                    ],
                    [
                        'label' => 'Edit User',
                        'link' => $url('users', ['action'=>'index'])
                    ]
                ] 
            ],
            [
                'label' => 'State Configuration',
                'controller' => 'StateConfigurationController',
                'rel' => '',
                'link' => $url('state-configuration', ['action'=>'index'])
            ],
            [
                'label' => 'Image Entry',
                'controller' => 'ReportentryController',
                'rel' => '',
                'link'  => $url('report-entry')
            ],
            [
                'label' => 'View Keyed Reports',
                'controller' => 'ViewKeyedImageController',
                'rel' => '',
                'link' => $url('view-keyed-image', ['action'=>'index'])
            ],
            [
                'label' => 'Bad Image Queue',
                'rel' => '',
                'controller' => 'BadImageController',
                'link' => $url('bad-image')
            ],
            [
                'label' => 'Data Elements Assignment',
                'rel' => 'dropmenu2',
                'controller' => 'AssignFormCodeValuesController',
                'sub_menus' => [
                    [
                        'label' => 'Form Code Assignment',
                        'link' => $url('assign-form-code-values', ['action'=>'index'])
                    ],
                    [
                        'label' => 'Assign Data Elements',
                        'link' => $url('assign-data-elements', ['action'=>'index'])
                    ]
                ]
            ],
            [
                'label' => 'Metrics',
                'rel' => 'dropmenu3',
                'controller' => 'MetricsController',
                'sub_menus' => [
                    [
                        'label' => 'Image Status by Agency',
                        'link' => $url('metrics', ['action'=>'image-status-by-agency'])
                    ],
                    [
                        'label' => 'Operator by Agency Stats',
                        'link' => $url('metrics', ['action'=>'operator-by-agency-stats'])
                    ],
                    [
                        'label' => 'Operator Keying Accuracy',
                        'link' => $url('metrics', ['action'=>'operator-keying-accuracy'])
                    ],
                    [
                        'label' => 'Operator Summary Stats',
                        'link' => $url('metrics', ['action'=>'operator-summary-stats'])
                    ],
                    [
                        'label' => 'Vin Status Summary',
                        'link' => $url('metrics', ['action'=>'vin-status-summary'])
                    ],
                    [
                        'label' => 'Vin Status by Operator',
                        'link' => $url('metrics', ['action'=>'vin-status-by-operator'])
                    ]
					,
                    [
                        'label' => 'SLA Status Summary',
                        'link' => $url('metrics', ['action'=>'sla-status-summary'])
                    ]
                ]
            ],
            [
                'label' => 'Auto Extraction Metrics',
                'rel' => 'dropmenu4',
                'controller' => 'AutoExtractionMetricsController',
                'sub_menus' => [
                    [
                        'label' => 'Auto Vs Manual',
                        'link' => $url('auto-extraction-metrics', ['action'=>'auto-extraction-report'])
                    ],
                    [
                        'label' => 'Auto Extraction Accuracy',
                        'link' => $url('auto-extraction-metrics', ['action'=>'auto-extraction-accuracy'])
                    ],
                    [
                        'label' => 'Volume & Productivity Report',
                        'link' => $url('auto-extraction-metrics', ['action'=>'volume-productivity-report'])
                    ],
                    [
                        'label' => 'Quality Control',
                        'link' => $url('quality-control', ['action'=>'index'])
                    ],
                ]
            ]
        ];
    }
}

