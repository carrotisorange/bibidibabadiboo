<?php
namespace Data\Form\ReportForm\Field\Vehicle;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class VehicleInSpecialZone extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Vehicles[#]', 'Vehicle_in_Special_Zone');
	}

	public function getInputType()
	{
		return 'checkbox';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("Traffic Control")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Traffic Control'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}
