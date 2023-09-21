<?php
/**
* @copyright (c) 2020 LexisNexis Company. All rights reserved.
*/
namespace Auth\Form\Validator;

use Zend\Validator\AbstractValidator;

class CheckSecurityAnswerValidator extends AbstractValidator 
{
    const PREDICTABLE = "PREDICTABLE";
    
    /**
     * @var array
     */
    protected $messageTemplates = [
        self::PREDICTABLE => "Answers must not contain blanks or repeating characters."
    ];
    
	/**
	 * @var array
	 */
	protected $messageVariables = [];
    
	/**
	 * Constructor of this validator
	 *
	 * The argument to this constructor is the third argument to the elements' addValidator
	 * method.
	 *
	 * @param array|string $fieldsToMatch
	 */
     
	public function __construct()
	{

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

		if (strlen(count_chars(strtoupper($value), 3)) == 1) {
			$this->error(self::PREDICTABLE);
			return false;
		}

		return true;
	}
}
