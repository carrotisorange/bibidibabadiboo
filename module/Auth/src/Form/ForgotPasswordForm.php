<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Auth\Form;

use Zend\Form\Form;
use Zend\Validator\NotEmpty;

/**
 * This form is used to collect UserId and Email-id for the User .
 */
class ForgotPasswordForm extends Form
{ 
    /**
     * Constructor
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('forgotPassword');
        
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
                'tabindex' => '1'
            ],
        ]);
        
        // Add "email" field
        $this->add([
            'type'  => 'text',
            'name' => 'email',
            'attributes'=> [
                'id'    => 'email',
                'class' => 'inputText',
                'style' => 'width:150px',
                'tabindex' => '2'
            ],
        ]);
        
        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Submit',
                'id' => 'submit',
                'class' => 'btnstyle',
            ],
        ]);
        
        // Add the Cancel button
        $this->add([
            'name' => 'cancel',
            'type' => 'button',
            'attributes'=> [
                'id'    => 'cancel',
                'class' => 'btnstyle',
                'onclick'=>'redirectToLogin()'
            ],
            'options' => [
                'label' => 'cancel',
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
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => "User ID is required and can't be empty",
                        ],
                    ],
                ],
            ],
        ]);
        
        // Add input for "email" field
        $inputFilter->add([
            'name'     => 'email',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => "Email is required and can't be empty",
                        ],
                    ],
                ],
            ],
        ]);
    }
}
