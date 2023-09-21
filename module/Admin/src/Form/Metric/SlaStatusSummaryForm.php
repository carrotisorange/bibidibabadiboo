<?php
namespace Admin\Form\Metric;

use Zend\Form\Element;
use Zend\Authentication\AuthenticationService;
use Base\Service\StateService;
use Base\Service\KeyingVendorService;
use Base\Service\WorkTypeService;
use Base\Form\Form;
use Base\Form\KeyingVendorForm;

use Admin\Validator\CheckKeyingVendorId;

class SlaStatusSummaryForm extends KeyingVendorForm
{
    private $inputFilter;
        
    public function __construct(
        StateService $serviceState,
        AuthenticationService $serviceAuth,
        KeyingVendorService $serviceKeyingVendor,
        WorkTypeService $serviceWorkType)
    {
        $this->serviceState = $serviceState;
        $this->serviceWorkType = $serviceWorkType; 
        parent::__construct('slaStatusSummary', $serviceAuth, $serviceKeyingVendor);                 
        
        $this->init();
    }
    
    public function init()
    {
        $this->setAttribute('method', 'POST');
        $this->addFormState();
        $this->addWorkType();
        $this->addFormPriority();
        $this->addKeyingVendorId(KeyingVendorService::SRC_METRICS_SSS_FORM);      
        $this->addSubmit();
    }
    
    protected function addFormState()
    {
        // states dropdown
        $stateList = $this->serviceState->getStates();
        $stateOptions = [];
        $stateOptions['all'] = 'All';
        foreach ($stateList as $stateOption) {
            $stateOptions[$stateOption['state_id']] = $stateOption['name_abbr'] . ' - ' . $stateOption['name_full'];
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

    protected function addWorkType()
    {
        // worktype dropdown
        $workTypeList = $this->serviceWorkType->getAll();

        $workTypeOptions = [];
        $workTypeOptions['all'] = 'All';
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
                //'empty_option' => 'Select a Work Type',
                'options' => $workTypeOptions
            ],
        ]);
    }

    protected function addFormPriority() 
    {
        $this->add([
            'name' => 'priority',
            'type' => Element\Checkbox::class,
            'options' => [
                'label' => 'Only Priority Reports'
            ],
            'attributes' => [
                  'id' => 'priority'
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
        $this->inputFilter->add([
            'name' => 'state',
            'required' => true,
        ]);
        
        //Validation for keyingVendorId element
        $this->addKeyingVendorIdInputFilter(KeyingVendorService::SRC_METRICS_SSS_FORM);
                
        return $this->inputFilter;
    }
    
}
