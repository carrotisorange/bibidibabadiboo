<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */
namespace Data\Form\ReportForm;

/**
 * @category Form
 * @subcategory Field
 */
interface FieldInterface
{
    /**
     * Return a list of form-level options that apply to a field.
     *
     * @return array
     */
    public function getOptions();

    /**
     * @return string
     */
    public function getId();

    /**
     * Determines what database field this should be stored in based on a table.field notation.
     * By specifying table[#].field the value will go into a row specific to the # instance.
     * By specifying table.field# the value will go into a field specific to the # instance.
     * These can be combined.
     *
     * @return array [table, field]
     */
    public function getDbFieldName();

    /**
     * Valid types are: validateForce, validateForceFull, validateSoft, valueFormat, valueList, autoFill, autoTab
     * @return array [type => [hook, ...], ...];
     */
    public function getFunctionalityHooks();

    /**
     * Returns the input type to use.
     * @return string text|checkbox|textarea
     */
    public function getInputType();
}
