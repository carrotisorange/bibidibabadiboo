<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */
namespace Data\Form\ReportForm;

/**
 * Contains the active state of the form, such that it can be built on or rebuilt from scratch.
 */
class FormContext
{
    /**
     * @var Form
     */
    protected $formHandler;

    /**
     * @var FieldContainer
     */
    protected $fieldContainer;

    /**
     * @var <type> @var string;
     */
    protected $formName;

    /**
     * @var array
     */
    protected $pages = [];

    /**
     * @param Form $formHandler
     * @param FieldContainer $fieldContainer
     * @param string $formName
     */
    public function __construct(Form $formHandler, FieldContainer $fieldContainer, $formName)
    {
        $this->formHandler = $formHandler;
        $this->fieldContainer = $fieldContainer;
        $this->formName = $formName;
    }

    /**
     * Adds a page to the form and returns the fully processed (output) results.
     *
     * @param string $baseName
     * @return array|false ['additionalScript' => string, 'pageContents' => [string, ...], 'baseNames' => [string, ...]]
     */
    public function addPage($baseName)
    {
        if (in_array($baseName, $this->pages) || stripos($baseName, '-duplicate-') !== false) {
            $pageMode = 'duplicate';
        } else {
            $pageMode = 'original';
        }

        $this->pages[] = $baseName;

        $this->fieldContainer->setMode($pageMode);
        return $this->formHandler->processForm($this->formName, [$baseName]);
    }

    /**
     * Adds multiple pages in a batch mode similar to addPage.
     *
     * @param array $pages [baseName, ...]
     * @return array|false ['additionalScript' => string, 'pageContents' => [string, ...], 'baseNames' => [string, ...]]
     */
    public function addPages(Array $pages)
    {
        ksort($pages);

        $pageData = [
            'additionalScript' => '',
            'pageContents' => [],
            'baseNames' => [],
        ];

        foreach ($pages as $baseName) {
            $return = $this->addPage($baseName);

            $pageData['additionalScript'] .= $return['additionalScript'];
            $pageData['pageContents'] = array_merge($pageData['pageContents'], $return['pageContents']);
            $pageData['baseNames'] = array_merge($pageData['baseNames'], $return['baseNames']);
        }

        return $pageData;
    }
    
    /*
     * Returns the tab order of the object
     */
    function returnTabOrder() {
        return $this->formHandler->returnTabOrder();
    }

    /**
     * Adds pages which are indicated as being a 'base' (primary, default) page.
     *
     * A base page is specified as those with '-base-' in their name.
     *
     * @return array|false ['additionalScript' => string, 'pageContents' => [string, ...]]
     */
    public function addBasePages()
    {
        $basePages = [];
        foreach ($this->formHandler->getFormPages($this->formName) as $baseName => $path) {
            if (stripos($baseName, '-base-') !== false) {
                $basePages[] = $baseName;
                $this->pages[] = $baseName;
            }
        }

        return $this->formHandler->processForm($this->formName, $basePages);
    }

    /**
     * Returns the base => path list of pages available to the underlying form.
     *
     * @return array [string baseName => string path, ...]
     */
    public function getAvailablePages()
    {
        return $this->formHandler->getFormPages($this->formName);
    }

    public function extractPagesFromRequestData($requestData)
    {
        /** @todo Write code to pull baseName of pages from request data sent back. */
    }
}
