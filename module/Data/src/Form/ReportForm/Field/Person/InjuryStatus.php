<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class InjuryStatus extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Injury_Status');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("injuryStatus")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('injuryStatus'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}
