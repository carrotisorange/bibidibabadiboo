<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Auth\Form;

use Zend\Form\Form;
use Zend\Validator\NotEmpty;

/**
 * This form is used to collect user Security Question details.
 */
class SecurityQuestionForm extends Form
{
    /**
     * Constructor.
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
        // Add "answer_1" field
        $this->add([
            'type'  => 'text',
            'name' => 'answer_1',
            'attributes'=> [
                'id'    => 'answer-1',
                'class' => 'inputText',
                'style' => 'width:150px',
                'tabindex' => '1',
            ],
        ]);
        
        // Add "answer_2" field
        $this->add([
            'type'  => 'text',
            'name' => 'answer_2',
            'attributes'=> [
                'id'    => 'answer-2',
                'class' => 'inputText',
                'style' => 'width:150px',
                'tabindex' => '2',
            ],
        ]);
        
        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Submit',
                'id' => 'login',
                'class' => 'btnstyle',
            ],
        ]);
        
        // Add the Cancel button
        $this->add([
            'name' => 'cancel',
            'type' => 'button',
            'value'=> 'cancel',
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

        // Add input for "answer_1" field
        $inputFilter->add([
            'name'     => 'answer_1',
             'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [NotEmpty::IS_EMPTY => "You need to answer on all questions"],
                    ],
                ],
            ],
        ]);

        // Add input for "answer_2" field
        $inputFilter->add([
            'name'     => 'answer_2',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [NotEmpty::IS_EMPTY => "You need to answer on all questions"],
                    ],
                ],
            ],
        ]);
    }
}
