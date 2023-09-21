<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Auth\Form;

use Zend\Form\Form;

/**
 * This form is used to collect Authenticator's login, password .
 */
class LoginForm extends Form
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('login-form');
        
        // Set POST method for this form
        $this->setAttribute('method', 'post');
        
        $this->addElements();
        $this->addInputFilter();
    }
    
    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements() 
    {
        // Add "username" field
        $this->add([
            'type'  => 'text',
            'name' => 'username',
            'attributes'=> [
                'id'    => 'username',
                'class' => 'inputText',
                'style' => 'width:150px',
                'tabindex' => '1',
                'autofocus' => 'autofocus'
            ],
        ]);
        
        // Add "password" field
        $this->add([
            'type' => 'password',
            'name' => 'password',
            'attributes'=> [
                'id'    => 'password',
                'class' => 'inputText',
                'style' => 'width:150px',
                'tabindex' => '2',
                'autocomplete' => 'off'
            ],
        ]);
        
        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'login',
            'attributes' => [
                'value' => 'Login',
                'id' => 'login',
                'class' => 'btnstyle btn-sm',
                'tabindex' => '3'
            ],
        ]);
    }
    
    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter() 
    {
        // Create main input filter
        $inputFilter = $this->getInputFilter();
        
        // Add input for "username" field
        $inputFilter->add([
            'name'     => 'username',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'max' => 64
                    ],
                ],
            ],
        ]);
        
        // Add input for "password" field
        $inputFilter->add([
            'name'     => 'password',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'max' => 64
                    ],
                ],
            ],
        ]);
    }
}
