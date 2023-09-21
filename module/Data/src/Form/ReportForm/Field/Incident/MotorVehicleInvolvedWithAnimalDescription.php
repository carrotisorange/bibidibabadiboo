<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class MotorVehicleInvolvedWithAnimalDescription extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Motor_Vehicle_Involved_With_Animal_Description');
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
			'valueList' => array(''),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}
