<?php
namespace Admin\Form\Metric;

use Zend\Form\Element;
use Zend\Filter\StringTrim;
use Zend\Filter\ToInt;
use Zend\Authentication\AuthenticationService;

use Base\Service\AgencyService;
use Base\Service\StateService;
use Base\Service\KeyingVendorService;

use Base\Form\Form;
use Base\Form\KeyingVendorForm;

use Admin\Validator\CheckKeyingVendorId;

class ImageStatusByAgencyForm extends KeyingVendorForm
{
    private $inputFilter;
    
    public function __construct(
        AgencyService $serviceAgency,
        StateService $serviceState,
        AuthenticationService $serviceAuth,
        KeyingVendorService $serviceKeyingVendor)
    {
        $this->serviceAgency = $serviceAgency;
        $this->serviceState = $serviceState;
        parent::__construct('imageStatusByAgency', $serviceAuth, $serviceKeyingVendor);
        
        $this->init();
    }
    
    public function init()
    {
        $this->setAttribute('method', 'POST');
        $this->addFormState();
        $this->addFormAgency();
        $this->addKeyingVendorId(KeyingVendorService::SRC_METRICS_ISBA_FORM);
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
    
    protected function addFormAgency()
    {
        $this->add([
            'name' => 'agency',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'agency',
                'disabled' => false,
            ],
            'options' => [
                'label' => 'Agency'
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
        $this->inputFilter->add([
            'name' => 'agency',
            'required' => true,
        ]);
        
        //Validation for keyingVendorId element
        $this->addKeyingVendorIdInputFilter(KeyingVendorService::SRC_METRICS_ISBA_FORM);
        
        return $this->inputFilter;
    }
    
}
