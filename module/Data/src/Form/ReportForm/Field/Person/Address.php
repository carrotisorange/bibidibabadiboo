<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class Address extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Address');
	}

	public function getInputType()
	{
		return 'textarea';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('checkSpecialChars()'),
			'validateForceImmediate' => array('byLengthMax(100)', 'checkSpecialChars()'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}
