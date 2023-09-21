<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class TransportedTo extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Transported_To');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array('byLengthMax(60)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => array(),
		);
	}
}
