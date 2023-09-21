<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;

use Base\Adapter\Db\FormFieldAttributeAdapter;

class FormFieldAttributeService extends BaseService
{
    /**
     * Field is available to be keyed
     */
    const ATTRIBUTE_AVAILABLE = 'isAvailable';
    /**
     * Field is not in the tab sequence (have to click it)
     */
    const ATTRIBUTE_SKIPPED = 'isSkipped';
    /**
     * Field must be completed before the form will save
     */
    const ATTRIBUTE_REQUIRED = 'isRequired';
    /**
     * Field is highlighted on the form
     */
    const ATTRIBUTE_HIGHLIGHTED = 'isHighlighted';
    
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Adapter\Db\FormAdapter
     */
    protected $adapterForm;
    
    public function __construct(
        Array $config,
        Logger $logger,
        FormFieldAttributeAdapter $adapterFormFieldAttribute)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterFormFieldAttribute = $adapterFormFieldAttribute;
    }

    public function fetchByGroupId($formFieldAttributeGroupId)
    {
        return $this->adapterFormFieldAttribute->fetchByGroupId($formFieldAttributeGroupId);
    }

    public function getTabOrder($formFieldAttributeGroupId) {
        return $this->adapterFormFieldAttribute->getTabOrder($formFieldAttributeGroupId);
    }

    public function updateAttributesByGroupId($formFieldAttrGroupId, $elements)
    {
        return $this->adapterFormFieldAttribute->updateAttributesByGroupId($formFieldAttrGroupId, $elements);
    }
}
