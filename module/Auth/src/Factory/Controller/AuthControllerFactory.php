<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Auth\Factory\Controller;

use Zend\Session\SessionManager;
use Zend\Session\Container;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\Helper\HeadTitle;

use Base\Service\UserService;
use Base\Factory\BaseFactory;
use Auth\Service\LNAAAuthService;
use Auth\Service\LNAAAdapterService;
use Auth\Controller\AuthController;
use Auth\Form\LoginForm;
use Auth\Form\ForgotPasswordForm;
use Auth\Form\SecurityQuestionForm;
use Auth\Form\ChangePasswordForm;
use Base\Helper\LnHelper;
use Auth\Form\Validator\CheckNewPasswordValidator;
use Auth\Form\Validator\CheckPasswordValidator;
use Auth\Form\Validator\CheckSecurityAnswerValidator;

/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class AuthControllerFactory extends BaseFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AuthController
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $serviceUser = $container->get(UserService::class);
        $config = $container->get('config');
        $serviceLnaaAuth = $container->get(LNAAAuthService::class);
        $passwordChangeInterval = $config['app']['user']['passwordChangeInterval'];
        
        return new AuthController(
            $config,
            $this->getSession($config),
            $container->get('Logger'),
            $container->get('AuthService'),
            $serviceUser,                    
            $serviceLnaaAuth,
            $container->get(LNAAAdapterService::class),
            new LoginForm(),
            new ForgotPasswordForm(),
            new SecurityQuestionForm(),
            new changePasswordForm(new CheckNewPasswordValidator($serviceUser),
                new CheckPasswordvalidator($serviceUser, $serviceLnaaAuth, $passwordChangeInterval),
                new CheckSecurityAnswerValidator()
            ),
            new LnHelper(),
            $container->get('ViewHelperManager')->get(HeadTitle::class)
        );
    }
}
