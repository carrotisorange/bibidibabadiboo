<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class EmergencyPersonnelOnCall extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Emergency_Personnel_on_Call');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}
