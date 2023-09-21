<?php
namespace Admin\Form\Metric;

use Zend\Form\Element;
use Zend\Filter\StringTrim;
use Zend\I18n\Validator\Alnum;
use Zend\Validator\Date;
use Zend\Authentication\AuthenticationService;

use Base\Service\AgencyService;
use Base\Service\StateService;
use Base\Service\FormService;
use Base\Service\KeyingVendorService;

use Base\Form\Form;
use Base\Form\KeyingVendorForm;

use Admin\Validator\CheckKeyingVendorId;

class OperatorKeyingAccuracyForm extends KeyingVendorForm
{
    private $inputFilter;
    
    public function __construct(
        AgencyService $serviceAgency,
        StateService $serviceState,
        FormService $serviceForm,
        AuthenticationService $serviceAuth,
        KeyingVendorService $serviceKeyingVendor)
    {
      $this->serviceAgency = $serviceAgency;
      $this->serviceState = $serviceState;
      $this->serviceForm = $serviceForm;
      parent::__construct('operatorKeyingAccuracy', $serviceAuth, $serviceKeyingVendor);

      $this->init();
    }
    
    public function init()
    {
        $this->setAttribute('method', 'POST');
        $this->addNameUser();
        $this->addNameFirst();
        $this->addNameLast();
        $this->addDateStart();
        $this->addDateEnd();
        $this->addFormState();
        $this->addFormName();
        $this->addFormAgency();
        $this->addKeyingVendorId(KeyingVendorService::SRC_METRICS_OKA_FORM);
        $this->addSubmit();
    }

    protected function addNameUser()
    {
        $this->add([
            'name' => 'userName',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Username'
            ],
            'attributes' => [
                'id' => 'userName',
                'class' => ''
            ]
        ]);
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
                'id' => 'firstName',
                'class' => ''
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
                'id' => 'lastName',
                'class' => ''
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
                'autocomplete' => 'off'
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
                    'autocomplete' => 'off'
                ],
        ]);
    }

    protected function addFormState()
    {
        // states dropdown
        $states = $this->serviceState->getStatesWithReports();
        $stateOptions = [];
        foreach($states as $key => $row) {
            $stateOptions[$row['state_id']] = $row['name_full'];
        }
        
        $this->add([
            'name' => 'formState',
            'type' => Element\Select::class,
            'attributes'=> [
                'id' => 'formState'
            ],
            'options' => [
                'label' => 'State',
                'empty_option' => 'Select a State',
                'options' => $stateOptions
            ],
        ]);
    }

    protected function addFormName()
    {
        // forms dropdown
        $formTypes = $this->serviceForm->getFormsWithReports();
        $formTypesOptions = [];
        foreach($formTypes as $key => $row) {
            $formTypesOptions[$row['form_id']] = $row['name_external'];
        }
        $this->add([
            'name' => 'formId',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'formId',
                'disabled' => false
            ],
            'options' => [
                'label' => 'Name',
                'empty_option' => 'Select Form',
                'options' => $formTypesOptions
            ],
        ]);
    }

    protected function addFormAgency()
    {
        //agency dropdown
        $anencies = $this->serviceAgency->getAgenciesWithReports();
        $agencyOptions = [];

        foreach($anencies as $key => $row) {
            $agencyOptions[$row['agency_id']] = $row['name'];
        }

        $this->add([
            'name' => 'formAgencyId',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'formAgencyId',
                'disabled' => false
            ],
            'options' => [
                'label' => 'Agency',
                'empty_option' => 'Select an Agency',
                'options' => $agencyOptions
            ]
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
            'name' => 'userName',
            'required' => false,
        ]);
        $this->inputFilter->add([
            'name' => 'fromDate',
            'required' => false,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => Date::class,
                    'options' => [
                        'format' => 'm/d/Y',
                        'messages' => [
                            Date::FALSEFORMAT => 'From Date should be in the format MM/DD/YYYY',
                        ]
                    ],
                ],
            ],
        ]);
        $this->inputFilter->add([
            'name' => 'toDate',
            'required' => false,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => Date::class,
                    'options' => [
                        'format' => 'm/d/Y',
                        'messages' => [
                            Date::FALSEFORMAT => 'To Date should be in the format MM/DD/YYYY',
                        ]
                    ],
                ],
            ],
        ]);
        $this->inputFilter->add([
            'name' => 'formState',
            'required' => false,
        ]);
        $this->inputFilter->add([
            'name' => 'formId',
            'required' => false,
        ]);
        $this->inputFilter->add([
            'name' => 'formAgencyId',
            'required' => false,
        ]);
        
        //Validation for keyingVendorId element
        $this->addKeyingVendorIdInputFilter(KeyingVendorService::SRC_METRICS_OKA_FORM);
        
        return $this->inputFilter;
    }
    
}
