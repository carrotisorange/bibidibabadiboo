<?php
namespace Admin\Form;

use Zend\Form\Element;
use Zend\Filter\StringTrim;
use Zend\I18n\Validator\Alnum;
use Zend\I18n\Validator\IsInt;
use Zend\Authentication\AuthenticationService;

use Zend\Validator\NotEmpty;
use Zend\Validator\Date;

use Base\Service\AgencyService;
use Base\Service\StateService;
use Base\Service\FormTypeService;
use Base\Form\Form;
use Base\Form\KeyingVendorForm;
use Base\Service\KeyingVendorService;
use Admin\Validator\CheckKeyingVendorId;

class ViewKeyedImageForm extends KeyingVendorForm
{
    private $inputFilter;

    private $totalUsedFilter  = 0;

    public function __construct(
        AgencyService $serviceAgency,
        StateService $serviceState,
        FormTypeService $serviceFormType,
        KeyingVendorService $serviceKeyingVendor,
        AuthenticationService $serviceAuth)
    {
        $this->serviceAgency = $serviceAgency;
        $this->serviceState = $serviceState;
        $this->serviceFormType = $serviceFormType;
        $this->serviceKeyingVendor = $serviceKeyingVendor;
        $this->serviceAuth = $serviceAuth;
        parent::__construct('viewkeyedimage', $serviceAuth, $serviceKeyingVendor);
        
        $this->init();
    }
    
    public function init()
    {
        $this->setAttribute('method', 'POST');
        $this->setName('viewkeyedimage');
        $this->addCruOrderId();
        $this->addReportId();
        $this->addReturnAllPasses();
        $this->addFromDate();
        $this->addToDate();
        $this->addState();
        $this->addAgency();
        $this->addVin();
        $this->addTag();
        $this->addCaseIdentifier();
        $this->addReportType();
        $this->addIncidentDate();
        $this->addTagState();
        $this->addPartyNameLast();
        $this->addPartyNameFirst();
        $this->addOperatorNameLast();
        $this->addOperatorNameFirst();
        $this->addKeyingVendorId(KeyingVendorService::SRC_VIEW_KEYED_FORM);
        
        $this->addSubmit();
    }

    protected function addCruOrderId() {
        $this->add([
            'name' => 'cruOrderId',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'CRU Order ID',
               
            ],
            'attributes' => [
                'id' => 'cruOrderId',
                'class' => 'inputText',
                'style' => 'width: 115px'
            ]
        ]);
    }

    protected function addReportId() 
    {
        $this->add([
            'name' => 'reportId',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'OR Report ID'
            ],
            'attributes' => [
                'id' => 'reportId',
                'class' => 'inputText',
                'style' => 'width: 115px'
            ]
        ]);
    }

    protected function addReturnAllPasses() 
    {
        $this->add([
            'name' => 'returnAllPasses',
            'type' => Element\Checkbox::class,
            'options' => [
                'label' => 'Include All Passes'
            ],
            'attributes' => [
                  'id' => 'returnAllPasses'
              ],
        ]);
    }

    protected function addFromDate() 
    {
        $this->add([
            'name' => 'processingStartTime',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'From Date *'
            ],
            'required' => true,

            'attributes' => [
                'class' => 'hasCalendar inputText',
                'style' => 'width: 115px',
                'id' => 'processingStartTime',
                'autocomplete' => 'off',
                'readOnly' =>  true,
                'format' => 'Y-m-d'
            ],
        ]);
    }

    protected function addToDate() 
    {
        $this->add([
            'name' => 'processingEndTime',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'To Date *'
            ],

            'required' => true,
            
            'attributes' => [
                'class' => 'inputText hasCalendar',
                'style' => 'width: 115px',
                'id' => 'processingEndTime',
                'autocomplete' => 'off',
                'readOnly' => true,
                'format' => 'Y-m-d'
            ],
        ]);
    }

    protected function addState()
    {
        // states dropdown
        $states = $this->serviceState->fetchAlltoArray();
        $stateOptions = [];

        foreach($states as $key => $row) {
            $stateOptions[$row['stateId']] = $row['nameAbbr'] . ' - ' . $row['nameFull'];
        }

        $this->add([
            'name' => 'stateId',
            'type' => Element\Select::class,
            'attributes'=> [
                'id' => 'stateId',
                'onChange' => 'SelectAgencyViewKeyedImage();',
                'class' => 'txt-login',
                'style' => 'width: 162px'
            ],
            'options' => [
                'label' => 'State',
                'empty_option' => 'Select a State',
                'options' => $stateOptions
            ],
        ]);
    }

    protected function addAgency()
    {
        // agency dropdown
        $agencies = $this->serviceAgency->fetchActive();
        $agencyOptions = [];
        foreach($agencies as $key => $row) {
            $agencyOptions[$row['agency_id']] = $row['name'];
        }

        $this->add([
            'name' => 'agencyId',
            'type' => Element\Select::class,
            'attributes'=> [
                'id' => 'agencyId',
                'disabled' => false,
                'class' => 'txt-login',
                'style' => 'width: 210px'
            ],
            'options' => [
                'label' => 'eCrash Agency',
                'empty_option' => 'Select an Agency',
                'disable_inarray_validator' => true,
                'options' => $agencyOptions
            ],
        ]);
    }

    protected function addVin()
    {
        //Add VIN element
        $this->add([
            'name' => 'vin',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'VIN #'
            ],
            'attributes' => [
                'id' => 'vin',
                'class' => 'inputText',
                'style' => 'width: 155px'
            ]
        ]);
    }

    protected function addTag()
    {
        //Add License plate element
        $this->add([
            'name' => 'licensePlate',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Tag'
            ],
            'attributes' => [
                'id' => 'licensePlate',
                'class' => 'inputText',
                'style' => 'width: 155px'
            ]
        ]);
    }

    protected function addTagState()
    {
        $registrationStates = $this->serviceState->fetchAlltoArray();
        $registrationStatesOptions = [];

        foreach($registrationStates as $key => $row) {
            $registrationStatesOptions[$row['nameAbbr']] = $row['nameAbbr'] . ' - ' . $row['nameFull'];
        }

        $this->add([
            'name' => 'registrationState',
            'type' => Element\Select::class,
            'attributes'=> [
                'id' => 'registrationState',
                'class' => 'txt-login',
                'style' => 'width: 162px'
            ],
            'options' => [
                'label' => 'Tag State',
                'empty_option' => 'Tag State',
                'options' => $registrationStatesOptions
            ],
        ]);
    }

    protected function addCaseIdentifier()
    {
        //Add Case Identifier element
        $this->add([
            'name' => 'caseIdentifier',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Report #'
            ],
            'attributes' => [
                'id' => 'caseIdentifier',
                'class' => 'inputText',
                'style' => 'width: 155px'
            ]
        ]);
    }

    protected function addReportType()
    {
        // Add Report Type element
        $formTypes = $this->serviceFormType->fetchAllKeyedFormTypes();
        $formTypesOptions = [];
        foreach($formTypes as $key => $row) {
            $formTypesOptions[$row['formTypeId']] = $row['formTypeCode'] . ' - ' . $row['formTypeDescription'];
        }
        
        $this->add([
            'name' => 'reportType',
            'type' => Element\Select::class,
            'attributes'=> [
                'id' => 'reportType',
                'disabled' => false,
                'class' => 'txt-login',
                'style' => 'width: 162px'
            ],
            'options' => [
                'label' => 'Report Type*',
                'disable_inarray_validator' => true,
                'options' => $formTypesOptions
            ],
        ]);
    }

    protected function addIncidentDate()
    {
        $this->add([
            'name' => 'crashDate',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Incident Date'
            ],
            'attributes' => [
                'class' => 'inputText hasCalendar',
                'id' => 'crashDate',
                'autocomplete' => 'off',
                'style' => 'width: 155px',
                'readOnly' => true,
                'format' => 'Y-m-d'
            ],
        ]);
    }

    protected function addPartyNameLast()
    {
        // Add Party Last Name element
        $this->add([
            'name' => 'partyLastName',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Last Name'
            ],
            'attributes' => [
                'id' => 'partyLastName',
                'class' => 'txt-login',
                'style' => 'width: 155px',
            ]
        ]);
    }

    protected function addPartyNameFirst()
    {
        //Add Party First Name element
        $this->add([
            'name' => 'partyFirstName',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'First Name'
            ],
            'attributes' => [
                'id' => 'partyFirstName',
                'class' => 'txt-login',
                'style' => 'width: 155px',
            ]
        ]);
    }

    protected function addOperatorNameLast()
    {
        // Add an last name element
        $this->add([
            'name' => 'operatorLastName',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Last Name'
            ],
            'attributes' => [
                'id' => 'operatorLastName',
                'class' => 'txt-login',
                'style' => 'width: 155px',
            ]
        ]);
    }

    protected function addOperatorNameFirst()
    {
        // Add an first name element
        $this->add([
            'name' => 'operatorFirstName',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'First Name'
            ],
            'attributes' => [
                'id' => 'operatorFirstName',
                'class' => 'txt-login',
                'style' => 'width: 155px',
            ]
        ]);
    }

    protected function addSubmit()
    {
        $this->add([
            'type' => Element\Submit::class,
            'name' => 'submit',
            'attributes' => [
                'class' => 'btnstyle',
                'value' => 'Search',
                'id' => 'submit'
            ]
        ]);
    }
    
    /**
     * call only after a form submit
     */    
    public function reportIdInputFilterIfNotEmpty()
    {
        $reportid = $this->get('reportId');

        $reportidFilter = [
            'name' => 'reportId',
            'required' => false,
            'filters' => [
                ['name' => StringTrim::class],
            ],
        ];
        
        $reportidFilter['validators']  = [
            [
                'name' => IsInt::class,
                'options' => [
                    'encoding' => 'UTF-8',
                    'messages' => [
                        IsInt::NOT_INT => 'Report ID should contain only digits',
                    ]
                ],
            ],
        ];
		// Report ID element validators rules  
		$this->inputFilter->add($reportidFilter);
    }
    /**
     * call only if form is submitted
     */

    public function timeProcessingRangeValidate()
    {
        $processingStartTimeValue = $this->get('processingStartTime')->getValue();
        $processingEndTimeValue = $this->get('processingEndTime')->getValue();

        /**
         * check if end time is grater than start time
         */
        if ($processingEndTimeValue > $processingStartTimeValue) {
            $this->setMessages([['Time Range is not valid!']]);
            return false;
        }
            
        return true;
    }

    public function addInputFilters()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $this->inputFilter = $this->getInputFilter();
        // First Name element validators rules
        $this->inputFilter->add([
            'name' => 'cruOrderId',
            'required' => false,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => IsInt::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'messages' => [
                            IsInt::NOT_INT => 'CRU Order ID should contain only digits',
                        ]
                    ],
                ],
            ],
        ]);

        $this->inputFilter->add([
            'name' => 'operatorLastName',
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
            'name' => 'operatorFirstName',
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
            'name' => 'returnAllPasses',
            'required' => false
        ]);

        $this->inputFilter->add([
            'name' => 'processingStartTime',
            'required' => false,
            'filters' => [
                ['name' => StringTrim::class],
            ],
        ]);

        $this->inputFilter->add([
            'name' => 'processingEndTime',
            'required' => false,
            'filters' => [
                ['name' => StringTrim::class],
            ],
        ]);

        $this->inputFilter->add([
            'name' => 'stateId',
            'required' => false
        ]);

        $this->inputFilter->add([
            'name' => 'agencyId',
            'required' => false
        ]);

        $this->inputFilter->add([
            'name' => 'vin',
            'required' => false,
            'filters' => [
                ['name' => StringTrim::class],
            ],
        ]);

        $this->inputFilter->add([
            'name' => 'licensePlate',
            'required' => false,
            'filters' => [
                ['name' => StringTrim::class],
            ],
        ]);

        $this->inputFilter->add([
            'name' => 'caseIdentifier',
            'required' => false,
            'filters' => [
                ['name' => StringTrim::class],
            ],
        ]);

        $this->inputFilter->add([
            'name' => 'reportType',
            'required' => false
        ]);

        $this->inputFilter->add([
            'name' => 'crashDate',
            'required' => false,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
              [
                  'name' => Date::class,
                  'options' => [
                      'format' => 'Y-m-d',
                      'messages' => [
                          Date::INVALID_DATE => 'Incident date does not appear to be a valid date',
                      ]
                  ],
              ],
          ],
        ]);

        $this->inputFilter->add([
            'name' => 'partyLastName',
            'required' => false,
            'filters' => [
                ['name' => StringTrim::class],
            ],
        ]);

        $this->inputFilter->add([
            'name' => 'partyFirstName',
            'required' => false,
            'filters' => [
                ['name' => StringTrim::class],
            ],
        ]);

        $this->inputFilter->add([
            'name' => 'registrationState',
            'required' => false,
        ]);
        
        //Validation for keyingVendorId element
        $this->addKeyingVendorIdInputFilter(KeyingVendorService::SRC_VIEW_KEYED_FORM);

        return $this->inputFilter;
    }


    /**
     * check if field is not empty
     * then add a filter 
     */
    public function checkAddTotalUsedFilter($fieldNameOrNames, $inputParams = [])
    {
        //temporary filter counter
        $totalUsedFilter = 0;

        if (is_array($fieldNameOrNames)) {
            foreach ($fieldNameOrNames as $field) {
                $inputValue = $inputParams[$field] ?? null;
                if (!empty($inputValue)) {
                    $totalUsedFilter++;
                }
            }
        } else {
            if (isset($inputParams[$fieldNameOrNames]) && !empty($inputParams[$fieldNameOrNames])) {
                $totalUsedFilter++;
            }
        }

        $this->totalUsedFilter += $totalUsedFilter;

        return $this->allowOrSearch();
    }

    /**
     * Check if allow search is allowed
     * or search will be allowed if total used filter is greatar than 3
     */
    public function allowOrSearch()
    {
        $retVal = false;

        if ($this->totalUsedFilter > 1) {
            $retVal = true;
        } else {            
            $this->setMessages([["You must fill another input to Search!"]]); 
        }

        return $retVal;
    }
}
