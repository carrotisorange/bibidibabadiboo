<?php
namespace Data\Form\ReportForm\Field\Vehicle;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class TrafficControlDeviceType extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Vehicles[#]', 'Traffic_Control_Device_Type');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Traffic_Control_Device_Type")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Traffic_Control_Device_Type'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}
