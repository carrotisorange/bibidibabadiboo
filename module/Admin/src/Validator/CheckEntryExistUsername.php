<?php
/**
 * @copyright   Copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Validator;

use Zend\Validator\AbstractValidator;
use Admin\Form\UserForm;

class CheckEntryExistUsername extends AbstractValidator
{
    const USER_EXISTS = "USER_EXISTS";
    const MIN_LENGTH = "MIN_LENGTH";
    const PREDICTABLE = "PREDICTABLE";
    const SAME_DATA = "SAME_DATA";
    const INVALID_USERID = 'INVALID_USERID';
    const INVALID_INTERNAL_USERID = 'INVALID_INTERNAL_USERID';
    
    /**
     * @var array
     */
    protected $messageTemplates = [
        self::USER_EXISTS => "User ID already exists. Please enter a different User ID.",
        self::MIN_LENGTH  => "User ID must be at least 7 characters long.",
        self::PREDICTABLE => "User ID must not contain repeating characters or a sequence.",
        self::SAME_DATA   => "User ID should not be same as First name, Last name or Email Address.",
        self::INVALID_USERID => "User ID should be combination of alphabets, numbers and underscores only. No special characters allowed.",
        self::INVALID_INTERNAL_USERID => "User ID must not contain an internal domain."
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
        
        //Check for min length
        if (strlen($value ) < 7) {
            $this->error(self::MIN_LENGTH);
            return false;
        }
        
        //Check for UserID Contains only special characters
        if (!preg_match('/[0-9A-Za-z]/', $value)) {
            $this->error(self::PREDICTABLE);
            return false;
        }
        
        //Check for Predictable numeric values
        if (strlen(count_chars(strtoupper($value), 3)) == 1) {
            $this->error(self::PREDICTABLE);
            return false;
        }
        
        if (strlen(count_chars(strtoupper($value), 3)) == 1) {
            $this->error(self::PREDICTABLE);
            return false;
        }
        
        //Check if any charcters are repeated continously for more than twice.
        $chars = str_split(strtoupper($value));
        for ($iLoop = 0; $iLoop < count($chars) ; $iLoop++) {
            if ((($iLoop + 1 < count($chars)) && ($chars[$iLoop] == $chars[$iLoop+ 1]))
                && (($iLoop + 2 < count($chars)) && ($chars[$iLoop] == $chars[$iLoop+ 2]))) {
                $this->error(self::PREDICTABLE);
                return false;
            }
        }
        
        if (is_numeric($value)) {
            $sequence = "0123456789";
            $revSequence = "9876543210";
            if ((strpos($sequence, $value) !== false) || (strpos($revSequence, $value) !== false)) {
                $this->error(self::PREDICTABLE);
                return false;
            }
        }
        
        //Check for Password is same as First name, last name and email.
        if (is_array($context)) {
            if ((isset($context['nameFirst']) && strcasecmp($context['nameFirst'], $value) == 0)
                || (isset($context['nameLast']) && strcasecmp($context['nameLast'], $value) == 0)
                || (isset($context['email']) && strcasecmp($context['email'], $value) == 0)
                || (isset($context['email']) && strcasecmp(preg_replace('/([^@]*).*/', '$1', $context['email']), $value) == 0)) {
                
                $this->error(self::SAME_DATA);
                return false;
            }
        }
        
        $userInternalInfo = $this->serviceUser->getUserIsInternalAndDomainInfo($value);
        $isInternal = $userInternalInfo['isInternal'];
     
        //Check for User ID consists special charactors 
        $specialCharacters = " ~`!#$%^&*()-=+[]{}|\\;:',.<>?/\"";
        
        //We are doing this condition check because for internal users in edit mode, 
        //the username may come with a domain (@risk) so we dont want to cause a 
        //validation error for that. So only include '@' if user is non-internal or
        //if mode is add user and include that in the validation
        if (!$isInternal || $this->mode == UserForm::USER_ADD) {
            $specialCharacters .= '@';
        }
        $bSpecialCharcter = false;
        if (strpbrk($value, $specialCharacters)) {
            $bSpecialCharcter = true;
        }
        if ($bSpecialCharcter) {
            $this->error(self::INVALID_USERID);
            return false;
        }
        
        //Check for user already exists
        $excludeUserId = !empty($context['userId']) ? $context['userId'] : null;
        if (!$this->serviceUser->isUsernameUnique($value, $excludeUserId)) {
            $this->error(self::USER_EXISTS);
            return false;
        }
        
        return true;
    }
}