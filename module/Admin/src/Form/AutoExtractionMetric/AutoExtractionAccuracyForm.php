<?php
namespace Admin\Form\AutoExtractionMetric;

use Zend\Form\Element;
use Zend\Filter\StringTrim;
use Zend\I18n\Validator\Alnum;
use Zend\Validator\NotEmpty;
use Zend\Validator\Date;
use Zend\Authentication\AuthenticationService;

use Base\Service\StateService;
use Base\Service\WorkTypeService;
use Base\Service\AgencyService;
use Base\Service\KeyingVendorService;

use Base\Form\Form;
use Base\Form\KeyingVendorForm;

use Admin\Validator\CheckKeyingVendorId;

class AutoExtractionAccuracyForm extends KeyingVendorForm
{
    private $inputFilter;

    public function __construct(
        StateService $serviceState,
        WorkTypeService $serviceWorkType,
        AgencyService $serviceAgency,
        AuthenticationService $serviceAuth,
        KeyingVendorService $serviceKeyingVendor)
    {
        $this->serviceState = $serviceState;
        $this->serviceWorkType = $serviceWorkType;
        $this->serviceAgency = $serviceAgency;
        parent::__construct('autoExtractionAccuracy', $serviceAuth, $serviceKeyingVendor);

        $this->init();
    }
    
    public function init()
    {
        $this->setAttribute('method', 'POST');
        $this->addDateStart();
        $this->addDateEnd();
        $this->addState();
        $this->addReportID();
        $this->addWorkType();
        $this->addAgency();
        $this->addUserID();        
        $this->addLastName();
        $this->addFirstName();
        $this->addKeyingVendorId(KeyingVendorService::SRC_AUTOEXTRACTMETRICS_AEA);
        $this->addSubmit();
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

    protected function addState()
    {
        // states dropdown
        $stateList = $this->serviceState->getAutoExtractionEnabledStates();

        $stateOptions = [];
        foreach ($stateList as $stateOption) {
            $stateOptions[$stateOption['stateId']] = $stateOption['nameAbbr'] . ' - ' . $stateOption['nameFull'];
        }
         
        $this->add([
            'name' => 'state',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'state',
                'class' => 'w-inherit'
            ],
            'options' => [
                'label' => 'State',
                'empty_option' => 'Select a State',
                'options' => $stateOptions
            ],
        ]);
    }

    protected function addReportID()
    {
        $this->add([
            'name'    => 'reportID',
            'type'    => Element\Text::class,
            'attributes' => [
                'id' => 'reportID',
                'autocomplete' => 'off'
            ],
            'options' => [
                'label' => 'Report Id'
            ],
        ]);
    }

    protected function addWorkType()
    {
        // worktype dropdown
        $workTypeList = $this->serviceWorkType->getAll();

        $workTypeOptions = [];
        foreach ($workTypeList as $workTypeOption) {
            $workTypeOptions[$workTypeOption['work_type_id']] = $workTypeOption['name_external'];
        }
         
        $this->add([
            'name' => 'workType',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'workType',
                'class' => 'w-inherit'
            ],
            'options' => [
                'label' => 'Work Type',
                'empty_option' => 'Select a Work Type',
                'options' => $workTypeOptions
            ],
        ]);
    }

    protected function addAgency()
    {
        //agency dropdown
        $anencies = $this->serviceAgency->getAgenciesWithReports();
        $agencyOptions = [];

        foreach($anencies as $key => $row) {
            $agencyOptions[$row['agency_id']] = $row['name'];
        }

        $this->add([
            'name' => 'agencyId',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'agencyId',
                'size' => 1,
            ],
            'options' => [
                'label' => 'Select an Agency',
                'empty_option' => 'Select an Agency',
                'options' => $agencyOptions
            ],
        ]);
    }

    protected function addUserID()
    {
        $this->add([
            'name'    => 'userID',
            'type'    => Element\Text::class,
            'attributes' => [
                'id' => 'userID',
                'autocomplete' => 'off'
            ],
            'options' => [
                'label' => 'User Id'
            ],
        ]);
    }

    protected function addLastName()
    {
        $this->add([
            'name' => 'lastName',
            'type' => Element\Text::class,
            'attributes' => [
                'id' => 'lastName',
                'autocomplete' => 'off'
            ],
            'options' => [
                'label' => 'Last Name'
            ],
        ]);
    }

    protected function addFirstName()
    {
        $this->add([
            'name' => 'firstName',
            'type' => Element\Text::class,
            'attributes' => [
                'id' => 'firstName',
                'autocomplete' => 'off'
            ],
            'options' => [
                'label' => 'First Name'
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

        $this->inputFilter->add([
            'name' => 'state',
            'required' => false,
        ]);

        $this->inputFilter->add([
            'name' => 'reportID',
            'required' => false,
        ]);

        $this->inputFilter->add([
            'name' => 'workType',
            'required' => false,
        ]);

        $this->inputFilter->add([
            'name' => 'agencyId',
            'required' => false,
        ]);

        $this->inputFilter->add([
            'name' => 'userID',
            'required' => false,
        ]);

        $this->inputFilter->add([
            'name' => 'lastName',
            'required' => false,
        ]);

        $this->inputFilter->add([
            'name' => 'firstName',
            'required' => false,
        ]);
        
        //Validation for keyingVendorId element
        $this->addKeyingVendorIdInputFilter(KeyingVendorService::SRC_AUTOEXTRACTMETRICS_AEA);

        return $this->inputFilter;
    }
    
}
