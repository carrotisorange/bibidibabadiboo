<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class attitude extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Lattitude');
	}

	public function getInputType()
	{
		return 'text';
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
			'autoTab' => array(),
		);
	}
}
