<?php
namespace Admin\Form\Metric;

use Zend\Form\Element;
use Zend\Filter\StringTrim;
use Zend\I18n\Validator\Alnum;
use Zend\Validator\NotEmpty;
use Zend\Validator\Date;
use Zend\Authentication\AuthenticationService;

use Base\Form\Form;
use Base\Form\KeyingVendorForm;

use Base\Service\KeyingVendorService;
use Admin\Validator\CheckKeyingVendorId;

class VinStatusByOperatorForm extends KeyingVendorForm
{
    private $inputFilter;
    
    public function __construct(
        AuthenticationService $serviceAuth,
        KeyingVendorService $serviceKeyingVendor) 
    {
      parent::__construct('vinStatusByOperator', $serviceAuth, $serviceKeyingVendor);
      
      $this->init();
    }
    
    public function init()
    {
        $this->setAttribute('method', 'POST');
        $this->addNameFirst();
        $this->addNameLast();
        $this->addDateStart();
        $this->addDateEnd();
        $this->addKeyingVendorId(KeyingVendorService::SRC_METRICS_VSBO_FORM);
        $this->addSubmit();
    }

    protected function addNameFirst() 
    {
        $this->add([
            'name' => 'firstName',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'First Name'
            ],
            'attributes' => [
                'id' => 'firstName'
            ]
        ]);
    }

    protected function addNameLast() 
    {
        $this->add([
            'name' => 'lastName',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Last Name'
            ],
            'attributes' => [
                'id' => 'lastName'
            ]
        ]);
    }

    protected function addDateStart() 
    {
        $this->add([
            'name'    => 'fromDate',
            'type'    => Element\Text::class,
            'options' => [
                'label' => 'From Date'
            ],
            'attributes' => [
                'class' => 'hasCalendar',
                'id' => 'fromDate',
                'autocomplete' => 'off',
                'value' => date('m/d/Y')
            ],
        ]);
    }

    protected function addDateEnd() 
    {
        $this->add([
            'name'    => 'toDate',
            'type'    => Element\Text::class,
            'options' => [
                'label' => 'To Date'
            ],
            'attributes' => [
                'class' => 'hasCalendar',
                'id' => 'toDate',
                'autocomplete' => 'off',
                'value' => date('m/d/Y')
            ],
        ]);
    }

    protected function addSubmit()
    {
        $this->add([
            'type' => Element\Submit::class,
            'name' => 'submit',
            'attributes' => [
                'class' => 'btnstyle btnReportEntry',
                'value' => 'Search',
                'onClick' => 'return ValidateForm();'
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
            'name' => 'firstName',
            'required' => false,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => Alnum::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'allowWhiteSpace' => true,
                        'messages' => [
                            Alnum::NOT_ALNUM => 'First Name should not contain special characters',
                        ]
                    ],
                ],
            ],
        ]);
        $this->inputFilter->add([
            'name' => 'lastName',
            'required' => false,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => Alnum::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'allowWhiteSpace' => true,
                        'messages' => [
                            Alnum::NOT_ALNUM => 'Last Name should not contain special characters',
                        ]
                    ],
                ],
            ],
        ]);
        $this->inputFilter->add([
            'name' => 'fromDate',
            'required' => true,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                  [
                      'name' => NotEmpty::class,
                      'options' => [
                          'messages' => [
                              NotEmpty::IS_EMPTY => "From Date is required and can't be empty"
                          ],
                      ],
                  ],
                [
                    'name' => Date::class,
                    'options' => [
                        'format' => 'm/d/Y',
                        'messages' => [
                            Date::INVALID_DATE => 'From Date should be in the format MM/DD/YYYY',
                        ]
                    ],
                ],
            ],
        ]);
        $this->inputFilter->add([
            'name' => 'toDate',
            'required' => true,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => "To Date is required and can't be empty"
                        ],
                    ],
                ],
                [
                    'name' => Date::class,
                    'options' => [
                        'format' => 'm/d/Y',
                        'messages' => [
                            Date::INVALID_DATE => 'To Date should be in the format MM/DD/YYYY',
                        ]
                    ],
                ],
            ],
        ]);
        
        //Validation for keyingVendorId element
        $this->addKeyingVendorIdInputFilter(KeyingVendorService::SRC_METRICS_VSBO_FORM);
        
        return $this->inputFilter;
    }
    
}
