<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Factory\Controller;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\Helper\HeadTitle;

use Base\Factory\ControllerFactory;
use Base\Service\UserService;
use Base\Service\EntryStageService;
use Base\Service\StateService;
use Base\Service\AgencyService;
use Base\Service\FormService;
use Base\Service\FormWorkTypeService;
use Base\Service\WorkTypeService;
use Base\Service\UserEntryStageService;
use Base\Service\UserNoteService;
use Base\Service\UserFormPermissionService;
use Base\Service\IsitService;
use Base\Service\ReportService;
use Base\Service\ReportEntryService;
use Base\Service\ReportEntryQueueService;
use Base\Service\RekeyService;
use Base\Helper\LnHelper;
use Auth\Service\LNAAAuthService;
use Admin\Controller\UsersController;
use Admin\Form\SearchUsersForm;
use Admin\Form\UserForm;
use Admin\Form\UserNotesForm;
use Admin\Form\UserNoteHistoryForm;
use Admin\Form\ResetPasswordForm;
use Admin\Form\ConfigureTimeoutForm;
use Base\Service\KeyingVendorService;

class UsersControllerFactory extends ControllerFactory implements FactoryInterface
{
    /**
     * @param object        $container      Interop\Container\ContainerInterface
     * @param string        $requestedName  Requested controller name
     * @param null|array    $options
     * @return object       Admin\Controller\UsersController
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $searchUsersForm = new SearchUsersForm(
            $container->get('AuthService'),
            $container->get(UserService::class),
            $container->get(EntryStageService::class),
            $container->get(KeyingVendorService::class)
        );
        
        $config = $container->get('Config');
        $request = $container->get('Request');
        $queryParams = $request->getQuery();
        $queryParams = (!empty($queryParams)) ? (array) $queryParams : [];
        
        /**
         * @TODO: To be removed later
         * Option to disable the webservice validation in development.
         */
        $disablePeopleSoftIdValidation = ($config['app']['env'] == 'local') ? true : false;
        
        $userForm = new UserForm(
            $container->get('AuthService'),
            $container->get(UserService::class),
            $container->get(EntryStageService::class),
            $container->get(StateService::class),
            $container->get(LNAAAuthService::class),
            $container->get(KeyingVendorService::class),
            $queryParams,
            $config,
            $disablePeopleSoftIdValidation
        );
        
        return new UsersController(
            $config,
            $this->getSession($config),
            $container->get('Logger'),
            $container->get('AuthService'),
            $container->get(UserService::class),
            $container->get(AgencyService::class),
            $container->get(FormService::class),
            $container->get(FormWorkTypeService::class),
            $container->get(WorkTypeService::class),
            $container->get(StateService::class),
            $container->get(UserEntryStageService::class),
            $container->get(UserNoteService::class),
            $container->get(UserFormPermissionService::class),
            $container->get(IsitService::class),
            $container->get(LNAAAuthService::class),
            $container->get(ReportService::class),
            $container->get(ReportEntryService::class),
            $container->get(ReportEntryQueueService::class),
            $container->get(RekeyService::class),
            $container->get('Zend\View\Renderer\PhpRenderer'),
            $searchUsersForm,
            $userForm,
            new UserNotesForm(),
            new LnHelper(),
            new UserNoteHistoryForm($queryParams),
            new ResetPasswordForm(),
            $container->get('ViewHelperManager')->get(HeadTitle::class),
            new ConfigureTimeoutForm(),
            $container->get(KeyingVendorService::class)
        );
    }
}
