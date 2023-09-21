<?php
namespace Data\Form\ReportForm\Field\Person\PersonType;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class Hidden extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Person_Type');
	}

	public function getInputType()
	{
		return 'hidden';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array('byLengthMax(50)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Person Types'),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => array(),
		);
	}
}
