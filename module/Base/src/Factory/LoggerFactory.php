<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Log\Formatter\Simple;
use Zend\Authentication\AuthenticationService;

class LoggerFactory extends BaseFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * User service factory to inject required parameters to user model service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Zend\Log\Logger]
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $config = $container->get('Config');
        $session = $this->getSession($config);
        $serviceAuth = $container->get('AuthService');
        $logger = new Logger;
        $writer = new Stream($config['app']['log']['file_path']);
        $userName = '';
        $reportId = '';

        // Append Username for all log before message 
        if ($serviceAuth->hasIdentity()) {
            $userName = '[username :' . trim($serviceAuth->getIdentity()->username) . '] ';
        }
        // End append Username for all log before message

        //  Append Report id for all log after username 
        if (!empty($session->reportId)) {
            $reportId = '[reportId:' . $session->reportId . '] ';
        }
        // End Report id for all log after username 
        
        $simpleFormat = str_replace("%message%", $userName . $reportId . "%message%", Simple::DEFAULT_FORMAT); 
        $formatter = new Simple($simpleFormat);
        $writer->setFormatter($formatter);
        $logger->addWriter($writer);
        return $logger;
    }
}
