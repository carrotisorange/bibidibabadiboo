<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Acl;

use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

use Base\Service\UserService;

class Acl extends ZendAcl
{
    /**
     * @TODO: ACL functionalities will be implemented
     */
    public function __construct()
    {
        $this->addRole(new Role(UserService::ROLE_GUEST));
        $this->addRole(new Role(UserService::ROLE_OPERATOR), UserService::ROLE_GUEST);
        $this->addRole(new Role(UserService::ROLE_SUPER_OPERATOR), UserService::ROLE_OPERATOR);
        $this->addRole(new Role(UserService::ROLE_SUPERVISOR), UserService::ROLE_SUPER_OPERATOR);
        $this->addRole(new Role(UserService::ROLE_ADMIN), UserService::ROLE_SUPERVISOR);
        
        // CONTROLLERS
        $this->addResource(new Resource('AuthController'));
        $this->addResource(new Resource('UsersController'));
        $this->addResource(new Resource('AdminController'));
        $this->addResource(new Resource('IndexController'));
        $this->addResource(new Resource('ReportentryController'));
        $this->addResource(new Resource('SupportController'));
        $this->addResource(new Resource('ViewKeyedImageController'));
        $this->addResource(new Resource('InvalidVinController'));
        $this->addResource(new Resource('MetricsController'));
        $this->addResource(new Resource('AssignDataElementsController'));
        $this->addResource(new Resource('BadImageController'));
        $this->addResource(new Resource('AssignFormCodeValuesController'));
        $this->addResource(new Resource('StateConfigurationController'));
		$this->addResource(new Resource('AutoExtractionMetricsController'));
        
        // MODULES & CONTROLLERS
        /*$this->addResource('Application');
        $this->addResource('Application:error', 'Application');
        $this->addResource('Application:remote', 'Application');
        $this->addResource('Authentication');
        $this->addResource('Authentication:auth', 'Authentication');*/
        
        $this->allow(UserService::ROLE_GUEST, 'SupportController', 'login');
        $this->allow(UserService::ROLE_GUEST, 'AuthController', [
            'login', 'forgotPassword', 'changePassword', 'changeUserPassword', 'securityQuestion',
            'checkConcurrentUserLogin', 'index','security-question-login', 'confirm-password'
        ]);
        
        $this->allow(UserService::ROLE_GUEST, 'AdminController', 'configuretimeout');
        $this->allow(UserService::ROLE_GUEST, 'IndexController', 'index');
        
        $this->allow(UserService::ROLE_OPERATOR, 'AuthController', ['report-entry']);
        $this->allow(UserService::ROLE_OPERATOR, 'ReportentryController', ['report-entry']);
        $this->allow(UserService::ROLE_OPERATOR, 'ReportentryController', [
            'index', 'view', 'edit', 'delete', 'display', 'notes', 'save', 'exit', 'valueList', 'addPage', 'prefetchNextImage'
        ]);
        $this->allow(UserService::ROLE_OPERATOR, 'InvalidVinController', ['index', 'potential-match-json']);
        
        $this->allow(UserService::ROLE_SUPER_OPERATOR, 'AdminController', [
            'index', 'edit', 'potential-match-json', 'save-validated-vin-json', 'mark-as-invalid-vin-json',
            'export', 'image-viewer-pdf'
        ]);

        $this->allow(UserService::ROLE_SUPER_OPERATOR, 'MetricsController', ['image-status-by-agency', 'operator-by-agency-stats', 'operator-keying-accuracy', 'operator-summary-stats', 'vin-status-summary', 'vin-status-by-operator']);
        
        $this->allow(UserService::ROLE_SUPER_OPERATOR, 'AdminController', ['view-keyed-image', 'bad-image']);
        $this->allow(UserService::ROLE_SUPER_OPERATOR, 'AdminController',
            ['index', 'export', 'edit-keyed-image', 'report-entry']
        );

        $this->deny(UserService::ROLE_SUPER_OPERATOR, 'AdminController', 'process-discarded');

        $this->allow(UserService::ROLE_SUPER_OPERATOR, 'AssignDataElementsController', ['assign-data-elements','form-code-lists-json','list-code-pairs-json']);
        $this->allow(UserService::ROLE_SUPER_OPERATOR, 'AssignDataElementsController', ['assign-data-elements','show-notes','fetch-agencies-json','fetch-forms-json','fetch-form-attrs-json']);
        $this->allow(UserService::ROLE_SUPER_OPERATOR, 'ViewKeyedImageController', ['view-keyed-image','index','bad-image']);

        $this->allow(UserService::ROLE_SUPER_OPERATOR, 'BadImageController', ['index']);
        $this->deny(UserService::ROLE_SUPER_OPERATOR, 'BadImageController', ['process-discarded']);
        
        $this->allow(UserService::ROLE_SUPERVISOR, 'UsersController', ['users', 'index']);
        $this->allow(UserService::ROLE_SUPERVISOR, 'AdminController', 'assigndataelements');
        $this->allow(UserService::ROLE_SUPERVISOR, 'MetricsController', ['index', 'operatorbyagency', 'operatorsummary', 'operator-keying-accuracy', 'vinstatussummary', 'vinstatusbyoperator', 'imagestatusbyagency', 'export', 'image-viewer-pdf']);
        
        $this->allow(UserService::ROLE_ADMIN);
        $this->allow(UserService::ROLE_ADMIN, 'BadImageController', ['process-discarded']);
        $this->allow(UserService::ROLE_ADMIN, 'AdminController', 'process-discarded');

        $this->allow(UserService::ROLE_ADMIN, 'StateConfigurationController', ['state-configuration']);
        $this->allow(UserService::ROLE_ADMIN, 'AutoExtractionMetricsController', ['auto-extraction-report', 'auto-extraction-accuracy']);
    }
}