<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Data\Form;

use Base\Form\Form;

/**
 * To generate work type selection form
 */
class WorkTypeSelectionForm extends Form
{
    public function __construct()
    {
        parent::__construct(false);
        
        // Set POST method for this form
        $this->setAttribute('method', 'POST');
        
        $this->addAdditionalKeying();
        $this->addWorkType();
        $this->addSubmit();
    }

    protected function addAdditionalKeying()
    {
        $this->add([
            'name' => 'addKeying',
            'required' => true,
            'type' => 'select',
            'options' => [
                'label' => 'Additional Keying',
                'value_options' => [
                    '' => 'Select Additional Keying Type'
                ],
            ],
            'attributes' => [
                'id' => 'addKeying'
            ]
        ]);
    }

    protected function addWorkType()
    {
        $this->add([
            'name' => 'workType',
            'required' => true,
            'type' => 'select',
            'options' => [
                'label' => 'Work Type',
                'value_options' => [
                    '' => 'Select a Work Type'
                ],
            ],
            'attributes' => [
                'id' => 'workType'
            ]
        ]);
    }
    
    protected function addSubmit()
    {
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'class' => 'btn-sm btnstyle btn-submit-user',
                'value' => 'Search',
            ]
        ]);
    }
}