<?php
namespace Admin\Form;

use Zend\Form\Element;

use Base\Form\Form;

class UserNotesForm extends Form
{
    public function __construct($formName = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('addnotes');
        
        $this->setAttribute('id', 'addnotes');
        
        // Notes element
        $this->add([
            'name' => 'note',
            'type' => 'textarea',
            'attributes'=> [
                'id'    => 'note',
                'class' => 'form-control',
                'placeholder' => 'Enter Notes',
                'rows' => '5',
                'cols' => '45',
            ],
        ]);
        
        // Save button
        $this->add([
            'name' => 'save',
            'type' => 'button',
            'attributes'=> [
                'id'    => 'save',
                'class' => 'btnstyle btn-sm',
                'disabled' => true,
            ],
            'options' => [
                'label' => 'Save Notes',
            ],
        ]);
        
        // Cancel button
        $this->add([
            'name' => 'cancel',
            'type' => 'button',
            'attributes'=> [
                'id'    => 'cancel',
                'class' => 'btnstyle btn-sm',
            ],
            'options' => [
                'label' => 'Cancel',
            ],
        ]);
    }
}
