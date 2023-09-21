<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Factory\Controller;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\Container;
use Zend\Validator\Date;
use Zend\View\Renderer\PhpRenderer;

use Base\Factory\ControllerFactory;
use Base\View\Helper\DataRenderer\ReportMaker;
use Base\Service\VendorService;
use Base\Service\EntryStageService;
use Base\Service\ReportQueueService;
use Base\Service\ReportStatusService;
use Admin\Controller\BadImageController;
use Admin\Form\BadImageSearchForm;
use Base\Service\ReportService;
use Base\Service\KeyingVendorService;

class BadImageControllerFactory extends ControllerFactory implements FactoryInterface
{
    /**
     * @param object        $container      Interop\Container\ContainerInterface
     * @param string        $requestedName  Requested controller name
     * @param null|array    $options
     * @return object       Admin\Controller\BadImageController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $request = $container->get('Request');
        $queryParams = (array) $request->getQuery();
        $postParams = (array) $request->getPost();
        $requestParams = (!empty($queryParams)) ? $queryParams : 
                (!empty($postParams) ? $postParams : []);
        
        $reportStatus = (!empty($requestParams['reportStatus'])) ? 
                $requestParams['reportStatus'] : ReportStatusService::STATUS_BAD_IMAGE;
        
        $badImageSearchForm = new BadImageSearchForm(
            $container->get(EntryStageService::class),
            $container->get(VendorService::class),
            $container->get(KeyingVendorService::class),
            $container->get('AuthService'),
            $reportStatus
        );

        $reportMaker = new ReportMaker(
            $config,
            $container->get('Zend\View\Renderer\PhpRenderer'),
            $this->getViewModel()
        );
        
        return new BadImageController(
            $container->get('Config'),
            $container->get('Logger'),
            $this->getSession($config),
            $reportMaker,
            new Date(),
            $badImageSearchForm,
            $container->get(ReportQueueService::class),
            $container->get(ReportStatusService::class),
            $container->get(ReportService::class),
            $container->get(KeyingVendorService::class)
        );
    }
}
