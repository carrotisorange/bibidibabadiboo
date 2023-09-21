<?php
namespace Admin\Form;

use Zend\Form\Element;

use Base\Service\WorkTypeService;
use Base\Form\Form;

class StateConfigurationForm extends Form
{
    private $inputFilter;

    public function __construct(WorkTypeService $serviceWorkType)
    {
        parent::__construct();
        $this->serviceWorkType = $serviceWorkType;

        $this->init();
    }
    
    public function init()
    {
        $this->addAutoExtraction();
        $this->addWorkTypes();
        $this->addSubmit();
    }

    protected function addAutoExtraction() 
    {
        $this->add([
            'name' => 'autoExtraction',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'autoExtraction'
            ],
            'options' => [
                'label' => 'Auto Extraction',
                'value_options' => [
                  0 => 'No',
                  1 => 'Yes'
                ],

            ],
        ]);
    }

    protected function addWorkTypes()
    {
        $workTypes = $this->serviceWorkType->getWorkTypeNamePairs();

        $this->add([
            'name' => 'workType',
            'type' => Element\MultiCheckbox::class,
            'options' => [
                'label' => 'Work Types',
                'label_attributes' => [
                    'style' => 'display:block'
                ],
                'value_options' => $workTypes
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
                'value' => 'Update',
                'style'=>'float:right;'
            ]
        ]);
    }

    public function addInputFilters()
    {
        if (!$this->inputFilter) {
            
            $this->inputFilter = $this->getInputFilter();
          
            $this->inputFilter->add([
                'name' => 'autoExtraction',
                'required' => false,
            ]);

            $this->inputFilter->add([
                'name' => 'workType',
                'required' => false,
            ]);
        }

        return $this->inputFilter;
    }
}
