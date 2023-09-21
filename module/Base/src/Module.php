<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base;

use Zend\Session\SessionManager;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManager;
use Exception;

use Base\Acl\Acl;

class Module
{
    const VERSION = '3.1.0';
    
    /**
     * @var Zend\Session\Container
     */
    protected $session;
    
    /**
     * @var Array Application configuration
     */
    protected $config;
    
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    
    /**
     * @param  ModuleManager
     * @return void
     */
    public function init(ModuleManager $moduleManager)
    {
        $events = $moduleManager->getEventManager();
        
        // Registering a listener at default priority, 1, which will trigger after the ConfigListener merges config.
        $events->attach(ModuleEvent::EVENT_MERGE_CONFIG, [$this, 'onMergeConfig']);
    }
    
    /**
     * PHP ini set configuration
     */
    private function initPHPIniSet()
    {
        // PHP ini_set configuration options
        if ((!empty($this->config['ini_set']))
            && (is_array($this->config['ini_set']))) {
            
            try {
                foreach($this->config['ini_set'] as $key => $value) {
                    // To convert type of the option value from integer to string
                    $value = (is_int($value) === false) ? $value : strval($value);
                    
                    if ((is_string($key) === false) || (is_string($value) === false)) {
                        $message = 'Invalid ini_set option/value provided, expecting string but option('
                                 . gettype($key) . ')/value(' . gettype($value) . ') given.';
                        
                        throw new Exception($message);
                    }
                    
                    if (ini_set($key, $value) === false) {
                        throw new Exception( 'Unable to set ini_set option "' . $key . '"' );
                    }
                }
            } catch(Exception $e) {
                echo "Exception: " . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
                exit(0);
            }
        }
    }
    
    public function onMergeConfig(ModuleEvent $e)
    {
        $configListener = $e->getConfigListener();
        $this->config   = $configListener->getMergedConfig(false);
        
        $this->initPHPIniSet();
        
        if ($this->config['app']['env'] == 'prod') {
            // To disable the detailed information about the "Page not Found" error in production environment
            if (isset($this->config['view_manager']['display_not_found_reason'])) {
                unset($this->config['view_manager']['display_not_found_reason']);
            }
            
            // To disable the detailed information about the "Exception" error in production environment
            if (isset($this->config['view_manager']['display_exceptions'])) {
                unset($this->config['view_manager']['display_exceptions']);
            }
        } else {
            // To enable the detailed information about the "Page not Found" error in non-production environment
            if (!isset($this->config['view_manager']['display_not_found_reason'])) {
                $this->config['view_manager']['display_not_found_reason'] = true;
            }
            
            // To enable the detailed information about the "Exception" error in non-production environment
            if (!isset($this->config['view_manager']['display_exceptions'])) {
                $this->config['view_manager']['display_exceptions'] = true;
            }
        }
        
        // Checked the per page value in the application configuration
        if (empty($this->config['pagination']['perpage'])) {
            $this->config['pagination']['perpage'] = 30;
        }
        
        // Pass the changed configuration back to the listener
        $configListener->setMergedConfig($this->config);
    }
    
    public function initSession(MvcEvent $e)
    {
        $options = [
            'name' => $this->config['session']['cookie'],
            'gc_maxlifetime' => $this->config['session_config']['gc_maxlifetime']
        ];
        
        $sessionConfig = new SessionConfig();
        $sessionConfig->setOptions($options);
        $sessionManager = new SessionManager($sessionConfig);
        
        // Setting default session manager for the entire application.
        Container::setDefaultManager($sessionManager);
        return new Container($this->config['session_containers'][0]);
    }
    
    /**
     * @param  MvcEvent
     * @return [type]
     */
    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $eManager = $application->getEventManager();
        
        $serviceManager = $application->getServiceManager();
        $headTitleHelper = $serviceManager->get('ViewHelperManager')->get('headTitle');
        $headTitleHelper->append('LexisNexis')->setSeparator(' - ')->setAutoEscape(false);
        $this->session = $this->initSession($e);
        
        /*$this->initAcl($e, $this->session);
        $eManager->attach(MvcEvent::EVENT_ROUTE, [$this, 'checkAcl']);*/
        
        $sharedEventManager = $eManager->getSharedManager();
        // Register the event listener method.
        $sharedEventManager->attach(
            AbstractActionController::class,
            MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100
        );
    }
    
    /**
     * @param  MvcEvent
     * @return [type]
     */
    public function onDispatch(MvcEvent $e)
    {
        $application = $e->getApplication();
        $serviceManager = $application->getServiceManager();
        $serviceAuth = $serviceManager->get(AuthenticationService::class);
        $controller = $e->getTarget();
        $controllerName = $e->getRouteMatch()->getParam('controller', null);
        $actionName = $e->getRouteMatch()->getParam('action', null);
        
        $explode_controller = explode('\\', $controllerName);
        $currentController =  array_pop($explode_controller);
        
        // Convert dash-style action name to camel-case.
        $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));
        $sessionManager = $serviceManager->get(SessionManager::class);
        $sessionContainer = new Container('user', $sessionManager);
        
        $match       = $e->getRouteMatch();
        $routeName   = $match->getMatchedRouteName();
        
        if ((!$serviceAuth->hasIdentity()) && ($routeName != 'login') && ($currentController != 'AuthController')) {
            $controller->plugin('redirect')->toRoute('login');
        }
        
        /** @TODO: Initialize ACl and Page access Checking
         * For ACL implementation
        
        $acl = new Acl();
        $status = $acl->isAllowed($userRole, $currentController, $actionName) ;
        
        if (!$status){
            $controller->plugin('redirect')->toRoute('index');
        }
        */
        
        $viewModel = $e->getViewModel();
        
        // Config variable($this->config) will be initialized in onMergeConfig function
        $viewModel->setVariable('rowsPerPage', $this->config['pagination']['perpage']);
    }
    
    public function initAcl(MvcEvent $e, Container $sessionContainer)
    {
        $acl = new Acl($sessionContainer);
        $e->getViewModel()->acl = $acl;
    }
    
    public function checkAcl(MvcEvent $e)
    {
        $route = $e->getRouteMatch()->getMatchedRouteName();
        $route = explode('/', $route);
        $route = $route[0];
        
        $controller = $e->getTarget();
        $controllerName = $e->getRouteMatch()->getParam('controller', null);
        $actionName = $e->getRouteMatch()->getParam('action', null);
        
        $explode_controller = explode('\\', $controllerName);
        $currentController =  array_pop($explode_controller);
        
        if (!$e->getViewModel()->acl->isAllowed($userRole, $currentController, $route)) {
            //$controller->plugin('redirect')->toRoute('index');
        }
    }
}