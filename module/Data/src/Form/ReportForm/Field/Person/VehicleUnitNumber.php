<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class VehicleUnitNumber extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Unit_Number');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('isNumeric()','byLengthMax(3)'),
			'validateForceImmediate' => array('isNumeric()','byLengthMax(3)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => array(),
		);
	}
}
