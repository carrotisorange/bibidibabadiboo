<?php
namespace Admin\Form\AutoExtractionMetric;

use Zend\Form\Element;
use Zend\Filter\StringTrim;
use Zend\I18n\Validator\Alnum;
use Zend\Validator\NotEmpty;
use Zend\Validator\Date;
use Zend\Authentication\AuthenticationService;

use Base\Service\StateService;
use Base\Service\KeyingVendorService;

use Base\Form\Form;
use Base\Form\KeyingVendorForm;

use Admin\Validator\CheckDateRange;
use Admin\Validator\CheckKeyingVendorId;

class AutoExtractionReportForm extends KeyingVendorForm
{
    // Duration to get the report in days
    const AUTO_EXTRACTION_REPORT_DURATION = 7;
    
    private $inputFilter;
    protected $queryParams;

    public function __construct(
        StateService $serviceState, 
        AuthenticationService $serviceAuth,
        KeyingVendorService $serviceKeyingVendor,
        Array $queryParams) 
    {
        $this->serviceState = $serviceState;
        $this->queryParams = $queryParams;
        parent::__construct('autoExtractionReport', $serviceAuth, $serviceKeyingVendor);

        $this->setAttribute('class', 'default');
        $this->init();
    }
    
    public function init()
    {
        $this->setAttribute('method', 'POST');
        $this->addState();
        $this->addDateStart();
        $this->addDateEnd();
        $this->addKeyingVendorId(KeyingVendorService::SRC_AUTOEXTRACTMETRICS_AER);
        $this->addSubmit();
        $this->addExport();
    }

    protected function addState()
    {
        // states dropdown
        $stateList = $this->serviceState->getAutoExtractionEnabledStates();

        $stateOptions = [];
        $stateOptions[''] = 'Select a State';
        $stateOptions['all'] = 'All';
        
        foreach ($stateList as $stateOption) {
            $stateOptions[$stateOption['stateId']] = $stateOption['nameAbbr'] . ' - ' . $stateOption['nameFull'];
        }
         
        $this->add([
            'name' => 'state',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'state'
            ],
            'options' => [
                'label' => 'State',
                'options' => $stateOptions
            ],
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
                'value' => date('m/d/Y'),
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
                'value' => 'Submit',
                'onClick' => 'return ValidateForm();'
            ]
        ]);
    }
    
    protected function addExport()
    {
        $this->add([
            'type' => Element\Submit::class,
            'name' => 'export',
            'attributes' => [
                'class' => 'btnstyle btn-export-excel',
                'value' => 'Export',
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
            'name' => 'state',
            'required' => true,
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => "State can't be empty"
                        ],
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
                [
                    'name' => CheckDateRange::class,
                    'options' => [
                        'from_date' => $this->queryParams['fromDate'],
                        'duration' => self::AUTO_EXTRACTION_REPORT_DURATION,
                    ],
                ],
            ],
        ]);
        
        //Validation for keyingVendorId element
        $this->addKeyingVendorIdInputFilter(KeyingVendorService::SRC_AUTOEXTRACTMETRICS_AER);
        
        return $this->inputFilter;
    }
    
}
