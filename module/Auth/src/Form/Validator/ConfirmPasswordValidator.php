<?php
/**
* @copyright (c) 2020 LexisNexis Company. All rights reserved.
*/

namespace Auth\Form\Validator;

use Zend\Validator\AbstractValidator;

class ConfirmPasswordValidator extends AbstractValidator
{
    const MISMATCH = 'mismatch';
    const SAME_PASSWORD = 'same_password';
    
    /**
     * @var array
     */
    protected $messageTemplates = [
        self::MISMATCH => 'Passwords do not match',
        self::SAME_PASSWORD => 'Current Password and New Password can not be the same'
    ];
    
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
        if (empty($value) or empty($context['passwordConfirm'])) {
            return true;
        }
        
        if ($value != $context['passwordConfirm']) {
            $this->error(self::MISMATCH);
            return false;
        }
        
        if (!empty($context['passwordCurrent']) and $context['passwordCurrent'] == $value) {
            $this->error(self::SAME_PASSWORD);
            return false;
        }
        
        return true;
    }
}
