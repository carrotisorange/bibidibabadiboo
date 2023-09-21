<?php
namespace Data\Form\ReportForm\Field\Vehicle;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class TotalOccupantsInVehicle extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Vehicles[#]', 'Total_Occupants_in_Vehicle');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array('byLengthMax(4)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}
