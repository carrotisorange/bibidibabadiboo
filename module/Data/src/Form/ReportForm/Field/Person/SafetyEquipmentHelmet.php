<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class SafetyEquipmentHelmet extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Safety_Equipment_Helmet');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Safety_Equipment_Helmet")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Safety_Equipment_Helmet'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}
