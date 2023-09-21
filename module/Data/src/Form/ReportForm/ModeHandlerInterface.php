<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */
namespace Data\Form\ReportForm;

/**
 * Handles default actions when the form is access in a particular mode.
 * This can be used to render the form, or to only gather data from each field.
 *
 * @package Form
 */
interface ModeHandlerInterface
{
//    public function __construct(ReportForm_FieldContainer $fieldContainer);

    /**
     * Triggered when a field is added to the form.
     *
     * @param FieldInterface $field
     */
    public function addField(FieldInterface $field);

    /**
     * Triggered before any fields are added to the form.
     */
    public function preFormProcess();

    /**
     * Triggered after all fields have been added to the form.
     */
    public function postFormProcess();

    public function getAdditionalScript();
}
