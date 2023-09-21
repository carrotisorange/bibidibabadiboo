<?php
namespace Base\Form;

use Zend\Form\Element;
use Zend\Filter\StringTrim;
use Zend\Authentication\AuthenticationService;

use Base\Form\Form;
use Admin\Form\UserForm;

use Base\Service\KeyingVendorService;
use Admin\Validator\CheckKeyingVendorId;

class KeyingVendorForm extends Form
{
    /**
     * Keying Vendor Form
     * @param object $serviceAuth   Zend\Authentication\AuthenticationService;
     * @param object $serviceKeyingVendor   Base\Service\KeyingVendorService;
     */
    public function __construct(
        $formName,
        AuthenticationService $serviceAuth,
        KeyingVendorService $serviceKeyingVendor)
    {
        $this->serviceAuth = $serviceAuth;
        $this->serviceKeyingVendor = $serviceKeyingVendor;
        
        parent::__construct($formName);
    }
    
    public function addKeyingVendorId($source, $mode = false, $isInternal = false)
    {
        $loggedInUser = $this->serviceAuth->getIdentity();

        if (!empty($loggedInUser)) {
            if ($this->serviceKeyingVendor->isLoggedInLNUser()) {
                $vendorOptions = [];
                //Vendor Name dropdown
                if ($source == KeyingVendorService::SRC_USER_FORM && $isInternal 
                        && $mode == UserForm::USER_EDIT) {
                    $lnVendor = $this->serviceKeyingVendor->fetchKeyingVendorByName(KeyingVendorService::VENDOR_LN);
                    $vendors = [$lnVendor['keying_vendor_id'] => $lnVendor['vendor_name']];
                } else {
                    $excludeList = null;
                    if ($source == KeyingVendorService::SRC_USER_FORM) {
                        $excludeList = ["'" . KeyingVendorService::VENDOR_LN . "'"];
                    } 
                    $vendors = $this->serviceKeyingVendor->fetchKeyingVendorNamePairs($excludeList);
                }
                
                if ($source != KeyingVendorService::SRC_USER_FORM) {
                    $vendorOptions[] = [
                        'value' => KeyingVendorService::VENDOR_ALL,
                        'label' => KeyingVendorService::VENDOR_ALL,
                        'attributes' => [
                            'id' => 'keyingVendorId-all'
                        ]
                    ];
                }

                foreach($vendors as $key => $value) {
                    $vendorOptions[] = [
                        'value' => $key,
                        'label' => $value,
                        'attributes' => [
                            'id' => 'keyingVendorId-' . $key
                        ],
                        'label_attributes' => [
                            'class' => 'col-form-label keying-vendor-id-options',
                        ],
                    ];
                }
                // Vendor Name element
                $attrClass = ($source == KeyingVendorService::SRC_VIEW_KEYED_FORM) ? 'txt-login' : 'form-control';
                $this->add([
                    'name' => 'keyingVendorId',
                    'type' => Element\Select::class,
                    'options' => [
                        'label' => 'Company',
                        'value_options' => $vendorOptions,
                        'label_attributes' => [
                            'class' => 'col-form-label'
                        ]
                    ],
                    'attributes' => [
                        'id' => 'keyingVendorId',
                        'class' => $attrClass,
                        'size'  => 1
                    ]
                ]);
            } else {
                // Vendor Name element                
                $this->add([
                    'name' => 'keyingVendorId',
                    'type' => 'hidden',
                    'attributes'=> [
                        'id'    => 'keyingVendorId',
                        'value' => $loggedInUser->keyingVendorId,
                    ],
                ]);
            }
        }
    }
    
    public function addKeyingVendorIdInputFilter($source) 
    {
        //Validation for keyingVendorId element
        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'keyingVendorId',
            'required' => true,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => CheckKeyingVendorId::class,
                    'options' => [
                        'serviceKeyingVendor' => $this->serviceKeyingVendor,
                        'serviceAuth' => $this->serviceAuth,
                        'source' => $source
                    ]
                ],
            ],
        ]);
    }
}
