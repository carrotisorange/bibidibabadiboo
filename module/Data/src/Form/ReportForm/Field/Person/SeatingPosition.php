<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class SeatingPosition extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Seating_Position');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Seating_Position")'),
			'validateForceImmediate' => array('byLengthMax(50)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Seating_Position'),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => array(),
		);
	}
}
