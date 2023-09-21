<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class MotorVehicleInvolvedWith extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Motor_Vehicle_Involved_With');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Motor_Vehicle_Involved_With")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Motor_Vehicle_Involved_With'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}
