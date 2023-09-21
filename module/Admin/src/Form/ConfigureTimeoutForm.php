<?php
namespace Admin\Form;

use Base\Form\Form;

class ConfigureTimeoutForm extends Form
{
    public function __construct($formName = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct($formName);
        
        // Ok button
        $this->add([
            'name' => 'ok',
            'type' => 'button',
            'attributes'=> [
                'id'    => 'ok',
                'class' => 'btnstyle btn btn-sm',
                'onClick' => 'setSessionTimeout();',
            ],
            'options' => [
                'label' => 'OK',
            ],
        ]);
    }
}
