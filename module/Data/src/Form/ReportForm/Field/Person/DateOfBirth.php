<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class DateOfBirth extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Date_Of_Birth');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('isDateMDY()', 'isFutureDate()'),
			'validateForceImmediate' => array('byLengthMax(10)','isDatePartial()'),
			'validateSoft' => array(),
			'valueFormat' => array('asDateMDY()'),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array('byLengthMin(10)'),
			'customFunction' => array(),
		);
	}
}
