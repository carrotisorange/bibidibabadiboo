<?php
namespace Admin\Form;

use Zend\Form\Element;

use Base\Form\Form;

class UserNoteHistoryForm extends Form
{
    public function __construct(Array $queryParams)
    {
        $this->queryParams = $queryParams;
        parent::__construct('usernote');
        
        $this->setAttribute('id', 'usernote');
        
        // Notes element
        $this->add([
            'name' => 'note',
            'type' => 'textarea',
            'attributes'=> [
                'id'    => 'note',
                'class' => 'form-control',
                'placeholder' => 'Enter Notes',
                'rows' => '2',
                'cols' => '50',
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
        
        // User ID hidden element
        $this->add([
            'name' => 'userId',
            'type' => 'hidden',
            'attributes'=> [
                'id'    => 'userId',
                'value' => (!empty($this->queryParams['userId'])) ? $this->queryParams['userId'] : '',
            ],
        ]);
    }
}
