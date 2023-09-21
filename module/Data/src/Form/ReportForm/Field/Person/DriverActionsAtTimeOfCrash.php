<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class DriverActionsAtTimeOfCrash extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Driver_Actions_At_Time_Of_Crash');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Driver_Actions_At_Time_Of_Crash")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array("Driver_Actions_At_Time_Of_Crash"),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => array(),
		);
	}
}
