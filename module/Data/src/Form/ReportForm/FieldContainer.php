<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */
namespace Data\Form\ReportForm;

use Interop\Container\ContainerInterface;
use Zend\Log\Logger;

use Base\Service\FormFieldAttributeService;

/**
 * Container for all the fields on a form and any high-level field related functionality.
 *
 * This includes assigning the unique id for each instance of any given field.
 */
class FieldContainer
{
    /**
     * A uniquely keyed array based on an object hash.
     * @var array
     */
    protected $fields = [];

    /**
     * A uniquely keyed array based on an object hash that contains a public id.
     * @var array
     */
    protected $fieldIds = [];
    protected $fieldName = [];

    /**
     * Tracks the number of instances of a particular field for both dbTable and dbField counters.
     * @var array [
     *     $dbTable => [
     *         $dbTableCounter => [
     *             $dbField => integer,
     *             ...
     *         ],
     *         ...
     *     ],
     *     ...
     * ];
     */
    protected $fieldInstanceInfo = [];

    /**
     * @var array
     */
    protected $fieldGroupTranslation = [];

    // @TODO: Need to ContainerInterface $container = null to make work assign data element form
    /**
     *
     * @var string original or duplicate
     */
    protected $fieldMode = 'original';
    protected $activeGroup = 0;
    protected $fieldExternalGroupIds = [];

    public function __construct(Logger $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Reset the object hash indexes when the object is woken up.
     */
    public function __wakeup()
    {
        $newFields = $newFieldIds = $newFieldName = [];
        foreach ($this->fieldIds as $hash => $id) {
            $newHash = spl_object_hash($this->fields[$hash]);

            $newFieldIds[$newHash] = $id;
            $newFields[$newHash] = $this->fields[$hash];
            $newFieldName[$newHash] = $id;
        }

        $this->fieldIds = $newFieldIds;
        $this->fields = $newFields;
        $this->fieldName = $newFieldName;
    }

    /**
     *
     * @param $string $fieldMode
     */
    public function setMode($fieldMode)
    {
        $this->fieldMode = $fieldMode;

        if ($fieldMode == 'duplicate') {
            $this->activeGroup++;
        }
    }

    public function getMode()
    {
        return $this->fieldMode;
    }

    /**
     *
     * @param FieldInterface $field
     */
    public function addField(FieldInterface $field)
    {
        $this->createFieldId($field);
    }

    /**
     * Lists all fields
     * @return array [hash => ReportForm_FieldInterface, ...]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Returns a particular field's public name.
     * @param FieldInterface $field
     * @return string
     */
    public function getFieldName(FieldInterface $field)
    {
        return $this->fieldName[spl_object_hash($field)];
    }

    /**
     * Returns a particular field's public id.
     * @param FieldInterface $field
     * @return string
     */
    public function getFieldId(FieldInterface $field)
    {
        return $this->fieldIds[spl_object_hash($field)];
    }

    /**
     * Keeps track of separate external field groupings or relations based on occurance or usage.
     *
     * @param string $instanceGroup
     * @return integer
     */
    public function getExternalGroupId($instanceGroup)
    {
        $type = $instanceGroup[0];

        if ($this->fieldMode == 'duplicate') {
            $instanceGroup = $this->activeGroup . '`' . $instanceGroup;
        }

        if (empty($this->fieldExternalGroupIds[$type])) {
            $this->fieldExternalGroupIds[$type] = [];
        }

        if (!isset($this->fieldExternalGroupIds[$type][$instanceGroup])) {
            $this->fieldExternalGroupIds[$type][$instanceGroup] = count($this->fieldExternalGroupIds[$type]) + 1;
        }

        return $this->fieldExternalGroupIds[$type][$instanceGroup];
    }

    /**
     *
     * @param FieldInterface $field
     */
    protected function createFieldId(FieldInterface $field)
    {
        $id = $field->getId();
        $dbFieldName = $field->getDbFieldName();
        $name = reset($dbFieldName) . '[' . end($dbFieldName) . ']';
        $dbTableCounter = 0;
        $dbFieldCounter = 0;

        $options = $field->getOptions();

        list($dbTable, $dbField) = $field->getDbFieldName();

        if (strpos($dbTable, '[#]') !== false) {
            if (!empty($options['instanceGroup'])) {
                $dbTableCounter = $this->getTableGroupCounter($dbTable, $options['instanceGroup']);
            } else {
                $this->logger->err('Field instanceGroup was not provided, yet table must be grouped: ' . $id);
            }

            $id .= '-t' . $dbTableCounter;
            $name = str_replace('#', $dbTableCounter - 1, $name);
        }

        if (strpos($dbField, '#') !== false) {
            $dbFieldCounter = $this->getFieldCounter($dbTable, $dbTableCounter, $dbField);

            $id .= '-f' . $dbFieldCounter;
        }

        $hash = spl_object_hash($field);
        $this->fields[$hash] = $field;
        $this->fieldIds[$hash] = $id;
        $this->fieldName[$hash] = $name;
    }

    protected function getTableGroupCounter($dbTable, $instanceGroup)
    {
        if ($this->fieldMode == 'duplicate') {
            $instanceGroup = $this->activeGroup . '`' . $instanceGroup;
        }

        if (!isset($this->fieldGroupTranslation[$dbTable][$instanceGroup])) {
            if (empty($this->fieldGroupTranslation[$dbTable])) {
                $this->fieldGroupTranslation[$dbTable] = [];
            }

            $this->fieldGroupTranslation[$dbTable][$instanceGroup] = count($this->fieldGroupTranslation[$dbTable]) + 1;
        }

        return $this->fieldGroupTranslation[$dbTable][$instanceGroup];
    }

    protected function getFieldCounter($dbTable, $dbTableCounter, $dbField)
    {
        if (empty($this->fieldInstanceInfo[$dbTable])) {
            $this->fieldInstanceInfo[$dbTable] = [];
        }

        if (!isset($this->fieldInstanceInfo[$dbTable][$dbTableCounter][$dbField])) {
            $this->fieldInstanceInfo[$dbTable][$dbTableCounter][$dbField] = 1;
        } else {
            $this->fieldInstanceInfo[$dbTable][$dbTableCounter][$dbField]++;
        }

        return $this->fieldInstanceInfo[$dbTable][$dbTableCounter][$dbField];
    }
    
    /**
     * For when we need to clear everything and start over. 
     */
    protected function resetFields()
    {
        $this->fields = [];
        $this->fieldIds = [];
        $this->fieldName = [];
    }
    
    /**
     * Get a list of fields from the common data structure. 
     */
    public function getFieldsFromCommon($formFieldAttributeGroupId, $reset = true)
    {
        if ($reset) {
            $this->resetFields();
        }
        
        $modelFactory = $this->container->get(FormFieldAttributeService::class);
        $fields = $modelFactory->fetchByGroupId($formFieldAttributeGroupId);
        return $fields;
    }
}
