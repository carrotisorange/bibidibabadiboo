<?php
namespace Data\Form\ReportForm\Field\Vehicle;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class MotorCarrierIDStateID extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Vehicles[#]', 'Motor_Carrier_ID_State_ID');
	}

	public function getInputType()
	{
		return 'text';
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
			'customFunction' => array(),
		);
	}
}
