<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class HomePhone extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Home_Phone');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('checkSpecialChars()'),
			'validateForceImmediate' => array('isValidPhoneNumber()', 'checkSpecialChars()'),
			'validateSoft' => array('isValidPhoneNumber()'),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}
