<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

use Base\Factory\ServiceFactory;
use Base\Service\ReportEntryService;
use Base\Service\FormService;
use Base\Service\FormFieldService;
use Base\Service\DataTransformerService;
use Base\Service\AutoExtractionService;

class ReportEntryServiceFactory extends ServiceFactory implements FactoryInterface
{
    /**
     * Implementation of FactoryInterface abstract method, will not be invoked by default!
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
    
    /**
     * ReportEntry service factory to inject required parameters to report entry model service
     * @param object        $container      [Interop\Container\ContainerInterface]
     * @param string        $requestedName  Requested model service name
     * @param null|array    $options
     * @return object       [Base\Factory\Service\ReportEntryService]
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');

        $serviceForm = new FormService(
            $config,
            $container->get('Logger'),
            $this->getFormAdapter($config),
            $this->getReadOnlyFormAdapter($config),
            $this->getFormSystemAdapter($config),
            $container->get(DataTransformerService::class)
        );

        return new ReportEntryService(
            $config,
            $container->get('Logger'),
            $this->getReportEntryAdapter($config),
            $this->getReadOnlyReportEntryAdapter($config),
            $this->getReportEntryDataAdapter($config),
            $this->getEntryStageProcessAdapter($config),
            $this->getFormFieldCommonAdapter($config),
            $serviceForm,
            $this->getUserEntryPrefetchAdapter($config),
            $this->getReportAdapter($config),
            $this->getFormCodeMapAdapter($config),
            $this->getFCGCAdapter($config),
            $this->getReportEntryDataValueAdapter($config),
            $container->get(FormFieldService::class),
            $container->get(AutoExtractionService::class)
        );
    }
}
