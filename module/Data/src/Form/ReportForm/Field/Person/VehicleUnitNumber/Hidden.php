<?php
namespace Data\Form\ReportForm\Field\Person\VehicleUnitNumber;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class Hidden extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Unit_Number');
	}

	public function getInputType()
	{
		return 'hidden';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array('byLengthMax(3)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => array(),
		);
	}
}
