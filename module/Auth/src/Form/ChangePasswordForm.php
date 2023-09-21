<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Auth\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Zend\Validator\Regex;

use Base\Service\UserService;
use Auth\Form\Validator\CheckNewPasswordValidator;
use Auth\Form\Validator\ConfirmPasswordValidator;
use Auth\Form\Validator\CheckPasswordvalidator;
use Auth\Form\Validator\CheckSecurityAnswerValidator;

/**
 * This form is used to collect Password details for the Application user.
 */
class ChangePasswordForm extends Form
{
    /**
     * @var Auth\Form\Validator\CheckNewPasswordValidator
     */
    protected $validatorCheckNewPassword;
    
    /**
     * @var Auth\Form\Validator\CheckPasswordvalidator
     */
    protected $validatorCheckPassword;
    
    /**
     * @var Auth\Form\Validator\CheckSecurityAnswerValidator
     */
    protected $validatorCheckSecurityAnswer;
    
    public function __construct(
    CheckNewPasswordValidator $validatorCheckNewPassword , 
    CheckPasswordvalidator $validatorCheckPassword,
    CheckSecurityAnswerValidator $validatorCheckSecurityAnswer)
    {
        // Define form name
        parent::__construct('Changepassword-form');
        
        // Set POST method for this form
        $this->setAttribute('method', 'post');
        
        $this->addElements();
        $this->validatorCheckNewPassword = $validatorCheckNewPassword;
        $this->validatorCheckPassword = $validatorCheckPassword;
        $this->validatorCheckSecurityAnswer = $validatorCheckSecurityAnswer;
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
            ],
        ]);
        
        // Add "Current Password" field
        $this->add([
            'type'  => 'password',
            'name' => 'passwordCurrent',
            'attributes'=> [
                'id'    => 'passwordCurrent',
                'class' => 'inputText',
                'style' => 'width:150px',
                'tabindex' => '2',
                'autocomplete' => 'off'
            ],
        ]);
        
        // Add "New Password" field
        $this->add([
            'type'  => 'password',
            'name' => 'passwordNew',
            'attributes'=> [
                'id' =>'passwordNew',
                'class' => 'inputText',
                'style' => 'width:150px',
                'tabindex' => '3',
                'autocomplete' => 'off'
            ],
        ]);
        
        // Add "Confirm password" field
        $this->add([
            'type'  => 'password',
            'name' => 'passwordConfirm',
            'attributes'=> [
                'id' =>'passwordConfirm',
                'class' => 'inputText',
                'style' => 'width: 150px',
                'tabindex' => '4',
                'autocomplete' => 'off'
            ],
        ]);
        
        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'save',
            'attributes' => [
                'value' => 'Ok',
                'class' => 'btnstyle',
                'id' => 'submit',
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
       
        // Add input for "username" field 
        $inputFilter->add ([
            'name' => 'username',
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => "User ID is required and can't be empty"
                        ],
                    ],
                ],
            ]
        ]);
       
        // Add input for "Current Password" field 
        $inputFilter->add ([
            'name' => 'passwordCurrent',
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => "Current Password is required and can't be empty"
                        ],
                    ],
                ],
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 8,
                        'messages' => [StringLength::TOO_SHORT => "Current Password Should be 8 characters long"],
                    ],
                ],
            ]
        ]);
        
        // Add additional inputfiler for "New Password" field
        $inputFilter->add([
            'name'     => 'passwordCurrent',
            'required' => true,
            'validators' => [$this->validatorCheckPassword]
        ]);
        
        // Add input for "New Password" field 
        $inputFilter->add ([
            'name' => 'passwordNew',
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [NotEmpty::IS_EMPTY => "New Password is required and can't be empty"],
                    ],
                ],
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 8,
                        'messages' => [StringLength::TOO_SHORT => "New Password Should be 8 characters long"],
                    ],
                ],
               
            ]
        ]);
        
        // Add additional inputfiler for "New Password" field  
        $inputFilter->add([
            'name'     => 'passwordNew',
            'required' => true,
            'validators' => [$this->validatorCheckNewPassword]
        ]);
        
        // Add input for "Confirm Password" field 
        $inputFilter->add ([
            'name' => 'passwordConfirm',
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => "Confirm Password is required and can't be empty"
                        ],
                    ],
                ],
                [
                    'name' => ConfirmPasswordValidator::class,
                    'options' => [
                        'messages' => [ConfirmPasswordValidator::MISMATCH => "Passwords do not match"],
                    ],
                ],
            ]
        ]);
    }
    
    //Preparing security question and answer Elements
    public function prepareQuestion(Array $questionList)
    {
        $numberSection = 4;
        $questionsPerSection = 3;
        $questionElement = [];
        $inputFilter = $this->getInputFilter();
        
        for ($sectionNum = 1; $sectionNum <= $numberSection; $sectionNum ++) {
            $questionElement = new Element\Select("question_$sectionNum");
            $answerElement = new Element\Text("answer_$sectionNum");
            $questionElement ->setAttributes(['class' => 'inputText','style' => 'width:400px', 
                                                'id' => "question-$sectionNum"
                                            ]);
            $answerElement ->setAttributes(['class' => 'inputText','style' => 'width:250px',
                                                'id' => "answer-$sectionNum"
                                            ]);
            
            $questionOptions = [];
            $questionOptions[''] = 'Select a Question';
            for ($questionNum = 1; $questionNum <= $questionsPerSection; $questionNum ++) {
                $question = current($questionList);
                next($questionList);
                $questionOptions[$question['question_id']] = $question['question'];
            }
            
            $questionElement->setValueOptions($questionOptions);
            // @TODO: Check validator Working functionality 
            $answerElement->setOptions([
            'validators' => [$this->validatorCheckSecurityAnswer]
            ]);
            $this->add($questionElement);
            $this->add($answerElement);
            
            // Add input Filter for "Question" field 
            $inputFilter->add ([
                'name' => "question_$sectionNum",
                'validators' => [
                    [
                        'name' => NotEmpty::class,
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => "Question $sectionNum is required and can't be empty"
                            ],
                        ],
                    ],
                ]
            ]);
            
            // Add input Filter for "Answer" field 
            $inputFilter->add ([
                'name' => "answer_$sectionNum",
                'validators' => [
                    [
                        'name' => NotEmpty::class,
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => "Answer $sectionNum is required and can't be empty"
                            ],
                        ],
                    ],
                ]
            ]);
        }
    }
}
