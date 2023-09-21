<?php
/**
 * @copyright   Copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Validator;

use Zend\Validator\AbstractValidator;

class CheckEntryExistPeopleSoftId extends AbstractValidator
{
    const INVALID_MIN_LENGTH = 'INVALID_MIN_LENGTH';
    const INVALID_ENTRY = 'INVALID_ENTRY';
    const INVALID_ASSIGNED = 'INVALID_ASSIGNED';
    
    /**
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID_MIN_LENGTH => "PeopleSoft Employee ID must be at least 6 digits long.",
        self::INVALID_ENTRY => "PeopleSoft Employee ID should be digits only. No alphabets, spaces, underscores and special characters allowed.",
        self::INVALID_ASSIGNED => "Invalid Employee ID. Please enter PeopleSoft assigned Employee ID."
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
        $this->serviceLnaaAuth = $options['serviceLnaaAuth'];
        
        /**
         * @TODO: To be removed later
         * Option to disable the webservice validation in development.
         */
        $this->disablePeopleSoftIdValidation = $options['disablePeopleSoftIdValidation'];
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
        
        //Check for min length
        if (strlen($value) < 6) {
            $this->error(self::INVALID_MIN_LENGTH);
            return false;
        }
        
        //Check if PeopleSoft Employee ID consists only of digits 
        if (!ctype_digit($value)) {
            $this->error(self::INVALID_ENTRY);
            return false;
        }
        
        if (!empty($this->disablePeopleSoftIdValidation)) {
            return true;
        }
        
        //Check if PeopleSoft Employee ID is valid
        $isExistingInLDAP = $this->serviceLnaaAuth->isValidEmployeeId($value);
        if (!$isExistingInLDAP) {
            $this->error(self::INVALID_ASSIGNED);
            return false;
        }
        
        return true;
    }
    
}
