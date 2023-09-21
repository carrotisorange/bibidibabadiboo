<?php
namespace Base\View\Helper;

use Zend\View\Helper\AbstractHelper;
 
/**
 * This view helper class displays a menu bar.
 */
class UrlSimple extends AbstractHelper 
{
    public $router;
    
    public function __construct($router)
    {
        $this->router = $router;
    }
    /**
     * Generates an url given the name of a route.
     */
    public function urlSimple($action = null, $controller = null, $module = null, array $urlOptions = [], $name = null, $reset = false, $encode = true)
    {
        if (!is_array($urlOptions)) {
            $urlOptions = [];
        }
        if (!empty($module)) {
            $urlOptions['module'] = $module;
        }
        if (!empty($controller)) {
            $urlOptions['controller'] = $controller;
        }
        if (!empty($action)) {
            $urlOptions['action'] = $action;
        }
        if (empty($name)) {
            $name = 'default';
        }
        
        return $this->router->assemble(['action' => $urlOptions['action']], ['name' => $urlOptions['controller']]);
    }
}