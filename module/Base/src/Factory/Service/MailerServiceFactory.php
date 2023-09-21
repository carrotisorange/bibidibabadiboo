<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;
use Zend\Log\Logger;
use Zend\View\Renderer\PhpRenderer;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

use Base\Adapter\Db\BaseAdapter;
use Base\Factory\BaseFactory;
use Base\Service\MailerService;

class MailerServiceFactory extends BaseFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * Form service factory to inject required parameters to form model service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Base\Service\MailerService]
     */

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        
        if(!empty($config['smtp'])){
            $mailTransport = new SmtpTransport();
            $options   = new SmtpOptions(array(
                'host'  => $config['smtp']['host'],
                'port'  => $config['smtp']['port']
            ));
            $mailTransport->setOptions($options);
        } else {
            $mailTransport = new Sendmail();
        }

        return new MailerService(
            $config,
            $container->get('Logger'),
            $container->get('Zend\View\Renderer\PhpRenderer'),
            $this->getViewModel(),
            new Message(),
            $mailTransport
        );
    }
}
