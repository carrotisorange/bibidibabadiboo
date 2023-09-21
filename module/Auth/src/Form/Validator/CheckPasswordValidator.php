<?php
/**
* @copyright (c) 2020 LexisNexis Company. All rights reserved.
*/
namespace Auth\Form\Validator;

use Zend\Validator\AbstractValidator;
use Base\Service\UserService;

use Auth\Service\LNAAAuthService;

class CheckPasswordValidator extends AbstractValidator 
{
    /**
     * @var Base\Service\UserService
     */
    protected $serviceUser;
    
    const INCORRECT = 'incorrect';
    const VALIDATION_FAILED = 'validation_failed';
    const INVALID_CREDENTIALS = 'invalid_credentials';
    const PASSWORD_CHANGED_TOO_OFTEN = 'password_changed_too_often';
    const INACTIVE = 'user is in inactive state';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::INCORRECT => 'Incorrect password',
        self::VALIDATION_FAILED => 'The New Password does not meet security standards. Please make sure the password does not contain your name or User ID, and that you have not already used that password as one of your last 12 passwords',
        self::INVALID_CREDENTIALS => 'Invalid User ID or Password',
        self::PASSWORD_CHANGED_TOO_OFTEN => 'Password cannot be changed more than once within %value% days without administrative assistance',
        self::INACTIVE => 'Inactive User ID. Please contact Administrator'
    ];
    
    public function __construct(UserService $serviceUser , LNAAAuthService $serviceLnaaAuth, $passwordChangeInterval)
    {
        $this->serviceUser = $serviceUser;
        $this->serviceLnaaAuth = $serviceLnaaAuth;
        $this->passwordChangeInterval = $passwordChangeInterval;
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
        $username = $context['username'];
        $password = $value;
        $valid = false;
        
        $userInfo = $this->serviceUser->getUserRowInfoByUsername($username);
        $userEmail = null;
        if (!empty($userInfo)) {
            $userEmail = $userInfo['email'];
        }
        //Check if username contains a domain - it is an internal user
        $userNameInfo = explode("@", $username);
        $userNameWithoutDomain = $userNameInfo[0];
        $userInternalInfo = $this->serviceUser->getUserIsInternalAndDomainInfo($username, $userEmail);
        $domain = $userInternalInfo['domain'];
        
        $result = $this->serviceLnaaAuth->authUser($userNameWithoutDomain, $password, $domain);
        if (!empty($userInfo['userId']) && !($userInfo['isActive'] == true)) {
            $valid = true;
        }        
        if ($result->status->code == LNAAAuthService::CODE_SUCCESS) {
            
            if (!empty($username)) {
                if ($valid) {
                    $this->error(self::INVALID_CREDENTIALS);
                    return false;
                }
                $intervalDays = floor(abs(strtotime(date('Y-m-d H:i:s')) - strtotime($userInfo['datePasswordSet'])) / (60 * 60 * 24));
                if ($intervalDays < $this->passwordChangeInterval) {
                    // @TODO: ZF1 function not available in ZF3.
                    // $this->setObscureValue(false);
                    $this->setValue($this->passwordChangeInterval);
                    $this->error(self::PASSWORD_CHANGED_TOO_OFTEN);
                    return false;
                }
            }
            return true;
        } elseif ($result->status->code == LNAAAuthService::PASSWORD_RESET_REQUIRED || $result->status->code == LNAAAuthService::PASSWORD_EXPIRED) {
            
            if (!empty($username)) {
                if ($valid) {
                    $this->error(self::INACTIVE);
                    return false;
                }
            }
            return true;
        } else {
            $this->error(self::INVALID_CREDENTIALS);
            return false;
        }
    }
}