<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class SafetyEquipmentAvailableOrUsed extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Safety_Equipment_Available_Or_Used');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Safety_Equipment_Available_Or_Used")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Safety_Equipment_Available_Or_Used'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}
