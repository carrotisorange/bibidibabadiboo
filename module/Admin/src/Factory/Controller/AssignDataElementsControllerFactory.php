<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Factory\Controller;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Helper\HeadTitle;

use Base\Factory\ControllerFactory;
use Base\Service\StateService;
use Base\Service\AgencyService;
use Base\Service\FormService;
use Base\View\Helper\DataRenderer\ReportMaker;
use Base\Service\FormFieldAttributeService;
use Base\Service\FormNoteService;
use Admin\Controller\AssignDataElementsController;
use Admin\Form\AssignDataElementsForm;
use Data\Form\ReportForm\FieldContainer;
use Data\Form\ReportForm\ModeHandler\ModeHandler_Null;
use Data\Form\ReportForm\Form;


class AssignDataElementsControllerFactory extends ControllerFactory implements FactoryInterface
{
    /**
     * @param object        $container      Interop\Container\ContainerInterface
     * @param string        $requestedName  Requested controller name
     * @param null|array    $options
     * @return object       Admin\Controller\AssignDataElementsController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        
        $config = $container->get('Config');

        $assignDataElementsForm = new AssignDataElementsForm(
            $container->get(StateService::class)
        );

        $reportMaker = new ReportMaker(
            $config,
            $container->get('Zend\View\Renderer\PhpRenderer'),
            $this->getViewModel()
        );

        $fieldContainer = new FieldContainer($container->get('Logger'), $container);
        $modeHandler = new ModeHandler_Null($fieldContainer);
        
        return new AssignDataElementsController(
            $container->get('Config'),
            $container->get('Logger'),
            $this->getSession($config),
            $assignDataElementsForm,
            $container->get(FormService::class),
            $reportMaker,
            $container->get(AgencyService::class),
            $container->get(FormFieldAttributeService::class),
            $container,
            $container->get(FormNoteService::class),
            $fieldContainer,
            new Form($fieldContainer, $modeHandler),
            $container->get('ViewHelperManager')->get(HeadTitle::class)
        );
    }
}
