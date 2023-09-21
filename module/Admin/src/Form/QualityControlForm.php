<?php
namespace Admin\Form;

use Zend\Form\Element;
use Zend\Filter\StringTrim;
use Zend\Validator\NotEmpty;
use Zend\Validator\Date;

use Base\Service\StateService;
use Base\Service\WorkTypeService;
use Base\Form\Form;

class QualityControlForm extends Form
{
    private $inputFilter;

    public function __construct(
        StateService $serviceState,
        WorkTypeService $serviceWorkType)
    {
        $this->serviceState = $serviceState;
        $this->serviceWorkType = $serviceWorkType;
        parent::__construct();

        $this->init();
    }
    
    public function init()
    {
        $this->addInputFilters();
        
        $this->setAttribute('method', 'POST');
        $this->addDateStart();
        $this->addDateEnd();
        $this->addState();
        $this->addWorkType();
        $this->addReportID();
        $this->addMonth();
        $this->addYear();
        $this->addkWorkType();
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

    protected function addMonth()
    {
        $this->add([
            'name' => 'month',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'month',
                'class' => 'w-inherit'
            ],
            'options' => [
                'label' => 'Month',
                'empty_option' => 'Select Month Filter',
                'options' => [1 => "January", 2=>"February", 3=> "March", 4=>"April",5=>"May",6=>"June",7=>"July",
                8=>"August",9=>"September",10=>"October",11=>"November",12=>"December"]
            ],
        ]);
    }

    protected function addkWorkType()
    {
        $this->add([
            'name' => 'kWorkType',
            'type' => Element\Checkbox::class,
            'attributes'=> [
                'id'    => 'kWorkType',
                'class' => 'w-inherit'
            ],
            'options' => [
                'label' => 'Work Type'
            ],
        ]);
    }

    protected function addYear()
    {
        $years = [];

        $year = (int) date('Y');

        for($i = $year; $i >= ($year- 5) ; $i--){
            $years[$i] = $i;
        }
        
        $this->add([
            'name' => 'year',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'year',
                'class' => 'w-inherit'
            ],
            'options' => [
                'label' => 'Year',
                'empty_option' => 'Select Year Filter',
                'options' => $years
            ],
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
            'name' => 'workType',
            'required' => false,
        ]);
        return $this->inputFilter;
    }
    
}
