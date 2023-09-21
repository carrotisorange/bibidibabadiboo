<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class Ejection extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Ejection');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("Ejection")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Ejection'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}
