<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */
namespace Data\Form\ReportForm;

/**
 * Base implementation of the FieldInterface with some reasonable defaults.
 */
abstract class Field implements FieldInterface
{
    /**
     * @var array Contains any extra options that should be applied to the field.
     */
    protected $options = [];

    /**
     * @param array $options Options to be applied at render or verification stages.
     */
    public function __construct(Array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Any options that were set at creation or during run.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set an option after creation.
     *
     * @param string $option
     * @param string|integer|boolean $value
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    /**
     * Define the unique id of this field instance.
     * The field id will be generated from the namespace of the class file.
     * @return string
     */
    public function getId()
    {
        //Data\Form\ReportForm\Field\Incident\IncidentHitAndRun => Incident\IncidentHitAndRun
        $fieldId = substr(get_class($this), strlen(__NAMESPACE__ . '\Field\\'));
        
        //Incident\IncidentHitAndRun => Incident_IncidentHitAndRun
        return str_replace("\\", "_", $fieldId);
    }

    /**
     * A list of functionality hooks; validation, front-end UI, etc.
     *
     * @return null|array
     */
    public function getFunctionalityHooks()
    {
        return null;
    }

    /**
     * Presentation type (and functionality) of field input.
     *
     * @return string
     */
    public function getInputType()
    {
        return 'text';
    }
}
