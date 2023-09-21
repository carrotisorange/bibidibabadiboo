<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class Sex extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Sex');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("Sex")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Sex'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}
