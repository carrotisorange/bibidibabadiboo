<?php
namespace Data\Form\ReportForm\Field\Vehicle;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class VehicleTypeIfPullingOtherUnit extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Vehicles[#]', 'Vehicle_Type_if_Pulling_Other_Unit');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array('byLengthMax(255)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}
