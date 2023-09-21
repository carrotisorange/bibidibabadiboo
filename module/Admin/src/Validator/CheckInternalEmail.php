<?php
/**
 * @copyright   Copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Validator;

use Zend\Validator\AbstractValidator;
use Admin\Form\UserForm;

class CheckInternalEmail extends AbstractValidator
{
    const INVALID_INTERNAL_EMAIL = 'INVALID_INTERNAL_USERID';
    
    /**
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID_INTERNAL_EMAIL => "Internal Email is not allowed. Please use another email."
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
        $this->serviceUser = $options['serviceUser'];
        $this->mode = $options['mode'];
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
        
        $userInternalInfo = $this->serviceUser->getUserIsInternalAndDomainInfo(null, $value);
        $isInternal = $userInternalInfo['isInternal'];
        
        if ($this->mode == UserForm::USER_ADD && $isInternal) {
            $this->error(self::INVALID_INTERNAL_EMAIL);
            return false;
        } 
        
        return true;
    }
}