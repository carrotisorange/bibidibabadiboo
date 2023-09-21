<?php
namespace Data\Form\ReportForm\Field\Vehicle;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class InstanceId extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Vehicles[#]', 'Instance_Id');
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
		);
	}
}
