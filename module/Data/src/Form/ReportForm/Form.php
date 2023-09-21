<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */
namespace Data\Form\ReportForm;

use InvalidArgumentException;
use Zend\View\Model\ViewModel;

/**
 * @todo Determine method to group multiple row/column ids to fields.
 * @todo Create derivative field rules with the ability to apply them on a per-form basis.
 */

/**
 * Handles form access and management of form elements.
 *
 * @package Form
 */
class Form
{
    /**
     * Determines what mode the form is in and how it should be handled.
     * @var ModeHandlerInterface
     */
    protected $modeHandler;

    /**
     * Contains a list of all fields previously initialized.
     * @var FieldContainer
     */
    protected $fieldContainer;
    
    /**
     * Contains the tab order of the field - required for view keyed reports
     * @var array
     */ 
    protected $tabOrder;
    protected $i;
    
    /**
     * @param ModeHandlerInterface $modeHandler
     */
    public function __construct(FieldContainer $fieldContainer, ModeHandlerInterface $modeHandler)
    {
        $this->modeHandler = $modeHandler;
        $this->fieldContainer = $fieldContainer;
    }

    /**
     * Creates a new field and passes it through the mode handler.
     *
     * @param array $name
     * @param array $options
     */
    public function addField(Array $name, Array $options = [])
    {
        $class = __NAMESPACE__ .'\\Field\\'.$name[0].'\\'.$name[1];
        if (!empty($name[2])) {
            $class .= '\\'.$name[2];
        }

        $field = new $class($options);
        
        $this->fieldContainer->addField($field);
        $fieldInfo = $this->modeHandler->addField($field);
        if ($fieldInfo['tabIndex'] >= 1 && $fieldInfo['tabIndex'] <= 999) {
            $this->tabOrder[$fieldInfo['tabIndex']] = $fieldInfo['fieldName'];
        }
    }
    
    /*
     * Returns the tab order of the object
     */
    function returnTabOrder() {
        return $this->tabOrder;
    }

    /**
     * Process a form, calling the pre/post FormProcess handlers and including the form HTML.
     *
     * @param string $formName
     * @todo Add database validation of form name.
     */
    public function processForm($formName, Array $pageRequest = null)
    {
        $result = $this->modeHandler->preFormProcess();
        if ($result === false) {
            return false;
        }

        $pages = $this->getFormPages($formName, $pageRequest);

        $pageContents = [];
        $baseNames = [];
        foreach ($pages as $page) {
            ob_start();
                        
            $this->processPage($page, new ViewModel);

            $pageContents[] = ob_get_clean();
            $baseNames[] = basename($page, '.phtml');
        }

        $result = $this->modeHandler->postFormProcess();
        if ($result === false) {
            return false;
        }

        return [
            'additionalScript' => $this->modeHandler->getAdditionalScript(),
            'pageContents' => $pageContents,
            'baseNames' => $baseNames,
        ];
    }

    /**
     * Provide a (mostly) sterile environment to process a page in.
     *
     * This allows pages to use variables within a page (if necessary) without fear of colliding.
     * It also stops variable sharing between pages. Some pages require a ViewModel variable to be set,
     * thus the optional $view parameter.
     *
     * @param string $page
     * @param ViewModel $view
     */
    protected function processPage($page, $view = null)
    {
        $form = $this;
        require($page);
    }

    /**
     * Get all of the form page names for a form (based on its directory contents)
     *
     * @param string $formName - Name of the form to get pages for
     * @param array $pageRequest - Filter page names to only items in this array
     * @return array - All of the form page names (page-01-base)
     * @throws InvalidArgumentException
     */
    public function getFormPages($formName, Array $pageRequest = null)
    {
        $formBasePath = REPORTFORM_TEMPLATE_PATH;
        if (!file_exists($formBasePath . '/' . $formName)) {
            throw new InvalidArgumentException('Form path does not exist: ' . $formName);
        }
        $formBasePath = str_replace(['[',']'], ['\[','\]'], $formBasePath);
        $globPath = $formBasePath . '/' . $formName . '/page-*.phtml';

        $pages = glob($globPath);
        natsort($pages);

        $baseNamePages = [];
        foreach ($pages as $page) {
            $baseNamePages[basename($page, '.phtml')] = $page;
        }

        if (!empty($pageRequest)) {
            foreach ($pageRequest as $page) {
                if (isset($baseNamePages[$page])) {
                    $requestedPages[] = $baseNamePages[$page];
                } else {
                    throw new InvalidArgumentException('Unknown form page requested: ' . $page);
                }
            }

            return $requestedPages;
        } else {
            return $baseNamePages;
        }
    }
}