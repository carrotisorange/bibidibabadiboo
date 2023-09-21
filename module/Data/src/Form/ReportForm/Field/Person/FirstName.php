<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class FirstName extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'First_Name');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('checkSpecialChars()', 'isAlphaNumericChar()'), 
			'validateForceImmediate' => array('byLengthMax(100)', 'checkSpecialChars()', 'isAlphaNumericChar()'), 
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => array(),
		);
	}
}
