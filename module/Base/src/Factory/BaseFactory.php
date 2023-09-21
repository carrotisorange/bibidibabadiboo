<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Factory;

use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

abstract class BaseFactory
{
    public function getSession(Array $config)
    {
        $sessionContainer = new Container($config['session_containers'][0]);
        if ( empty($sessionContainer->isInitialized)) {
            $sessionContainer->isInitialized = true;
        }
        return $sessionContainer;
    }
    
    /**
     * Method to get view model from the factories
     *
     * @return object [Zend\View\Model\ViewModel]
     */
    public function getViewModel()
    {
        return new ViewModel();
    }
    
    /**
     * Method to get json model from the factories
     *
     * @return object [Zend\View\Model\JsonModel]
     */
    public function getJsonModel()
    {
        return new JsonModel();
    }
}
