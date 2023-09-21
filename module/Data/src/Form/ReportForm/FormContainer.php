<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */
namespace Data\Form\ReportForm;

use Exception;
use Base\Service\ReportService;
use Base\Service\EntryStageService;

/**
 * Interface for the form container objects.
 *
 * The objective is to modularize/genericize the specifics of
 * generating different types of form.
 */
abstract class FormContainer
{
    /**
     * Data to be sent to the form 'page' display
     *
     * @var string
     */
    protected $pageData;
    /**
     * What stage the entry is in (EntryStageService::STAGE_*)
     *
     * @see EntryStageService
     * @var string
     */
    protected $entryStage;
    /**
     * Whether the report has been obsoleted by another report
     *
     * @var bool
     */
    protected $isObsolete;
    /**
     * The name of the form for internal purposes. aka 'template'
     *
     * @var string
     */
    protected $formNameInternal;
    /**
     * The name of the form for external (display) purposes
     *
     * @var string
     */
    protected $formNameExternal;
    /**
     * Tab order of elements on the page
     *
     * @var mixed
     */
    protected $tabOrder;

    /**
     * Constructor to set params needed by default implementations of methods
     *
     * @param string $entryStage - EntryStageService::STAGE_*
     * @param string $formNameInternal - aka template
     * @param string $formNameExternal
     * @param bool $isObsolete
     */
    public function __construct(
        $reportId,
        $entryStage,
        $isObsolete,
        $formNameInternal,
        $formNameExternal)
    {
        $this->reportId = $reportId;
        $this->isObsolete = $isObsolete;
        $this->entryStage = $entryStage;
        $this->formNameExternal = $formNameExternal;
        $this->formNameInternal = $formNameInternal;
    }
    
    /**
     * Gets page count necessary for calculating buttons by default
     *
     * @return int
     */
    abstract protected function getPageCount();

    /**
     * Gets the order that elements are tabbed on the page
     *
     * @return mixed
     */
    public function getTabOrder()
    {
        return $this->tabOrder;
    }

    /**
     * Sets the order elements are tabbed on the page
     *
     * @param mixed $tabOrder
     */
    protected function setTabOrder($tabOrder)
    {
        $this->tabOrder = $tabOrder;
    }

    /**
     * Gets the display name of the form
     *
     * @return string
     */
    public function getFormNameExternal()
    {
        return $this->formNameExternal;
    }

    /**
     * Gets the internal/template/directory name of the form
     *
     * @return string
     */
    public function getFormNameInternal()
    {
        return $this->formNameInternal;
    }

    /**
     * Gets the data that will be sent to the front end to render the form
     *
     * Return will have 3 sections used by edit.phtml
     * header - Stuff that needs to go in the header (raw javascript)
     * pageContents - The html for the form contents
     * baseNames - This will go in '_pages[]'. It is really only needed by
     *      universal in order to reconstruct the form. Its not important for
     *      silverlight outside of informational purposes.
     *
     * @return mixed
     */
    public function getPageData()
    {
        return $this->pageData;
    }

    /**
     * Sets 'pageData' to be sent to the form when it is rendered
     *
     * This is protected because it should be generated inside of a container class
     *
     * @param mixed $pageData
     */
    protected function setPageData($pageData)
    {
        $this->pageData = $pageData;
    }
    
    /**
     * Returns an array of buttons that will be displayed by default and their state
     *
     * @return mixed
     */
    protected function getDefaultButtons()
    {

        $defaultButtons = [
            'formTemplates' => false,
            'pageAdd' => true,
            'save' => true,
            'rekey' => true,
            'bad' => true,
            'discard' => true,
            'reorder' => true,
            'notes' => true,
            'exit' => true,
            'pageForward' => false,
            'pageBack' => false,
            'pageList' => true,
			'clear' => true
        ];

        return $defaultButtons;
    }

    /**
     * Figures out what buttons should be shown on the form
     *
     * @param string $entryFlow - ReportService::ENTRY_FLOW_*
     * @return mixed
     */
    public function getButtons($entryFlow)
    {
        $renderButtons = $this->getDefaultButtons();

        switch ($entryFlow) {

            case ReportService::ENTRY_FLOW_ENTRY:
                unset(
                    $renderButtons['rekey'],
                    $renderButtons['discard'],
                    $renderButtons['reorder'],
                    $renderButtons['formTemplates']
                );

                /**
                 * @TODO: Will be enabled in future based on the template switch option
                if ($this->entryStage == EntryStageService::STAGE_ALL) {
                    $renderButtons['formTemplates'] = true;
                }*/
                break;

            case ReportService::ENTRY_FLOW_VIEW:
                if ($this->entryStage == EntryStageService::STAGE_NONE) {
                    $renderButtons = [
                        'notesViewOnly' => true,
                        'exit' => true,
                    ];
                } elseif ($this->isObsolete) {
                    $renderButtons = [
                        'exit' => true
                    ];
                } else {
                    unset(
                        $renderButtons['reorder'],
                        $renderButtons['bad'],
                        $renderButtons['rekey'],
                        $renderButtons['discard']
                    );
                }
                break;

            case ReportService::ENTRY_FLOW_BAD:
                unset(
                    $renderButtons['bad'],
                    $renderButtons['reorder']
                );
                break;

            case ReportService::ENTRY_FLOW_DISCARD:
                unset(
                    $renderButtons['pageAdd'],
                    $renderButtons['save'],
                    $renderButtons['bad'],
                    $renderButtons['discard']
                );
                break;

            case ReportService::ENTRY_FLOW_DEAD:
                $renderButtons = [
                    'notes' => true,
                    'exit' => true,
                    'pageForward' => false,
                    'pageBack' => false,
                ];

                break;

            default:
                throw new Exception('Unknown report entry flow given : ' . $entryFlow);
        }

        if ($this->getPageCount() > 1 && isset($renderButtons['pageForward'])) {
            $renderButtons['pageForward'] = true;
        }

        return $renderButtons;
    }
}
