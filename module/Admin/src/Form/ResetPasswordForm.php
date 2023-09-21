<?php
namespace Admin\Form;

use Zend\Form\Element;

use Base\Form\Form;

class ResetPasswordForm extends Form
{
    public function __construct($formName = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('resetpassword');
        
        $this->setAttribute('id', 'resetpassword');
        
        // OK button
        $this->add([
            'name' => 'ok',
            'type' => 'button',
            'attributes'=> [
                'id'    => 'ok',
                'class' => 'btnstyle btn-sm',
                'onClick' => 'window.close();',
            ],
            'options' => [
                'label' => 'OK',
            ],
        ]);
    }
}
