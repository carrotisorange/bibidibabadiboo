<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class DamagedPropertyType extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Damaged_Property_Type');
	}

	public function getInputType()
	{
		return 'checkbox';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array('byLengthMax(255)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array('byLengthMin(255)'),
		);
	}
}
