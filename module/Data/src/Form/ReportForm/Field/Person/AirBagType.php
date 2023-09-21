<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class AirBagType extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Air_Bag_Type');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("Air Bag Side")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Air Bag Side'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}
