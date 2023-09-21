<?php
namespace Data\Form\ReportForm\Field\Citations;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class UnitNumber extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Citations[#]', 'Unit_Number');
	}

	public function getInputType()
	{
		return 'hidden';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => array('syncHiddenVehicleUnitNumber()'),
		);
	}
}
