<?php
namespace Admin\Form;

use Zend\Form\Element;

use Base\Service\StateService;
use Base\Form\Form;

class AssignDataElementsForm extends Form
{
    private $inputFilter;

    public function __construct(StateService $serviceState) {
        parent::__construct('AssignDataElementsForm',[]);
        $this->serviceState = $serviceState;

        $this->init();
    }

    public function init()
    {
        $this->addStateSelect();
        $this->addAgencySelect();
        $this->addFormSelect();
    }

    protected function addStateSelect()
    {
        // states dropdown
        $stateList = $this->serviceState->fetchStateIdNamePairs();

        $this->add([
            'name' => 'stateSelect',
            'type' => Element\Select::class,
            'attributes'=> [
                'id' => 'stateSelect'
            ],
            'options' => [
                'label' => 'State',
                'label_attributes' => [
                    'class' => ''
                ],
                'empty_option' => 'Select a State',
                'options' => $stateList
            ],
        ]);
    }

    protected function addAgencySelect()
    {
        // agency dropdown
        $this->add([
            'name' => 'agencySelect',
            'type' => Element\Select::class,
            'attributes'=> [
                'id' => 'agencySelect'
            ],
            'options' => [
                'label' => 'eCrash Agency',
                'empty_option' => 'Select one'
            ],
        ]);
    }


    protected function addFormSelect()
    {
        $this->add([
            'name' => 'formSelect',
            'type' => Element\Select::class,
            'attributes'=> [
                'id' => 'formSelect'
            ],
            'options' => [
                'label' => 'Form Name',
                'empty_option' => 'Select one'
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
            'name' => 'stateSelect',
            'required' => false,
        ]);

        $this->inputFilter->add([
            'name' => 'agencySelect',
            'required' => false,
        ]);

        $this->inputFilter->add([
            'name' => 'formSelect',
            'required' => false,
        ]);

        return $this->inputFilter;
    }
}
