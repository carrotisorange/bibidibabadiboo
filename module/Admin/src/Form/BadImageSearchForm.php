<?php
namespace Admin\Form;

use Zend\Form\Element;
use Zend\Filter\StringTrim;
use Zend\Authentication\AuthenticationService;

use Base\Service\VendorService;
use Base\Service\EntryStageService;
use Base\Service\ReportStatusService;
use Base\Service\KeyingVendorService;
use Base\Form\Form;
use Base\Form\KeyingVendorForm;
use Admin\Validator\CheckKeyingVendorId;

class BadImageSearchForm extends KeyingVendorForm
{
    private $inputFilter;
    private $reportStatus;
    private $source;

    public function __construct(
        EntryStageService $serviceEntryStage,
        VendorService $serviceVendor,
        KeyingVendorService $serviceKeyingVendor,
        AuthenticationService $serviceAuth,
        $reportStatus)
    {
        $this->serviceVendor = $serviceVendor;
        $this->serviceEntryStage = $serviceEntryStage;
        $this->serviceKeyingVendor = $serviceKeyingVendor;
        $this->serviceAuth = $serviceAuth;
        $this->reportStatus = $reportStatus;
        parent::__construct('badImageSearch', $serviceAuth, $serviceKeyingVendor);
        
        $this->source = ucwords($reportStatus) . ' Reports';

        $this->init();
    }
    
    public function init()
    {
        $this->addStatus();
        $this->addEntryStage();
        $this->addOperatorLastName();
        $this->addOperatorFirstName();
        $this->addKeyingVendorId($this->source);
        $this->addReportID();
        $this->addVendorCode();
        $this->addSubmit();
    }

    protected function addStatus() 
    {
        $this->add([
            'name' => 'reportStatus',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'stateId'
            ],
            'options' => [
                'label' => 'Status',
                'value_options' => [
                  'bad image' => 'Bad Image',
                  'discarded' => 'Discarded',
                  'reordered' => 'Discarded, Reordered',
                  'dead' => 'Discarded, Not Reordered'
                ],

            ],
        ]);
    }

    protected function addEntryStage()
    {
        $entryStages = $this->serviceEntryStage->getExternalNamePairs(true);

        $this->add([
            'name' => 'entryStage',
            'type' => Element\MultiCheckbox::class,
            'options' => [
                'label' => 'Universal Entry Stage(s)',
                'label_attributes' => [
                    'style' => 'display:block'
                ],
                'value_options' => $entryStages
            ],
            'attributes' => [
                    'value' => array_keys($entryStages)
                ],
        ]);
    }

    protected function addOperatorLastName() 
    {
        $this->add([
            'name' => 'operatorLastName',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Last Name'
            ],
            'attributes' => [
                'id' => 'operatorLastName'
            ]
        ]);
    }

    protected function addOperatorFirstName() 
    {
        $this->add([
            'name' => 'operatorFirstName',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'First Name'
            ],
            'attributes' => [
                'id' => 'operatorFirstName'
            ]
        ]);
    }

    protected function addReportID() 
    {
        $this->add([
            'name' => 'reportId',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Report ID'
            ],
            'attributes' => [
                'id' => 'reportId'
            ]
        ]);
    }

    protected function addVendorCode()
    {
        // vendor dropdown
        $vendorCodes = $this->serviceVendor->fetchActiveVendorPairs();
        $vendors = [];
        foreach ($vendorCodes as $row) {
            $vendors[$row['vendor_id']] = $row['vendor_code'];
        }

        $this->add([
            'name' => 'vendorCode',
            'type' => Element\Select::class,
            'attributes'=> [
                'id'    => 'vendorCode',
            ],
            'options' => [
                'label' => 'Vendor Code',
                'empty_option' => 'Select a Vendor',
                'options' => $vendors
            ],
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
                'style'=>'float:right;'
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
            'name' => 'reportStatus',
            'required' => false,
        ]);

        $this->inputFilter->add([
            'name' => 'entryStage',
            'required' => false,
        ]);

        $this->inputFilter->add([
            'name' => 'operatorLastName',
            'required' => false,
        ]);

        $this->inputFilter->add([
            'name' => 'operatorFirstName',
            'required' => false,
        ]);
                
        //Validation for keyingVendorId element
        $this->addKeyingVendorIdInputFilter($this->source);
        
        $this->inputFilter->add([
            'name' => 'reportId',
            'required' => false,
        ]);

        $this->inputFilter->add([
            'name' => 'vendorCode',
            'required' => false,
        ]);

        return $this->inputFilter;
    }
}
