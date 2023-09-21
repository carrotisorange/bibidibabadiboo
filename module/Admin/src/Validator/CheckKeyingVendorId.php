<?php
/**
 * @copyright   Copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Validator;

use Zend\Validator\AbstractValidator;
use Base\Service\KeyingVendorService;

class CheckKeyingVendorId extends AbstractValidator
{
    const INVALID_LIST_REQUEST = 'INVALID_LIST_REQUEST';
    const INVALID_VENDOR_ID = 'INVALID_VENDOR_ID';
    
    /**
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID_LIST_REQUEST => "Invalid source list request",
        self::INVALID_VENDOR_ID => "Invalid Keying Vendor ID"
    ];
    
    /**
     * Constructor to exchange the passed options as validator property
     *
     * @param $options array Provided options from the inputfilter.
     * @return void
     */
    public function __construct(Array $options)
    {
        parent::__construct();
        $this->serviceKeyingVendor = $options['serviceKeyingVendor'];
        $this->serviceAuth = $options['serviceAuth'];
        $this->source = $options['source'];
    }
    
    /**
     * Check if the element using this validator is valid
     *
     * This method will compare the $value of the element to the other elements
     * it needs to match. If they all match, the method returns true.
     *
     * @param $value string
     * @param $context array All other elements from the form
     * @return boolean Returns true if the element is valid
     */
    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->setValue($value);
        
        $isLNUser = $this->serviceKeyingVendor->isLoggedInLNUser();
        
        if ($this->source == KeyingVendorService::SRC_USER_FORM) {
            $errIndex = self::INVALID_VENDOR_ID;
        } else {
            //override default message
            $errMsg = str_replace('source', $this->source,  $this->messageTemplates[self::INVALID_LIST_REQUEST]);
            $this->setMessage($errMsg, self::INVALID_LIST_REQUEST);
            $errIndex = self::INVALID_LIST_REQUEST;
        }
        
        //Check if value is empty
        if (empty($value)) {
            $this->error($errIndex);
            return false;
        }
        
        //Check if Keying Vendor Id consists only of digits 
        if ((!$isLNUser || ($isLNUser && $value != KeyingVendorService::VENDOR_ALL)) 
                && !ctype_digit($value)) {
                $this->error($errIndex);
                return false;
        }
                
        //Check if Keying Vendor Id is valid
        $isValid = $this->serviceKeyingVendor->fetchKeyingVendorById($value);
        if ((!$isLNUser || ($isLNUser && $value != KeyingVendorService::VENDOR_ALL)) 
                && !$isValid) {
            $this->error($errIndex);
            return false;
        }
        
        //Check if user is still logged in and still has the keying vendor id user info
        if ($this->serviceAuth->hasIdentity()) {
            $keyingVendorId = $this->serviceAuth->getIdentity()->keyingVendorId;
            if (empty($keyingVendorId)) {
                $this->error($errIndex);
                return false;
            }
        } else {
            $this->error($errIndex);
            return false;
        }
        
        //Check if Keying Vendor ID is matches with non-LN user
        $isLoggedInSameVendor = $this->serviceKeyingVendor->isLoggedInSameVendor($value);
        if (!$isLNUser && !$isLoggedInSameVendor) {
            $this->error($errIndex);
            return false;
        }
        
        return true;
    }
    
}
