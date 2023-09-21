<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class TransportedSourceId extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Transported_Source_ID');
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
			'valueList' => array('Transported (Medical Treatment)'),
			'autoFill' => array(),
			'autoTab' => array('byLengthMin(255)'),
		);
	}
}
