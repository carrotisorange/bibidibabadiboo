<?php
namespace Data\Form\ReportForm\Field\Vehicle;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class UnitNumber extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Vehicles[#]', 'Unit_Number');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('isUnitNumberUnique()', 'isNumeric()', 'byLengthMax(3)'),
			'validateForceImmediate' => array('isNumeric()', 'byLengthMax(3)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array('byLengthMin(3)'),
			'customFunction' => array('syncHiddenVehicleUnitNumber()'),
		);
	}
}
