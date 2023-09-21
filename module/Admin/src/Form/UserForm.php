<?php
/**
 * @copyright   Copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Form;

use Zend\Form\Element;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;
use Zend\Validator\Regex;
use Zend\I18n\Validator\Alnum;
use Zend\Validator\Callback;
use Zend\Validator\Exception\RuntimeException;
use Zend\Authentication\AuthenticationService;
use Zend\Validator\NotEmpty;

use Base\Form\Form;
use Base\Form\KeyingVendorForm;
use Base\Service\UserService;
use Base\Service\EntryStageService;
use Base\Service\StateService;
use Auth\Service\LNAAAuthService;
use Admin\Validator\CheckEntryExistUsername;
use Admin\Validator\CheckEntryExistPeopleSoftId;
use Admin\Validator\CheckKeyingVendorId;
use Admin\Validator\CheckInternalEmail;
use Base\Service\KeyingVendorService;

class UserForm extends KeyingVendorForm
{
    const USER_ADD = 'add';
    const USER_EDIT = 'edit';
    
    protected $inputFilter;
    protected $queryParams;
    
    /**
    * @var Array
    */
    protected $config;
    
    /**
     * @var Zend\Authentication\AuthenticationService
     */
    protected $serviceAuth;
    
    /**
     * @var Base\Service\UserService
     */
    protected $serviceUser;
    
    /**
     * @var Base\Service\EntryStageService
     */
    protected $serviceEntryStage;
    
    /**
     * @var Base\Service\StateService
     */
    protected $serviceState;
    
    /**
     * @var Auth\Service\LNAAAuthService
     */
    protected $serviceLnaaAuth;
    
    /**
     * @var Base\Service\KeyingVendorService
     */
    protected $serviceKeyingVendor;
    
    /**
     * @var mode To identify the user form is add or edit
     */
    protected $mode;
    
    /**
     * @var isInternal To identify the user if internal or not
     */
    protected $isInternal;
    
    /**
     * @TODO: To be removed before move to production
     * @var disablePeopleSoftIdValidation To toggle people soft employee id validation
     */
    protected $disablePeopleSoftIdValidation;
    
    /**
     * @var loggedInUserInfo user information of logged in user
     */
    protected $loggedInUserInfo;
    
    /**
     * Constructor will be invoked from the UserControllerFactory
     * @param object $serviceUser   Base\Service\UserService;
     * @param object $serviceAuth   Zend\Log\Logger;
     * @param array  $queryParams   Http request query parameters
     */
    public function __construct(
        AuthenticationService $serviceAuth,
        UserService $serviceUser,
        EntryStageService $serviceEntryStage,
        StateService $serviceState,
        LNAAAuthService $serviceLnaaAuth,
        KeyingVendorService $serviceKeyingVendor,
        Array $queryParams,
        Array $config, 
        Bool $disablePeopleSoftIdValidation = null
    )
    {
        $this->serviceUser = $serviceUser;
        $this->serviceEntryStage = $serviceEntryStage;
        $this->serviceAuth = $serviceAuth;
        $this->serviceState = $serviceState;
        $this->serviceLnaaAuth = $serviceLnaaAuth;
        $this->serviceKeyingVendor = $serviceKeyingVendor;
        $this->queryParams = $queryParams;
        $this->config = $config;
        $this->disablePeopleSoftIdValidation = $disablePeopleSoftIdValidation;
        parent::__construct('user', $serviceAuth, $serviceKeyingVendor);
        
        $this->isInternal = false;
        $this->loggedInUserInfo = $this->serviceAuth->getIdentity();
        
        $this->setMode();
        if ($this->mode == self::USER_EDIT) {
            $this->setIsInternalUser();
        }
        $this->addFormElement();
    }
    
    protected function setMode()
    {
        $this->mode = (empty($this->queryParams['userId'])) ? self::USER_ADD : self::USER_EDIT;
    }
    
    protected function setIsInternalUser()
    {
        $editUserId = $this->queryParams['userId'];
        $editUserInfo = $this->serviceUser->getIdentityData($editUserId);
        $editUserInternalInfo = $this->serviceUser->getUserIsInternalAndDomainInfo($editUserInfo['username'], $editUserInfo['email']);
        $this->isInternal = $editUserInternalInfo['isInternal'];
    }
    
    public function addFormElement()
    {
        $readOnly = ($this->isInternal)? 'readonly' : '';
        
        // User ID hidden element
        $this->add([
            'name' => 'userId',
            'type' => 'hidden',
            'attributes'=> [
                'id'    => 'userId',
                'value' => '',
            ],
        ]);
        
        // First Name element
        $this->add([
            'name' => 'nameFirst',
            'type' => 'text',
            'attributes'=> [
                'id'    => 'nameFirst',
                'class' => 'form-control',
                'maxlength' => '64',
                'readonly' => $readOnly,
            ],
            'options' => [
                'label' => 'First Name *',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ]
            ],
        ]);
        
        // Last Name element
        $this->add([
            'name' => 'nameLast',
            'type' => 'text',
            'attributes'=> [
                'id'    => 'nameLast',
                'class' => 'form-control',
                'maxlength' => '64',
                'readonly' => $readOnly,
            ],
            'options' => [
                'label' => 'Last Name *',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ]
            ],
        ]);
        
        // User ID element
        $attributes = [
            'id'    => 'username',
            'class' => 'form-control'
        ];
        
        if ($this->mode == self::USER_EDIT) {
            $attributes['readonly'] = 'readonly';
            $attributes['class'] .= ' disabled';
        }
        
        $this->add([
            'name' => 'username',
            'type' => 'text',
            'attributes'=> $attributes,
            'options' => [
                'label' => 'User ID *',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ]
            ],
        ]);
        
        $userRoles = [];
        $roles = $this->serviceUser->getValidRoles();
        foreach ($roles as $role) {
            // supervisors can not see admin role
            if ((($this->loggedInUserInfo->role != UserService::ROLE_SUPERVISOR) && ($role['name'] == UserService::ROLE_ADMIN))
                || ($role['name'] != UserService::ROLE_ADMIN)) {
                $userRoles[$role['userRoleId']] = $role['nameExternal'];
            }
        }
        
        $rolesFlipped = array_flip($userRoles);
        $roleVal = $rolesFlipped['Basic Operator'];
        
        // User Role select element
        $this->add([
            'name' => 'userRoleId',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'userRoleId',
                'class' => 'form-control form-select',
                'value' => $roleVal,
                'size'  => 1,
            ],
            'options' => [
                'value_options' => $userRoles,
                'label' => 'Role',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ],
            ],
        ]);
        
        $entryStages = $this->serviceEntryStage->getExternalNamePairs(true);
        $entryStagesOption = [];
        foreach($entryStages as $key => $value) {
            $entryStagesOption[] = [
                'value' => $key,
                'label' => $value,
                'attributes' => [
                    'id' => 'entryStage-' . $key,
                ],
                'label_attributes' => [
                    'class' => 'col-form-label entry-stage-options',
                ],
            ];
        }
        
        // Universal Entry Stage(s) element
        $this->add([
            'name' => 'entryStage',
            'type' => Element\MultiCheckbox::class,
            'options' => [
                'label' => 'Universal Entry Stage(s)',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ],
                'value_options' => $entryStagesOption,
            ],
        ]);
        
        // Email element
        $this->add([
            'name' => 'email',
            'type' => Element\Email::class,
            'attributes'=> [
                'id'    => 'email',
                'class' => 'form-control',
                'maxlength' => '255',
                'readonly' => $readOnly,
            ],
            'options' => [
                'label' => 'Email *',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ]
            ],
        ]);
        
        // PeopleSoft Employee ID element
        $this->add([
            'name' => 'peoplesoftEmployeeId',
            'type' => 'text',
            'attributes'=> [
                'id'    => 'peoplesoftEmployeeId',
                'class' => 'form-control',
                'readonly' => $readOnly,
            ],
            'options' => [
                'label' => 'PeopleSoft Employee ID *',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ]
            ],
        ]);
        
        $this->addKeyingVendorId(KeyingVendorService::SRC_USER_FORM, $this->mode, $this->isInternal);
        
        $states = $this->serviceState->getAllStates();
        $stateOptions = [];
        foreach($states as $key => $row) {
            $stateOptions[$row['stateId']] = $row['nameAbbr'] . ' - ' . $row['nameFull'];
        }
        
        // State select element
        $this->add([
            'name' => 'stateId',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'stateId',
                'class' => 'form-control form-select',
                'size'  => 1,
            ],
            'options' => [
                'label' => 'State',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ],
                'empty_option' => 'Select a State',
                'value_options' => $stateOptions,
                'disable_inarray_validator' => true,
            ],
        ]);
        
        // Select By radio button element
        $this->add([
            'name' => 'selectBy',
            'type' => Element\Radio::class,
            'attributes'=> [
                'id'    => 'selectBy',
                'class' => 'radiobtnstyle',
                'disabled' => true,
            ],
            'options' => [
                'label' => 'Select By',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ],
                'value_options' => [
                    [
                        'value' => 'eCrashAgency',
                        'label' => 'eCrash Agency',
                        'label_attributes' => [
                            'for' => 'selectBy-eCrashAgency',
                            'class' => 'col-md-12 pl-0'
                        ],
                        'attributes'=> [
                            'id'    => 'selectBy-eCrashAgency',
                            'class' => 'radiobtnstyle',
                            'disabled' => true,
                        ]
                    ],
                    [
                        'value' => 'form',
                        'label' => 'Form',
                        'label_attributes' => [
                            'for' => 'selectBy-form',
                            'class' => 'col-md-12 pl-0'
                        ],
                        'attributes'=> [
                            'id'    => 'selectBy-form',
                            'class' => 'radiobtnstyle',
                            'disabled' => true,
                        ]
                    ],
                ],
            ],
        ]);
        
        // Agency select element
        $this->add([
            'name' => 'agencyId',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'agencyId',
                'class' => 'form-control form-select',
                'disabled' => true,
                'size' => 1,
            ],
            'options' => [
                'empty_option' => 'Select an Agency',
                'disable_inarray_validator' => true
            ],
        ]);
        
        // Agency form select element
        $this->add([
            'name' => 'agencyFormId',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'agencyFormId',
                'class' => 'form-control form-select',
                'size' => 1,
                'disabled' => true,
            ],
            'options' => [
                'label' => 'Agency Form',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ],
                'empty_option' => 'Select a Form',
                'disable_inarray_validator' => true
            ],
        ]);
        
        // Form select element
        $this->add([
            'name' => 'formId',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'formId',
                'class' => 'form-control form-select',
                'size' => 1,
                'disabled' => true,
            ],
            'options' => [
                'empty_option' => 'Select a Form',
                'disable_inarray_validator' => true
            ],
        ]);
        
        // Report Type select element
        $this->add([
            'name' => 'reportTypeId',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'reportTypeId',
                'class' => 'form-control form-select',
                'value' => 'Auto Accident',
                'disabled' => true,
                'size' => 1,
            ],
            'options' => [
                'empty_option' => 'Report Type',
                'disable_inarray_validator' => true,
                'value_options' => [
                    'Auto Accident' => 'Auto Accident'
                ],
            ],
        ]);
        
        // Notes hidden element
        $this->add([
            'name' => 'note',
            'type' => 'hidden',
            'attributes' => [
                'value' => '',
                'id'    => 'note'
            ],
        ]);
        
        // Assign button
        $this->add([
            'name' => 'assign',
            'type' => 'button',
            'attributes'=> [
                'id'    => 'assign',
                'class' => 'btnstyle btn-sm',
                'disabled' => true,
            ],
            'options' => [
                'label' => 'assign',
            ],
        ]);
        
        // Save button
        $this->add([
            'name' => 'save',
            'type' => 'button',
            'attributes'=> [
                'id'    => 'save',
                'class' => 'btnstyle btn-sm',
            ],
            'options' => [
                'label' => 'Save',
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
                'label' => 'cancel',
            ],
        ]);
        
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
        
        if ($this->mode == self::USER_EDIT) {
            //isActive checkbox
            $this->isActive();
            
            //Set report to user
            $this->reportID();
            
            //Start date
            $this->addFromDate();
            
            //End date
            $this->addToDate();
            
            //Add set Report Button
            $this->reportAssignButton();
            
            //Add Notes History Button
            $this->notesHistoryButton();
            
            // Reset password button
            $this->resetPasswordButton();
            
            // Delete button
            $this->deleteButton();
        } else {
            // For add form
            $this->get('save')->setAttributes(['disabled' => true]);
        }
    }
    
    protected function isActive()
    {
        $disabled = ($this->isInternal)? 'disabled' : '';
        // IsActive in MAE checkbox
        $this->add([
            'name' => 'isActive',
            'type' => Element\Checkbox::class,
            'checked' => false,
            'options' => [
                'label' => 'Active',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ],
            ],
            'attributes' => [
                'disabled' => $disabled,
            ],
        ]);
    }
    
    protected function reportID()
    {
        $this->add([
            'name' => 'reportID',
            'type' => 'text',
            'attributes'=> [
                'id'    => 'reportID',
                'class' => 'form-control',
                'maxlength' => '10',
            ],
            'options' => [
                'label' => 'Report Id',
            ],
        ]);
    }
    
    protected function reportAssignButton()
    {
        $this->add([
            'name' => 'setreport',
            'type' => 'button',
            'attributes'=> [
                'id'    => 'setreport',
                'class' => 'btnstyle btn-sm',
            ],
            'options' => [
                'label' => 'Assign',
            ],
        ]);
    }
    
    protected function addFromDate()
    {
        $this->add([
            'name'    => 'processingStartTime',
            'type'    => Element\Text::class,
            'options' => [
                'label' => 'From Date',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ],
            ],
            'attributes' => [
                'id' => 'processingStartTime',
                'class' => 'form-control hasCalendar inputText',
                //'readOnly' => 'true',
                'autocomplete' => 'off',
                'style' => 'width:155px',
                'max'  => date('Y-m-d'),
                'step' => '1', // days; default step interval is 1 day
                'format' => 'Y-m-d',
            ],
        ]);
    }
    
    protected function addToDate()
    {
        $this->add([
            'name'    => 'processingEndTime',
            'type'    => Element\Text::class,
            'options' => [
                'label' => 'To Date',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ],
            ],
            'attributes' => [
                'id' => 'processingEndTime',
                'class' => 'form-control hasCalendar inputText',
                //'readOnly' => 'true',
                'autocomplete' => 'off',
                'style' => 'width:155px',
                'max'  => date('Y-m-d'),
                'step' => '1', // days; default step interval is 1 day
                'format' => 'Y-m-d',
            ],
        ]);
    }
    
    public function notesHistoryButton() {
        // Notes button
        $this->add([
            'name' => 'noteHistory',
            'type' => 'button',
            'attributes' => [
                'id' => 'noteHistory',
                'class' => 'btnstyle btn-sm',
            ],
            'options' => [
                'label' => 'Notes',
            ],
        ]);
    }
    
    public function resetPasswordButton() {
        $this->add([
            'name' => 'resetPassword',
            'type' => 'button',
            'attributes' => [
                'id' => 'resetPassword',
                'class' => 'btnstyle btn-sm',
            ],
            'options' => [
                'label' => 'Reset Password',
            ],
        ]);
    }
    
    public function deleteButton() {
        $this->add([
            'name' => 'delete',
            'type' => 'button',
            'attributes' => [
                'id' => 'delete',
                'class' => 'btnstyle btn-sm',
            ],
            'options' => [
                'label' => 'Delete',
            ],
        ]);
    }
    
    public function addInputFilter()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }
        
        $this->inputFilter = $this->getInputFilter();
        
        $userId = [
            'name' => 'userId',
            'required' => false,
        ];
        
        if ($this->mode == self::USER_EDIT) {
            $userId['required'] = true;
            $userId['validators'] = [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => "User ID is required and can't be empty",
                        ],
                    ],
                ],
            ];
        }
        
        $this->inputFilter->add($userId);
        
        // First Name element validators rules
        $this->inputFilter->add([
            'name' => 'nameFirst',
            'required' => true,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'max' => 64,
                        'messages' => [
                            StringLength::TOO_LONG => 'First Name can not be more than 64 characters long!',
                        ]
                    ],
                ],
                [
                    'name' => Alnum::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'allowWhiteSpace' => true,
                        'messages' => [
                            Alnum::NOT_ALNUM => 'First Name should not contain special characters!',
                        ]
                    ],
                ],
            ],
        ]);
        
        // Last Name element validators rules
        $this->inputFilter->add([
            'name' => 'nameLast',
            'required' => true,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'max' => 64,
                        'messages' => [
                            StringLength::TOO_LONG => 'Last Name can not be more than 64 characters long!',
                        ]
                    ],
                ],
                [
                    'name' => Alnum::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'allowWhiteSpace' => true,
                        'messages' => [
                            Alnum::NOT_ALNUM => 'Last Name should not contain special characters!',
                        ]
                    ],
                ],
            ],
        ]);
        
        // User ID element validators rules
        $this->inputFilter->add([
            'name' => 'username',
            'required' => true,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'max' => 20,
                        'messages' => [
                            StringLength::TOO_LONG => 'User ID can not be more than 20 characters long!',
                        ]
                    ],
                ],
                [
                    'name' => CheckEntryExistUsername::class,
                    'options' => [
                        'serviceUser' => $this->serviceUser,
                        'mode' => $this->mode
                    ]
                ],
            ],
        ]);
        
        // Email element validators rules
        $email = [
            'name' => 'email',
            'required' => true,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'max' => 255,
                        'messages' => [
                            StringLength::TOO_LONG => 'Email can not be more than 255 characters long!',
                        ]
                    ],
                ]
            ],
        ];
        
        $loggedInUserName = strtolower($this->loggedInUserInfo->username);
        $internalUserExceptions = $this->config['internalUserExceptions'];
        if (!in_array($loggedInUserName, $internalUserExceptions)) {
            $email['validators'][] = [
                'name' => CheckInternalEmail::class,
                'options' => [
                    'serviceUser' => $this->serviceUser,
                    'mode' => $this->mode
                ],
            ];
        }
        
        $this->inputFilter->add($email);
        
        // PeopleSoft Employee ID element validators rules
        $this->inputFilter->add([
            'name' => 'peoplesoftEmployeeId',
            'required' => true,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'max' => 20,
                        'messages' => [
                            StringLength::TOO_LONG => 'PeopleSoft Employee ID can not be more than 20 characters long!',
                        ]
                    ],
                ],
                [
                    'name' => CheckEntryExistPeopleSoftId::class,
                    'options' => [
                        'serviceLnaaAuth' => $this->serviceLnaaAuth,
                        /**
                         * @TODO: To be removed
                         * To test the people soft employee id without webservice.
                         */
                        'disablePeopleSoftIdValidation' => $this->disablePeopleSoftIdValidation, // Option to disable the webservice validation in development.
                    ]
                ],
            ],
        ]);
        
        // Disable required validation for entry stage element
        $this->inputFilter->add([
            'name' => 'entryStage',
            'required' => false,
        ]);
        
        // Disable required validation for stateId element
        $this->inputFilter->add([
            'name' => 'stateId',
            'required' => false,
        ]);
        
        // Disable required validation for selectBy element
        $this->inputFilter->add([
            'name' => 'selectBy',
            'required' => false,
        ]);
        
        // Disable required validation for agencyId element
        $this->inputFilter->add([
            'name' => 'agencyId',
            'required' => false,
        ]);
        
        // Disable required validation for agencyFormId element
        $this->inputFilter->add([
            'name' => 'agencyFormId',
            'required' => false,
        ]);
        
        // Disable required validation for formId element
        $this->inputFilter->add([
            'name' => 'formId',
            'required' => false,
        ]);
        
        // Disable required validation for reportTypeId element
        $this->inputFilter->add([
            'name' => 'reportTypeId',
            'required' => false,
        ]);
        
        //Validation for keyingVendorId element
        $this->addKeyingVendorIdInputFilter(KeyingVendorService::SRC_USER_FORM);
        
        if ($this->mode == self::USER_EDIT) {
            // Additional filter for user edit form
            // Report ID element validators rules
            $this->inputFilter->add([
                'name' => 'reportID',
                'required' => false,
                'filters' => [
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    [
                        'name' => Regex::class,
                        'options' => [
                            'pattern' => '/[0-9]/',
                            'messages' => [
                                Regex::INVALID => 'Report Id should contain only numbers.',
                            ]
                        ],
                    ],
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'max' => 10,
                            'messages' => [
                                StringLength::TOO_LONG => 'Report ID can not be more than 10 digits long!',
                            ]
                        ],
                    ]
                ],
            ]);
            
            // Disable required validation for processingStartTime element
            $this->inputFilter->add([
                'name' => 'processingStartTime',
                'required' => false,
            ]);
            
            // Disable required validation for processingEndTime element
            $this->inputFilter->add([
                'name' => 'processingEndTime',
                'required' => false,
            ]);
        }
        
        return $this->inputFilter;
    }
}
