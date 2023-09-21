<?php
/**
* @copyright (c) 2020 LexisNexis Company. All rights reserved.
*/
namespace Auth\Form\Validator;

use Zend\Validator\AbstractValidator;
use Base\Service\UserService;

class CheckNewPasswordValidator extends AbstractValidator 
{
    /**
     * @var Base\Service\UserService
     */
    protected $serviceUser;
    
    const PASSWORD_LENGTH = 'password_length';
    const VALIDATION_FAILED = 'validation_failed';
    const PASSWORD_SECURITY = 'password_security';
    const REPEATED_CHARACTERS = 'repeated_characters';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::PASSWORD_LENGTH => 'Password must be at least 8 characters',
        self::VALIDATION_FAILED => 'The New Password does not meet security standards. Please make sure the password does not contain your name or User ID',
        self::PASSWORD_SECURITY => 'Password should be combination of alphabets, numbers and special characters',
        self::REPEATED_CHARACTERS => 'Password characters must not be repeated'
    ];
    
    public function __construct(UserService $serviceUser)
    {
        $this->serviceUser = $serviceUser;
        parent::__construct();
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
        $userId = $context['username'];
        $newPassword = $value;
        $alphabet = "abcdefghijklmnopqrstuvwxyz";
        $numbers = "1234567890";
        $specialCharacters = " ~!@#$%^&*()-_=+[]{}|\\;:',.<>?/\"";
        $bSpecialCharcter = false;
        $bLower = false;
        $bUpper = false;
        $bNumber = false;
        
        if (strpbrk($newPassword, $alphabet)) {
            $bLower = true;
        }
        if (strpbrk($newPassword, strtoupper($alphabet))) {
            $bUpper = true;
        }
        if (strpbrk($newPassword, $numbers)) {
            $bNumber = true;
        }
        if (strpbrk($newPassword, $specialCharacters)) {
            $bSpecialCharcter = true;
        }
        $bValid = false;
        if (($bUpper && $bLower && $bNumber) || ($bUpper && $bLower && $bSpecialCharcter) ||
                ($bUpper && $bNumber && $bSpecialCharcter) || ($bLower && $bNumber && $bSpecialCharcter)) {
            $bValid = true;
        }
        if (!$bValid) {
            $this->error(self::PASSWORD_SECURITY);
            return false;
        }
        
        $userInfo = $this->serviceUser->getIdentityData($userId);
        if (!empty($userInfo) && (stripos($newPassword, $userInfo['username']) !== false
            || stripos($newPassword, $userInfo['nameFirst']) !== false
            || stripos($newPassword, $userInfo['nameLast']) !== false)) {
            $this->error(self::VALIDATION_FAILED);
            return false;
        }
        
        if (preg_match('/(.)\1{3}/i', $newPassword)) {
            $rValid = true;
        } else {
            $rValid = false;
        }
        
        if ($rValid) {
            $this->error(self::REPEATED_CHARACTERS);
            return false;
        }
        
        return true;
    }
}
