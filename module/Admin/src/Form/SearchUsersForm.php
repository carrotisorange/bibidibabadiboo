<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Form;

use Zend\Form\Element;
use Zend\Filter\StringTrim;
use Zend\I18n\Validator\Alnum;
use Zend\Authentication\AuthenticationService;
use Zend\Filter\StripTags;
use Zend\InputFilter\InputFilter;
use Zend\Validator\StringLength;
use Zend\Validator\Csrf;
use Zend\Validator\Regex;

use Base\Form\Form;
use Base\Form\KeyingVendorForm;

use Base\Service\UserService;
use Base\Service\EntryStageService;
use Base\Service\KeyingVendorService;
use Admin\Validator\CheckKeyingVendorId;

/**
 * To generate user search form
 */
class SearchUsersForm extends KeyingVendorForm
{
    /**
     * @var Base\Service\UserService
     */
    private $serviceUser;
    
    /**
     * @var Zend\Authentication\AuthenticationService
     */
    private $serviceEntryStage;
    
    private $inputFilter;
    
    /**
     * @var Zend\Authentication\AuthenticationService
     */
    protected $serviceAuth;
    
    /**
     * @var Base\Service\KeyingVendorService
     */
    protected $serviceKeyingVendor;
    
    /**
     * Constructor will be invoked from the UserControllerFactory
     * @param object $serviceAuth   Zend\Authentication\AuthenticationService;
     * @param object $serviceUser   Base\Service\UserService;
     * @param object $serviceEntryStage   Base\Service\EntryStageService;
     * @param object $serviceKeyingVendor   Base\Service\KeyingVendorService;
     */
    public function __construct(
        AuthenticationService $serviceAuth,
        UserService $serviceUser,
        EntryStageService $serviceEntryStage,
        KeyingVendorService $serviceKeyingVendor)
    {
        $this->serviceUser = $serviceUser;
        $this->serviceEntryStage = $serviceEntryStage;
        $this->serviceAuth = $serviceAuth;
        $this->serviceKeyingVendor = $serviceKeyingVendor;
        
        $formName = 'searchUser';
        parent::__construct($formName, $serviceAuth, $serviceKeyingVendor);
        
        $this->setAttribute('id', $formName);
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        
        $this->init();
        $this->addInputFilters();
    }
    
    public function init()
    {
        $this->addNameFirst();
        $this->addNameLast();
        $this->addKeyingVendorId(KeyingVendorService::SRC_SEARCH_USERS_FORM);
        $this->addRoles();
        $this->addEntryStage();
        $this->addSubmit();
    }
    
    protected function addNameFirst()
    {
        $this->add([
            'name' => 'nameFirst',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control',
                'id' => 'nameFirst',
                'maxlength' => '64',
            ],
            'options' => [
                'label' => 'First Name',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ]
            ],
        ]);
    }
    
    protected function addNameLast()
    {
        $this->add([
            'name' => 'nameLast',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control',
                'id' => 'nameLast',
                'maxlength' => '64',
            ],
            'options' => [
                'label' => 'Last Name',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ]
            ],
        ]);
    }
    
    /**
     * Add keying roles and containing element in the search page
     */
    protected function addRoles()
    {
        $userRoles = $this->serviceUser->getValidRoles();
        $userInfo = $this->serviceAuth->getIdentity();
        
        $roles[''] = 'All Roles';
        foreach ($userRoles as $row) {
            if ((($userInfo->role != UserService::ROLE_SUPERVISOR) && ($row['name'] == UserService::ROLE_ADMIN))
                || ($row['name'] != UserService::ROLE_ADMIN)) {
                $roles[$row['userRoleId']] = $row['nameExternal'];
            }
        }
        
        $this->add([
            'name' => 'userRoleId',
            'type' => Element\Select::class,
            'attributes' => [
                'class' => 'form-control form-select',
                'id' => 'userRoleId',
                'size' => '1'
            ],
            'options' => [
                'value_options' => $roles,
                'label' => 'Role',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ],
            ],
        ]);
    }
    
    /**
     * Entry stages checkbox
     */
    protected function addEntryStage()
    {
        $entryStages = $this->serviceEntryStage->getExternalNamePairs(true);
        
        $this->add([
            'type' => Element\MultiCheckbox::class,
            'name' => 'entryStage',
            'attributes' => [
                'class' => 'entryStage',
            ],
            'options' => [
                'value_options' => $entryStages,
                'label' => 'Universal Entry Stage(s)',
                'label_attributes' => [
                    'class' => 'col-form-label'
                ]
            ],
        ]);
    }
    
    protected function addSubmit()
    {
        $this->add([
            'type' => Element\Submit::class,
            'name' => 'Search',
            'attributes' => [
                'class' => 'btn-sm btnstyle',
                'value' => 'Search',
            ]
        ]);
    }
    
    public function addInputFilters()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }
        
        $this->inputFilter = $this->getInputFilter();
        
        // First Name element validators rules
        $this->inputFilter->add([
            'name' => 'nameFirst',
            'required' => false,
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
            'required' => false,
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
        
        //Validation for keyingVendorId element
        $this->addKeyingVendorIdInputFilter(KeyingVendorService::SRC_SEARCH_USERS_FORM);
        
        // Disable required validation for entry stage element
        $this->inputFilter->add([
            'name' => 'entryStage',
            'required' => false,
        ]);
        
        // Disable required validation for stateId element
        $this->inputFilter->add([
            'name' => 'userRoleId',
            'required' => false,
        ]);
        
        return $this->inputFilter;
    }
}
